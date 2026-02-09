<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use ObzoraNMS\Interfaces\Models\Keyable;

class Ospfv3Nbr extends PortRelatedModel implements Keyable
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'device_id',
        'ospfv3_instance_id',
        'port_id',
        'router_id',
        'context_name',
        'ospfv3NbrIfIndex',
        'ospfv3NbrIfInstId',
        'ospfv3NbrRtrId',
        'ospfv3NbrAddress',
        'ospfv3NbrAddressType',
        'ospfv3NbrOptions',
        'ospfv3NbrPriority',
        'ospfv3NbrState',
        'ospfv3NbrEvents',
        'ospfv3NbrLsRetransQLen',
        'ospfv3NbmaNbrStatus',
        'ospfv3NbmaNbrPermanence',
        'ospfv3NbrHelloSuppressed',
        'ospfv3NbrIfId',
        'ospfv3NbrRestartHelperStatus',
        'ospfv3NbrRestartHelperAge',
        'ospfv3NbrRestartHelperExitReason',
    ];

    public function getCompositeKey(): string
    {
        return "$this->device_id-$this->ospfv3NbrIfIndex-$this->ospfv3NbrIfInstId-$this->ospfv3NbrRtrId-$this->context_name";
    }
}
