<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class PackagesController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->packages()->exists();
    }

    public function slug(): string
    {
        return 'packages';
    }

    public function icon(): string
    {
        return 'fa-folder';
    }

    public function name(): string
    {
        return __('Pkgs');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
