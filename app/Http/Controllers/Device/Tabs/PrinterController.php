<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class PrinterController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->printerSupplies()->exists();
    }

    public function slug(): string
    {
        return 'printer';
    }

    public function icon(): string
    {
        return 'fa-print';
    }

    public function name(): string
    {
        return __('Printer');
    }

    public function data(Device $device, Request $request): array
    {
        return [
            'tab' => 'toner',
        ];
    }
}
