<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BgpPeer extends DeviceRelatedModel
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'bgpPeers';
    protected $primaryKey = 'bgpPeer_id';
    protected $fillable = [
        'vrf_id',
        'bgpPeerIdentifier',
        'bgpPeerRemoteAs',
        'bgpPeerState',
        'bgpPeerAdminStatus',
        'bgpLocalAddr',
        'bgpPeerRemoteAddr',
        'bgpPeerInUpdates',
        'bgpPeerOutUpdates',
        'bgpPeerInTotalMessages',
        'bgpPeerOutTotalMessages',
        'bgpPeerFsmEstablishedTime',
        'bgpPeerInUpdateElapsedTime',
        'bgpPeerDescr',
        'bgpPeerIface',
        'astext',
    ];
    // ---- Query scopes ----

    public function scopeInAlarm(Builder $query)
    {
        return $query->where(function (Builder $query) {
            $query->where('bgpPeerAdminStatus', 'start')
                ->orWhere('bgpPeerAdminStatus', 'running');
        })->where('bgpPeerState', '!=', 'established');
    }
}
