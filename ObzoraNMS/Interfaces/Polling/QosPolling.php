<?php
namespace ObzoraNMS\Interfaces\Polling;

use Illuminate\Database\Eloquent\Collection;

interface QosPolling
{
    /**
     * Poll Qos data
     *
     * @param  Collection<int, \App\Models\Qos>  $qos
     */
    public function pollQos(Collection $qos);
}
