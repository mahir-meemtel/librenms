<?php
namespace ObzoraNMS\Tests\Unit\Data;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use Illuminate\Support\Facades\Http as LaravelHttp;
use ObzoraNMS\Data\Store\Prometheus;
use ObzoraNMS\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('datastores')]
class PrometheusStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ObzoraConfig::set('prometheus.enable', true);
        ObzoraConfig::set('prometheus.url', 'http://fake:9999');
    }

    public function testFailWrite(): void
    {
        LaravelHttp::fakeSequence()->push('Bad response', 422);
        $prometheus = app(Prometheus::class);

        \Log::shouldReceive('debug');
        \Log::shouldReceive('error')->once()->with('Prometheus Error: Bad response');
        $prometheus->write('none', ['one' => 1]);
    }

    public function testSimpleWrite(): void
    {
        LaravelHttp::fake([
            '*' => LaravelHttp::response(),
        ]);

        $prometheus = app(Prometheus::class);

        $measurement = 'testmeasure';
        $tags = ['ifName' => 'testifname', 'type' => 'testtype'];
        $fields = ['ifIn' => 234234, 'ifOut' => 53453];
        $meta = ['device' => new Device(['hostname' => 'testhost'])];

        \Log::shouldReceive('debug');
        \Log::shouldReceive('error')->times(0);

        $prometheus->write($measurement, $fields, $tags, $meta);

        LaravelHttp::assertSentCount(1);
        LaravelHttp::assertSent(function (\Illuminate\Http\Client\Request $request) {
            return $request->method() == 'POST' &&
                $request->url() == 'http://fake:9999/metrics/job/obzora/instance/testhost/measurement/testmeasure/ifName/testifname/type/testtype' &&
                $request->body() == "ifIn 234234\nifOut 53453\n";
        });
    }
}
