<?php
namespace App\Http\Controllers\Select;

use App\Models\Port;

class PortController extends SelectController
{
    /**
     * Defines validation rules (will override base validation rules for select2 responses too)
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'device' => 'nullable|int',
            'devices' => 'nullable|array',
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
        return (array) $request->get('field', ['ifAlias', 'ifName', 'ifDescr', 'devices.hostname', 'devices.sysName']);
    }

    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function baseQuery($request)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Port::hasAccess($request->user())
            ->isNotDeleted()
            ->has('device')
            ->with(['device' => function ($query) {
                $query->select('device_id', 'hostname', 'sysName', 'display');
            }])
            ->select('ports.device_id', 'port_id', 'ifAlias', 'ifName', 'ifDescr')
            ->groupBy(['ports.device_id', 'port_id', 'ifAlias', 'ifName', 'ifDescr']);

        if ($request->get('term')) {
            // join with devices for searches
            $query->leftJoin('devices', 'devices.device_id', 'ports.device_id');
        }

        if ($device_id = $request->get('device')) {
            $query->where('ports.device_id', $device_id);
        }

        if ($device_ids = $request->get('devices')) {
            $query->whereIn('ports.device_id', $device_ids);
        }

        return $query;
    }

    public function formatItem($port)
    {
        /** @var Port $port */
        $label = $port->getShortLabel();
        $description = ($label == $port->ifAlias ? '' : ' - ' . $port->ifAlias);

        return [
            'id' => $port->port_id,
            'text' => $label . ' - ' . $port->device->shortDisplayName() . $description,
            'device_id' => $port->device_id,
        ];
    }
}
