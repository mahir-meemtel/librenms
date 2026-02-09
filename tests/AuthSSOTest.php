<?php
namespace ObzoraNMS\Tests;

use App\Facades\ObzoraConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use ObzoraNMS\Authentication\LegacyAuth;

class AuthSSOTest extends DBTestCase
{
    use DatabaseTransactions;

    private $original_auth_mech = null;
    private $server;

    protected function setUp(): void
    {
        parent::setUp();

        $this->original_auth_mech = ObzoraConfig::get('auth_mechanism');
        ObzoraConfig::set('auth_mechanism', 'sso');

        $this->server = $_SERVER;
    }

    // Set up an SSO config for tests
    public function basicConfig()
    {
        ObzoraConfig::set('sso.mode', 'env');
        ObzoraConfig::set('sso.create_users', true);
        ObzoraConfig::set('sso.update_users', true);
        ObzoraConfig::set('sso.trusted_proxies', ['127.0.0.1', '::1']);
        ObzoraConfig::set('sso.user_attr', 'REMOTE_USER');
        ObzoraConfig::set('sso.realname_attr', 'displayName');
        ObzoraConfig::set('sso.email_attr', 'mail');
        ObzoraConfig::set('sso.descr_attr', null);
        ObzoraConfig::set('sso.level_attr', null);
        ObzoraConfig::set('sso.group_strategy', 'static');
        ObzoraConfig::set('sso.group_attr', 'member');
        ObzoraConfig::set('sso.group_filter', '/(.*)/i');
        ObzoraConfig::set('sso.group_delimiter', ';');
        ObzoraConfig::set('sso.group_level_map', null);
        ObzoraConfig::set('sso.static_level', -1);
    }

    // Set up $_SERVER in env mode
    public function basicEnvironmentEnv()
    {
        unset($_SERVER);

        ObzoraConfig::set('sso.mode', 'env');

        $_SERVER['REMOTE_ADDR'] = '::1';
        $_SERVER['REMOTE_USER'] = 'test';

        $_SERVER['mail'] = 'test@example.org';
        $_SERVER['displayName'] = Str::random();
    }

    // Set up $_SERVER in header mode
    public function basicEnvironmentHeader()
    {
        unset($_SERVER);

        ObzoraConfig::set('sso.mode', 'header');

        $_SERVER['REMOTE_ADDR'] = '::1';
        $_SERVER['REMOTE_USER'] = Str::random();

        $_SERVER['HTTP_MAIL'] = 'test@example.org';
        $_SERVER['HTTP_DISPLAYNAME'] = 'Test User';
    }

    public function makeUser()
    {
        $user = Str::random();
        $_SERVER['REMOTE_USER'] = $user;

        return $user;
    }

    // Excercise general auth flow
    public function testValidAuthNoCreateUpdate(): void
    {
        $this->basicConfig();
        $a = LegacyAuth::reset();

        ObzoraConfig::set('sso.create_users', false);
        ObzoraConfig::set('sso.update_users', false);

        // Create a random username and store it with the defaults
        $this->basicEnvironmentEnv();
        $user = $this->makeUser();
        $this->assertTrue($a->authenticate(['username' => $user]));

        // Retrieve it and validate
        $this->assertFalse(User::thisAuth()->where('username', $user)->exists());
    }

    // Excercise general auth flow with creation enabled
    public function testValidAuthCreateOnly(): void
    {
        $this->basicConfig();
        /** @var \ObzoraNMS\Authentication\SSOAuthorizer */
        $a = LegacyAuth::reset();

        ObzoraConfig::set('sso.create_users', true);
        ObzoraConfig::set('sso.update_users', false);

        // Create a random username and store it with the defaults
        $this->basicEnvironmentEnv();
        $user = $this->makeUser();
        $this->assertTrue($a->authenticate(['username' => $user]));

        // Retrieve it and validate
        $dbuser = User::thisAuth()->where('username', $user)->firstOrNew();
        $this->assertSame($dbuser->realname, $a->authSSOGetAttr(ObzoraConfig::get('sso.realname_attr')));
        $this->assertEmpty($dbuser->getRoleNames());
        $this->assertSame($dbuser->email, $a->authSSOGetAttr(ObzoraConfig::get('sso.email_attr')));

        // Change a few things and reauth
        $_SERVER['mail'] = 'test@example.net';
        $_SERVER['displayName'] = 'Testier User';
        ObzoraConfig::set('sso.static_level', 10);
        $this->assertTrue($a->authenticate(['username' => $user]));

        // Retrieve it and validate the update was not persisted
        $dbuser = User::thisAuth()->where('username', $user)->firstOrNew();
        $this->assertFalse($a->authSSOGetAttr(ObzoraConfig::get('sso.realname_attr')) === $dbuser->realname);
        $this->assertFalse($dbuser->roles()->where('name', 'admin')->exists());
        $this->assertFalse($a->authSSOGetAttr(ObzoraConfig::get('sso.email_attr')) === $dbuser->email);
    }

    // Excercise general auth flow with updates enabled
    public function testValidAuthUpdate(): void
    {
        $this->basicConfig();
        /** @var \ObzoraNMS\Authentication\SSOAuthorizer */
        $a = LegacyAuth::reset();

        // Create a random username and store it with the defaults
        $this->basicEnvironmentEnv();
        $user = $this->makeUser();
        $this->assertTrue(auth()->attempt(['username' => $user]));

        // Change a few things and reauth
        $_SERVER['mail'] = 'test@example.net';
        $_SERVER['displayName'] = 'Testier User';
        ObzoraConfig::set('sso.static_level', 10);
        $this->assertTrue(auth()->attempt(['username' => $user]));

        // Retrieve it and validate the update persisted
        $dbuser = User::thisAuth()->where('username', $user)->firstOrNew();
        $this->assertSame($dbuser->realname, $a->authSSOGetAttr(ObzoraConfig::get('sso.realname_attr')));
        $this->assertTrue($dbuser->roles()->where('name', 'admin')->exists());
        $this->assertSame($dbuser->email, $a->authSSOGetAttr(ObzoraConfig::get('sso.email_attr')));
    }

    // Check some invalid authentication modes
    public function testBadAuth(): void
    {
        $this->basicConfig();
        /** @var \ObzoraNMS\Authentication\SSOAuthorizer */
        $a = LegacyAuth::reset();

        $this->basicEnvironmentEnv();
        unset($_SERVER);

        $this->expectException('ObzoraNMS\Exceptions\AuthenticationException');
        $a->authenticate([]);

        $this->basicEnvironmentHeader();
        unset($_SERVER);

        $this->expectException('ObzoraNMS\Exceptions\AuthenticationException');
        $a->authenticate([]);
    }

    // Test some missing attributes
    public function testNoAttribute(): void
    {
        $this->basicConfig();
        LegacyAuth::reset();

        $this->basicEnvironmentEnv();
        unset($_SERVER['displayName']);
        unset($_SERVER['mail']);

        $this->assertTrue(auth()->attempt(['username' => $this->makeUser()]));

        $this->basicEnvironmentHeader();
        unset($_SERVER['HTTP_DISPLAYNAME']);
        unset($_SERVER['HTTP_MAIL']);

        $this->assertTrue(auth()->attempt(['username' => $this->makeUser()]));
    }

    // Document the modules current behaviour, so that changes trigger test failures
    public function testCapabilityFunctions(): void
    {
        $a = LegacyAuth::reset();

        $this->assertFalse($a->canUpdatePasswords());
        $this->assertTrue($a->canManageUsers());
        $this->assertTrue($a->canUpdateUsers());
        $this->assertTrue($a->authIsExternal());
    }

    /* Everything from here comprises of targeted tests to excercise single methods */

    public function testGetExternalUserName(): void
    {
        $this->basicConfig();
        /** @var \ObzoraNMS\Authentication\SSOAuthorizer */
        $a = LegacyAuth::reset();

        $this->basicEnvironmentEnv();
        $this->assertIsString($a->getExternalUsername());

        // Missing
        unset($_SERVER['REMOTE_USER']);
        $this->assertNull($a->getExternalUsername());
        $this->basicEnvironmentEnv();

        // Missing pointer to attribute
        ObzoraConfig::forget('sso.user_attr');
        $this->assertNull($a->getExternalUsername());
        $this->basicEnvironmentEnv();

        // Non-existant attribute
        ObzoraConfig::set('sso.user_attr', 'foobar');
        $this->assertNull($a->getExternalUsername());
        $this->basicEnvironmentEnv();

        // null pointer to attribute
        ObzoraConfig::set('sso.user_attr', null);
        $this->assertNull($a->getExternalUsername());
        $this->basicEnvironmentEnv();

        // null attribute
        ObzoraConfig::set('sso.user_attr', 'REMOTE_USER');
        $_SERVER['REMOTE_USER'] = null;
        $this->assertNull($a->getExternalUsername());
    }

    public function testGetAttr(): void
    {
        /** @var \ObzoraNMS\Authentication\SSOAuthorizer */
        $a = LegacyAuth::reset();

        $_SERVER['HTTP_VALID_ATTR'] = 'string';
        $_SERVER['alsoVALID-ATTR'] = 'otherstring';

        ObzoraConfig::set('sso.mode', 'env');
        $this->assertNull($a->authSSOGetAttr('foobar'));
        $this->assertNull($a->authSSOGetAttr(null));
        $this->assertNull($a->authSSOGetAttr(1));
        $this->assertIsString($a->authSSOGetAttr('alsoVALID-ATTR'));
        $this->assertIsString($a->authSSOGetAttr('HTTP_VALID_ATTR'));

        ObzoraConfig::set('sso.mode', 'header');
        $this->assertNull($a->authSSOGetAttr('foobar'));
        $this->assertNull($a->authSSOGetAttr(null));
        $this->assertNull($a->authSSOGetAttr(1));
        $this->assertNull($a->authSSOGetAttr('alsoVALID-ATTR'));
        $this->assertIsString($a->authSSOGetAttr('VALID-ATTR'));
    }

    public function testTrustedProxies(): void
    {
        /** @var \ObzoraNMS\Authentication\SSOAuthorizer */
        $a = LegacyAuth::reset();

        ObzoraConfig::set('sso.trusted_proxies', ['127.0.0.1', '::1', '2001:630:50::/48', '8.8.8.0/25']);

        // v4 valid CIDR
        $_SERVER['REMOTE_ADDR'] = '8.8.8.8';
        $this->assertTrue($a->authSSOProxyTrusted());

        // v4 valid single
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->assertTrue($a->authSSOProxyTrusted());

        // v4 invalid CIDR
        $_SERVER['REMOTE_ADDR'] = '9.8.8.8';
        $this->assertFalse($a->authSSOProxyTrusted());

        // v6 valid CIDR
        $_SERVER['REMOTE_ADDR'] = '2001:630:50:baad:beef:feed:face:cafe';
        $this->assertTrue($a->authSSOProxyTrusted());

        // v6 valid single
        $_SERVER['REMOTE_ADDR'] = '::1';
        $this->assertTrue($a->authSSOProxyTrusted());

        // v6 invalid CIDR
        $_SERVER['REMOTE_ADDR'] = '2600::';
        $this->assertFalse($a->authSSOProxyTrusted());

        // Not an IP
        $_SERVER['REMOTE_ADDR'] = 16;
        $this->assertFalse($a->authSSOProxyTrusted());

        //null
        $_SERVER['REMOTE_ADDR'] = null;
        $this->assertFalse($a->authSSOProxyTrusted());

        // Invalid String
        $_SERVER['REMOTE_ADDR'] = 'Not an IP address at all, but maybe PHP will end up type juggling somehow';
        $this->assertFalse($a->authSSOProxyTrusted());

        // Not a list
        ObzoraConfig::set('sso.trusted_proxies', '8.8.8.0/25');
        $_SERVER['REMOTE_ADDR'] = '8.8.8.8';
        $this->assertFalse($a->authSSOProxyTrusted());

        // Unset
        unset($_SERVER['REMOTE_ADDR']);
        $this->assertFalse($a->authSSOProxyTrusted());
    }

    public function testLevelCaulculationFromAttr(): void
    {
        /** @var \ObzoraNMS\Authentication\SSOAuthorizer $a */
        $a = LegacyAuth::reset();

        ObzoraConfig::set('sso.mode', 'env');
        ObzoraConfig::set('sso.group_strategy', 'attribute');

        //Integer
        ObzoraConfig::set('sso.level_attr', 'level');
        $_SERVER['level'] = 5;
        $this->assertSame(['global-read'], $a->getRoles(''));

        //String
        ObzoraConfig::set('sso.level_attr', 'level');
        $_SERVER['level'] = '5';
        $this->assertSame(['global-read'], $a->getRoles(''));

        // invalid level
        ObzoraConfig::set('sso.level_attr', 'level');
        $_SERVER['level'] = 9;
        $this->assertSame([], $a->getRoles(''));

        //Invalid String
        ObzoraConfig::set('sso.level_attr', 'level');
        $_SERVER['level'] = 'foobar';
        $this->expectException('ObzoraNMS\Exceptions\AuthenticationException');
        $a->getRoles('');

        //null
        ObzoraConfig::set('sso.level_attr', 'level');
        $_SERVER['level'] = null;
        $this->expectException('ObzoraNMS\Exceptions\AuthenticationException');
        $a->getRoles('');

        //Unset pointer
        ObzoraConfig::forget('sso.level_attr');
        $_SERVER['level'] = '9';
        $this->expectException('ObzoraNMS\Exceptions\AuthenticationException');
        $a->getRoles('');

        //Unset attr
        ObzoraConfig::set('sso.level_attr', 'level');
        unset($_SERVER['level']);
        $this->expectException('ObzoraNMS\Exceptions\AuthenticationException');
        $a->getRoles('');
    }

    public function testGroupParsing(): void
    {
        $this->basicConfig();
        /** @var \ObzoraNMS\Authentication\SSOAuthorizer */
        $a = LegacyAuth::reset();

        $this->basicEnvironmentEnv();

        ObzoraConfig::set('sso.static_level', 0);
        ObzoraConfig::set('sso.group_strategy', 'map');
        ObzoraConfig::set('sso.group_delimiter', ';');
        ObzoraConfig::set('sso.group_attr', 'member');
        ObzoraConfig::set('sso.group_level_map', ['obzora-admins' => 10, 'obzora-readers' => 1, 'obzora-billingcontacts' => 5]);
        $_SERVER['member'] = 'obzora-admins;obzora-readers;obzora-billingcontacts;unrelatedgroup;confluence-admins';

        // Valid options
        $this->assertSame(10, $a->authSSOParseGroups());

        // No match
        $_SERVER['member'] = 'confluence-admins';
        $this->assertSame(0, $a->authSSOParseGroups());

        // Delimiter only
        $_SERVER['member'] = ';;;;';
        $this->assertSame(0, $a->authSSOParseGroups());

        // Empty
        $_SERVER['member'] = '';
        $this->assertSame(0, $a->authSSOParseGroups());

        // Empty with default access level
        ObzoraConfig::set('sso.static_level', 5);
        $this->assertSame(5, $a->authSSOParseGroups());
        ObzoraConfig::forget('sso.static_level');

        // Null
        $_SERVER['member'] = null;
        $this->assertSame(0, $a->authSSOParseGroups());

        // Unset
        unset($_SERVER['member']);
        $this->assertSame(0, $a->authSSOParseGroups());

        $_SERVER['member'] = 'obzora-admins;obzora-readers;obzora-billingcontacts;unrelatedgroup;confluence-admins';

        // Empty
        ObzoraConfig::set('sso.group_level_map', []);
        $this->assertSame(0, $a->authSSOParseGroups());

        // Not associative
        ObzoraConfig::set('sso.group_level_map', ['foo', 'bar', 'obzora-admins']);
        $this->assertSame(0, $a->authSSOParseGroups());

        // Null
        ObzoraConfig::set('sso.group_level_map', null);
        $this->assertSame(0, $a->authSSOParseGroups());

        // Unset
        ObzoraConfig::forget('sso.group_level_map');
        $this->assertSame(0, $a->authSSOParseGroups());

        // No delimiter
        ObzoraConfig::forget('sso.group_delimiter');
        $this->assertSame(0, $a->authSSOParseGroups());

        // Test group filtering by regex
        ObzoraConfig::set('sso.group_filter', '/confluence-(.*)/i');
        ObzoraConfig::set('sso.group_delimiter', ';');
        ObzoraConfig::set('sso.group_level_map', ['obzora-admins' => 10, 'obzora-readers' => 1, 'obzora-billingcontacts' => 5, 'confluence-admins' => 7]);
        $this->assertSame(7, $a->authSSOParseGroups());

        // Test group filtering by empty regex
        ObzoraConfig::set('sso.group_filter', '');
        $this->assertSame(10, $a->authSSOParseGroups());

        // Test group filtering by null regex
        ObzoraConfig::set('sso.group_filter', null);
        $this->assertSame(10, $a->authSSOParseGroups());
    }

    protected function tearDown(): void
    {
        ObzoraConfig::set('auth_mechanism', $this->original_auth_mech);
        ObzoraConfig::forget('sso');
        $_SERVER = $this->server;
        parent::tearDown();
    }
}
