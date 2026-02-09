<?php
namespace App\Http\Controllers\Select;

use App\Models\CustomMap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CustomMapController extends SelectController
{
    protected function searchFields($request): array
    {
        return ['custom_map_id', 'name'];
    }

    protected function baseQuery(Request $request): Builder
    {
        return CustomMap::query()->hasAccess($request->user())
            ->select('custom_map_id', 'name')
            ->orderBy('name');
    }
}
