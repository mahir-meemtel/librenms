<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Facades\DeviceCache;
use App\Models\Component;
use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class RoutingController implements DeviceTab
{
    private $tabs;

    public function __construct()
    {
        $device = DeviceCache::getPrimary();
        //dd($device);
        $this->tabs = [
            'ospf' => $device->ospfInstances()->count(),
            'ospfv3' => $device->ospfv3Instances()->count(),
            'isis' => $device->isisAdjacencies()->count(),
            'bgp' => $device->bgppeers()->count(),
            'vrf' => $device->vrfs()->count(),
            'cef' => $device->cefSwitching()->count(),
            'mpls' => $device->mplsServices()->count(),
            'cisco-otv' => Component::query()->where('device_id', $device->device_id)->where('type', 'Cisco-OTV')->count(),
            'loadbalancer_rservers' => $device->rServers()->count(),
            'ipsec_tunnels' => $device->ipsecTunnels()->count(),
            'routes' => $device->routes()->count(),
        ];
    }

    public function visible(Device $device): bool
    {
        return in_array(true, $this->tabs);
    }

    public function slug(): string
    {
        return 'routing';
    }

    public function icon(): string
    {
        return 'fa-random';
    }

    public function name(): string
    {
        return __('Routing');
    }

    public function data(Device $device, Request $request): array
    {
        return [
            'routing_tabs' => array_filter($this->tabs),
        ];
    }
}
