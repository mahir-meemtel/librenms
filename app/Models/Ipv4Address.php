<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ObzoraNMS\Interfaces\Models\Keyable;

class Ipv4Address extends PortRelatedModel implements Keyable
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'ipv4_address_id';
    protected $fillable = [
        'ipv4_address',
        'ipv4_prefixlen',
        'ipv4_network_id',
        'port_id',
        'context_name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Ipv4Network, $this>
     */
    public function network(): BelongsTo
    {
        return $this->belongsTo(Ipv4Network::class, 'ipv4_network_id', 'ipv4_network_id');
    }

    public function getCompositeKey(): string
    {
        return "$this->ipv4_address-$this->ipv4_prefixlen-$this->port_id-$this->context_name";
    }
}
