<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use App\Models\TnmsneInfo;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class TnmsneController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->os == 'coriant' && TnmsneInfo::query()->where('device_id', $device->device_id)->exists();
    }

    public function slug(): string
    {
        return 'tnmsne';
    }

    public function icon(): string
    {
        return 'fa-link';
    }

    public function name(): string
    {
        return __('Hardware');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
