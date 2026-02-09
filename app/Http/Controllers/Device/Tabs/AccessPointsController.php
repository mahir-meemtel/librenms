<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class AccessPointsController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->accessPoints()->exists();
    }

    public function slug(): string
    {
        return 'accesspoints';
    }

    public function icon(): string
    {
        return 'fa-wifi';
    }

    public function name(): string
    {
        return __('Access Points');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
