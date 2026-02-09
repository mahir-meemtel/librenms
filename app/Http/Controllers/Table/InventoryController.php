<?php
namespace App\Http\Controllers\Table;

use App\Models\EntPhysical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

class InventoryController extends TableController
{
    protected $model = EntPhysical::class;

    public function rules()
    {
        return [
            'device' => 'nullable|int',
            'descr' => 'nullable|string',
            'model' => 'nullable|string',
            'serial' => 'nullable|string',
        ];
    }

    protected function filterFields($request)
    {
        return [
            'device_id' => 'device',
        ];
    }

    protected function searchFields($request)
    {
        return ['entPhysicalDescr', 'entPhysicalModelName', 'entPhysicalSerialNum'];
    }

    protected function sortFields($request)
    {
        return [
            'device' => 'device_id',
            'name' => 'entPhysicalName',
            'descr' => 'entPhysicalDescr',
            'model' => 'entPhysicalModelName',
            'serial' => 'entPhysicalSerialNum',
        ];
    }

    protected function baseQuery($request)
    {
        $query = EntPhysical::hasAccess($request->user())
            ->with('device')
            ->select(['entPhysical_id', 'device_id', 'entPhysicalDescr', 'entPhysicalName', 'entPhysicalModelName', 'entPhysicalSerialNum']);

        // apply specific field filters
        $this->search($request->get('descr'), $query, ['entPhysicalDescr']);
        $this->search($request->get('model'), $query, ['entPhysicalModelName']);
        $this->search($request->get('serial'), $query, ['entPhysicalSerialNum']);

        return $query;
    }

    /**
     * @param  EntPhysical  $entPhysical
     * @return array|Model|Collection
     */
    public function formatItem($entPhysical)
    {
        return [
            'device' => Blade::render('<x-device-link :device="$device"/>', ['device' => $entPhysical->device]),
            'descr' => htmlspecialchars($entPhysical->entPhysicalDescr),
            'name' => htmlspecialchars($entPhysical->entPhysicalName),
            'model' => htmlspecialchars($entPhysical->entPhysicalModelName),
            'serial' => htmlspecialchars($entPhysical->entPhysicalSerialNum),
        ];
    }

    /**
     * Get headers for CSV export
     *
     * @return array
     */
    protected function getExportHeaders()
    {
        return [
            'Device',
            'Description',
            'Name',
            'Model',
            'Serial Number',
        ];
    }

    /**
     * Format a row for CSV export
     *
     * @param  EntPhysical  $entPhysical
     * @return array
     */
    protected function formatExportRow($entPhysical)
    {
        return [
            $entPhysical->device ? $entPhysical->device->displayName() : '',
            $entPhysical->entPhysicalDescr,
            $entPhysical->entPhysicalName,
            $entPhysical->entPhysicalModelName,
            $entPhysical->entPhysicalSerialNum,
        ];
    }
}
