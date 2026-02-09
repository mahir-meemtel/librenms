<?php
namespace ObzoraNMS\Tests\Unit\Data;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use Carbon\Carbon;
use ObzoraNMS\Data\Store\Graphite;
use ObzoraNMS\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;
use Socket\Raw\Socket;

#[Group('datastores')]
class GraphiteStoreTest extends TestCase
{
    protected int $timestamp = 1197464400;

    protected function setUp(): void
    {
        parent::setUp();

        // fix the date
        Carbon::setTestNow(Carbon::createFromTimestampUTC($this->timestamp));
        ObzoraConfig::set('graphite.enable', true);
    }

    protected function tearDown(): void
    {
        // restore Carbon:now() to normal
        Carbon::setTestNow();
        ObzoraConfig::set('graphite.enable', false);

        parent::tearDown();
    }

    public function testSocketConnectError(): void
    {
        $mockFactory = \Mockery::mock(\Socket\Raw\Factory::class);

        $mockFactory->shouldReceive('createClient')
            ->andThrow('Socket\Raw\Exception', 'Failed to handle connect exception')->once();

        new Graphite($mockFactory);
    }

    public function testSocketWriteError(): void
    {
        $mockSocket = \Mockery::mock(\Socket\Raw\Socket::class);
        $graphite = $this->mockGraphite($mockSocket);

        $mockSocket->shouldReceive('write')
            ->andThrow('Socket\Raw\Exception', 'Did not handle socket exception')->once();

        $graphite->write('fake', ['one' => 1], ['rrd_name' => 'name']);
    }

    public function testSimpleWrite(): void
    {
        $mockSocket = \Mockery::mock(\Socket\Raw\Socket::class);
        $graphite = $this->mockGraphite($mockSocket);

        $measurement = 'testmeasure';
        $tags = ['ifName' => 'testifname', 'type' => 'testtype'];
        $fields = ['ifIn' => 234234, 'ifOut' => 53453];
        $meta = ['device' => new Device(['hostname' => 'testhost']), 'rrd_name' => 'rrd_name'];

        $mockSocket->shouldReceive('write')
            ->with("testhost.testmeasure.rrd_name.ifIn 234234 $this->timestamp\n")->once();
        $mockSocket->shouldReceive('write')
            ->with("testhost.testmeasure.rrd_name.ifOut 53453 $this->timestamp\n")->once();
        $graphite->write($measurement, $fields, $tags, $meta);
    }

    private function mockGraphite(Socket $mockSocket): Graphite
    {
        $mockFactory = \Mockery::mock(\Socket\Raw\Factory::class);

        $mockFactory->shouldReceive('createClient')
            ->andReturn($mockSocket);

        return new Graphite($mockFactory);
    }
}
