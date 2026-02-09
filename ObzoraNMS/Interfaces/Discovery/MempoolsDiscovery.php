<?php
namespace ObzoraNMS\Interfaces\Discovery;

interface MempoolsDiscovery
{
    /**
     * Discover a Collection of Mempool models.
     * Will be keyed by mempool_type and mempool_index
     *
     * @return \Illuminate\Support\Collection \App\Models\Mempool
     */
    public function discoverMempools();
}
