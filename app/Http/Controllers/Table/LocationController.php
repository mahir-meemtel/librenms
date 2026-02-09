<?php
namespace App\Http\Controllers\Table;

use App\Models\Device;
use App\Models\Location;

class LocationController extends TableController
{
    /**
     * Defines search fields will be searched in order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function searchFields($request)
    {
        return ['location'];
    }

    protected function sortFields($request)
    {
        return ['location', 'devices', 'down'];
    }

    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function baseQuery($request)
    {
        // joins are needed for device count sorts
        $sort = $request->get('sort');
        $key = key($sort);
        $join = $this->getJoinQuery($key);

        if ($join) {
            return Location::hasAccess($request->user())
                ->select(['id', 'location', 'lat', 'lng', \DB::raw("COUNT(device_id) AS `$key`")])
                ->leftJoin('devices', $join)
                ->groupBy(['id', 'location', 'lat', 'lng']);
        }

        return Location::hasAccess($request->user());
    }

    /**
     * @param  Location  $location
     * @return array|\Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     */
    public function formatItem($location)
    {
        return [
            'id' => $location->id,
            'location' => htmlspecialchars($location->location),
            'lat' => $location->lat,
            'lng' => $location->lng,
            'down' => $location->devices()->isDown()->count(),
            'devices' => $location->devices()->count(),
        ];
    }

    private function getJoinQuery($field)
    {
        switch ($field) {
            case 'devices':
                return function ($query) {
                    $query->on('devices.location_id', 'locations.id');
                };
            case 'down':
                return function ($query) {
                    $query->on('devices.location_id', 'locations.id');
                    (new Device)->scopeIsDown($query);
                };
            default:
                return null;
        }
    }
}
