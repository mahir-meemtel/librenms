<?php

namespace App\Http\Controllers\Graphs;

use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller for memory pool graphs
 */
class MempoolGraphController extends BaseGraphController
{
    /**
     * Get memory pool usage graph data
     *
     * @param Request $request
     * @param int $deviceId
     * @return JsonResponse
     */
    public function usage(Request $request, int $deviceId): JsonResponse
    {
        try {
            $device = Device::findOrFail($deviceId);
            $from = $this->getTimeRange($request);

            // Get RRD file path
            $rrdFile = $this->rrdStore->name($device->hostname, ['mempool']);

            if (!file_exists($rrdFile)) {
                return $this->errorResponse("RRD file not found: " . basename($rrdFile));
            }

            // Get data source names
            $dsNames = $this->graphDataService->getDataSourceNames($rrdFile);
            $usedDs = in_array('used', $dsNames) ? 'used' : ($dsNames[0] ?? 'value');
            $freeDs = in_array('free', $dsNames) ? 'free' : null;

            // Fetch time-series data
            $usedData = $this->graphDataService->fetchTimeSeries($rrdFile, $usedDs, $from);
            
            $datasets = [
                [
                    'label' => 'Memory Used',
                    'data' => $usedData['datasets'][0]['data'] ?? [],
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true
                ]
            ];

            // Add free memory if available
            if ($freeDs) {
                $freeData = $this->graphDataService->fetchTimeSeries($rrdFile, $freeDs, $from);
                $datasets[] = [
                    'label' => 'Memory Free',
                    'data' => $freeData['datasets'][0]['data'] ?? [],
                    'borderColor' => '#2F2F2F',
                    'backgroundColor' => 'rgba(47, 47, 47, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true
                ];
            }

            $data = [
                'labels' => $usedData['labels'] ?? [],
                'datasets' => $datasets
            ];

            return $this->jsonResponse($data);

        } catch (\Exception $e) {
            Log::error('Mempool graph error', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display graph view
     *
     * @param Request $request
     * @param int $deviceId
     * @return \Illuminate\View\View
     */
    public function view(Request $request, int $deviceId)
    {
        $device = Device::findOrFail($deviceId);
        
        return view('graphs.mempool.usage', [
            'device' => $device,
            'deviceId' => $deviceId,
            'from' => $this->getTimeRange($request)
        ]);
    }
}

