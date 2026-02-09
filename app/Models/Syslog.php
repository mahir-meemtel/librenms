<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Syslog extends DeviceRelatedModel
{
    use HasFactory;

    protected $table = 'syslog';
    protected $primaryKey = 'seq';
    public $timestamps = false;
}
