<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class SlasController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->slas()->exists();
    }

    public function slug(): string
    {
        return 'slas';
    }

    public function icon(): string
    {
        return 'fa-flag';
    }

    public function name(): string
    {
        return __('SLAs');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
