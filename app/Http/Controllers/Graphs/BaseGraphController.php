<?php

namespace App\Http\Controllers\Graphs;

use App\Http\Controllers\Controller;
use App\Services\GraphDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Data\Store\Rrd as RrdStore;

/**
 * Base controller for all graph controllers
 * Provides common functionality for fetching RRD data
 */
abstract class BaseGraphController extends Controller
{
    protected GraphDataService $graphDataService;
    protected RrdStore $rrdStore;

    public function __construct(GraphDataService $graphDataService, RrdStore $rrdStore)
    {
        $this->middleware('auth');
        $this->graphDataService = $graphDataService;
        $this->rrdStore = $rrdStore;
    }

    /**
     * Get time range from request
     *
     * @param Request $request
     * @return string
     */
    protected function getTimeRange(Request $request): string
    {
        return $request->get('from', '-24h');
    }

    /**
     * Return JSON response with chart data
     *
     * @param array $data Chart data in Chart.js format
     * @param int $statusCode HTTP status code
     * @return JsonResponse
     */
    protected function jsonResponse(array $data, int $statusCode = 200): JsonResponse
    {
        return response()->json($data, $statusCode);
    }

    /**
     * Return error response
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = 404): JsonResponse
    {
        Log::warning('Graph API error', [
            'controller' => static::class,
            'message' => $message
        ]);

        return response()->json([
            'error' => $message,
            'labels' => [],
            'datasets' => []
        ], $statusCode);
    }

    /**
     * Prepare Chart.js data from RRD fetch output
     *
     * @param array $rrdData Output from GraphDataService::fetchTimeSeries
     * @param string $dataSource Data source name to extract
     * @param string $label Dataset label
     * @param string $color Line color
     * @return array Chart.js format data
     */
    protected function prepareChartData(array $rrdData, string $dataSource, string $label, string $color): array
    {
        $timestamps = $rrdData['timestamps'] ?? [];
        $values = $rrdData['values'][$dataSource] ?? [];
        
        // Convert to Chart.js format
        $dataPoints = [];
        foreach ($timestamps as $index => $timestamp) {
            $value = $values[$index] ?? null;
            if ($value !== null && !is_nan($value)) {
                $dataPoints[] = [
                    'x' => $timestamp,
                    'y' => (float)$value
                ];
            }
        }
        
        return [
            'labels' => $timestamps,
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $dataPoints,
                    'borderColor' => $color,
                    'backgroundColor' => $color . '40', // 25% opacity
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 0,
                    'borderWidth' => 2,
                ]
            ]
        ];
    }
}

