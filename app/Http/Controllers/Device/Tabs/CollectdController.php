<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class CollectdController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return ObzoraConfig::has('collectd_dir') && is_dir(ObzoraConfig::get('collectd_dir') . '/' . $device->hostname . '/');
    }

    public function slug(): string
    {
        return 'collectd';
    }

    public function icon(): string
    {
        return 'fa-pie-chart';
    }

    public function name(): string
    {
        return __('CollectD');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
