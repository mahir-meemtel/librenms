<?php
namespace ObzoraNMS\Interfaces\Polling;

use Illuminate\Database\Eloquent\Collection;

interface SlaPolling
{
    /**
     * Poll Sla data for Sla in database.
     *
     * @param  Collection<int, \App\Models\Sla>  $slas
     */
    public function pollSlas(Collection $slas): void;
}
