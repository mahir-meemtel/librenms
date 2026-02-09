<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class HealthController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->storage()->exists() || $device->sensors()->exists() || $device->mempools()->exists() || $device->processors()->exists();
    }

    public function slug(): string
    {
        return 'health';
    }

    public function icon(): string
    {
        return 'fa-heartbeat';
    }

    public function name(): string
    {
        return __('Health');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
