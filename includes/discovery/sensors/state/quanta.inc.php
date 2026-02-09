<?php
use Illuminate\Support\Facades\Log;

Log::debug('Quanta Chassis Power Supply state');

$tables = [
    ['powerSupplyTable', 'boxServicesPowSupplyItemState', 'quantaPowerSupplyStatus', 'Power Supply ', 'NETGEAR-BOXSERVICES-PRIVATE-MIB', '.1.3.6.1.4.1.4413.1.1.43.1.7.1.3'],
];

foreach ($tables as $tablevalue) {
    $temp = snmpwalk_cache_multi_oid($device, $tablevalue[1], [], $tablevalue[4]);
    $cur_oid = $tablevalue[1];

    if (is_array($temp)) {
        $state_name = $tablevalue[2];
        if ($state_name == 'quantaPowerSupplyStatus') {
            $states = [
                ['value' => 0, 'generic' => 3, 'graph' => 0, 'descr' => 'other'],
                ['value' => 1, 'generic' => 3, 'graph' => 0, 'descr' => 'notpresent'],
                ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'operational'],
                ['value' => 3, 'generic' => 2, 'graph' => 0, 'descr' => 'failed'],
                ['value' => 4, 'generic' => 0, 'graph' => 0, 'descr' => 'powering'],
                ['value' => 5, 'generic' => 1, 'graph' => 0, 'descr' => 'nopower'],
                ['value' => 6, 'generic' => 1, 'graph' => 0, 'descr' => 'notpowering'],
                ['value' => 7, 'generic' => 2, 'graph' => 0, 'descr' => 'incompatible'],
            ];
        }
        create_state_index($state_name, $states);

        foreach ($temp as $index => $entry) {
            $descr = $tablevalue[3] . $index;
            $oid_for_entry = $tablevalue[5] . '.' . $index;

            discover_sensor(null, 'state', $device, $oid_for_entry, $index, $state_name, $descr, 1, 1, null, null, null, null, $entry[$cur_oid], 'snmp');
        }
    }
}
