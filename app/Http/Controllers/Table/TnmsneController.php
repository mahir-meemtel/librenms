<?php
namespace App\Http\Controllers\Table;

use App\Models\TnmsneInfo;
use Illuminate\Http\Request;

class TnmsneController extends TableController
{
    protected function rules()
    {
        return [
            'device_id' => 'nullable|integer',
        ];
    }

    protected function sortFields($request)
    {
        return [
            'neName',
            'neLocation',
            'neType',
            'neOpMode',
            'neAlarm',
            'neOpState',
        ];
    }

    protected function searchFields(Request $request)
    {
        return [
            'neName',
            'neLocation',
            'neType',
            'neOpMode',
            'neAlarm',
            'neOpState',
        ];
    }

    protected function filterFields(Request $request)
    {
        return ['device_id'];
    }

    /**
     * @inheritDoc
     */
    protected function baseQuery(Request $request)
    {
        return TnmsneInfo::query();
    }

    /**
     * @param  TnmsneInfo  $tnmsne
     * @return array
     */
    public function formatItem($tnmsne)
    {
        $neOp = $tnmsne->neOpMode == 'operation'
            ? '<span style="min-width:40px;" class="label label-success">operation</span>'
            : '<span style="min-width:40px;" class="label label-danger">' . htmlspecialchars($tnmsne->neOpMode) . '</span>';

        $opState = $tnmsne->neOpState == 'enabled'
            ? '<td class="list"><span style="min-width:40px;" class="label label-success">enabled</span></td>'
            : '<td class="list"><span style="min-width:40px;" class="label label-danger">' . htmlspecialchars($tnmsne->neOpState) . '</span></td>';

        return [
            'neName' => htmlspecialchars($tnmsne->neName),
            'neLocation' => htmlspecialchars($tnmsne->neLocation),
            'neType' => htmlspecialchars($tnmsne->neType),
            'neOpMode' => $neOp,
            'neAlarm' => $this->getAlarmLabel($tnmsne->neAlarm),
            'neOpState' => $opState,
        ];
    }

    private function getAlarmLabel(string $neAlarm): string
    {
        return match ($neAlarm) {
            'cleared' => '<span style="min-width:40px;" class="label label-success">cleared</span>',
            'warning' => '<span style="min-width:40px;" class="label label-warning">warning</span>',
            'minor', 'major', 'critical', 'indeterminate' => '<span style="min-width:40px;" class="label label-danger">' . htmlspecialchars($neAlarm) . '</span>',
            default => '<span style="min-width:40px;" class="label label-default">' . htmlspecialchars($neAlarm) . '</span>',
        };
    }
}
