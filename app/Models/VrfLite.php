<?php
namespace App\Models;

class VrfLite extends DeviceRelatedModel
{
    protected $table = 'vrf_lite_cisco';
    protected $primaryKey = 'vrf_lite_cisco_id';
    public $timestamps = false;
}
