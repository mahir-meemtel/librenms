<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class ProcessesController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return DB::table('processes')->where('device_id', $device->device_id)->exists();
    }

    public function slug(): string
    {
        return 'processes';
    }

    public function icon(): string
    {
        return 'fa-microchip';
    }

    public function name(): string
    {
        return __('Processes');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
