<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;
use ObzoraNMS\Util\Smokeping;

class LatencyController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        return ObzoraConfig::get('smokeping.integration') || $device->getAttrib('override_icmp_disable') !== 'true';
    }

    public function slug(): string
    {
        return 'latency';
    }

    public function icon(): string
    {
        return 'fa-line-chart';
    }

    public function name(): string
    {
        return __('Latency');
    }

    public function data(Device $device, Request $request): array
    {
        $from = $request->get('dtpickerfrom', Carbon::now(session('preferences.timezone'))->subDays(2)->format(ObzoraConfig::get('dateformat.byminute')));
        $to = $request->get('dtpickerto', Carbon::now(session('preferences.timezone'))->format(ObzoraConfig::get('dateformat.byminute')));

        $smokeping = new Smokeping($device);
        $smokeping_tabs = [];
        if ($smokeping->hasInGraph()) {
            $smokeping_tabs[] = 'in';
        }
        if ($smokeping->hasOutGraph()) {
            $smokeping_tabs[] = 'out';
        }

        return [
            'from' => $from,
            'to' => $to,
            'smokeping' => $smokeping,
            'smokeping_tabs' => $smokeping_tabs,
        ];
    }
}
