<?php
namespace App\Http\Controllers\Widgets;

use App\Facades\ObzoraConfig;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorldMapController extends WidgetController
{
    protected string $name = 'world-map';

    public function __construct()
    {
        $this->defaults = [
            'title' => null,
            'init_lat' => ObzoraConfig::get('leaflet.default_lat'),
            'init_lng' => ObzoraConfig::get('leaflet.default_lng'),
            'init_zoom' => ObzoraConfig::get('leaflet.default_zoom'),
            'init_layer' => ObzoraConfig::get('geoloc.layer'),
            'group_radius' => ObzoraConfig::get('leaflet.group_radius'),
            'status' => '0,1',
            'device_group' => null,
        ];
    }

    public function getView(Request $request): string|View
    {
        $settings = $this->getSettings();
        $settings['dimensions'] = $request->get('dimensions');
        $settings['status'] = array_map('intval', explode(',', $settings['status']));
        $settings['map_config'] = [
            'engine' => ObzoraConfig::get('geoloc.engine'),
            'api_key' => ObzoraConfig::get('geoloc.api_key'),
            'tile_url' => ObzoraConfig::get('leaflet.tile_url'),
            'lat' => $settings['init_lat'],
            'lng' => $settings['init_lng'],
            'zoom' => $settings['init_zoom'],
            'layer' => $settings['init_layer'],
        ];

        return view('widgets.world-map', $settings);
    }
}
