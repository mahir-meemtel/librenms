<?php
namespace App\Http\Controllers\Select;

use App\Models\Application;

class ApplicationController extends SelectController
{
    protected function rules()
    {
        return [
            'type' => 'nullable|string',
        ];
    }

    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function baseQuery($request)
    {
        $query = Application::hasAccess($request->user())->with(['device' => function ($query) {
            $query->select('device_id', 'hostname', 'sysName', 'display');
        }]);

        if ($type = $request->get('type')) {
            $query->where('app_type', $type);
        }

        return $query;
    }

    /**
     * @param  Application  $app
     */
    public function formatItem($app)
    {
        return [
            'id' => $app->app_id,
            'text' => $app->displayName() . ' - ' . $app->device->displayName(),
        ];
    }
}
