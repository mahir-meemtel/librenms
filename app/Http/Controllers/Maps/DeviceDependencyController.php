<?php
namespace App\Http\Controllers\Maps;

use App\Facades\ObzoraConfig;
use App\Http\Controllers\Controller;
use App\Models\DeviceGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceDependencyController extends Controller
{
    // Device Dependency Map
    public function dependencyMap(Request $request): View
    {
        $group_id = $request->get('group');

        $group_name = DeviceGroup::where('id', '=', $group_id)->first('name');
        if (! empty($group_name)) {
            $group_name = $group_name->name;
        }

        $data = [
            'page_refresh' => ObzoraConfig::get('page_refresh', 300),
            'group_id' => $group_id,
            'options' => ObzoraConfig::get('network_map_dependencymap_vis_options') ?? ObzoraConfig::get('network_map_vis_options'),
            'group_name' => $group_name,
            'highlight_style' => [
                'color' => [
                    'highlight' => [
                        'border' => ObzoraConfig::get('network_map_legend.highlight.border'),
                    ],
                    'border' => ObzoraConfig::get('network_map_legend.highlight.border'),
                ],
                'borderWidth' => ObzoraConfig::get('network_map_legend.highlight.borderWidth'),
            ],
        ];

        return view('map.device-dependency', $data);
    }
}
