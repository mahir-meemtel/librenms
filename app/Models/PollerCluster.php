<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ObzoraNMS\Exceptions\InvalidNameException;

class PollerCluster extends Model
{
    public $timestamps = false;
    protected $table = 'poller_cluster';
    protected $primaryKey = 'id';
    protected $fillable = ['poller_name'];

    /**
     * @return array{last_report: 'datetime'}
     */
    protected function casts(): array
    {
        return [
            'last_report' => 'datetime',
        ];
    }

    // ---- Accessors/Mutators ----

    /**
     * @param  array|string  $groups
     * @return void
     */
    public function setPollerGroupsAttribute($groups): void
    {
        $this->attributes['poller_groups'] = is_array($groups) ? implode(',', $groups) : $groups;
    }

    // ---- Scopes ----

    public function scopeIsActive(Builder $query): Builder
    {
        $default = (int) \App\Facades\ObzoraConfig::get('service_poller_frequency');

        return $query->where('last_report', '>=', \DB::raw("DATE_SUB(NOW(),INTERVAL COALESCE(`poller_frequency`, $default) SECOND)"));
    }

    public function scopeIsInactive(Builder $query): Builder
    {
        $default = (int) \App\Facades\ObzoraConfig::get('service_poller_frequency');

        return $query->where('last_report', '<', \DB::raw("DATE_SUB(NOW(),INTERVAL COALESCE(`poller_frequency`, $default) SECOND)"));
    }

    // ---- Helpers ----

    /**
     * Get the value of a setting (falls back to the global value if not set on this node)
     *
     * @param  string  $name
     * @return mixed
     *
     * @throws InvalidNameException
     */
    public function getSettingValue(string $name)
    {
        $definition = $this->configDefinition(false);

        foreach ($definition as $entry) {
            if ($entry['name'] == $name) {
                return $entry['value'];
            }
        }

        throw new InvalidNameException("Poller group setting named \"$name\" is invalid");
    }

    /**
     * Get the frontend config definition for this poller
     *
     * @param  \Illuminate\Support\Collection|bool|null  $groups  optionally supply full list of poller groups to avoid fetching multiple times
     * @return array[]
     */
    public function configDefinition($groups = null)
    {
        if ($groups === null || $groups === true) {
            $groups = PollerGroup::list();
        }

        $scheduleType = \App\Facades\ObzoraConfig::get('schedule_type');

        $pollerGloballyEnabled = $scheduleType['poller'] == 'legacy' ? \App\Facades\ObzoraConfig::get('service_poller_enabled', true) : $scheduleType['poller'] == 'dispatcher';
        $discoveryGloballyEnabled = $scheduleType['discovery'] == 'legacy' ? \App\Facades\ObzoraConfig::get('service_discovery_enabled', true) : $scheduleType['discovery'] == 'dispatcher';
        $servicesGloballyEnabled = $scheduleType['services'] == 'legacy' ? \App\Facades\ObzoraConfig::get('service_services_enabled', true) : $scheduleType['services'] == 'dispatcher';
        $alertGloballyEnabled = $scheduleType['alerting'] == 'legacy' ? \App\Facades\ObzoraConfig::get('service_alerting_enabled', true) : $scheduleType['alerting'] == 'dispatcher';
        $billingGloballyEnabled = $scheduleType['billing'] == 'legacy' ? \App\Facades\ObzoraConfig::get('service_billing_enabled', true) : $scheduleType['billing'] == 'dispatcher';
        $pingGloballyEnabled = $scheduleType['ping'] == 'legacy' ? \App\Facades\ObzoraConfig::get('service_ping_enabled', true) : $scheduleType['ping'] == 'dispatcher';

        return [
            [
                'name' => 'poller_groups',
                'default' => \App\Facades\ObzoraConfig::get('distributed_poller_group'),
                'value' => $this->poller_groups ?? \App\Facades\ObzoraConfig::get('distributed_poller_group'),
                'type' => 'multiple',
                'options' => $groups,
            ],
            [
                'name' => 'poller_enabled',
                'default' => $pollerGloballyEnabled,
                'value' => (bool) ($this->poller_enabled ?? $pollerGloballyEnabled),
                'type' => 'boolean',
            ],
            [
                'name' => 'poller_workers',
                'default' => \App\Facades\ObzoraConfig::get('service_poller_workers'),
                'value' => $this->poller_workers ?? \App\Facades\ObzoraConfig::get('service_poller_workers'),
                'type' => 'integer',
                'units' => 'workers',
            ],
            [
                'name' => 'poller_frequency',
                'default' => \App\Facades\ObzoraConfig::get('service_poller_frequency'),
                'value' => $this->poller_frequency ?? \App\Facades\ObzoraConfig::get('service_poller_frequency'),
                'type' => 'integer',
                'units' => 'seconds',
                'advanced' => true,
            ],
            [
                'name' => 'poller_down_retry',
                'default' => \App\Facades\ObzoraConfig::get('service_poller_down_retry'),
                'value' => $this->poller_down_retry ?? \App\Facades\ObzoraConfig::get('service_poller_down_retry'),
                'type' => 'integer',
                'units' => 'seconds',
            ],
            [
                'name' => 'discovery_enabled',
                'default' => $discoveryGloballyEnabled,
                'value' => (bool) ($this->discovery_enabled ?? $discoveryGloballyEnabled),
                'type' => 'boolean',
            ],
            [
                'name' => 'discovery_workers',
                'default' => \App\Facades\ObzoraConfig::get('service_discovery_workers'),
                'value' => $this->discovery_workers ?? \App\Facades\ObzoraConfig::get('service_discovery_workers'),
                'type' => 'integer',
                'units' => 'workers',
            ],
            [
                'name' => 'discovery_frequency',
                'default' => \App\Facades\ObzoraConfig::get('service_discovery_frequency'),
                'value' => $this->discovery_frequency ?? \App\Facades\ObzoraConfig::get('service_discovery_frequency'),
                'type' => 'integer',
                'units' => 'seconds',
                'advanced' => true,
            ],
            [
                'name' => 'services_enabled',
                'default' => $servicesGloballyEnabled,
                'value' => (bool) ($this->services_enabled ?? $servicesGloballyEnabled),
                'type' => 'boolean',
            ],
            [
                'name' => 'services_workers',
                'default' => \App\Facades\ObzoraConfig::get('service_services_workers'),
                'value' => $this->services_workers ?? \App\Facades\ObzoraConfig::get('service_services_workers'),
                'type' => 'integer',
                'units' => 'workers',
            ],
            [
                'name' => 'services_frequency',
                'default' => \App\Facades\ObzoraConfig::get('service_services_frequency'),
                'value' => $this->services_frequency ?? \App\Facades\ObzoraConfig::get('service_services_frequency'),
                'type' => 'integer',
                'units' => 'seconds',
                'advanced' => true,
            ],
            [
                'name' => 'billing_enabled',
                'default' => $billingGloballyEnabled,
                'value' => (bool) ($this->billing_enabled ?? $billingGloballyEnabled),
                'type' => 'boolean',
            ],
            [
                'name' => 'billing_frequency',
                'default' => \App\Facades\ObzoraConfig::get('service_billing_frequency'),
                'value' => $this->billing_frequency ?? \App\Facades\ObzoraConfig::get('service_billing_frequency'),
                'type' => 'integer',
                'units' => 'seconds',
                'advanced' => true,
            ],
            [
                'name' => 'billing_calculate_frequency',
                'default' => \App\Facades\ObzoraConfig::get('service_billing_calculate_frequency'),
                'value' => $this->billing_calculate_frequency ?? \App\Facades\ObzoraConfig::get('service_billing_calculate_frequency'),
                'type' => 'integer',
                'units' => 'seconds',
                'advanced' => true,
            ],
            [
                'name' => 'alerting_enabled',
                'default' => $alertGloballyEnabled,
                'value' => (bool) ($this->alerting_enabled ?? $alertGloballyEnabled),
                'type' => 'boolean',
            ],
            [
                'name' => 'alerting_frequency',
                'default' => \App\Facades\ObzoraConfig::get('service_alerting_frequency'),
                'value' => $this->alerting_frequency ?? \App\Facades\ObzoraConfig::get('service_alerting_frequency'),
                'type' => 'integer',
                'units' => 'seconds',
                'advanced' => true,
            ],
            [
                'name' => 'ping_enabled',
                'default' => $pingGloballyEnabled,
                'value' => (bool) ($this->ping_enabled ?? $pingGloballyEnabled),
                'type' => 'boolean',
            ],
            [
                'name' => 'ping_frequency',
                'default' => \App\Facades\ObzoraConfig::get('ping_rrd_step'),
                'value' => $this->ping_frequency ?? \App\Facades\ObzoraConfig::get('ping_rrd_step'),
                'type' => 'integer',
                'units' => 'seconds',
                'advanced' => true,
            ],
            [
                'name' => 'update_enabled',
                'default' => \App\Facades\ObzoraConfig::get('service_update_enabled'),
                'value' => (bool) ($this->update_enabled ?? \App\Facades\ObzoraConfig::get('service_update_enabled')),
                'type' => 'boolean',
                'advanced' => true,
            ],
            [
                'name' => 'update_frequency',
                'default' => \App\Facades\ObzoraConfig::get('service_update_frequency'),
                'value' => $this->update_frequency ?? \App\Facades\ObzoraConfig::get('service_update_frequency'),
                'type' => 'integer',
                'units' => 'seconds',
                'advanced' => true,
            ],
            [
                'name' => 'loglevel',
                'default' => \App\Facades\ObzoraConfig::get('service_loglevel'),
                'value' => $this->loglevel ?? \App\Facades\ObzoraConfig::get('service_loglevel'),
                'type' => 'select',
                'options' => [
                    'DEBUG' => 'DEBUG',
                    'INFO' => 'INFO',
                    'WARNING' => 'WARNING',
                    'ERROR' => 'ERROR',
                    'CRITICAL' => 'CRITICAL',
                ],
            ],
            [
                'name' => 'watchdog_enabled',
                'default' => \App\Facades\ObzoraConfig::get('service_watchdog_enabled'),
                'value' => (bool) ($this->watchdog_enabled ?? \App\Facades\ObzoraConfig::get('service_watchdog_enabled')),
                'type' => 'boolean',
            ],
            [
                'name' => 'watchdog_log',
                'default' => \App\Facades\ObzoraConfig::get('log_file'),
                'value' => $this->watchdog_log ?? \App\Facades\ObzoraConfig::get('log_file'),
                'type' => 'text',
                'advanced' => true,
            ],
        ];
    }

    // ---- Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\PollerClusterStat, $this>
     */
    public function stats(): HasMany
    {
        return $this->hasMany(PollerClusterStat::class, 'parent_poller', 'id');
    }
}
