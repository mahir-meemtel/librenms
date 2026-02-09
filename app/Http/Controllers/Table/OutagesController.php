<?php
namespace App\Http\Controllers\Table;

use App\Facades\ObzoraConfig;
use App\Models\DeviceOutage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;

class OutagesController extends TableController
{
    protected $model = DeviceOutage::class;

    public function rules()
    {
        return [
            'device' => 'nullable|int',
            'to' => 'nullable|date',
            'from' => 'nullable|date',
        ];
    }

    protected function filterFields($request)
    {
        return [
            'device_id' => 'device',
        ];
    }

    protected function sortFields($request)
    {
        return ['going_down', 'up_again', 'device_id'];
    }

    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function baseQuery($request)
    {
        return DeviceOutage::hasAccess($request->user())
            ->with('device')
            ->when($request->from, function ($query) use ($request) {
                $query->where('going_down', '>=', Carbon::parse($request->from, session('preferences.timezone'))->getTimestamp());
            })
            ->when($request->to, function ($query) use ($request) {
                $query->where('going_down', '<=', Carbon::parse($request->to, session('preferences.timezone'))->getTimestamp());
            });
    }

    /**
     * @param  DeviceOutage  $outage
     */
    public function formatItem($outage)
    {
        $start = $this->formatDatetime($outage->going_down);
        $end = $outage->up_again ? $this->formatDatetime($outage->up_again) : '-';
        $duration = ($outage->up_again ?: time()) - $outage->going_down;

        return [
            'status' => $this->statusLabel($outage),
            'going_down' => $start,
            'up_again' => $end,
            'device_id' => Blade::render('<x-device-link :device="$device"/>', ['device' => $outage->device]),
            'duration' => $this->formatTime($duration),
        ];
    }

    private function formatTime($duration)
    {
        $day_seconds = 86400;

        $duration_days = (int) ($duration / $day_seconds);

        $output = '';
        if ($duration_days) {
            $output .= $duration_days . 'd ';
        }
        $output .= (new Carbon($duration))->format(ObzoraConfig::get('dateformat.time'));

        return $output;
    }

    private function formatDatetime($timestamp)
    {
        if (! $timestamp) {
            $timestamp = 0;
        }

        // Convert epoch to local time
        return Carbon::createFromTimestamp($timestamp, session('preferences.timezone'))
            ->format(ObzoraConfig::get('dateformat.compact'));
    }

    private function statusLabel($outage)
    {
        if (empty($outage->up_again)) {
            $label = 'label-danger';
        } else {
            $label = 'label-success';
        }

        $output = "<span class='alert-status " . $label . "'></span>";

        return $output;
    }

    /**
     * Get headers for CSV export
     *
     * @return array
     */
    protected function getExportHeaders()
    {
        return [
            'Device Hostname',
            'Start',
            'End',
            'Duration',
        ];
    }

    /**
     * Format a row for CSV export
     *
     * @param  DeviceOutage  $outage
     * @return array
     */
    protected function formatExportRow($outage)
    {
        return [
            $outage->device ? $outage->device->displayName() : '',
            $this->formatDatetime($outage->going_down),
            $outage->up_again ? $this->formatDatetime($outage->up_again) : '-',
            $this->formatTime(($outage->up_again ?: time()) - $outage->going_down),
        ];
    }
}
