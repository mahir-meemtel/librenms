<?php
namespace ObzoraNMS\Tests\Feature\Commands;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Tests\InMemoryDbTestCase;

class TestConfigCommands extends InMemoryDbTestCase
{
    public function testSetting(): void
    {
        // simple
        ObzoraConfig::set('login_message', null);
        $this->assertCliSets('login_message', 'hello');

        // nested
        ObzoraConfig::forget('allow_entity_sensor.amperes');
        $this->assertCliSets('allow_entity_sensor.amperes', 'false');

        // set inside
        $this->assertCliGets('auth_ldap_groups.somegroup', null);
        $this->artisan('config:set', ['setting' => 'auth_ldap_groups.somegroup', 'value' => '{"roles": ["banana"]}'])->assertExitCode(0);
        $this->assertCliGets('auth_ldap_groups.somegroup', ['roles' => ['banana']]);
        $this->artisan('config:set', ['setting' => 'auth_ldap_groups.somegroup'])
            ->expectsConfirmation(trans('commands.config:set.forget_from', ['path' => 'somegroup', 'parent' => 'auth_ldap_groups']), 'yes')
            ->assertExitCode(0);

        // test append
        $community = ObzoraConfig::get('snmp.community');
        $this->assertCliGets('snmp.community', $community);
        $community[] = 'extra_community';
        $this->artisan('config:set', ['setting' => 'snmp.community.+', 'value' => 'extra_community'])->assertExitCode(0);
        $this->assertCliGets('snmp.community', $community);

        // os bool
        $this->assertCliSets('os.ios.rfc1628_compat', true);

        // os array
        $this->assertCliSets('os.netonix.bad_iftype', ['ethernet', 'psuedowire']);

        // os array append
        $this->artisan('config:set', ['setting' => 'os.netonix.bad_iftype', 'value' => '["ethernet","psuedowire"]'])->assertExitCode(0);
        $this->assertEquals(['ethernet', 'psuedowire'], ObzoraConfig::get('os.netonix.bad_iftype'));
        $this->artisan('config:set', ['setting' => 'os.netonix.bad_iftype.+', 'value' => 'other'])->assertExitCode(0);
        $this->assertCliGets('os.netonix.bad_iftype', ['ethernet', 'psuedowire', 'other']);

        // dump
        $this->artisan('config:get', ['--dump' => true])
            ->expectsOutput(ObzoraConfig::toJson())
            ->assertExitCode(0);
    }

    public function testInvalidSetting(): void
    {
        // non-existent setting
        $this->artisan('config:set', ['setting' => 'this_will_never_be.a.setting'])
            ->assertExitCode(2);

        // invalid type
        $this->artisan('config:set', ['setting' => 'alert_rule.interval', 'value' => 'string', '--no-ansi' => true])
            ->expectsOutput(trans('settings.validate.integer', ['value' => '"string"']))
            ->assertExitCode(2);

        // non-existent os
        $this->artisan('config:set', ['setting' => 'os.someos.this_will_never_be.a.setting'])
            ->expectsOutput(trans('commands.config:set.errors.invalid_os', ['os' => 'someos']))
            ->assertExitCode(2);

        // non-existent os setting
        $this->artisan('config:set', ['setting' => 'os.ios.this_will_never_be.a.setting'])
            ->doesntExpectOutput(trans('commands.config:set.errors.invalid_os', ['os' => 'ios']))
            ->assertExitCode(2);

        // append to non-array
        ObzoraConfig::set('login_message', 'blah');
        ObzoraConfig::get('login_message');
        $this->artisan('config:set', ['setting' => 'login_message.+', 'value' => 'something', '--no-ansi' => true])
            ->expectsOutput(trans('commands.config:set.errors.append'))
            ->assertExitCode(2);
    }

    /**
     * @param  string  $setting
     * @param  mixed  $expected
     */
    private function assertCliSets(string $setting, $expected): void
    {
        $this->assertCliGets($setting, null);
        $this->artisan('config:set', ['setting' => $setting, 'value' => json_encode($expected)])->assertExitCode(0);
        $this->assertCliGets($setting, $expected);
        $this->artisan('config:set', ['setting' => $setting])
            ->expectsQuestion(trans('commands.config:set.confirm', ['setting' => $setting]), true)
            ->assertExitCode(0);
        $this->assertCliGets($setting, null);
    }

    /**
     * @param  string  $setting
     * @param  mixed  $expected
     */
    private function assertCliGets(string $setting, $expected): void
    {
        $this->assertSame($expected, ObzoraConfig::get($setting));

        $command = $this->artisan('config:get', ['setting' => $setting]);
        if ($expected === null) {
            $command->assertExitCode(1);

            return;
        }

        $command->assertExitCode(0)
            ->expectsOutput(is_string($expected) ? $expected : json_encode($expected, JSON_PRETTY_PRINT))
            ->assertExitCode(0);
    }
}
