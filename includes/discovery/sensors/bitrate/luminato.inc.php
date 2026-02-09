<?php
use Illuminate\Support\Facades\Log;

$oids = SnmpQuery::hideMib()->enumStrings()->walk([
    'TELESTE-LUMINATO-MIB::transferTable',
    'TELESTE-LUMINATO-MIB::ifExtTable',
])->table(1);

$ver = intval($device['version']);
if (! empty($oids)) {
    foreach ($oids as $index => $data) {
        if ($data['ifExtDirection'] == 'output') {
            if ($ver < 10) { //v10 and up auto reset transBitrateMax, older version could not use this sensor
                $value = $data['transBitrate'] ?? 0;
                $oid = '.1.3.6.1.4.1.3715.17.3.3.1.2.' . $index;
            } else {
                $value = $data['transBitrateMax'] ?? 0;
                $oid = '.1.3.6.1.4.1.3715.17.3.3.1.4.' . $index;
            }
            $ifExtModule = $data['ifExtModule'];
            unset($defrate);
            $mnr = $data['ifExtModule']; //module nr
            $mname = $pre_cache['entPhysicalDescr'][$ifExtModule]['entPhysicalDescr'] ?? 'unknown'; //module name
            switch ($mname) {
                case 'LAS-D':   // AsiOut
                case 'LRT-C':   // DVB-T/T2
                case 'LCM-B':   // DVB-T
                case 'LRS-D':   // DVB-S2
                case 'LCM-B':   // DVB-T
                    $defrate = 60;
                    break;
                case 'LQM-C':   // QAM module
                case 'LDM-C':   // QAM module
                    $defrate = 50;
                    break;
                default:
                    $defrate = 50;
                    Log::info('Unknown module type');
                    break;
            }

            $defrate = $defrate * 1000 * 1000;
            $descr = $mname . ' output ';
            $descr .= sprintf('%02d', $data['ifExtModule']) . '.'; //include module nr
            $descr .= sprintf('%02d', $data['ifExtPhysInterface']) . '.';
            $descr .= sprintf('%02d', $data['ifExtLogiInterface']);

            app('sensor-discovery')->discover(new \App\Models\Sensor([
                'poller_type' => 'snmp',
                'sensor_class' => 'bitrate',
                'sensor_oid' => $oid,
                'sensor_index' => $index,
                'sensor_type' => 'Transfer_' . $mname,
                'sensor_descr' => $descr,
                'sensor_divisor' => 1,
                'sensor_multiplier' => 1,
                'sensor_limit_low' => 0,
                'sensor_limit_low_warn' => $defrate * 0.1, //10%,
                'sensor_limit_warn' => $defrate * 0.8, //80%
                'sensor_limit' => $defrate * 1, //100%
                'sensor_current' => $value,
                'entPhysicalIndex' => null,
                'entPhysicalIndex_measured' => null,
                'user_func' => null,
                'group' => 'Slot ' . $mnr,
            ]));
        }
    }
}
