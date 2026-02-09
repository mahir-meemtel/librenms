<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use ObzoraNMS\Interfaces\Models\Keyable;

class Ospfv3Instance extends DeviceRelatedModel implements Keyable
{
    public $timestamps = false;
    protected $fillable = [
        'device_id',
        'context_name',
        'router_id',
        'ospfv3RouterId',
        'ospfv3AdminStatus',
        'ospfv3VersionNumber',
        'ospfv3AreaBdrRtrStatus',
        'ospfv3ASBdrRtrStatus',
        'ospfv3AsScopeLsaCount',
        'ospfv3AsScopeLsaCksumSum',
        'ospfv3ExtLsaCount',
        'ospfv3OriginateNewLsas',
        'ospfv3RxNewLsas',
        'ospfv3ExtAreaLsdbLimit',
        'ospfv3ExitOverflowInterval',
        'ospfv3ReferenceBandwidth',
        'ospfv3RestartSupport',
        'ospfv3RestartInterval',
        'ospfv3RestartStrictLsaChecking',
        'ospfv3RestartStatus',
        'ospfv3RestartAge',
        'ospfv3RestartExitReason',
        'ospfv3StubRouterSupport',
        'ospfv3StubRouterAdvertisement',
        'ospfv3DiscontinuityTime',
        'ospfv3RestartTime',
    ];

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Ospfv3Area, $this>
     */
    public function areas(): HasMany
    {
        return $this->hasMany(Ospfv3Area::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Ospfv3Nbr, $this>
     */
    public function nbrs(): HasMany
    {
        return $this->hasMany(Ospfv3Nbr::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Ospfv3Port, $this>
     */
    public function ospfv3Ports(): HasMany
    {
        return $this->hasMany(Ospfv3Port::class);
    }

    public function getCompositeKey(): string
    {
        return "$this->device_id-$this->context_name";
    }
}
