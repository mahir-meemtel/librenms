<?php
namespace App\Http\Controllers\Select;

use App\Models\Location;

class LocationController extends SelectController
{
    protected function searchFields($request)
    {
        return ['location'];
    }

    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function baseQuery($request)
    {
        return Location::hasAccess($request->user())
            ->orderBy('location')
            ->select('id', 'location');
    }
}
