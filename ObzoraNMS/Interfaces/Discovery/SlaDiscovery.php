<?php
namespace ObzoraNMS\Interfaces\Discovery;

use Illuminate\Support\Collection;

interface SlaDiscovery
{
    /**
     * Discover additional information about the OS.
     * Primarily this is just version, hardware, features, serial, but could be anything
     *
     * @return Collection<int, \App\Models\Sla>
     */
    public function discoverSlas(): Collection;
}
