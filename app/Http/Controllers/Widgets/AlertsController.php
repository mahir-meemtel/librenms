<?php
namespace App\Http\Controllers\Widgets;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertsController extends WidgetController
{
    protected string $name = 'alerts';
    protected $defaults = [
        'title' => null,
        'device' => null,
        'acknowledged' => null,
        'fired' => null,
        'min_severity' => null,
        'state' => null,
        'device_group' => null,
        'proc' => 0,
        'location' => 1,
        'sort' => 1,
        'hidenavigation' => 0,
        'uncollapse_key_count' => 1,
        'unreachable' => null,
    ];

    public function getSettingsView(Request $request): View
    {
        $data = $this->getSettings(true);
        $data['severities'] = [
            // alert_rules.status is enum('ok','warning','critical')
            'ok' => 1,
            'warning' => 2,
            'critical' => 3,
            'ok only' => 4,
            'warning only' => 5,
            'critical only' => 6,
        ];
        $data['states'] = [
            // divined from obzora/alerts.php
            'recovered' => '0',
            'alerted' => '1',
            'acknowledged' => '2',
            'worse' => '3',
            'better' => '4',
            'changed' => '5',
        ];

        return view('widgets.settings.alerts', $data);
    }
}
