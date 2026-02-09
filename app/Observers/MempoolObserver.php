<?php
namespace App\Observers;

use App\Models\Mempool;
use Log;
use Rrd;

class MempoolObserver
{
    public function updating(Mempool $mempool): void
    {
        // prevent update of mempool_perc_warn
        $mempool->mempool_perc_warn = $mempool->getOriginal('mempool_perc_warn');
    }

    public function updated(Mempool $mempool): void
    {
        if ($mempool->isDirty('mempool_class')) {
            Log::debug("Mempool class changed $mempool->mempool_descr ($mempool->mempool_id)");
            Rrd::renameFile(
                $mempool->device,
                ['mempool', $mempool->mempool_type, $mempool->getOriginal('mempool_class'), $mempool->mempool_index],
                ['mempool', $mempool->mempool_type, $mempool->mempool_class, $mempool->mempool_index]
            );
        }
    }
}
