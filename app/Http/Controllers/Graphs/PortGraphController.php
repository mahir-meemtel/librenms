<?php

namespace App\Http\Controllers\Graphs;

use App\Models\Port;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller for port-level graphs (bits, errors, packets)
 */
class PortGraphController extends BaseGraphController
{
    /**
     * Get port traffic (bits) graph data
     *
     * @param Request $request
     * @param int $portId
     * @return JsonResponse
     */
    public function bits(Request $request, int $portId): JsonResponse
    {
        try {
            $port = Port::findOrFail($portId);
            $from = $this->getTimeRange($request);

            // Get RRD file path using RrdStore
            $rrdName = $this->rrdStore->portName($port->port_id);
            $rrdFile = $this->rrdStore->name($port->device->hostname, $rrdName);

            if (!file_exists($rrdFile)) {
                return $this->errorResponse("RRD file not found: " . basename($rrdFile));
            }

            // Get data source names
            $dsNames = $this->graphDataService->getDataSourceNames($rrdFile);
            
            // Prefer INOCTETS and OUTOCTETS for traffic graphs
            $inDs = in_array('INOCTETS', $dsNames) ? 'INOCTETS' : null;
            $outDs = in_array('OUTOCTETS', $dsNames) ? 'OUTOCTETS' : null;

            if (!$inDs || !$outDs) {
                // Fallback to first two data sources
                $inDs = $dsNames[0] ?? 'value';
                $outDs = $dsNames[1] ?? 'value';
            }

            // Fetch time-series data for both directions
            $inData = $this->graphDataService->fetchTimeSeries($rrdFile, $inDs, $from);
            $outData = $this->graphDataService->fetchTimeSeries($rrdFile, $outDs, $from);

            // Convert octets to bits (multiply by 8)
            $inValues = array_map(function($v) { return $v * 8; }, $inData['datasets'][0]['data'] ?? []);
            $outValues = array_map(function($v) { return $v * 8; }, $outData['datasets'][0]['data'] ?? []);

            // Combine into multi-dataset format
            $data = [
                'labels' => $inData['labels'] ?? [],
                'datasets' => [
                    [
                        'label' => 'Inbound (bps)',
                        'data' => $inValues,
                        'borderColor' => '#4CAF50',
                        'backgroundColor' => 'rgba(76, 175, 80, 0.1)',
                        'borderWidth' => 2,
                        'fill' => true
                    ],
                    [
                        'label' => 'Outbound (bps)',
                        'data' => $outValues,
                        'borderColor' => '#FFD369',
                        'backgroundColor' => 'rgba(255, 211, 105, 0.1)',
                        'borderWidth' => 2,
                        'fill' => true
                    ]
                ]
            ];

            return $this->jsonResponse($data);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse("Port with ID $portId not found");
        } catch (\Exception $e) {
            Log::error('Port bits graph error', [
                'port_id' => $portId,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get port errors graph data
     *
     * @param Request $request
     * @param int $portId
     * @return JsonResponse
     */
    public function errors(Request $request, int $portId): JsonResponse
    {
        try {
            $port = Port::findOrFail($portId);
            $from = $this->getTimeRange($request);

            // Get RRD file path
            $rrdName = $this->rrdStore->portName($port->port_id);
            $rrdFile = $this->rrdStore->name($port->device->hostname, $rrdName);

            if (!file_exists($rrdFile)) {
                return $this->errorResponse("RRD file not found: " . basename($rrdFile));
            }

            // Get data source names
            $dsNames = $this->graphDataService->getDataSourceNames($rrdFile);
            
            // Prefer INERRORS and OUTERRORS
            $inDs = in_array('INERRORS', $dsNames) ? 'INERRORS' : null;
            $outDs = in_array('OUTERRORS', $dsNames) ? 'OUTERRORS' : null;

            if (!$inDs || !$outDs) {
                $inDs = $dsNames[0] ?? 'value';
                $outDs = $dsNames[1] ?? 'value';
            }

            // Fetch time-series data
            $inData = $this->graphDataService->fetchTimeSeries($rrdFile, $inDs, $from);
            $outData = $this->graphDataService->fetchTimeSeries($rrdFile, $outDs, $from);

            // Combine into multi-dataset format
            $data = [
                'labels' => $inData['labels'] ?? [],
                'datasets' => [
                    [
                        'label' => 'Inbound Errors',
                        'data' => $inData['datasets'][0]['data'] ?? [],
                        'borderColor' => '#FF4C4C',
                        'backgroundColor' => 'rgba(255, 76, 76, 0.1)',
                        'borderWidth' => 2,
                        'fill' => true
                    ],
                    [
                        'label' => 'Outbound Errors',
                        'data' => $outData['datasets'][0]['data'] ?? [],
                        'borderColor' => '#FFC107',
                        'backgroundColor' => 'rgba(255, 193, 7, 0.1)',
                        'borderWidth' => 2,
                        'fill' => true
                    ]
                ]
            ];

            return $this->jsonResponse($data);

        } catch (\Exception $e) {
            Log::error('Port errors graph error', [
                'port_id' => $portId,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get port packets graph data
     *
     * @param Request $request
     * @param int $portId
     * @return JsonResponse
     */
    public function packets(Request $request, int $portId): JsonResponse
    {
        try {
            $port = Port::findOrFail($portId);
            $from = $this->getTimeRange($request);

            // Get RRD file path
            $rrdName = $this->rrdStore->portName($port->port_id);
            $rrdFile = $this->rrdStore->name($port->device->hostname, $rrdName);

            if (!file_exists($rrdFile)) {
                return $this->errorResponse("RRD file not found: " . basename($rrdFile));
            }

            // Get data source names
            $dsNames = $this->graphDataService->getDataSourceNames($rrdFile);
            
            // Prefer INUCASTPKTS and OUTUCASTPKTS
            $inDs = in_array('INUCASTPKTS', $dsNames) ? 'INUCASTPKTS' : null;
            $outDs = in_array('OUTUCASTPKTS', $dsNames) ? 'OUTUCASTPKTS' : null;

            if (!$inDs || !$outDs) {
                $inDs = $dsNames[0] ?? 'value';
                $outDs = $dsNames[1] ?? 'value';
            }

            // Fetch time-series data
            $inData = $this->graphDataService->fetchTimeSeries($rrdFile, $inDs, $from);
            $outData = $this->graphDataService->fetchTimeSeries($rrdFile, $outDs, $from);

            // Combine into multi-dataset format
            $data = [
                'labels' => $inData['labels'] ?? [],
                'datasets' => [
                    [
                        'label' => 'Inbound Packets',
                        'data' => $inData['datasets'][0]['data'] ?? [],
                        'borderColor' => '#4CAF50',
                        'backgroundColor' => 'rgba(76, 175, 80, 0.1)',
                        'borderWidth' => 2,
                        'fill' => true
                    ],
                    [
                        'label' => 'Outbound Packets',
                        'data' => $outData['datasets'][0]['data'] ?? [],
                        'borderColor' => '#2F2F2F',
                        'backgroundColor' => 'rgba(47, 47, 47, 0.1)',
                        'borderWidth' => 2,
                        'fill' => true
                    ]
                ]
            ];

            return $this->jsonResponse($data);

        } catch (\Exception $e) {
            Log::error('Port packets graph error', [
                'port_id' => $portId,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display graph view
     *
     * @param Request $request
     * @param int $portId
     * @param string $metric
     * @return \Illuminate\View\View
     */
    public function view(Request $request, int $portId, string $metric)
    {
        $port = Port::with('device')->findOrFail($portId);
        
        return view('graphs.port.' . $metric, [
            'port' => $port,
            'portId' => $portId,
            'metric' => $metric,
            'from' => $this->getTimeRange($request)
        ]);
    }
}

