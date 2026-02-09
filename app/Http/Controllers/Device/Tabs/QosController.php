<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class QosController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return $device->qos()->whereNull('port_id')->whereNull('parent_id')->count() > 0;
    }

    public function slug(): string
    {
        return 'qos';
    }

    public function icon(): string
    {
        return 'fa-code-fork';
    }

    public function name(): string
    {
        return __('QoS');
    }

    public function data(Device $device, Request $request): array
    {
        $show = null;
        $vars = $request->vars;
        if ($vars) {
            $showvars = array_filter(explode('/', $vars), function ($v) {
                return str_starts_with($v, 'show=');
            });
            if ($showvars) {
                $showvar = explode('=', $showvars[0]);
                $show = array_pop($showvar);
                if ($show) {
                    $show = intval($show);
                }
            }
        }

        return [
            'show' => $show,
        ];
    }
}
