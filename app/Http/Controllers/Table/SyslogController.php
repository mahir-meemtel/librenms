<?php
namespace App\Http\Controllers\Table;

use App\Models\Syslog;
use Illuminate\Support\Facades\Blade;
use ObzoraNMS\Enum\SyslogSeverity;

class SyslogController extends TableController
{
    public function rules()
    {
        return [
            'device' => 'nullable|int',
            'device_group' => 'nullable|int',
            'program' => 'nullable|string',
            'priority' => 'nullable|string',
            'to' => 'nullable|date',
            'from' => 'nullable|date',
            'level' => 'nullable|string',
        ];
    }

    public function searchFields($request)
    {
        return ['msg'];
    }

    public function filterFields($request)
    {
        return [
            'device_id' => 'device',
            'program' => 'program',
            'priority' => 'priority',
        ];
    }

    public function sortFields($request)
    {
        return ['label', 'timestamp', 'level', 'device_id', 'program', 'msg', 'priority'];
    }

    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function baseQuery($request)
    {
        return Syslog::hasAccess($request->user())
            ->with('device')
            ->when($request->device_group, function ($query, $group) {
                $query->inDeviceGroup($group);
            })
            ->when($request->from, function ($query, $from) {
                $query->where('timestamp', '>=', $from);
            })
            ->when($request->to, function ($query, $to) {
                $query->where('timestamp', '<=', $to);
            })
            ->when($request->level, function ($query, $level) {
                if ($level >= 7) {
                    return;  // include everything
                }

                $levels = array_slice(SyslogSeverity::LEVELS, 0, $level + 1);
                $query->whereIn('level', $levels);
            });
    }

    /**
     * @param  Syslog  $syslog
     */
    public function formatItem($syslog)
    {
        return [
            'label' => $this->setLabel($syslog),
            'timestamp' => $syslog->timestamp,
            'level' => htmlentities($syslog->level),
            'device_id' => Blade::render('<x-device-link :device="$device"/>', ['device' => $syslog->device]),
            'program' => htmlentities($syslog->program),
            'msg' => htmlentities($syslog->msg),
            'priority' => htmlentities($syslog->priority),
        ];
    }

    private function setLabel($syslog)
    {
        $output = "<span class='alert-status ";
        $output .= $this->priorityLabel($syslog->priority);
        $output .= "'>";
        $output .= '</span>';

        return $output;
    }

    /**
     * @param  int  $syslog_priority
     * @return string
     */
    private function priorityLabel($syslog_priority)
    {
        switch ($syslog_priority) {
            case 'debug':
                return 'label-default'; //Debug
            case 'info':
                return 'label-info'; //Informational
            case 'notice':
                return 'label-primary'; //Notice
            case 'warning':
                return 'label-warning'; //Warning
            case 'err':
                return 'label-danger'; //Error
            case 'crit':
                return 'label-danger'; //Critical
            case 'alert':
                return 'label-danger'; //Alert
            case 'emerg':
                return 'label-danger'; //Emergency
            default:
                return '';
        }
    }

    // end syslog_priority
}
