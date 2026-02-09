<?php
namespace App\Http\Controllers\Widgets;

use App\Models\Device;
use Illuminate\Http\Request;

class EventlogController extends WidgetController
{
    protected string $name = 'eventlog';
    protected $defaults = [
        'title' => null,
        'device' => null,
        'device_group' => null,
        'eventtype' => null,
        'hidenavigation' => 0,
    ];

    public function getSettingsView(Request $request): \Illuminate\View\View
    {
        $data = $this->getSettings(true);

        $data['device'] = Device::hasAccess($request->user())->find($data['device']);

        return view('widgets.settings.eventlog', $data);
    }
}
