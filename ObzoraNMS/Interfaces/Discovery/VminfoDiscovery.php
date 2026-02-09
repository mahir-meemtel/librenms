<?php
namespace ObzoraNMS\Interfaces\Discovery;

use App\Models\Vminfo;
use Illuminate\Support\Collection;

interface VminfoDiscovery
{
    /**
     * Discover all the VMs and return a collection of Vminfo models
     *
     * @return Collection<Vminfo>
     */
    public function discoverVminfo(): Collection;
}
