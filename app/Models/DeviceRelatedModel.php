<?php
namespace App\Models;

use App\Facades\DeviceCache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceRelatedModel extends BaseModel
{
    // ---- Query Scopes ----

    public function scopeHasAccess($query, User $user)
    {
        return $this->hasDeviceAccess($query, $user);
    }

    public function scopeInDeviceGroup($query, $deviceGroup)
    {
        // Build the list of device IDs in SQL
        $deviceIdsSubquery = \DB::table('device_group_device')
        ->where('device_group_id', $deviceGroup)
        ->pluck('device_id');

        // Use the result in the whereIn clause to avoid unoptimized subqueries
        // use whereIntegerInRaw to avoid a the defautl limit of 1000 for whereIn
        return $query->whereIntegerInRaw($query->qualifyColumn('device_id'), $deviceIdsSubquery);
    }

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Device, $this>
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }

    // ---- Accessors/Mutators ----

    /**
     * Use cached device instance to load device relationships
     */
    public function getDeviceAttribute(): ?Device
    {
        if (! $this->relationLoaded('device')) {
            $device = DeviceCache::get($this->device_id);
            $this->setRelation('device', $device->exists ? $device : null);
        }

        return $this->getRelationValue('device');
    }
}
