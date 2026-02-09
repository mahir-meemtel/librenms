<?php
namespace App\Models;

class Vrf extends DeviceRelatedModel
{
    public $timestamps = false;
    protected $table = 'vrfs';
    protected $primaryKey = 'vrf_id';
    protected $fillable = [
        'vrf_oid',
        'vrf_name',
    ];
}
