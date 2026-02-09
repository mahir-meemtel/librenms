<?php

namespace App\Http\Controllers\Graphs;

use App\Models\Device;
use App\Models\Processor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller for processor graphs
 */
class ProcessorGraphController extends BaseGraphController
{
    /**
     * Get processor usage graph data
     *
     * @param Request $request
     * @param int $deviceId
     * @param int|null $processorId
     * @return JsonResponse
     */
    public function usage(Request $request, int $deviceId, ?int $processorId = null): JsonResponse
    {
        try {
            $device = Device::findOrFail($deviceId);
            $from = $this->getTimeRange($request);

            // Get processor
            if ($processorId) {
                $processor = Processor::where('device_id', $deviceId)
                    ->where('processor_id', $processorId)
                    ->firstOrFail();
            } else {
                $processor = Processor::where('device_id', $deviceId)->first();
                if (!$processor) {
                    return $this->errorResponse("No processor found for device $deviceId");
                }
            }

            // Get RRD file path
            $rrdName = ['processor', $processor->processor_type, $processor->processor_index];
            $rrdFile = $this->rrdStore->name($device->hostname, $rrdName);

            if (!file_exists($rrdFile)) {
                return $this->errorResponse("RRD file not found: " . basename($rrdFile));
            }

            // Get data source name
            $dsNames = $this->graphDataService->getDataSourceNames($rrdFile);
            $dataSource = in_array('usage', $dsNames) ? 'usage' : ($dsNames[0] ?? 'value');

            // Fetch time-series data
            $data = $this->graphDataService->fetchTimeSeries($rrdFile, $dataSource, $from);

            // Update dataset label and colors
            if (!empty($data['datasets'])) {
                $data['datasets'][0]['label'] = 'Processor Usage (%)';
                $data['datasets'][0]['borderColor'] = '#FFD369';
                $data['datasets'][0]['backgroundColor'] = 'rgba(255, 211, 105, 0.1)';
                $data['datasets'][0]['fill'] = true;
            }

            return $this->jsonResponse($data);

        } catch (\Exception $e) {
            Log::error('Processor graph error', [
                'device_id' => $deviceId,
                'processor_id' => $processorId,
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
     * @param int|null $processorId
     * @return \Illuminate\View\View
     */
    public function view(Request $request, int $deviceId, ?int $processorId = null)
    {
        $device = Device::findOrFail($deviceId);
        
        return view('graphs.processor.usage', [
            'device' => $device,
            'deviceId' => $deviceId,
            'processorId' => $processorId,
            'from' => $this->getTimeRange($request)
        ]);
    }
}

