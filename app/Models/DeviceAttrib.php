<?php
namespace App\Models;

class DeviceAttrib extends DeviceRelatedModel
{
    protected $table = 'devices_attribs';
    protected $primaryKey = 'attrib_id';
    public $timestamps = false;
    protected $fillable = ['attrib_type', 'attrib_value'];
//    protected $casts = ['attrib_value' => 'array'];
}
