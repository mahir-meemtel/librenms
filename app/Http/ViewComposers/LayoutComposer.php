<?php
namespace App\Http\ViewComposers;

use App\Checks;
use App\Facades\ObzoraConfig;
use App\Models\UserPref;
use Illuminate\View\View;

class LayoutComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // build page title
        if ($view->getFactory()->hasSection('title')) {
            // short sections escape the html entities, reverse that
            $title = html_entity_decode(trim($view->getFactory()->getSection('title')), ENT_QUOTES);
            $title = str_replace('    ', ' : ', $title);
            $title .= ' | ' . ObzoraConfig::get('page_title_suffix');
        } else {
            $title = ObzoraConfig::get('page_title_suffix');
        }

        Checks::postAuth();

        $show_menu = auth()->check();
        if ($show_menu && ObzoraConfig::get('twofactor') && ! session('twofactor')) {
            $show_menu = empty(UserPref::getPref(auth()->user(), 'twofactor'));
        }

        $view->with('pagetitle', $title)
            ->with('show_menu', $show_menu);
    }
}
