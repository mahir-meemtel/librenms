<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Poller extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = ['poller_name'];

    // ---- Scopes ----

    public function scopeIsInactive(Builder $query): Builder
    {
        $default = (int) \App\Facades\ObzoraConfig::get('rrd.step');

        return $query->where('last_polled', '<', \DB::raw("DATE_SUB(NOW(),INTERVAL $default SECOND)"));
    }

    public function scopeIsActive(Builder $query): Builder
    {
        $default = (int) \App\Facades\ObzoraConfig::get('rrd.step');

        return $query->where('last_polled', '>=', \DB::raw("DATE_SUB(NOW(),INTERVAL $default SECOND)"));
    }
}
