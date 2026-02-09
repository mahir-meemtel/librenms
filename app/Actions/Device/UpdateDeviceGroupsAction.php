<?php
namespace App\Actions\Device;

use App\Models\Device;
use App\Models\DeviceGroup;
use Log;

class UpdateDeviceGroupsAction
{
    /**
     * @var Device
     */
    private $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    /**
     * @return array[]
     */
    public function execute(): array
    {
        if (! $this->device->exists) {
            // Device not saved to DB, cowardly refusing
            return [
                'attached' => [],
                'detached' => [],
                'updated' => [],
            ];
        }

        $device_group_ids = DeviceGroup::query()
            ->with(['devices' => function ($query) {
                $query->select('devices.device_id');
            }])
            ->get()
            ->filter(function (DeviceGroup $device_group) {
                if ($device_group->type == 'dynamic') {
                    try {
                        return $device_group->getParser()
                            ->toQuery()
                            ->where('devices.device_id', $this->device->device_id)
                            ->exists();
                    } catch (\Illuminate\Database\QueryException $e) {
                        Log::error("Device Group '$device_group->name' generates invalid query: " . $e->getMessage());

                        return false;
                    }
                }

                // for static, if this device is include, keep it.
                return $device_group->devices
                    ->where('device_id', $this->device->device_id)
                    ->isNotEmpty();
            })->pluck('id');

        return $this->device->groups()->sync($device_group_ids);
    }
}
