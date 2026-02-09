<?php
namespace ObzoraNMS\Interfaces\Polling;

use Illuminate\Database\Eloquent\Collection;

interface IsIsPolling
{
    /**
     * @param  Collection<int, \App\Models\IsisAdjacency>  $adjacencies
     * @return Collection<int, \App\Models\IsisAdjacency>
     */
    public function pollIsIs(Collection $adjacencies);
}
