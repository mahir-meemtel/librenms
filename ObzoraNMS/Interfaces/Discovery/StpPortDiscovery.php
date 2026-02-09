<?php
namespace ObzoraNMS\Interfaces\Discovery;

use Illuminate\Support\Collection;

interface StpPortDiscovery
{
    /**
     * Discover STP port data.  Previously discovered instances are passed in.
     *
     * @param  Collection<\App\Models\Stp>  $stpInstances
     * @return Collection<\App\Models\PortStp>
     */
    public function discoverStpPorts(Collection $stpInstances): Collection;
}
