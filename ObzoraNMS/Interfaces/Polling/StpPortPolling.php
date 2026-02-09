<?php
namespace ObzoraNMS\Interfaces\Polling;

use Illuminate\Support\Collection;

interface StpPortPolling
{
    /**
     * Poll STP port data for existing STP ports from the database.
     *
     * @param  Collection<\App\Models\PortStp>  $stpPorts
     * @return Collection<\App\Models\PortStp>
     */
    public function pollStpPorts(Collection $stpPorts): Collection;
}
