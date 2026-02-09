<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ObzoraNMS\Interfaces\Models\Keyable;

class Ipv6Address extends PortRelatedModel implements Keyable
{
    public $timestamps = false;
    protected $primaryKey = 'ipv6_address_id';
    protected $fillable = [
        'ipv6_address',
        'ipv6_compressed',
        'ipv6_prefixlen',
        'ipv6_origin',
        'ipv6_network_id',
        'port_id',
        'context_name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Ipv6Network, $this>
     */
    public function network(): BelongsTo
    {
        return $this->belongsTo(Ipv6Network::class, 'ipv6_network_id', 'ipv6_network_id');
    }

    public function getCompositeKey(): string
    {
        return "$this->ipv6_address-$this->ipv6_prefixlen-$this->port_id-$this->context_name";
    }
}
