<?php
namespace App\Http\Controllers\Widgets;

use App\Facades\ObzoraConfig;
use App\Models\CustomMap;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomMapController extends WidgetController
{
    protected string $name = 'custom-map';
    protected $defaults = [
        'title' => null,
        'custom_map' => null,
        'screenshot' => false,
    ];

    public function __construct()
    {
        $this->authorizeResource(CustomMap::class, 'map');
    }

    public function getView(Request $request): string|View
    {
        $data = $this->getSettings();

        $data['map'] = CustomMap::find($data['custom_map']);
        if (! $data['map']) {
            return __('map.custom.widget.not_found');
        }
        $data['base_url'] = ObzoraConfig::get('base_url');
        $data['background_config'] = $data['map']->getBackgroundConfig();
        $data['map_conf'] = $data['map']->options;

        $scalex = (float) $request->dimensions['x'] / (float) $data['map']->width;
        $scaley = (float) $request->dimensions['y'] / (float) $data['map']->height;
        $data['scale'] = min($scalex, $scaley);

        return view('widgets.custom-map', $data);
    }

    public function getSettingsView(Request $request): View
    {
        $data = $this->getSettings(true);
        $data['map'] = CustomMap::find($data['custom_map']);

        return view('widgets.settings.custom-map', $data);
    }
}
