<?php
namespace App\Http\Controllers\Table;

use App\Models\WirelessSensor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WirelessSensorController extends SensorsController
{
    protected function rules(): array
    {
        return [
            'view' => Rule::in(['detail', 'graphs']),
            'class' => Rule::in(array_keys(\ObzoraNMS\Device\WirelessSensor::getTypes())),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function baseQuery(Request $request): Builder
    {
        $class = $request->input('class');

        return WirelessSensor::query()
            ->hasAccess($request->user())
            ->where('sensor_class', $class)
            ->when($request->get('searchPhrase'), fn ($q) => $q->leftJoin('devices', 'devices.device_id', '=', 'sensors.device_id'))
            ->withAggregate('device', 'hostname');
    }
}
