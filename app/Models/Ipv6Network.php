<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ipv6Network extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'ipv6_network_id';
    protected $fillable = [
        'ipv6_network',
        'context_name',
    ];

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Ipv6Address, $this>
     */
    public function ipv6(): HasMany
    {
        return $this->hasMany(Ipv6Address::class, 'ipv6_network_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Port, \App\Models\Ipv6Address, $this>
     */
    public function connectedPorts(): HasManyThrough
    {
        return $this->hasManyThrough(Port::class, Ipv6Address::class, 'ipv6_network_id', 'port_id', 'ipv6_network_id', 'port_id');
    }
}
