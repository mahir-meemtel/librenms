<?php
namespace ObzoraNMS\Interfaces\Polling;

use Illuminate\Support\Collection;

interface StoragePolling
{
    /**
     * Poll Storage data for Storage in database.
     *
     * @param  Collection  $storages
     */
    public function pollStorage(Collection $storages);
}
