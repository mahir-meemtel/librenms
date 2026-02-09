<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

use App\Models\Device;
use App\Models\Location;
use App\View\SimpleTemplate;
use Illuminate\Support\Arr;
use ObzoraNMS\Snmptrap\Dispatcher;
use ObzoraNMS\Tests\TestCase;
use Mockery;

abstract class SnmpTrapTestCase extends TestCase
{
    protected function assertTrapLogsMessage(string $rawTrap, string|array $log, string $failureMessage = '', array $args = [], ?Device $device = null): void
    {
        if ($device === null) {
            $device = Device::factory()->make(['display' => '{{ hostname }}']);
            $device->setRelation('location', Location::factory()->make());
        }

        $template_variables = [
            'hostname' => $device->hostname,
            'ip' => $device->ip,
            'location' => $device->location,
            'sysDescr' => $device->sysDescr,
        ];
        $rawTrap = SimpleTemplate::parse($rawTrap, $template_variables);
        $trap = Mockery::mock('ObzoraNMS\Snmptrap\Trap[log,getDevice]', [$rawTrap]);
        $trap->shouldReceive('getDevice')->andReturn($device); // mock getDevice to avoid saving to database
        foreach (Arr::wrap($log) as $index => $message) {
            $call_args = is_array($args[$index] ?? null) ? $args[$index] : $args;

            $trap->shouldReceive('log')->once()->with(SimpleTemplate::parse($message, $template_variables), ...$call_args);
        }

        $log_spy = \Log::spy();

        /** @var \ObzoraNMS\Snmptrap\Trap $trap */
        $this->assertTrue(Dispatcher::handle($trap), $failureMessage);

        // if the test set any log expectations, log_spy will be null
        if ($log_spy != null) {
            $log_spy->shouldNotHaveReceived('error');
            $log_spy->shouldNotHaveReceived('warning');
        }
    }
}
