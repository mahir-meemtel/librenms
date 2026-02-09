<?php
namespace ObzoraNMS\Interfaces\Discovery;

use Illuminate\Support\Collection;

interface StorageDiscovery
{
    /**
     * Discover storage devices for the device
     */
    public function discoverStorage(): Collection;
}
