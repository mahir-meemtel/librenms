<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class WirelessController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->wirelessSensors()->exists();
    }

    public function slug(): string
    {
        return 'wireless';
    }

    public function icon(): string
    {
        return 'fa-wifi';
    }

    public function name(): string
    {
        return __('Wireless');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
