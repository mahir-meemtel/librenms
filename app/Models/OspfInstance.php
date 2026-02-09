<?php
namespace App\Models;

class OspfInstance extends DeviceRelatedModel
{
    public $timestamps = false;
    protected $fillable = [
        'device_id',
        'ospf_instance_id',
        'context_name',
        'ospfRouterId',
        'ospfAdminStat',
        'ospfVersionNumber',
        'ospfAreaBdrRtrStatus',
        'ospfASBdrRtrStatus',
        'ospfExternLsaCount',
        'ospfExternLsaCksumSum',
        'ospfTOSSupport',
        'ospfOriginateNewLsas',
        'ospfRxNewLsas',
        'ospfExtLsdbLimit',
        'ospfMulticastExtensions',
        'ospfExitOverflowInterval',
        'ospfDemandExtensions',
    ];
}
