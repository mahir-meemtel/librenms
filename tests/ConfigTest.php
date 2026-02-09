<?php
namespace ObzoraNMS\Tests;

use App\ConfigRepository;
use App\Facades\ObzoraConfig;

class ConfigTest extends TestCase
{
    private \ReflectionProperty $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new \ReflectionProperty(ConfigRepository::class, 'config');
    }

    public function testGetBasic(): void
    {
        $dir = realpath(__DIR__ . '/..');
        $this->assertEquals($dir, ObzoraConfig::get('install_dir'));
    }

    public function testSetBasic(): void
    {
        $instance = $this->app->make('obzora-config');
        ObzoraConfig::set('basics', 'first');
        $this->assertEquals('first', $this->config->getValue($instance)['basics']);
    }

    public function testGet(): void
    {
        $this->setConfig(function (&$config) {
            $config['one']['two']['three'] = 'easy';
        });

        $this->assertEquals('easy', ObzoraConfig::get('one.two.three'));
    }

    public function testGetDeviceSetting(): void
    {
        $device = ['set' => true, 'null' => null];
        $this->setConfig(function (&$config) {
            $config['null'] = 'notnull!';
            $config['noprefix'] = true;
            $config['prefix']['global'] = true;
        });

        $this->assertNull(ObzoraConfig::getDeviceSetting($device, 'unset'), 'Non-existing settings should return null');
        $this->assertTrue(ObzoraConfig::getDeviceSetting($device, 'set'), 'Could not get setting from device array');
        $this->assertTrue(ObzoraConfig::getDeviceSetting($device, 'noprefix'), 'Failed to get setting from global config');
        $this->assertEquals(
            'notnull!',
            ObzoraConfig::getDeviceSetting($device, 'null'),
            'Null variables should defer to the global setting'
        );
        $this->assertTrue(
            ObzoraConfig::getDeviceSetting($device, 'global', 'prefix'),
            'Failed to get setting from global config with a prefix'
        );
        $this->assertEquals(
            'default',
            ObzoraConfig::getDeviceSetting($device, 'something', 'else', 'default'),
            'Failed to return the default argument'
        );
    }

    public function testGetOsSetting(): void
    {
        $this->setConfig(function (&$config) {
            $config['os']['nullos']['fancy'] = true;
            $config['fallback'] = true;
        });

        $this->assertNull(ObzoraConfig::getOsSetting(null, 'unset'), '$os is null, should return null');
        $this->assertNull(ObzoraConfig::getOsSetting('nullos', 'unset'), 'Non-existing settings should return null');
        $this->assertFalse(ObzoraConfig::getOsSetting('nullos', 'unset', false), 'Non-existing settings should return $default');
        $this->assertTrue(ObzoraConfig::getOsSetting('nullos', 'fancy'), 'Failed to get setting');
        $this->assertNull(ObzoraConfig::getOsSetting('nullos', 'fallback'), 'Incorrectly loaded global setting');

        // load yaml
        $this->assertSame('ios', ObzoraConfig::getOsSetting('ios', 'os'));
        $this->assertGreaterThan(500, count(ObzoraConfig::get('os')), 'Not all OS were loaded from yaml');
    }

    public function testGetCombined(): void
    {
        $this->setConfig(function (&$config) {
            $config['num'] = ['one', 'two'];
            $config['withprefix']['num'] = ['four', 'five'];
            $config['os']['nullos']['num'] = ['two', 'three'];
            $config['assoc'] = ['a' => 'same', 'b' => 'same'];
            $config['withprefix']['assoc'] = ['a' => 'prefix_same', 'd' => 'prefix_same'];
            $config['os']['nullos']['assoc'] = ['b' => 'different', 'c' => 'still same'];
            $config['os']['nullos']['osset'] = 'ossetting';
            $config['gset'] = 'fallbackone';
            $config['withprefix']['gset'] = 'fallbacktwo';
        });

        $this->assertSame(['default'], ObzoraConfig::getCombined('nullos', 'non-existent', '', ['default']), 'Did not return default value on non-existent key');
        $this->assertSame(['ossetting'], ObzoraConfig::getCombined('nullos', 'osset', '', ['default']), 'Did not return OS value when global value is not set');
        $this->assertSame(['fallbackone'], ObzoraConfig::getCombined('nullos', 'gset', '', ['default']), 'Did not return global value when OS value is not set');
        $this->assertSame(['default'], ObzoraConfig::getCombined('nullos', 'non-existent', 'withprefix.', ['default']), 'Did not return default value on non-existent key');
        $this->assertSame(['ossetting'], ObzoraConfig::getCombined('nullos', 'osset', 'withprefix.', ['default']), 'Did not return OS value when global value is not set');
        $this->assertSame(['fallbacktwo'], ObzoraConfig::getCombined('nullos', 'gset', 'withprefix.', ['default']), 'Did not return global value when OS value is not set');

        $combined = ObzoraConfig::getCombined('nullos', 'num');
        sort($combined);
        $this->assertEquals(['one', 'three', 'two'], $combined);

        $combined = ObzoraConfig::getCombined('nullos', 'num', 'withprefix.');
        sort($combined);
        $this->assertEquals(['five', 'four', 'three', 'two'], $combined);

        $this->assertSame(['a' => 'same', 'b' => 'different', 'c' => 'still same'], ObzoraConfig::getCombined('nullos', 'assoc'));
        // should associative not ignore same values (d=>prefix_same)?  are associative arrays actually used?
        $this->assertSame(['a' => 'prefix_same', 'b' => 'different', 'c' => 'still same'], ObzoraConfig::getCombined('nullos', 'assoc', 'withprefix.'));
    }

    public function testSet(): void
    {
        $instance = $this->app->make('obzora-config');
        ObzoraConfig::set('you.and.me', "I'll be there");

        $this->assertEquals("I'll be there", $this->config->getValue($instance)['you']['and']['me']);
    }

    public function testSetPersist(): void
    {
        $this->dbSetUp();

        $key = 'testing.persist';

        $query = \App\Models\Config::query()->where('config_name', $key);

        $query->delete();
        $this->assertFalse($query->exists(), "$key should not be set, clean database");
        ObzoraConfig::persist($key, 'one');
        $this->assertEquals('one', $query->value('config_value'));
        ObzoraConfig::persist($key, 'two');
        $this->assertEquals('two', $query->value('config_value'));

        $this->dbTearDown();
    }

    public function testHas(): void
    {
        ObzoraConfig::set('long.key.setting', 'no one cares');
        ObzoraConfig::set('null', null);

        $this->assertFalse(ObzoraConfig::has('null'), 'Keys set to null do not count as existing');
        $this->assertTrue(ObzoraConfig::has('long'), 'Top level key should exist');
        $this->assertTrue(ObzoraConfig::has('long.key.setting'), 'Exact exists on value');
        $this->assertFalse(ObzoraConfig::has('long.key.setting.nothing'), 'Non-existent child setting');

        $this->assertFalse(ObzoraConfig::has('off.the.wall'), 'Non-existent key');
        $this->assertFalse(ObzoraConfig::has('off.the'), 'Config:has() should not modify the config');
    }

    public function testGetNonExistent(): void
    {
        $this->assertNull(ObzoraConfig::get('There.is.no.way.this.is.a.key'));
        $this->assertFalse(ObzoraConfig::has('There.is.no'));  // should not add kes when getting
    }

    public function testGetNonExistentNested(): void
    {
        $this->assertNull(ObzoraConfig::get('cheese.and.bologna'));
    }

    public function testGetSubtree(): void
    {
        ObzoraConfig::set('words.top', 'August');
        ObzoraConfig::set('words.mid', 'And Everything');
        ObzoraConfig::set('words.bot', 'After');
        $expected = [
            'top' => 'August',
            'mid' => 'And Everything',
            'bot' => 'After',
        ];

        $this->assertEquals($expected, ObzoraConfig::get('words'));
    }

    /**
     * Pass an anonymous function which will be passed the config variable to modify before it is set
     *
     * @param  callable  $function
     */
    private function setConfig($function)
    {
        $instance = $this->app->make('obzora-config');
        $config = $this->config->getValue($instance);
        $function($config);
        $this->config->setValue($instance, $config);
    }

    public function testForget(): void
    {
        ObzoraConfig::set('forget.me', 'now');
        $this->assertTrue(ObzoraConfig::has('forget.me'));

        ObzoraConfig::forget('forget.me');
        $this->assertFalse(ObzoraConfig::has('forget.me'));
    }

    public function testForgetSubtree(): void
    {
        ObzoraConfig::set('forget.me.sub', 'yep');
        $this->assertTrue(ObzoraConfig::has('forget.me.sub'));

        ObzoraConfig::forget('forget.me');
        $this->assertFalse(ObzoraConfig::has('forget.me.sub'));
    }
}
