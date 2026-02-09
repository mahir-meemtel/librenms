<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class VmInfoController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->vminfo()->exists();
    }

    public function slug(): string
    {
        return 'vm';
    }

    public function icon(): string
    {
        return 'fa-cog';
    }

    public function name(): string
    {
        return __('Virtual Machines');
    }

    public function data(Device $device, Request $request): array
    {
        return [
            'vms' => self::getVms($device),
        ];
    }

    private static function getVms(Device $device)
    {
        return $device->vminfo()
        ->select('vmwVmDisplayName', 'vmwVmState', 'vmwVmGuestOS', 'vmwVmMemSize', 'vmwVmCpus')
        ->with('parentDevice')
        ->orderBy('vmwVmDisplayName')
        ->get();
    }
}
