<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;
use ObzoraNMS\Util\Url;

class StpController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->stpInstances()->exists();
    }

    public function slug(): string
    {
        return 'stp';
    }

    public function icon(): string
    {
        return 'fa-sitemap';
    }

    public function name(): string
    {
        return __('STP');
    }

    public function data(Device $device, Request $request): array
    {
        $active_vlan = Url::parseOptions('vlan', 1);
        $stpInstances = $device->stpInstances;
        $vlanOptions = $stpInstances->pluck('vlan')->mapWithKeys(function ($vlan) use ($device) {
            if (empty($vlan)) {
                $vlan = 1;
            }

            return [$vlan => [
                'text' => $vlan,
                'link' => Url::deviceUrl($device, ['tab' => 'stp', 'vlan' => $vlan]),
            ]];
        });

        return [
            'vlans' => $vlanOptions->all(),
            'vlan' => $active_vlan,
            'device_id' => $device->device_id,
            'stpInstances' => $stpInstances->filter(function ($instance) use ($active_vlan) {
                return $active_vlan == 1 && $instance->vlan == null || $instance->vlan == $active_vlan;
            }),
            'stpPorts' => $device->stpPorts()->where('vlan', $active_vlan)->when($active_vlan == 1, function ($query) {
                return $query->orWhereNull('vlan');
            })->exists(),
            'bootgridUrl' => url('/ajax/table/'),
        ];
    }
}
