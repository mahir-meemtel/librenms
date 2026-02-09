<?php
namespace ObzoraNMS\Interfaces\Polling;

use App\Models\Vminfo;
use Illuminate\Support\Collection;

interface VminfoPolling
{
    /**
     * Poll the given VMs
     *
     * @param  Collection<Vminfo>  $vms
     * @return Collection<Vminfo>
     */
    public function pollVminfo(Collection $vms): Collection;
}
