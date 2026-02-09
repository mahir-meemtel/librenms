<?php
namespace App\Http\Controllers\Select;

use App\Models\PollerGroup;

class PollerGroupController extends SelectController
{
    protected function searchFields($request)
    {
        return ['group_name', 'descr'];
    }

    protected function baseQuery($request)
    {
        return PollerGroup::query()->select(['id', 'group_name']);
    }

    protected function prependItem(): array
    {
        return [
            'id' => 0,
            'text' => __('General'),
        ];
    }
}
