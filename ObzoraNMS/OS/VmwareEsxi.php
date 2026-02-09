<?php
namespace ObzoraNMS\OS;

use Illuminate\Support\Collection;
use ObzoraNMS\Interfaces\Discovery\VminfoDiscovery;
use ObzoraNMS\Interfaces\Polling\VminfoPolling;
use ObzoraNMS\OS\Traits\VminfoVmware;

class VmwareEsxi extends \ObzoraNMS\OS implements VminfoDiscovery, VminfoPolling
{
    use VminfoVmware;

    public function pollVminfo(Collection $vms): Collection
    {
        // no VMs, assume there aren't any
        if ($vms->isEmpty()) {
            return $vms;
        }

        return $this->discoverVmInfo(); // just do the same thing as discovery.
    }
}
