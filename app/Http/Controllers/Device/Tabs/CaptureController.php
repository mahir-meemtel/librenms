<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;

class CaptureController implements \ObzoraNMS\Interfaces\UI\DeviceTab
{
    public function visible(Device $device): bool
    {
        return false;
    }

    public function slug(): string
    {
        return 'capture';
    }

    public function icon(): string
    {
        return 'fa-bug';
    }

    public function name(): string
    {
        return __('Capture');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
