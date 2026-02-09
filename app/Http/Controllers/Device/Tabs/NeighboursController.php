<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use App\Models\Link;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;
use ObzoraNMS\Util\Url;

class NeighboursController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return Link::where('local_device_id', $device->device_id)->exists();
    }

    public function slug(): string
    {
        return 'neighbours';
    }

    public function icon(): string
    {
        return 'fa-sitemap';
    }

    public function name(): string
    {
        return __('Neighbours');
    }

    public function data(Device $device, Request $request): array
    {
        $selection = Url::parseOptions('selection', 'list');

        $devices[$device->device_id] = [
            'url' => Url::deviceLink($device, null, [], 0, 0, 0, 1),
            'hw' => $device->hardware,
            'name' => $device->shortDisplayName(),
        ];

        if ($selection == 'list') {
            $linksQuery = $device->links()->with('port', 'remoteDevice', 'remotePort');

            $links = $linksQuery->get()->sortBy('port.ifName');
        } else {
            $links = [];
        }

        return [
            'selections' => [
                'list' => [
                    'text' => 'List',
                    'link' => route('device', ['device' => $device, 'tab' => 'neighbours', 'vars' => 'selection=list']),
                ],
                'map' => [
                    'text' => 'Map',
                    'link' => route('device', ['device' => $device, 'tab' => 'neighbours', 'vars' => 'selection=map']),
                ],
            ],
            'selection' => $selection,
            'device' => $device,
            'links' => $links,
            'link_types' => ObzoraConfig::get('network_map_items', ['xdp', 'mac']),
            'visoptions' => ObzoraConfig::get('network_map_vis_options'),
        ];
    }
}
