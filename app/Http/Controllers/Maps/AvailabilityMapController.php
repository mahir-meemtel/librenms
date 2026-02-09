<?php
namespace App\Http\Controllers\Maps;

use App\Facades\ObzoraConfig;
use App\Http\Controllers\Controller;
use App\Models\DeviceGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityMapController extends Controller
{
    // Availability Map
    public function availabilityMap(Request $request): View
    {
        $data = [
            'page_refresh' => ObzoraConfig::get('page_refresh', 300),
            'compact' => ObzoraConfig::get('webui.availability_map_compact'),
            'box_size' => ObzoraConfig::get('webui.availability_map_box_size'),
            'sort' => ObzoraConfig::get('webui.availability_map_sort_status') ? 'status' : 'hostname',
            'use_groups' => ObzoraConfig::get('webui.availability_map_use_device_groups'),
            'services' => ObzoraConfig::get('show_services'),
            'uptime_warn' => ObzoraConfig::get('uptime_warning'),
            'devicegroups' => ObzoraConfig::get('webui.availability_map_use_device_groups') ? DeviceGroup::hasAccess($request->user())->orderBy('name')->get(['id', 'name']) : [],
        ];

        return view('map.availability', $data);
    }
}
