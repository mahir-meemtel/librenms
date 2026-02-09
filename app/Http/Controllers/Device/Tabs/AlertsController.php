<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class AlertsController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return true;
    }

    public function slug(): string
    {
        return 'alerts';
    }

    public function icon(): string
    {
        return 'fa-exclamation-circle';
    }

    public function name(): string
    {
        return __('Alerts');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
