<?php
namespace App\Http\Controllers\Table;

use App\Models\AlertSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use ObzoraNMS\Enum\MaintenanceBehavior;

class AlertScheduleController extends TableController
{
    protected $default_sort = ['title' => 'asc', 'start' => 'asc'];

    protected function baseQuery($request)
    {
        return AlertSchedule::query();
    }

    protected function searchFields($request)
    {
        return['title', 'start', 'end'];
    }

    protected function sortFields($request)
    {
        return [
            'start_recurring_dt' => DB::raw('DATE(`start`)'),
            'start_recurring_ht' => DB::raw('TIME(`start`)'),
            'end_recurring_dt' => DB::raw('DATE(`end`)'),
            'end_recurring_ht' => DB::raw('TIME(`end`)'),
            'title' => 'title',
            'recurring' => 'recurring',
            'start' => 'start',
            'end' => 'end',
            'status' => DB::raw("end < '" . Carbon::now('UTC') . "'"), // only partition lapsed
            'behavior' => 'behavior',
        ];
    }

    /**
     * @param  AlertSchedule  $schedule
     * @return array
     */
    public function formatItem($schedule)
    {
        $behavior = match ($schedule->behavior) {
            MaintenanceBehavior::SKIP_ALERTS->value => __('alerting.maintenance.behavior.options.skip_alerts'),
            MaintenanceBehavior::MUTE_ALERTS->value => __('alerting.maintenance.behavior.options.mute_alerts'),
            MaintenanceBehavior::RUN_ALERTS->value => __('alerting.maintenance.behavior.options.run_alerts'),
            default => 'Error: Unknown behavior',
        };

        return [
            'title' => htmlentities($schedule->title),
            'notes' => htmlentities($schedule->notes),
            'behavior' => $behavior,
            'id' => $schedule->schedule_id,
            'start' => $schedule->recurring ? '' : $schedule->start->toDateTimeString('minutes'),
            'end' => $schedule->recurring ? '' : $schedule->end->toDateTimeString('minutes'),
            'start_recurring_dt' => $schedule->recurring ? $schedule->start_recurring_dt : '',
            'start_recurring_hr' => $schedule->recurring ? $schedule->start_recurring_hr : '',
            'end_recurring_dt' => $schedule->recurring ? $schedule->end_recurring_dt : '',
            'end_recurring_hr' => $schedule->recurring ? $schedule->end_recurring_hr : '',
            'recurring' => $schedule->recurring ? __('Yes') : __('No'),
            'recurring_day' => $schedule->recurring ? htmlentities(implode(',', $schedule->recurring_day)) : '',
            'status' => $schedule->status,
        ];
    }
}
