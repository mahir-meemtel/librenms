<?php
namespace ObzoraNMS\Interfaces\Polling;

use Illuminate\Support\Collection;

interface StpInstancePolling
{
    /**
     * Poll STP instance data for existing STP instances from the database.
     *
     * @param  Collection<\App\Models\Stp>  $stpInstances
     * @return Collection<\App\Models\Stp>
     */
    public function pollStpInstances(Collection $stpInstances): Collection;
}
