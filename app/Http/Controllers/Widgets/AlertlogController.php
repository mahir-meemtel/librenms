<?php
namespace App\Http\Controllers\Widgets;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertlogController extends WidgetController
{
    protected string $name = 'alertlog';
    protected $defaults = [
        'title' => null,
        'device_id' => '',
        'device_group' => null,
        'state' => -1,
        'min_severity' => null,
        'hidenavigation' => 0,
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

        return view('widgets.settings.alertlog', $data);
    }
}
