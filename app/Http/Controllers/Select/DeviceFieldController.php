<?php
namespace App\Http\Controllers\Select;

use App\Facades\ObzoraConfig;
use App\Models\Device;

class DeviceFieldController extends SelectController
{
    /**
     * Defines validation rules (will override base validation rules for select2 responses too)
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'field' => 'required|in:features,hardware,os,type,version',
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
        $field = $request->get('field');
        $query = Device::hasAccess($request->user())
            ->select($field)->orderBy($field)->distinct();

        if ($device_id = $request->get('device')) {
            $query->where('ports.device_id', $device_id);
        }

        return $query;
    }

    /**
     * @param  Device  $device
     * @return array
     */
    public function formatItem($device)
    {
        $field = \Request::get('field');

        $text = $device[$field];
        if ($field == 'os') {
            $text = ObzoraConfig::getOsSetting($text, 'text');
        } elseif ($field == 'type') {
            $text = ucfirst($text);
        }

        return [
            'id' => $device[$field],
            'text' => $text,
        ];
    }
}
