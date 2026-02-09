<?php
namespace App\Http\Controllers\Select;

use App\Models\CustomMap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CustomMapMenuGroupController extends SelectController
{
    protected function searchFields($request): array
    {
        return ['menu_group'];
    }

    protected function baseQuery(Request $request): Builder
    {
        return CustomMap::query()->hasAccess($request->user())
            ->whereNotNull('menu_group')->select('menu_group')->groupBy('menu_group');
    }
}
