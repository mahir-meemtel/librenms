<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class AlertStatsController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return true;
    }

    public function slug(): string
    {
        return 'alert-stats';
    }

    public function icon(): string
    {
        return 'fa-bar-chart';
    }

    public function name(): string
    {
        return __('Alert Stats');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
