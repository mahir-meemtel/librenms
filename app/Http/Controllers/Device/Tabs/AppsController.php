<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class AppsController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->applications()->exists();
    }

    public function slug(): string
    {
        return 'apps';
    }

    public function icon(): string
    {
        return 'fa-cubes';
    }

    public function name(): string
    {
        return __('Apps');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
