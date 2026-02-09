<?php
namespace ObzoraNMS\Interfaces\Discovery;

interface IsIsDiscovery
{
    /**
     * Discover a Collection of IsIsAdjacency models.
     * Will be keyed by ifIndex
     *
     * @return \Illuminate\Support\Collection \App\Models\IsIsAdjacency
     */
    public function discoverIsIs();
}
