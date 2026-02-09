<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class ServicesController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return (bool) \App\Facades\ObzoraConfig::get('show_services') && $device->services()->exists();
    }

    public function slug(): string
    {
        return 'services';
    }

    public function icon(): string
    {
        return 'fa-cogs';
    }

    public function name(): string
    {
        return __('Services');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
