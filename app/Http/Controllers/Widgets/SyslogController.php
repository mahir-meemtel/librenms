<?php
namespace App\Http\Controllers\Widgets;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SyslogController extends WidgetController
{
    protected string $name = 'syslog';
    protected $defaults = [
        'title' => null,
        'device' => null,
        'device_group' => null,
        'hidenavigation' => 0,
        'level' => null,
    ];

    public function getSettingsView(Request $request): View
    {
        $data = $this->getSettings(true);

        $data['device'] = Device::hasAccess($request->user())->find($data['device']);

        $data['priorities'] = app('translator')->get('syslog.severity');

        return view('widgets.settings.syslog', $data);
    }
}
