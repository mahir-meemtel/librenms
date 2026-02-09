<?php
namespace App\Http\Controllers\Select;

use App\Models\ServiceTemplate;

class ServiceTemplateController extends SelectController
{
    protected function searchFields($request)
    {
        return ['name'];
    }

    protected function baseQuery($request)
    {
        return ServiceTemplate::hasAccess($request->user())->select('id', 'name');
    }

    /**
     * @param  ServiceTemplate  $template
     */
    public function formatItem($template)
    {
        return [
            'id' => $template->id,
            'text' => $template->name,
        ];
    }
}
