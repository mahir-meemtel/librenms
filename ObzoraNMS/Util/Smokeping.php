<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use Illuminate\Support\Str;

class Smokeping
{
    private $device;
    private $files;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public static function make(Device $device)
    {
        return new static($device);
    }

    public function getFiles()
    {
        if (is_null($this->files) && ObzoraConfig::has('smokeping.dir')) {
            $dir = $this->generateFileName();
            if (is_dir($dir) && is_readable($dir)) {
                foreach (array_diff(scandir($dir), ['.', '..']) as $file) {
                    if (stripos($file, '.rrd') !== false) {
                        if (strpos($file, '~') !== false) {
                            [$target, $slave] = explode('~', $this->filenameToHostname($file));
                            $this->files['in'][$target][$slave] = $file;
                            $this->files['out'][$slave][$target] = $file;
                        } else {
                            $target = $this->filenameToHostname($file);
                            $this->files['in'][$target][ObzoraConfig::get('own_hostname')] = $file;
                            $this->files['out'][ObzoraConfig::get('own_hostname')][$target] = $file;
                        }
                    }
                }
            }
        }

        return $this->files;
    }

    public function findFiles()
    {
        $this->files = null;

        return $this->getFiles();
    }

    public function generateFileName($file = '')
    {
        if (ObzoraConfig::get('smokeping.integration') === true) {
            return ObzoraConfig::get('smokeping.dir') . '/' . ($this->device->type ?: 'Ungrouped') . '/' . $file;
        } else {
            return ObzoraConfig::get('smokeping.dir') . '/' . $file;
        }
    }

    public function otherGraphs($direction)
    {
        $remote = $direction == 'in' ? 'src' : 'dest';
        $data = [];
        foreach ($this->getFiles()[$direction][$this->device->hostname] as $remote_host => $file) {
            if (Str::contains($file, '~')) {
                $device = \DeviceCache::getByHostname($remote_host);
                if (empty($device->device_id)) {
                    \Log::debug('Could not find smokeping slave device in ObzoraNMS', ['slave' => $remote_host]);
                    continue;
                }

                $data[] = [
                    'device' => $device,
                    'graph' => [
                        'type' => 'smokeping_' . $direction,
                        'device' => $this->device,
                        $remote => $device->device_id,
                    ],
                ];
            }
        }

        return $data;
    }

    public function hasGraphs()
    {
        return $this->hasInGraph() || $this->hasOutGraph();
    }

    public function hasInGraph()
    {
        return ! empty($this->getFiles()['in'][$this->device->hostname]);
    }

    public function hasOutGraph()
    {
        return ! empty($this->getFiles()['out'][$this->device->hostname]);
    }

    private function filenameToHostname($name)
    {
        if (ObzoraConfig::get('smokeping.integration') === true) {
            $name = str_replace('_', '.', $name);
        }

        return str_replace('.rrd', '', $name);
    }
}
