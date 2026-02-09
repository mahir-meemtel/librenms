<?php
namespace App\Http\Controllers\Widgets;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImageController extends WidgetController
{
    protected string $name = 'generic-image';
    protected $defaults = [
        'title' => null,
        'image_url' => null,
        'target_url' => null,
    ];

    public function getView(Request $request): string|View
    {
        $data = $this->getSettings();

        if (is_null($data['image_url'])) {
            return $this->getSettingsView($request);
        }

        $dimensions = $request->get('dimensions');
        $data['dimensions'] = $dimensions;

        // send size so generated images can generate the proper size
        $data['image_url'] = str_replace(['@AUTO_HEIGHT@', '@AUTO_WIDTH@'], [$dimensions['y'], $dimensions['x']], $data['image_url']);

        // bust cache
        if (Str::contains($data['image_url'], '?')) {
            $data['image_url'] .= '&' . mt_rand();
        } else {
            $data['image_url'] .= '?' . mt_rand();
        }

        return view('widgets.generic-image', $data);
    }

    public function getSettings($settingsView = false): array
    {
        if (is_null($this->settings)) {
            parent::getSettings();
            if (! empty($this->settings['image_title'])) {
                $this->settings['title'] = $this->settings['image_title'];
            }
        }

        return $this->settings;
    }
}
