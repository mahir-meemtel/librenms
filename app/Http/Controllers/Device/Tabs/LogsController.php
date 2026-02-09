<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class LogsController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return true;
    }

    public function slug(): string
    {
        return 'logs';
    }

    public function icon(): string
    {
        return 'fa-sticky-note';
    }

    public function name(): string
    {
        return __('Logs');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
