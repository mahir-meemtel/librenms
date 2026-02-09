<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class MefController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->mefInfo()->exists();
    }

    public function slug(): string
    {
        return 'mef';
    }

    public function icon(): string
    {
        return 'fa-link';
    }

    public function name(): string
    {
        return __('Metro Ethernet');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
