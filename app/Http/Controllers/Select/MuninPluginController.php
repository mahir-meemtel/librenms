<?php
namespace App\Http\Controllers\Select;

use App\Models\MuninPlugin;

class MuninPluginController extends SelectController
{
    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function baseQuery($request)
    {
        return MuninPlugin::hasAccess($request->user())
            ->with(['device' => function ($query) {
                $query->select('device_id', 'hostname', 'sysName', 'display');
            }])
            ->select('mplug_id', 'mplug_type', 'device_id');
    }

    /**
     * @param  MuninPlugin  $munin_plugin
     */
    public function formatItem($munin_plugin)
    {
        return [
            'id' => $munin_plugin->mplug_id,
            'text' => $munin_plugin->device->shortDisplayName() . ' - ' . $munin_plugin->mplug_type,
        ];
    }
}
