<?php
namespace ObzoraNMS\Interfaces\Polling;

use Illuminate\Database\Eloquent\Collection;

interface MempoolsPolling
{
    /**
     * @param  Collection<int, \App\Models\Mempool>  $mempools
     * @return Collection<int, \App\Models\Mempool>
     */
    public function pollMempools(Collection $mempools);
}
