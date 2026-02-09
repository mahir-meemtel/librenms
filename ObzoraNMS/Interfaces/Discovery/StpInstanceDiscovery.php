<?php
namespace ObzoraNMS\Interfaces\Discovery;

use Illuminate\Support\Collection;

interface StpInstanceDiscovery
{
    /**
     * Discover STP instances on the device.
     *
     * @param  string  $vlan  Vlan ID of the instance to discover (or null for default)
     * @return Collection<\App\Models\Stp>
     */
    public function discoverStpInstances(?string $vlan = null): Collection;
}
