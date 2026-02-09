<?php
namespace App\Http\Controllers\Widgets;

use Illuminate\Http\Request;
use Illuminate\View\View;

class NotesController extends WidgetController
{
    protected string $name = 'notes';
    protected $defaults = [
        'title' => null,
        'notes' => null,
    ];

    public function getView(Request $request): string|View
    {
        $settings = $this->getSettings();

        if (is_null($settings['notes'])) {
            return $this->getSettingsView($request);
        }

        $purifier_config = [
            'HTML.Allowed' => 'b,iframe[frameborder|src|width|height],i,ul,ol,li,h1,h2,h3,h4,br,p,pre',
            'HTML.Trusted' => true,
            'HTML.SafeIframe' => true,
            'URI.SafeIframeRegexp' => '%^(https?:)?//%',
        ];
        $output = \ObzoraNMS\Util\Clean::html(nl2br($settings['notes']), $purifier_config);

        return $output;
    }
}
