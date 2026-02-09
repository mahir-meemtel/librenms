<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ObzoraNMS\Data\Store\Rrd;
use ObzoraNMS\Util\Number;
use ObzoraNMS\Util\Color;

class QfpMemoryController extends Controller
{
    public function index()
    {
        // Replace this path with your actual RRD file for the device/component
        $rrd_filename = '/opt/obzora/rrd/qfp_memory.rrd';

        // Fetch last values (this is pseudo-code, adjust with your RRD library)
        $memoryUsed = Rrd::getLastValue($rrd_filename, 'InUse');        // Used memory
        $memoryFree = Rrd::getLastValue($rrd_filename, 'Free');         // Free memory
        $memoryTotal = Rrd::getLastValue($rrd_filename, 'Total');       // Total memory

        $lowWatermark = Rrd::getLastValue($rrd_filename, 'LowFreeWatermark');
        $risingThreshold = Rrd::getLastValue($rrd_filename, 'RisingThreshold');
        $fallingThreshold = Rrd::getLastValue($rrd_filename, 'FallingThreshold');

        // Calculate percentages
        $percUsed = Number::calculatePercent($memoryUsed, $memoryTotal);
        $percFree = 100 - $percUsed;

        // Assign colors based on usage
        $background = Color::percentage($percUsed, 75); // same as your old code

        // JSON structure for Chart.js
        $data = [
            'labels' => ['Used', 'Free'],
            'datasets' => [
                [
                    'label' => 'Memory (MB)',
                    'data' => [(float)$memoryUsed, (float)$memoryFree],
                    'backgroundColor' => [$background['right'], $background['left']],
                    'borderColor' => ['#ffffff', '#ffffff'],
                    'borderWidth' => 1
                ]
            ],
            'thresholds' => [
                'low_watermark' => $lowWatermark,
                'rising_threshold' => $risingThreshold,
                'falling_threshold' => $fallingThreshold
            ],
            'stats' => [
                'total' => $memoryTotal,
                'used'  => $memoryUsed,
                'free'  => $memoryFree,
                'perc_used' => $percUsed,
                'perc_free' => $percFree
            ]
        ];

        return response()->json($data);
    }

    public function view()
    {
        // Serve the Blade template with Chart.js
        return view('qfp-memory');
    }

    /**
     * Get QFP memory data as JSON for Chart.js
     *
     * @return JsonResponse
     */
    public function data()
    {
        // Replace this path with your actual RRD file for the device/component
        $rrd_filename = '/opt/obzora/rrd/qfp_memory.rrd';

        // Fetch last values using GraphDataService
        $graphDataService = app(\App\Services\GraphDataService::class);
        
        $memoryUsed = $graphDataService->getLastValue($rrd_filename, 'InUse');
        $memoryFree = $graphDataService->getLastValue($rrd_filename, 'Free');
        $memoryTotal = $graphDataService->getLastValue($rrd_filename, 'Total');

        $lowWatermark = $graphDataService->getLastValue($rrd_filename, 'LowFreeWatermark');
        $risingThreshold = $graphDataService->getLastValue($rrd_filename, 'RisingThreshold');
        $fallingThreshold = $graphDataService->getLastValue($rrd_filename, 'FallingThreshold');

        // Calculate percentages
        $percUsed = $memoryTotal > 0 ? ($memoryUsed / $memoryTotal) * 100 : 0;
        $percFree = 100 - $percUsed;

        // Assign colors based on usage
        $background = \ObzoraNMS\Util\Color::percentage($percUsed, 75);

        // JSON structure for Chart.js
        $data = [
            'labels' => ['Used', 'Free'],
            'datasets' => [
                [
                    'label' => 'Memory (MB)',
                    'data' => [(float)$memoryUsed, (float)$memoryFree],
                    'backgroundColor' => [$background['right'], $background['left']],
                    'borderColor' => ['#ffffff', '#ffffff'],
                    'borderWidth' => 1
                ]
            ],
            'thresholds' => [
                'low_watermark' => $lowWatermark,
                'rising_threshold' => $risingThreshold,
                'falling_threshold' => $fallingThreshold
            ],
            'stats' => [
                'total' => $memoryTotal,
                'used'  => $memoryUsed,
                'free'  => $memoryFree,
                'perc_used' => $percUsed,
                'perc_free' => $percFree
            ]
        ];

        return response()->json($data);
    }
}
