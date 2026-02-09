<?php
namespace App\Http\Controllers\Maps;

use App\Facades\ObzoraConfig;
use App\Http\Controllers\Controller;
use App\Models\DeviceGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FullscreenMapController extends Controller
{
    protected function fullscreenMap(Request $request): View|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'group' => 'int',
            'lat' => 'numeric',
            'lng' => 'numeric',
            'zoom' => 'numeric',
        ]);

        if ($validator->fails()) {
            return redirect('fullscreenmap');
        }

        $group_name = null;
        if ($request->get('group')) {
            $group_name = DeviceGroup::where('id', '=', $request->get('group'))->first('name');
            if (! empty($group_name)) {
                $group_name = $group_name->name;
            }
        }

        $init_lat = $request->get('lat');
        if (! $init_lat) {
            $init_lat = ObzoraConfig::get('leaflet.default_lat', 51.48);
        }

        $init_lng = $request->get('lng');
        if (! $init_lng) {
            $init_lng = ObzoraConfig::get('leaflet.default_lng', 0);
        }

        $init_zoom = $request->get('zoom');
        if (! $init_zoom) {
            $init_zoom = ObzoraConfig::get('leaflet.default_zoom', 5);
        }

        $data = [
            'map_engine' => ObzoraConfig::get('map.engine', 'leaflet'),
            'map_provider' => ObzoraConfig::get('geoloc.engine', 'openstreetmap'),
            'map_api_key' => ObzoraConfig::get('geoloc.api_key', ''),
            'show_netmap' => ObzoraConfig::get('network_map_show_on_worldmap', false),
            'netmap_source' => ObzoraConfig::get('network_map_worldmap_link_type', 'xdp'),
            'netmap_include_disabled_alerts' => ObzoraConfig::get('network_map_worldmap_show_disabled_alerts', true) ? 'null' : 0,
            'page_refresh' => ObzoraConfig::get('page_refresh', 300),
            'init_lat' => $init_lat,
            'init_lng' => $init_lng,
            'init_zoom' => $init_zoom,
            'group_radius' => ObzoraConfig::get('leaflet.group_radius', 80),
            'tile_url' => ObzoraConfig::get('leaflet.tile_url', '{s}.tile.openstreetmap.org'),
            'group_id' => $request->get('group'),
            'group_name' => $group_name,
        ];

        return view('map.fullscreen', $data);
    }
}
