<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class GraphsController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return true;
    }

    public function slug(): string
    {
        return 'graphs';
    }

    public function icon(): string
    {
        return 'fa-area-chart';
    }

    public function name(): string
    {
        return __('Graphs');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
