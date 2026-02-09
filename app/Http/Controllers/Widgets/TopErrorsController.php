<?php
namespace App\Http\Controllers\Widgets;

use App\Models\Port;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopErrorsController extends WidgetController
{
    protected string $name = 'top-errors';
    protected $defaults = [
        'interface_count' => 5,
        'time_interval' => 15,
        'interface_filter' => null,
        'device_group' => null,
        'port_group' => null,
    ];

    public function getView(Request $request): string|View
    {
        $data = $this->getSettings();

        $query = Port::hasAccess($request->user())->with(['device' => function ($query) {
            $query->select('device_id', 'hostname', 'sysName', 'display', 'status', 'os');
        }])
            ->isValid()
            ->select(['port_id', 'device_id', 'ifName', 'ifDescr', 'ifAlias'])
            ->groupBy('port_id', 'device_id', 'ifName', 'ifDescr', 'ifAlias')
            ->where('poll_time', '>', Carbon::now()->subMinutes($data['time_interval'])->timestamp)
            ->where(function ($query) {
                return $query
                    ->where('ifInErrors_rate', '>', 0)
                    ->orWhere('ifOutErrors_rate', '>', 0);
            })
            ->isUp()
            ->when($data['device_group'], function ($query) use ($data) {
                return $query->inDeviceGroup($data['device_group']);
            }, function ($query) {
                return $query->has('device');
            })
            ->when($data['port_group'], function ($query) use ($data) {
                return $query->inPortGroup($data['port_group']);
            })
            ->orderByRaw('SUM(LEAST(ifInErrors_rate, 9223372036854775807) + LEAST(ifOutErrors_rate, 9223372036854775807)) DESC')
            ->limit($data['interface_count']);

        if ($data['interface_filter']) {
            $query->where('ifType', '=', $data['interface_filter']);
        }

        $data['ports'] = $query->get();

        return view('widgets.top-errors', $data);
    }
}
