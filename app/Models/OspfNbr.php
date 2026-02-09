<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OspfNbr extends DeviceRelatedModel
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'device_id',
        'port_id',
        'ospf_nbr_id',
        'context_name',
        'ospfNbrIpAddr',
        'ospfNbrAddressLessIndex',
        'ospfNbrRtrId',
        'ospfNbrOptions',
        'ospfNbrPriority',
        'ospfNbrState',
        'ospfNbrEvents',
        'ospfNbrLsRetransQLen',
        'ospfNbmaNbrStatus',
        'ospfNbmaNbrPermanence',
        'ospfNbrHelloSuppressed',
    ];
}
