<?php
namespace App\Http\Controllers\Select;

use App\Models\Service;

class ServiceController extends SelectController
{
    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function baseQuery($request)
    {
        return Service::hasAccess($request->user())
            ->with(['device' => function ($query) {
                $query->select('device_id', 'hostname', 'sysName', 'display');
            }])
            ->select('service_id', 'service_type', 'service_desc', 'device_id');
    }

    /**
     * @param  Service  $service
     */
    public function formatItem($service)
    {
        return [
            'id' => $service->service_id,
            'text' => $service->device->shortDisplayName() . ' - ' . $service->service_type . ' (' . $service->service_desc . ')',
        ];
    }
}
