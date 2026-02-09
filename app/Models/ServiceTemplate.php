<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ObzoraNMS\Alerting\QueryBuilderFluentParser;
use Log;

class ServiceTemplate extends BaseModel
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'ip',
        'check',
        'type',
        'rules',
        'desc',
        'param',
        'ignore',
        'status',
        'changed',
        'disabled',
        'name',
    ];

    protected $attributes = [ // default values
        'ignore' => '0',
        'disabled' => '0',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function (ServiceTemplate $template) {
            $template->devices()->detach();
            $template->groups()->detach();
        });

        static::saving(function (ServiceTemplate $template) {
            if ($template->type == 'dynamic' and $template->isDirty('rules')) {
                $template->rules = $template->getDeviceParser()->generateJoins()->toArray();
            }
        });

        static::saved(function (ServiceTemplate $template) {
            if ($template->type == 'dynamic' and $template->isDirty('rules')) {
                $template->updateDevices();
            }
        });
    }

    /**
     * @return array{ignore: 'integer', disabled: 'integer', rules: 'array'}
     */
    protected function casts(): array
    {
        return [
            'ignore' => 'integer',
            'disabled' => 'integer',
            'rules' => 'array',
        ];
    }

    // ---- Helper Functions ----

    /**
     * Update devices included in this template (dynamic only)
     */
    public function updateDevices()
    {
        if ($this->type == 'dynamic') {
            $this->devices()->sync(QueryBuilderFluentParser::fromJson($this->rules)->toQuery()
                ->distinct()->pluck('devices.device_id'));
        }
    }

    /**
     * Update the device template groups for the given device or device_id
     *
     * @param  Device|int  $device
     * @return array
     */
    public static function updateServiceTemplatesForDevice($device)
    {
        $device = ($device instanceof Device ? $device : Device::find($device));
        if (! $device instanceof Device) {
            // could not load device
            return [
                'attached' => [],
                'detached' => [],
                'updated' => [],
            ];
        }

        $template_ids = static::query()
            ->with(['devices' => function ($query) {
                $query->select('devices.device_id');
            }])
            ->get()
            ->filter(function ($template) use ($device) {
                if ($template->type == 'dynamic') {
                    try {
                        return $template->getDeviceParser()
                            ->toQuery()
                            ->where('devices.device_id', $device->device_id)
                            ->exists();
                    } catch (\Illuminate\Database\QueryException $e) {
                        Log::error("Service Template '$template->name' generates invalid query: " . $e->getMessage());

                        return false;
                    }
                }

                // for static, if this device is include, keep it.
                return $template->devices
                    ->where('device_id', $device->device_id)
                    ->isNotEmpty();
            })->pluck('id');

        return $device->serviceTemplates()->sync($template_ids);
    }

    /**
     * Update the device template groups for the given device group or device_group_id
     *
     * @param  DeviceGroup|int  $deviceGroup
     * @return array
     */
    public static function updateServiceTemplatesForDeviceGroup($deviceGroup)
    {
        $deviceGroup = ($deviceGroup instanceof DeviceGroup ? $deviceGroup : DeviceGroup::find($deviceGroup));
        if (! $deviceGroup instanceof DeviceGroup) {
            // could not load device
            return [
                'attached' => [],
                'detached' => [],
                'updated' => [],
            ];
        }

        $template_ids = static::query()
            ->with(['groups' => function ($query) {
                $query->select('device_groups.id');
            }])
            ->get()
            ->filter(function ($template) use ($deviceGroup) {
                // for static, if this device group is include, keep it.
                return $template->groups
                    ->where('device_group_id', $deviceGroup->id)
                    ->isNotEmpty();
            })->pluck('id');

        return $deviceGroup->serviceTemplates()->sync($template_ids);
    }

    /**
     * Get a query builder parser instance from this Service Template device rule
     *
     * @return QueryBuilderFluentParser
     */
    public function getDeviceParser()
    {
        return QueryBuilderFluentParser::fromJson($this->rules);
    }

    // ---- Query Scopes ----

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeIsDisabled($query)
    {
        return $query->where('disabled', 1);
    }

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Device, $this>
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'service_templates_device', 'service_template_id', 'device_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Service, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_templates_device', 'service_template_id', 'device_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\DeviceGroup, $this>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(DeviceGroup::class, 'service_templates_device_group', 'service_template_id', 'device_group_id');
    }
}
