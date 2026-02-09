<?php
namespace App\Http\Controllers\Select;

use App\Models\EntPhysical;

class InventoryController extends SelectController
{
    protected function rules()
    {
        return [
            'field' => 'required|in:name,model,descr,class',
            'device' => 'nullable|int',
        ];
    }

    protected function filterFields($request)
    {
        return ['device_id'];
    }

    protected function searchFields($request)
    {
        return [$this->fieldToColumn($request->get('field'))];
    }

    protected function baseQuery($request)
    {
        $column = $this->fieldToColumn($request->get('field'));

        return EntPhysical::hasAccess($request->user())
            ->select($column)
            ->orderBy($column)
            ->distinct();
    }

    private function fieldToColumn(string $field): string
    {
        return match ($field) {
            'name' => 'entPhysicalName',
            'model' => 'entPhysicalModelName',
            'descr' => 'entPhysicalDescr',
            'class' => 'entPhysicalClass',
            default => 'entPhysicalName',
        };
    }
}
