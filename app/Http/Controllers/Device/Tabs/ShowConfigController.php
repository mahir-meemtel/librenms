<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Facades\DeviceCache;
use App\Facades\ObzoraConfig;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class ShowConfigController extends Controller implements DeviceTab
{
    private $rancidPath;
    private $rancidFile;

    public function visible(Device $device): bool
    {
        if (auth()->user()->can('show-config', $device)) {
            return $this->oxidizedEnabled($device) || $this->getRancidConfigFile() !== false;
        }

        return false;
    }

    public function slug(): string
    {
        return 'showconfig';
    }

    public function icon(): string
    {
        return 'fa-align-justify';
    }

    public function name(): string
    {
        return __('Config');
    }

    public function data(Device $device, Request $request): array
    {
        return [
            'rancid_path' => $this->getRancidPath(),
            'rancid_file' => $this->getRancidConfigFile(),
        ];
    }

    private function oxidizedEnabled(Device $device)
    {
        return ObzoraConfig::get('oxidized.enabled') === true
                && ObzoraConfig::has('oxidized.url')
                && $device->getAttrib('override_Oxidized_disable') !== 'true'
                && ! in_array($device->type, ObzoraConfig::get('oxidized.ignore_types', []))
                && ! in_array($device->os, ObzoraConfig::get('oxidized.ignore_os', []));
    }

    private function getRancidPath()
    {
        if (is_null($this->rancidPath)) {
            $this->rancidFile = $this->findRancidConfigFile();
        }

        return $this->rancidPath;
    }

    private function getRancidConfigFile()
    {
        if (is_null($this->rancidFile)) {
            $this->rancidFile = $this->findRancidConfigFile();
        }

        return $this->rancidFile;
    }

    private function findRancidConfigFile()
    {
        if (ObzoraConfig::has('rancid_configs') && ! is_array(ObzoraConfig::get('rancid_configs'))) {
            ObzoraConfig::set('rancid_configs', (array) ObzoraConfig::get('rancid_configs', []));
        }

        if (ObzoraConfig::has('rancid_configs.0')) {
            $device = DeviceCache::getPrimary();
            foreach (ObzoraConfig::get('rancid_configs') as $configs) {
                if ($configs[strlen($configs) - 1] != '/') {
                    $configs .= '/';
                }

                if (is_file($configs . $device['hostname'])) {
                    $this->rancidPath = $configs;

                    return $configs . $device['hostname'];
                } elseif (is_file($configs . strtok($device['hostname'], '.'))) { // Strip domain
                    $this->rancidPath = $configs;

                    return $configs . strtok($device['hostname'], '.');
                } else {
                    if (! empty(ObzoraConfig::get('mydomain'))) { // Try with domain name if set
                        if (is_file($configs . $device['hostname'] . '.' . ObzoraConfig::get('mydomain'))) {
                            $this->rancidPath = $configs;

                            return $configs . $device['hostname'] . '.' . ObzoraConfig::get('mydomain');
                        }
                    }
                }
            }
        }

        return false;
    }
}
