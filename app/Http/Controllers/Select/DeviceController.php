<?php
namespace App\Http\Controllers\Select;

use App\Models\Device;

class DeviceController extends SelectController
{
    private $id = 'device_id';

    protected function rules()
    {
        return [
            'access' => 'nullable|in:normal,inverted',
            'user' => 'nullable|int',
            'id' => 'nullable|in:device_id,hostname',
        ];
    }

    protected function searchFields($request)
    {
        return ['hostname', 'sysName'];
    }

    protected function baseQuery($request)
    {
        $this->id = $request->get('id', 'device_id');
        $user_id = $request->get('user');

        // list devices the user does not have access to
        if ($request->get('access') == 'inverted' && $user_id && $request->user()->isAdmin()) {
            return Device::query()
                ->select('device_id', 'hostname', 'sysName', 'display', 'icon')
                ->whereNotIn('device_id', function ($query) use ($user_id) {
                    $query->select('device_id')
                        ->from('devices_perms')
                        ->where('user_id', $user_id);
                })
                ->orderBy('hostname');
        }

        return Device::hasAccess($request->user())
            ->select('device_id', 'hostname', 'sysName', 'display', 'icon')
            ->orderBy('hostname');
    }

    public function formatItem($device)
    {
        /** @var Device $device */
        return [
            'id' => $device->{$this->id},
            'text' => $device->displayName(),
            'icon' => $device->icon,
        ];
    }
}
