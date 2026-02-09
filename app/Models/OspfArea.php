<?php
namespace App\Models;

class OspfArea extends DeviceRelatedModel
{
    public $timestamps = false;
    protected $fillable = [
        'device_id',
        'context_name',
        'ospfAreaId',
        'ospfAuthType',
        'ospfImportAsExtern',
        'ospfSpfRuns',
        'ospfAreaBdrRtrCount',
        'ospfAsBdrRtrCount',
        'ospfAreaLsaCount',
        'ospfAreaLsaCksumSum',
        'ospfAreaSummary',
        'ospfAreaStatus',
    ];
}
