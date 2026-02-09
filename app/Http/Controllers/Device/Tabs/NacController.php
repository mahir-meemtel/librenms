<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class NacController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->portsNac()->exists();
    }

    public function slug(): string
    {
        return 'nac';
    }

    public function icon(): string
    {
        return 'fa-lock';
    }

    public function name(): string
    {
        return __('NAC');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
