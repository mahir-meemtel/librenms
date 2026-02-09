<?php
namespace App\Http\Controllers\Widgets;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GraylogController extends WidgetController
{
    protected string $name = 'graylog';
    protected $defaults = [
        'title' => null,
        'stream' => null,
        'device' => null,
        'range' => null,
        'limit' => 15,
        'loglevel' => null,
        'hidenavigation' => 0,
    ];

    public function getSettingsView(Request $request): View
    {
        $data = $this->getSettings(true);

        if ($data['device']) {
            $data['device'] = Device::find($data['device']);
        }

        return view('widgets.settings.graylog', $data);
    }
}
