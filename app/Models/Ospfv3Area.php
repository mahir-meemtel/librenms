<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use ObzoraNMS\Interfaces\Models\Keyable;

class Ospfv3Area extends DeviceRelatedModel implements Keyable
{
    public $timestamps = false;
    protected $fillable = [
        'device_id',
        'ospfv3_instance_id',
        'context_name',
        'ospfv3AreaId',
        'ospfv3AreaImportAsExtern',
        'ospfv3AreaSpfRuns',
        'ospfv3AreaBdrRtrCount',
        'ospfv3AreaAsBdrRtrCount',
        'ospfv3AreaScopeLsaCount',
        'ospfv3AreaScopeLsaCksumSum',
        'ospfv3AreaSummary',
        'ospfv3AreaStubMetric',
        'ospfv3AreaStubMetricType',
        'ospfv3AreaNssaTranslatorRole',
        'ospfv3AreaNssaTranslatorState',
        'ospfv3AreaNssaTranslatorStabInterval',
        'ospfv3AreaNssaTranslatorEvents',
        'ospfv3AreaTEEnabled',
    ];

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Ospfv3Port, $this>
     */
    public function ospfv3Ports(): HasMany
    {
        return $this->hasMany(Ospfv3Port::class);
    }

    public function getCompositeKey(): string
    {
        return "$this->device_id-$this->ospfv3AreaId-$this->context_name";
    }
}
