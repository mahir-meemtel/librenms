<?php
namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SensorController
{
    public function index(Request $request, string $metric = '', string $legacyview = ''): View
    {
        $metric = str_replace('metric=', '', $metric);
        $view = str_replace('view=', '', $legacyview) ?: $request->get('view', 'detail');

        $metrics = $this->getMetrics($request);
        $metric = $metric ?: array_key_first($metrics);

        if (! array_key_exists($metric, $metrics)) {
            abort(404);
        }

        $views = [
            'graphs' => ['text' => __('Graphs'), 'link' => $request->fullUrlWithQuery(['view' => 'graphs'])],
            'detail' => ['text' => __('No Graphs'), 'link' => $request->fullUrlWithoutQuery('view')],
        ];

        $title = 'Health :: ' . match ($metric) {
            'dbm' => 'dBm',
            'snr' => 'SNR',
            default => ucfirst($metric),
        };

        $blade_view = match ($metric) {
            'mempool' => 'sensor.mempool',
            'processor' => 'sensor.processor',
            'storage' => 'sensor.storage',
            'diskio' => 'sensor.diskio',
            'printer-supply' => 'sensor.printer-supply',
            default => 'sensor.index',
        };

        return view($blade_view, [
            'title' => $title,
            'metrics' => $metrics,
            'metric' => $metric,
            'views' => $views,
            'view' => $view,
        ]);
    }

    /**
     * @return array<array<string, (array|string)>>
     */
    private function getMetrics(Request $request): array
    {
        $metrics = [
            'mempool' => [
                'text' => __('Memory'),
                'link' => route('sensor.index', $request->all() + ['metric' => 'mempool']),
                'icon' => 'fa-memory',
            ],
            'processor' => [
                'text' => __('Processor'),
                'link' => route('sensor.index', $request->all() + ['metric' => 'processor']),
                'icon' => 'fa-microchip',
            ],
            'storage' => [
                'text' => __('Storage'),
                'link' => route('sensor.index', $request->all() + ['metric' => 'storage']),
                'icon' => 'fa-hdd',
            ],
            'diskio' => [
                'text' => __('Disk I/O'),
                'link' => route('sensor.index', $request->all() + ['metric' => 'diskio']),
                'icon' => 'fa-hdd',
            ],
            'printer-supply' => [
                'text' => __('sensors.printer-supply.long'),
                'link' => route('sensor.index', $request->all() + ['metric' => 'printer-supply']),
                'icon' => 'fa-print',
            ],
        ];

        $sensors_menu = \ObzoraNMS\Util\ObjectCache::sensors();
        foreach ($sensors_menu as $types) {
            foreach ($types as $entry) {
                $class = $entry['class'];
                $metrics[$class] = [
                    'text' => trans('sensors.' . $class . '.short'),
                    'link' => route('sensor.index', $request->all() + ['metric' => $class]),
                    'icon' => 'fa-' . $entry['icon'],
                ];
            }
        }

        return $metrics;
    }
}
