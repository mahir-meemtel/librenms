<?php
namespace App\Models;

class DeviceOutage extends DeviceRelatedModel
{
    public $timestamps = false;
    protected $fillable = ['going_down', 'up_again'];
}
