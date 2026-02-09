<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ObzoraNMS\Enum\AlertState;

class AlertRule extends BaseModel
{
    public $timestamps = false;

    // ---- Query scopes ----

    /**
     * @param  Builder<AlertRule>  $query
     * @return Builder
     */
    public function scopeEnabled($query)
    {
        return $query->where('alert_rules.disabled', 0);
    }

    /**
     * Scope for only alert rules that are currently in alarm
     *
     * @param  Builder<AlertRule>  $query
     * @return Builder
     */
    public function scopeIsActive($query)
    {
        return $query->enabled()
            ->join('alerts', 'alerts.rule_id', 'alert_rules.id')
            ->whereNotIn('alerts.state', [AlertState::CLEAR, AlertState::ACKNOWLEDGED, AlertState::RECOVERED]);
    }

    /**
     * Scope to filter rules for devices permitted to user
     * (do not use for admin and global read-only users)
     *
     * @param  Builder<AlertRule>  $query
     * @param  User  $user
     * @return mixed
     */
    public function scopeHasAccess($query, User $user)
    {
        if ($user->hasGlobalRead()) {
            return $query;
        }

        if (! $this->isJoined($query, 'alerts')) {
            $query->join('alerts', 'alerts.rule_id', 'alert_rules.id');
        }

        return $this->hasDeviceAccess($query, $user, 'alerts');
    }

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Alert, $this>
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'rule_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Device, $this>
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'alert_device_map', 'rule_id', 'device_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\DeviceGroup, $this>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(DeviceGroup::class, 'alert_group_map', 'rule_id', 'group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Location, $this>
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'alert_location_map', 'rule_id');
    }
}
