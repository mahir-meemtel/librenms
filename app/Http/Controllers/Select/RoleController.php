<?php
namespace App\Http\Controllers\Select;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RoleController extends SelectController
{
    protected function searchFields(Request $request): array
    {
        return ['name'];
    }

    protected function baseQuery(Request $request): \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
    {
        $this->authorize('viewAny', Role::class);

        if (Role::exists()) {
            return Role::query()->select('name');
        }

        // Create a query builder from a raw SQL that returns default values
        return DB::table(DB::raw("(SELECT 'admin' as name UNION ALL SELECT 'global-read' UNION ALL SELECT 'user') as roles"));
    }

    /**
     * @param  Role  $role
     * @return array
     */
    public function formatItem($role): array
    {
        return [
            'id' => $role->name,
            'text' => Str::title(str_replace('-', ' ', $role->name)),
        ];
    }
}
