<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OspfPort extends PortRelatedModel
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'device_id',
        'port_id',
        'ospf_port_id',
        'context_name',
        'ospfIfIpAddress',
        'ospfAddressLessIf',
        'ospfIfAreaId',
        'ospfIfType',
        'ospfIfAdminStat',
        'ospfIfRtrPriority',
        'ospfIfTransitDelay',
        'ospfIfRetransInterval',
        'ospfIfHelloInterval',
        'ospfIfRtrDeadInterval',
        'ospfIfPollInterval',
        'ospfIfState',
        'ospfIfDesignatedRouter',
        'ospfIfBackupDesignatedRouter',
        'ospfIfEvents',
        'ospfIfAuthKey',
        'ospfIfStatus',
        'ospfIfMulticastForwarding',
        'ospfIfDemand',
        'ospfIfAuthType',
        'ospfIfMetricIpAddress',
        'ospfIfMetricAddressLessIf',
        'ospfIfMetricTOS',
        'ospfIfMetricValue',
        'ospfIfMetricStatus',
    ];

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Device, $this>
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
