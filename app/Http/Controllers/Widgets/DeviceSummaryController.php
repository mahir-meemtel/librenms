<?php
namespace App\Http\Controllers\Widgets;

use App\Facades\ObzoraConfig;
use Illuminate\Http\Request;
use Illuminate\View\View;
use ObzoraNMS\Util\ObjectCache;

abstract class DeviceSummaryController extends WidgetController
{
    protected string $name = 'device-summary';

    public function __construct()
    {
        // init defaults we need to check config, so do it in construct
        $this->defaults = [
            'show_services' => (int) ObzoraConfig::get('show_services', 1),
            'show_sensors' => (int) ObzoraConfig::get('show_sensors', 1),
            'summary_errors' => (int) ObzoraConfig::get('summary_errors', 0),
        ];
    }

    public function getView(Request $request): string|View
    {
        return view("widgets.$this->name", $this->getData($request));
    }

    protected function getData(Request $request)
    {
        $data = $this->getSettings();

        $data['devices'] = ObjectCache::deviceCounts(['total', 'up', 'down', 'ignored', 'disabled', 'disable_notify']);

        $data['ports'] = $data['summary_errors'] ?
            ObjectCache::portCounts(['total', 'up', 'down', 'ignored', 'shutdown', 'errored']) :
            ObjectCache::portCounts(['total', 'up', 'down', 'ignored', 'shutdown']);

        if ($data['show_services']) {
            $data['services'] = ObjectCache::serviceCounts(['total', 'ok', 'critical', 'ignored', 'disabled']);
        }

        if ($data['show_sensors']) {
            $data['sensors'] = ObjectCache::sensorCounts(['total', 'ok', 'critical', 'disable_notify']);
        }

        return $data;
    }
}
