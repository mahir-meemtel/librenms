<?php
namespace ObzoraNMS\Interfaces\Polling;

use Illuminate\Support\Collection;

interface NacPolling
{
    /**
     * @return Collection PortNac objects
     */
    public function pollNac();
}
