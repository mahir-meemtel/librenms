<?php

namespace App\Http\Controllers\Graphs;

use App\Models\Device;
use App\Models\Mempool;
use App\Models\Processor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller for device-level graphs (CPU, memory, uptime)
 */
class DeviceGraphController extends BaseGraphController
{
    /**
     * Get CPU/Processor usage graph data
     *
     * @param Request $request
     * @param int $deviceId
     * @return JsonResponse
     */
    public function cpu(Request $request, int $deviceId): JsonResponse
    {
        try {
            $device = Device::findOrFail($deviceId);
            $from = $this->getTimeRange($request);

            // Get first processor for the device
            $processor = Processor::where('device_id', $deviceId)->first();
            if (!$processor) {
                return $this->errorResponse("No processor found for device $deviceId");
            }

            // Get RRD file path using RrdStore
            $rrdName = ['processor', $processor->processor_type, $processor->processor_index];
            $rrdFile = $this->rrdStore->name($device->hostname, $rrdName);

            if (!file_exists($rrdFile)) {
                return $this->errorResponse("RRD file not found: " . basename($rrdFile));
            }

            // Get data source name (usually 'usage' for processors)
            $dsNames = $this->graphDataService->getDataSourceNames($rrdFile);
            $dataSource = in_array('usage', $dsNames) ? 'usage' : ($dsNames[0] ?? 'value');

            // Fetch time-series data
            $data = $this->graphDataService->fetchTimeSeries($rrdFile, $dataSource, $from);
            
            // Update dataset label and colors
            if (!empty($data['datasets'])) {
                $data['datasets'][0]['label'] = 'CPU Usage (%)';
                $data['datasets'][0]['borderColor'] = '#FFD369';
                $data['datasets'][0]['backgroundColor'] = 'rgba(255, 211, 105, 0.1)';
                $data['datasets'][0]['fill'] = true;
            }

            return $this->jsonResponse($data);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse("Device with ID $deviceId not found");
        } catch (\Exception $e) {
            Log::error('Device CPU graph error', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get memory usage graph data
     *
     * @param Request $request
     * @param int $deviceId
     * @return JsonResponse
     */
    public function memory(Request $request, int $deviceId): JsonResponse
    {
        // Log that the route was hit
        Log::info('DeviceGraphController::memory called', [
            'device_id' => $deviceId,
            'from' => $request->get('from', '-24h'),
            'url' => $request->fullUrl()
        ]);
        
        try {
            $device = Device::findOrFail($deviceId);
            $from = $this->getTimeRange($request);

            // Get mempools for the device - prefer 'system' class, otherwise use first available
            $mempool = Mempool::where('device_id', $deviceId)
                ->where('mempool_class', 'system')
                ->first();
            
            if (!$mempool) {
                // Fallback to first mempool if no system mempool found
                $mempool = Mempool::where('device_id', $deviceId)->first();
            }
            
            if (!$mempool) {
                return $this->errorResponse("No mempool found for device $deviceId");
            }

            // Get RRD file path for mempool - format: mempool-{type}-{class}-{index}
            $rrdFile = $this->rrdStore->name($device->hostname, [
                'mempool',
                $mempool->mempool_type,
                $mempool->mempool_class,
                $mempool->mempool_index
            ]);

            if (!file_exists($rrdFile)) {
                return $this->errorResponse("RRD file not found: " . basename($rrdFile) . " (mempool: {$mempool->mempool_type}-{$mempool->mempool_class}-{$mempool->mempool_index})");
            }

            // Get data source names
            $dsNames = $this->graphDataService->getDataSourceNames($rrdFile);
            $usedDs = in_array('used', $dsNames) ? 'used' : ($dsNames[0] ?? 'value');

            // Fetch time-series data
            $data = $this->graphDataService->fetchTimeSeries($rrdFile, $usedDs, $from);

            // Update dataset label and colors
            if (!empty($data['datasets'])) {
                $data['datasets'][0]['label'] = 'Memory Used';
                $data['datasets'][0]['borderColor'] = '#4CAF50';
                $data['datasets'][0]['backgroundColor'] = 'rgba(76, 175, 80, 0.1)';
                $data['datasets'][0]['fill'] = true;
            }

            return $this->jsonResponse($data);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse("Device with ID $deviceId not found");
        } catch (\Exception $e) {
            Log::error('Device memory graph error', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get uptime graph data
     *
     * @param Request $request
     * @param int $deviceId
     * @return JsonResponse
     */
    public function uptime(Request $request, int $deviceId): JsonResponse
    {
        try {
            $device = Device::findOrFail($deviceId);
            $from = $this->getTimeRange($request);

            // Get RRD file path for uptime
            $rrdFile = $this->rrdStore->name($device->hostname, ['uptime']);

            if (!file_exists($rrdFile)) {
                return $this->errorResponse("RRD file not found: " . basename($rrdFile));
            }

            // Get data source name
            $dsNames = $this->graphDataService->getDataSourceNames($rrdFile);
            $dataSource = $dsNames[0] ?? 'value';

            // Fetch time-series data
            $data = $this->graphDataService->fetchTimeSeries($rrdFile, $dataSource, $from);

            // Update dataset label and colors
            if (!empty($data['datasets'])) {
                $data['datasets'][0]['label'] = 'Uptime (seconds)';
                $data['datasets'][0]['borderColor'] = '#2F2F2F';
                $data['datasets'][0]['backgroundColor'] = 'rgba(47, 47, 47, 0.1)';
                $data['datasets'][0]['fill'] = false;
            }

            return $this->jsonResponse($data);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse("Device with ID $deviceId not found");
        } catch (\Exception $e) {
            Log::error('Device uptime graph error', [
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
     * @param string $metric
     * @return \Illuminate\View\View
     */
    public function view(Request $request, int $deviceId, string $metric)
    {
        $device = Device::findOrFail($deviceId);
        
        return view('graphs.device.' . $metric, [
            'device' => $device,
            'deviceId' => $deviceId,
            'metric' => $metric,
            'from' => $this->getTimeRange($request)
        ]);
    }
}

