<?php
namespace App\Http\Controllers\Select;

use App\Models\Dashboard;

class DashboardController extends SelectController
{
    protected function searchFields($request)
    {
        return ['dashboard_name', 'username'];
    }

    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function baseQuery($request)
    {
        return Dashboard::query()
            ->where('access', '>', 0)
            ->leftJoin('users', 'dashboards.user_id', 'users.user_id') // left join so we can search username
            ->orderBy('dashboards.user_id')
            ->orderBy('dashboard_name')
            ->select(['dashboard_id', 'username', 'dashboard_name']);
    }

    /**
     * @param  object  $dashboard
     * @return array
     */
    public function formatItem($dashboard): array
    {
        return [
            'id' => $dashboard->dashboard_id,
            'text' => $this->describe($dashboard),
        ];
    }

    protected function prependItem(): array
    {
        return [
            'id' => 0,
            'text' => __('No Default Dashboard'),
        ];
    }

    private function describe($dashboard): string
    {
        if ($dashboard->dashboard_id == 0) {
            return $this->prependItem()['text'];
        }

        return "{$dashboard->username}: {$dashboard->dashboard_name} ("
            . ($dashboard->access == 1 ? __('read-only') : __('read-write')) . ')';
    }
}
