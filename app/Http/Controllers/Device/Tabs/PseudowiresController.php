<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class PseudowiresController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->pseudowires()->exists();
    }

    public function slug(): string
    {
        return 'pseudowires';
    }

    public function icon(): string
    {
        return 'fa-arrows-alt';
    }

    public function name(): string
    {
        return __('Pseudowires');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
