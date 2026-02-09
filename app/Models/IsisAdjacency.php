<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ObzoraNMS\Interfaces\Models\Keyable;

class IsisAdjacency extends PortRelatedModel implements Keyable
{
    use HasFactory;

    //public $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'device_id',
        'index',
        'port_id',
        'ifIndex',
        'isisCircAdminState',
        'isisISAdjState',
        'isisISAdjNeighSysType',
        'isisISAdjNeighSysID',
        'isisISAdjNeighPriority',
        'isisISAdjLastUpTime',
        'isisISAdjAreaAddress',
        'isisISAdjIPAddrType',
        'isisISAdjIPAddrAddress',
    ];

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Port, $this>
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'device_id');
    }

    public function getCompositeKey()
    {
        return $this->ifIndex . $this->index;
    }
}
