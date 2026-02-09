<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    public $timestamps = false;
    protected $table = 'availability';
    protected $primaryKey = 'availability_id';
    protected $fillable = [
        'device_id',
        'duration',
        'availability_perc',
    ];
}
