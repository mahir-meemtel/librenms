<?php

namespace App\Services;

use App\Facades\ObzoraConfig;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Data\Store\Rrd as RrdStore;
use Symfony\Component\Process\Process;

/**
 * Service for fetching RRD data and converting to JSON format for Chart.js
 */
class GraphDataService
{
    protected RrdStore $rrdStore;
    protected string $rrdtool;

    public function __construct(RrdStore $rrdStore)
    {
        $this->rrdStore = $rrdStore;
        $this->rrdtool = ObzoraConfig::get('rrdtool', 'rrdtool');
    }

    /**
     * Fetch time-series data from RRD file
     *
     * @param string $rrdFile Full path to RRD file
     * @param string $dataSource Data source name (e.g., 'INOCTETS', 'usage', 'used')
     * @param string $from Time range (e.g., '-24h', '-1d', '-1w')
     * @param string $cf Consolidation function (AVERAGE, MIN, MAX, LAST)
     * @return array ['labels' => [], 'datasets' => []]
     */
    public function fetchTimeSeries(string $rrdFile, string $dataSource, string $from = '-24h', string $cf = 'AVERAGE'): array
    {
        if (!file_exists($rrdFile)) {
            Log::warning('RRD file not found', ['file' => $rrdFile]);
            return ['labels' => [], 'datasets' => []];
        }

        try {
            $endTime = time();
            $startTime = $this->parseTime($from, $endTime);

            if ($startTime >= $endTime) {
                $startTime = $endTime - (24 * 3600); // Default to 24 hours ago
            }

            // Build rrdtool fetch command
            $command = sprintf(
                '%s fetch %s %s -s %d -e %d 2>&1',
                escapeshellarg($this->rrdtool),
                escapeshellarg($rrdFile),
                $cf,
                $startTime,
                $endTime
            );

            $process = Process::fromShellCommandline($command);
            $process->setTimeout(30);
            $process->run();

            if (!$process->isSuccessful()) {
                $error = $process->getErrorOutput() ?: $process->getOutput();
                Log::error('RRD fetch failed', [
                    'command' => $command,
                    'error' => $error,
                    'rrd_file' => $rrdFile
                ]);
                return ['labels' => [], 'datasets' => []];
            }

            $output = $process->getOutput();
            return $this->parseRrdFetchOutput($output, $dataSource);

        } catch (\Exception $e) {
            Log::error('Error fetching RRD data', [
                'rrd_file' => $rrdFile,
                'data_source' => $dataSource,
                'error' => $e->getMessage()
            ]);
            return ['labels' => [], 'datasets' => []];
        }
    }

    /**
     * Get last value from RRD file
     *
     * @param string $rrdFile Full path to RRD file
     * @param string $dataSource Data source name
     * @return float|null
     */
    public function getLastValue(string $rrdFile, string $dataSource): ?float
    {
        if (!file_exists($rrdFile)) {
            return null;
        }

        try {
            $command = sprintf(
                '%s lastupdate %s 2>&1',
                escapeshellarg($this->rrdtool),
                escapeshellarg($rrdFile)
            );

            $process = Process::fromShellCommandline($command);
            $process->setTimeout(10);
            $process->run();

            if (!$process->isSuccessful()) {
                return null;
            }

            $output = $process->getOutput();
            $lines = explode("\n", trim($output));
            
            // Find header line with data source names
            $headerFound = false;
            $dsIndex = 0;
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                if (!$headerFound) {
                    $parts = preg_split('/\s+/', $line);
                    if (in_array($dataSource, $parts)) {
                        $headerFound = true;
                        $dsIndex = array_search($dataSource, $parts);
                        continue;
                    }
                } else {
                    // Data line: timestamp value1 value2 ...
                    $parts = preg_split('/\s+/', $line);
                    if (isset($parts[$dsIndex + 1])) {
                        $value = filter_var($parts[$dsIndex + 1], FILTER_VALIDATE_FLOAT);
                        return $value !== false ? $value : null;
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting last RRD value', [
                'rrd_file' => $rrdFile,
                'data_source' => $dataSource,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Parse rrdtool fetch output into Chart.js format
     *
     * @param string $output rrdtool fetch output
     * @param string $dataSource Data source name to extract
     * @return array
     */
    protected function parseRrdFetchOutput(string $output, string $dataSource): array
    {
        $lines = explode("\n", trim($output));
        $labels = [];
        $values = [];
        
        $headerFound = false;
        $dsIndex = 0;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (!$headerFound) {
                $parts = preg_split('/\s+/', $line);
                if (in_array($dataSource, $parts)) {
                    $headerFound = true;
                    $dsIndex = array_search($dataSource, $parts);
                    if ($dsIndex === false) {
                        $dsIndex = 0; // Fallback to first column
                    }
                    continue;
                } elseif (count($parts) > 0 && preg_match('/^[A-Z_]+$/', $parts[0])) {
                    // Header line with uppercase DS names
                    $headerFound = true;
                    $dsIndex = array_search($dataSource, $parts);
                    if ($dsIndex === false) {
                        $dsIndex = 0;
                    }
                    continue;
                }
            }
            
            // Process data lines (format: timestamp: value1 value2 ...)
            if ($headerFound && preg_match('/^(\d+):\s+(.+)$/', $line, $matches)) {
                $timestamp = (int)$matches[1];
                $dataValues = preg_split('/\s+/', trim($matches[2]));
                
                if (isset($dataValues[$dsIndex])) {
                    $value = trim($dataValues[$dsIndex]);
                    
                    // Skip NaN values
                    if ($value === 'nan' || $value === '' || strtolower($value) === 'nan') {
                        continue;
                    }
                    
                    $floatValue = filter_var($value, FILTER_VALIDATE_FLOAT);
                    if ($floatValue !== false) {
                        $labels[] = date('Y-m-d H:i:s', $timestamp);
                        $values[] = $floatValue;
                    }
                }
            }
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $dataSource,
                    'data' => $values,
                    'borderColor' => '#FFD369',
                    'backgroundColor' => 'rgba(255, 211, 105, 0.1)',
                    'borderWidth' => 2,
                    'fill' => false
                ]
            ]
        ];
    }

    /**
     * Parse time string to timestamp
     *
     * @param string|int $time Time string (e.g., '-24h', '-1d') or timestamp
     * @param int $endTime Current timestamp to calculate relative times from
     * @return int
     */
    protected function parseTime($time, int $endTime): int
    {
        if (is_numeric($time)) {
            return (int)$time;
        }

        if (is_string($time)) {
            // Handle relative time strings like -24h, -1d, -30m
            if (preg_match('/^-(\d+)([hdmsw])$/', $time, $matches)) {
                $value = (int)$matches[1];
                $unit = $matches[2];
                
                switch ($unit) {
                    case 'h': return $endTime - ($value * 3600);
                    case 'd': return $endTime - ($value * 24 * 3600);
                    case 'm': return $endTime - ($value * 60);
                    case 's': return $endTime - $value;
                    case 'w': return $endTime - ($value * 7 * 24 * 3600);
                }
            }
            
            // Fallback to strtotime
            $parsed = strtotime($time, $endTime);
            if ($parsed !== false) {
                return $parsed;
            }
        }

        return $endTime - (24 * 3600); // Default to 24 hours ago
    }

    /**
     * Get data source name from RRD file info
     *
     * @param string $rrdFile Full path to RRD file
     * @return array Array of data source names
     */
    public function getDataSourceNames(string $rrdFile): array
    {
        if (!file_exists($rrdFile)) {
            return [];
        }

        try {
            $command = sprintf(
                '%s info %s 2>&1',
                escapeshellarg($this->rrdtool),
                escapeshellarg($rrdFile)
            );

            $process = Process::fromShellCommandline($command);
            $process->setTimeout(10);
            $process->run();

            if (!$process->isSuccessful()) {
                return [];
            }

            $info = $process->getOutput();
            $dsNames = [];
            
            if (preg_match_all('/ds\[([^\]]+)\]/', $info, $matches)) {
                $dsNames = $matches[1];
            }

            return $dsNames;
        } catch (\Exception $e) {
            Log::error('Error getting RRD data source names', [
                'rrd_file' => $rrdFile,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}

