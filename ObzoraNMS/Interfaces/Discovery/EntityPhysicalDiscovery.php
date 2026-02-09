<?php
namespace ObzoraNMS\Interfaces\Discovery;

use Illuminate\Support\Collection;

interface EntityPhysicalDiscovery
{
    /**
     * Discover a Collection of IsIsAdjacency models.
     * Will be keyed by ifIndex
     *
     * @return \Illuminate\Support\Collection<\App\Models\EntPhysical>
     */
    public function discoverEntityPhysical(): Collection;
}
