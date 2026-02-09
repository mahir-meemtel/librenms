<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Facades\DeviceCache;
use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class LoadBalancerController implements DeviceTab
{
    private $tabs = [];

    public function __construct()
    {
        $device = DeviceCache::getPrimary();

        if ($device->os == 'netscaler') {
            if ($device->netscalerVservers()->exists()) {
                $this->tabs[] = 'netscaler_vsvr';
            }
        }

        // Cisco ACE
        if ($device->os == 'acsw') {
            if ($device->vServers()->exists()) {
                $this->tabs[] = 'loadbalancer_vservers';
            }
        }

        // F5 LTM
        if ($device->os == 'f5') {
            $component = new \ObzoraNMS\Component();
            $component_count = $component->getComponentCount($device['device_id']);

            if (isset($component_count['f5-ltm-bwc'])) {
                $this->tabs[] = 'ltm_bwc';
            }
            if (isset($component_count['f5-ltm-vs'])) {
                $this->tabs[] = 'ltm_vs';
            }
            if (isset($component_count['f5-ltm-pool'])) {
                $this->tabs[] = 'ltm_pool';
            }
            if (isset($component_count['f5-gtm-wide'])) {
                $this->tabs[] = 'gtm_wide';
            }
            if (isset($component_count['f5-gtm-pool'])) {
                $this->tabs[] = 'gtm_pool';
            }
            if (isset($component_count['f5-cert'])) {
                $this->tabs[] = 'f5-cert';
            }
        }
    }

    public function visible(Device $device): bool
    {
        return ! empty($this->tabs);
    }

    public function slug(): string
    {
        return 'loadbalancer';
    }

    public function icon(): string
    {
        return 'fa-balance-scale';
    }

    public function name(): string
    {
        return __('Load Balancer');
    }

    public function data(Device $device, Request $request): array
    {
        return [
            'loadbalancer_tabs' => $this->tabs,
        ];
    }
}
