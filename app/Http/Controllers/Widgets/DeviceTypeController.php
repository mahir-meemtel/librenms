<?php
namespace App\Http\Controllers\Widgets;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;
use ObzoraNMS\DB\Eloquent;

class DeviceTypeController extends WidgetController
{
    protected string $name = 'device-types';

    public function __construct()
    {
        // init defaults we need to check config, so do it in construct
        $this->defaults = [
            'top_device_group_count' => 5,
            'sort_order' => 'name',
        ];
    }

    public function getView(Request $request): string|View
    {
        return view('widgets.device-types', $this->getData($request));
    }

    protected function getData(Request $request): array
    {
        $data = $this->getSettings();

        $counts = Device::groupBy(['type'])->select('type', Eloquent::DB()->raw('COUNT(*) as total'))->orderByDesc('total')->pluck('total', 'type');

        if ($data['top_device_group_count']) {
            $top = $counts->take($data['top_device_group_count']);
        } else {
            $top = $counts;
        }

        $count = 0;
        $device_types = [];
        foreach (\App\Facades\ObzoraConfig::get('device_types') as $device_type) {
            $count++;
            $device_types[] = [
                'type' => $device_type['type'],
                'count' => $counts->get($device_type['type'], 0),
                'visible' => $top->has($device_type['type']) || (! $data['top_device_group_count'] || $count < $data['top_device_group_count']),
            ];
        }

        if ($data['sort_order'] == 'name') {
            usort($device_types, function ($item1, $item2) {
                return $item1['type'] <=> $item2['type'];
            });
        } else {
            usort($device_types, function ($item1, $item2) {
                return $item2['count'] <=> $item1['count'];
            });
        }

        $data['device_types'] = $device_types;

        return $data;
    }
}
