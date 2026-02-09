<?php
namespace App\Http\Controllers\Select;

use App\Models\PortGroup;

class PortGroupController extends SelectController
{
    protected function searchFields($request)
    {
        return ['name'];
    }

    protected function baseQuery($request)
    {
        return PortGroup::hasAccess($request->user())->select(['id', 'name']);
    }

    /**
     * @param  PortGroup  $port_group
     */
    public function formatItem($port_group)
    {
        return [
            'id' => $port_group->id,
            'text' => $port_group->name,
        ];
    }
}
