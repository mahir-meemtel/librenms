<?php
namespace ObzoraNMS\Tests;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Data\Store\Rrd;

class RrdtoolTest extends TestCase
{
    public function testBuildCommandLocal(): void
    {
        ObzoraConfig::set('rrdcached', '');
        ObzoraConfig::set('rrdtool_version', '1.4');
        ObzoraConfig::set('rrd_dir', '/opt/obzora/rrd');

        $cmd = $this->buildCommandProxy('create', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('create /opt/obzora/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('tune', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('tune /opt/obzora/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('update', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('update /opt/obzora/rrd/f o', $cmd);

        ObzoraConfig::set('rrdtool_version', '1.6');

        $cmd = $this->buildCommandProxy('create', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('create /opt/obzora/rrd/f o -O', $cmd);

        $cmd = $this->buildCommandProxy('tune', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('tune /opt/obzora/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('update', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('update /opt/obzora/rrd/f o', $cmd);
    }

    public function testBuildCommandRemote(): void
    {
        ObzoraConfig::set('rrdcached', 'server:42217');
        ObzoraConfig::set('rrdtool_version', '1.4');
        ObzoraConfig::set('rrd_dir', '/opt/obzora/rrd');

        $cmd = $this->buildCommandProxy('create', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('create /opt/obzora/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('tune', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('tune /opt/obzora/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('update', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('update f o --daemon server:42217', $cmd);

        ObzoraConfig::set('rrdtool_version', '1.6');

        $cmd = $this->buildCommandProxy('create', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('create f o -O --daemon server:42217', $cmd);

        $cmd = $this->buildCommandProxy('tune', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('tune f o --daemon server:42217', $cmd);

        $cmd = $this->buildCommandProxy('update', '/opt/obzora/rrd/f', 'o');
        $this->assertEquals('update f o --daemon server:42217', $cmd);
    }

    public function testBuildCommandException(): void
    {
        ObzoraConfig::set('rrdcached', '');
        ObzoraConfig::set('rrdtool_version', '1.4');

        $this->expectException('ObzoraNMS\Exceptions\FileExistsException');
        // use this file, since it is guaranteed to exist
        $this->buildCommandProxy('create', __FILE__, 'o');
    }

    private function buildCommandProxy($command, $filename, $options)
    {
        $mock = $this->mock(Rrd::class)->makePartial(); // avoid constructor
        // @phpstan-ignore method.protected
        $mock->loadConfig(); // load config every time to clear cached settings

        return $mock->buildCommand($command, $filename, $options);
    }
}
