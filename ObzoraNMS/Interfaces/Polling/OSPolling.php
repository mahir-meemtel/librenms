<?php
namespace ObzoraNMS\Interfaces\Polling;

use ObzoraNMS\Interfaces\Data\DataStorageInterface;

interface OSPolling
{
    /**
     * Poll additional OS data.
     * Data must be manually saved within this method.
     */
    public function pollOS(DataStorageInterface $datastore): void;
}
