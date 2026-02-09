<?php
namespace App\Http\Controllers\Select;

use App\Models\DeviceGroup;

class DeviceGroupController extends SelectController
{
    protected function rules()
    {
        return [
            'type' => 'nullable|in:static,dynamic',
        ];
    }

    protected function searchFields($request)
    {
        return ['name'];
    }

    protected function baseQuery($request)
    {
        return DeviceGroup::hasAccess($request->user())
            ->when($request->get('type'), fn ($query, $type) => $query->where('type', $type))
            ->select(['id', 'name']);
    }

    /**
     * @param  DeviceGroup  $device_group
     */
    public function formatItem($device_group)
    {
        return [
            'id' => $device_group->id,
            'text' => $device_group->name,
        ];
    }
}
