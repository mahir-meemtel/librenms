<?php
namespace App\Http\Controllers\Select;

use App\Models\Port;

class PortFieldController extends SelectController
{
    /**
     * Defines validation rules (will override base validation rules for select2 responses too)
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'field' => 'required|in:ifType',
            'device' => 'nullable|int',
        ];
    }

    /**
     * Defines fields that can be used as filters
     *
     * @param  $request
     * @return string[]
     */
    protected function filterFields($request)
    {
        return [
            'device_id' => 'device',
        ];
    }

    /**
     * Defines search fields will be searched in order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function searchFields($request)
    {
        return [$request->get('field')];
    }

    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function baseQuery($request)
    {
        return Port::hasAccess($request->user())
            ->select($request->get('field'))->distinct();
    }
}
