<?php
namespace App\Http\Controllers\Widgets;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServerStatsController extends WidgetController
{
    protected string $name = 'server-stats';
    protected $defaults = [
        'title' => null,
        'columnsize' => 3,
        'device' => null,
        'cpu' => 0,
        'mempools' => [],
        'disks' => [],
        'template' => 1,
    ];

    public function getTitle(): string
    {
        $settings = $this->getSettings();
        if ($settings['title']) {
            return $settings['title'];
        }

        $device = Device::hasAccess(request()->user())->find($settings['device']);
        if ($device) {
            return $device->displayName() . ' Stats';
        }

        return parent::getTitle();
    }

    public function getView(Request $request): string|View
    {
        $data = $this->getSettings();

        if (is_null($data['device'])) {
            return $this->getSettingsView($request);
        }

        $device = Device::hasAccess($request->user())->find($data['device']);
        if ($device) {
            $data['cpu'] = $device->processors()->avg('processor_usage');
            $data['mempools'] = $device->mempools()->select(\DB::raw('mempool_descr, ROUND(mempool_used / (1024*1024), 0) as used, ROUND(mempool_total /(1024*1024), 0) as total'))->get();
            $data['disks'] = $device->storage()->select(\DB::raw('storage_descr, ROUND(storage_used / (1024*1024), 0) as used, ROUND(storage_size / (1024*1024), 0) as total'))->get();
        }

        return view('widgets.server-stats', $data);
    }

    public function getSettingsView(Request $request): View
    {
        $settings = $this->getSettings(true);
        $settings['device'] = Device::hasAccess($request->user())->find($settings['device']) ?: null;

        return view('widgets.settings.server-stats', $settings);
    }

    public function getSettings($settingsView = false): array
    {
        $settings = parent::getSettings($settingsView);
        $settings['columns'] = 12 / $settings['columnsize'];

        return $settings;
    }
}
