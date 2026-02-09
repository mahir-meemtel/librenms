<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ipv4Network extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'ipv4_network_id';
    protected $fillable = [
        'ipv4_network',
        'context_name',
    ];

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Ipv4Address, $this>
     */
    public function ipv4(): HasMany
    {
        return $this->hasMany(Ipv4Address::class, 'ipv4_network_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Port, \App\Models\Ipv4Address, $this>
     */
    public function connectedPorts(): HasManyThrough
    {
        return $this->hasManyThrough(Port::class, Ipv4Address::class, 'ipv4_network_id', 'port_id', 'ipv4_network_id', 'port_id');
    }
}
