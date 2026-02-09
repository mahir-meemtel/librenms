<?php
namespace ObzoraNMS\Snmptrap\Handlers;

use App\Models\Device;
use ObzoraNMS\Interfaces\SnmptrapHandler;
use ObzoraNMS\Snmptrap\Trap;

class TrippliteAlarmAdded extends Tripplite implements SnmptrapHandler
{
    public function handle(Device $device, Trap $trap)
    {
        $trap->log($this->describe($trap), $this->getSeverity($trap));
    }
}
