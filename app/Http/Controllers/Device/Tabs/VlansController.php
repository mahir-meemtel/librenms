<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use App\Models\PortVlan;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class VlansController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->vlans()->exists();
    }

    public function slug(): string
    {
        return 'vlans';
    }

    public function icon(): string
    {
        return 'fa-tasks';
    }

    public function name(): string
    {
        return __('VLANs');
    }

    public function data(Device $device, Request $request): array
    {
        return [
            'vlans' => self::getVlans($device),
            'submenu' => [
                [
                    ['name' => 'Basic', 'url' => ''],
                ],
                'Graphs' => [
                    ['name' => 'Bits', 'url' => 'bits'],
                    ['name' => 'Unicast Packets', 'url' => 'upkts'],
                    ['name' => 'Non-Unicast Packets', 'url' => 'nupkts'],
                    ['name' => 'Errors', 'url' => 'errors'],
                ],
            ],
        ];
    }

    private static function getVlans(Device $device)
    {
        // port.device needed to prevent loading device multiple times
        $portVlan = PortVlan::where('ports_vlans.device_id', $device->device_id)
            ->join('vlans', function ($join) {
                $join
                ->on('ports_vlans.vlan', 'vlans.vlan_vlan')
                ->on('vlans.device_id', 'ports_vlans.device_id');
            })
            ->join('ports', function ($join) {
                $join
                ->on('ports_vlans.port_id', 'ports.port_id');
            })
            ->with(['port.device'])
            ->select('ports_vlans.*', 'vlans.vlan_name')->orderBy('vlan_vlan')->orderBy('ports.ifName')->orderBy('ports.ifDescr')
            ->get()->sortBy(['vlan', 'port']);

        $data = $portVlan->groupBy('vlan');

        return $data;
    }
}
