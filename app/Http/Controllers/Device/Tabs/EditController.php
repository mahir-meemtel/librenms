<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;

class EditController implements \ObzoraNMS\Interfaces\UI\DeviceTab
{
    public function visible(Device $device): bool
    {
        return false;
    }

    public function slug(): string
    {
        return 'edit';
    }

    public function icon(): string
    {
        return 'fa-gear';
    }

    public function name(): string
    {
        return __('Edit');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }
}
