<?php
namespace ObzoraNMS\Tests;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Authentication\LegacyAuth;

use function strip_tags;
use function strip_tags as strip_tags1;

class AuthHTTPTest extends TestCase
{
    private $original_auth_mech;
    private $server;

    protected function setUp(): void
    {
        parent::setUp();

        $this->original_auth_mech = ObzoraConfig::get('auth_mechanism');
        ObzoraConfig::set('auth_mechanism', 'http-auth');
        $this->server = $_SERVER;
    }

    protected function tearDown(): void
    {
        ObzoraConfig::set('auth_mechanism', $this->original_auth_mech);
        $_SERVER = $this->server;
        parent::tearDown();
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

    public function testOldBehaviourAgainstCurrent(): void
    {
        $old_username = null;
        $new_username = null;

        $users = ['steve',  '   steve', 'steve   ', '   steve   ', '    steve   ', '', 'CAT'];
        $vars = ['REMOTE_USER', 'PHP_AUTH_USER'];

        $a = LegacyAuth::reset();

        foreach ($vars as $v) {
            foreach ($users as $u) {
                $_SERVER[$v] = $u;

                // Old Behaviour
                if (isset($_SERVER['REMOTE_USER'])) {
                    $old_username = strip_tags1($_SERVER['REMOTE_USER']);
                } elseif (isset($_SERVER['PHP_AUTH_USER']) && ObzoraConfig::get('auth_mechanism') === 'http-auth') {
                    $old_username = strip_tags($_SERVER['PHP_AUTH_USER']);
                }

                // Current Behaviour
                if ($a->authIsExternal()) {
                    $new_username = $a->getExternalUsername();
                }

                $this->assertFalse($old_username === null);
                $this->assertFalse($new_username === null);

                $this->assertTrue($old_username === $new_username);
            }

            unset($_SERVER[$v]);
        }
    }
}
