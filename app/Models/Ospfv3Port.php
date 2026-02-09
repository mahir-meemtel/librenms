<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ObzoraNMS\Interfaces\Models\Keyable;

class Ospfv3Port extends PortRelatedModel implements Keyable
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'device_id',
        'ospfv3_instance_id',
        'ospfv3_area_id',
        'port_id',
        'context_name',
        'ospfv3IfIndex',
        'ospfv3IfInstId',
        'ospfv3IfAreaId',
        'ospfv3IfType',
        'ospfv3IfAdminStatus',
        'ospfv3IfRtrPriority',
        'ospfv3IfTransitDelay',
        'ospfv3IfRetransInterval',
        'ospfv3IfHelloInterval',
        'ospfv3IfRtrDeadInterval',
        'ospfv3IfPollInterval',
        'ospfv3IfState',
        'ospfv3IfDesignatedRouter',
        'ospfv3IfBackupDesignatedRouter',
        'ospfv3IfEvents',
        'ospfv3IfDemand',
        'ospfv3IfMetricValue',
        'ospfv3IfLinkScopeLsaCount',
        'ospfv3IfLinkLsaCksumSum',
        'ospfv3IfDemandNbrProbe',
        'ospfv3IfDemandNbrProbeRetransLimit',
        'ospfv3IfDemandNbrProbeInterval',
        'ospfv3IfTEDisabled',
        'ospfv3IfLinkLSASuppression',
    ];

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Ospfv3Area, $this>
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Ospfv3Area::class);
    }

    public function getCompositeKey(): string
    {
        return "$this->device_id-$this->ospfv3IfIndex-$this->ospfv3IfInstId-$this->context_name";
    }
}
