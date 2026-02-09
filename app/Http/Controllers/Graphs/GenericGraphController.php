<?php

namespace App\Http\Controllers\Graphs;

use App\Models\Device;
use App\Models\Port;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Data\Store\Rrd as RrdStore;
use ObzoraNMS\Util\Graph;

/**
 * Generic graph controller that handles any graph type
 * by reading RRD files directly based on graph type and parameters
 */
class GenericGraphController extends BaseGraphController
{
    /**
     * Get graph data for any graph type
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $type = $request->get('type', '');
            $vars = $request->except(['type', 'from', 'to']);
            $from = $this->getTimeRange($request);
            
            Log::debug('GenericGraphController::data', [
                'type' => $type,
                'vars' => $vars,
                'from' => $from
            ]);
            
            // Get RRD file path based on graph type
            $rrdFile = $this->getRrdFilePath($type, $vars);
            
            if (!$rrdFile || !file_exists($rrdFile)) {
                return $this->errorResponse("RRD file not found for graph type: $type");
            }
            
            // Get data source names from RRD file
            $dsNames = $this->graphDataService->getDataSourceNames($rrdFile);
            
            if (empty($dsNames)) {
                return $this->errorResponse("No data sources found in RRD file: " . basename($rrdFile));
            }
            
            // Fetch data for all data sources
            $datasets = [];
            $labels = [];
            
            foreach ($dsNames as $dsName) {
                $rrdData = $this->graphDataService->fetchTimeSeries($rrdFile, $dsName, $from);
                
                if (!empty($rrdData['labels']) && !empty($rrdData['datasets'])) {
                    // Use labels from first data source
                    if (empty($labels)) {
                        $labels = $rrdData['labels'];
                    }
                    
                    // Add dataset with appropriate color
                    $color = $this->getColorForDataSource($dsName, count($datasets));
                    $datasets[] = [
                        'label' => ucfirst(str_replace('_', ' ', $dsName)),
                        'data' => $rrdData['datasets'][0]['data'] ?? [],
                        'borderColor' => $color['border'],
                        'backgroundColor' => $color['background'],
                        'borderWidth' => 2,
                        'fill' => $this->shouldFill($dsName),
                        'tension' => 0.4,
                        'pointRadius' => 0,
                    ];
                }
            }
            
            if (empty($datasets)) {
                return $this->errorResponse("No data found in RRD file: " . basename($rrdFile));
            }
            
            return $this->jsonResponse([
                'labels' => $labels,
                'datasets' => $datasets
            ]);
            
        } catch (\Exception $e) {
            Log::error('GenericGraphController error', [
                'type' => $request->get('type'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * Get RRD file path based on graph type and variables
     * 
     * @param string $type Graph type (e.g., 'port_bits', 'device_processor')
     * @param array $vars Graph variables (device, id, etc.)
     * @return string|null RRD file path or null if cannot determine
     */
    protected function getRrdFilePath(string $type, array $vars): ?string
    {
        try {
            // Port graphs
            if (strpos($type, 'port_') === 0 && isset($vars['id'])) {
                $port = Port::find($vars['id']);
                if ($port && $port->device) {
                    return $this->rrdStore->name($port->device->hostname, ['port', $port->port_id]);
                }
            }
            
            // Device graphs
            if (isset($vars['device'])) {
                $device = is_numeric($vars['device']) 
                    ? Device::find($vars['device']) 
                    : Device::where('hostname', $vars['device'])->first();
                    
                if (!$device) {
                    return null;
                }
                
                // Device processor
                if ($type === 'device_processor' || $type === 'processor') {
                    $processor = \App\Models\Processor::where('device_id', $device->device_id)->first();
                    if ($processor) {
                        return $this->rrdStore->name($device->hostname, [
                            'processor',
                            $processor->processor_type,
                            $processor->processor_index
                        ]);
                    }
                }
                
                // Device mempool/memory
                if ($type === 'device_mempool' || $type === 'device_memory' || $type === 'mempool') {
                    $mempool = \App\Models\Mempool::where('device_id', $device->device_id)
                        ->where('mempool_class', 'system')
                        ->first();
                    
                    if (!$mempool) {
                        $mempool = \App\Models\Mempool::where('device_id', $device->device_id)->first();
                    }
                    
                    if ($mempool) {
                        return $this->rrdStore->name($device->hostname, [
                            'mempool',
                            $mempool->mempool_type,
                            $mempool->mempool_class,
                            $mempool->mempool_index
                        ]);
                    }
                }
                
                // Device uptime
                if ($type === 'device_uptime' || $type === 'uptime') {
                    return $this->rrdStore->name($device->hostname, ['uptime']);
                }
                
                // Generic device graph - try to use graph type as RRD name
                $rrdName = str_replace('device_', '', $type);
                $rrdFile = $this->rrdStore->name($device->hostname, [$rrdName]);
                if (file_exists($rrdFile)) {
                    return $rrdFile;
                }
            }
            
            // Try to use Graph utility to get RRD filename (legacy method)
            try {
                $graphVars = array_merge($vars, ['type' => $type]);
                $graph = Graph::get($graphVars);
                // Graph::get doesn't directly expose RRD file, but we can try another approach
            } catch (\Exception $e) {
                // Ignore
            }
            
            return null;
        } catch (\Exception $e) {
            Log::warning('Error determining RRD file path', [
                'type' => $type,
                'vars' => $vars,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Get color for data source based on name and index
     * 
     * @param string $dsName Data source name
     * @param int $index Dataset index
     * @return array Color array with 'border' and 'background' keys
     */
    protected function getColorForDataSource(string $dsName, int $index): array
    {
        $colors = [
            ['border' => '#FFD369', 'background' => 'rgba(255, 211, 105, 0.1)'], // Primary
            ['border' => '#4CAF50', 'background' => 'rgba(76, 175, 80, 0.1)'],   // Success
            ['border' => '#FF4C4C', 'background' => 'rgba(255, 76, 76, 0.1)'],   // Danger
            ['border' => '#FFC107', 'background' => 'rgba(255, 193, 7, 0.1)'],   // Warning
            ['border' => '#2F2F2F', 'background' => 'rgba(47, 47, 47, 0.1)'],    // Secondary
            ['border' => '#2196F3', 'background' => 'rgba(33, 150, 243, 0.1)'],   // Info
            ['border' => '#9C27B0', 'background' => 'rgba(156, 39, 176, 0.1)'],   // Purple
            ['border' => '#00BCD4', 'background' => 'rgba(0, 188, 212, 0.1)'],   // Cyan
        ];
        
        // Special colors for common data source names
        $dsNameLower = strtolower($dsName);
        if (strpos($dsNameLower, 'in') !== false || strpos($dsNameLower, 'input') !== false) {
            return $colors[0]; // Primary for input
        }
        if (strpos($dsNameLower, 'out') !== false || strpos($dsNameLower, 'output') !== false) {
            return $colors[1]; // Success for output
        }
        if (strpos($dsNameLower, 'error') !== false) {
            return $colors[2]; // Danger for errors
        }
        if (strpos($dsNameLower, 'used') !== false) {
            return $colors[1]; // Success for used
        }
        if (strpos($dsNameLower, 'free') !== false) {
            return $colors[4]; // Secondary for free
        }
        
        // Default: cycle through colors
        return $colors[$index % count($colors)];
    }
    
    /**
     * Determine if dataset should be filled
     * 
     * @param string $dsName Data source name
     * @return bool
     */
    protected function shouldFill(string $dsName): bool
    {
        $dsNameLower = strtolower($dsName);
        // Fill for usage, utilization, percentage metrics
        return strpos($dsNameLower, 'usage') !== false 
            || strpos($dsNameLower, 'util') !== false
            || strpos($dsNameLower, 'perc') !== false
            || strpos($dsNameLower, 'used') !== false;
    }
}

