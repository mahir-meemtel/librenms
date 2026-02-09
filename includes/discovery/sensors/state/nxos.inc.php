<?php
$fan_trays = SnmpQuery::hideMib()->numeric(true)->walk('CISCO-ENTITY-FRU-CONTROL-MIB::cefcFanTrayOperStatus')->values(0);

/* CISCO-ENTITY-FRU-CONTROL-MIB cefcFanTrayOperStatus
 *  unknown(1),
 *  up(2),
 *  down(3),
 *  warning(4)
*/

if (is_array($fan_trays)) {
    foreach ($fan_trays as $current_oid => $current_value) {
        $split_oid = explode('.', $current_oid);
        $index = $split_oid[count($split_oid) - 1];

        $entity_oid = '.1.3.6.1.2.1.47.1.1.1.1.7';
        $descr = SnmpQuery::get('ENTITY-MIB::entPhysicalName.' . $index)->value();

        $state_name = 'cefcFanTrayOperStatus';
        $states = [
            ['value' => 1, 'generic' => 3, 'graph' => 0, 'descr' => 'unknown'],
            ['value' => 2, 'generic' => 0, 'graph' => 1, 'descr' => 'up'],
            ['value' => 3, 'generic' => 2, 'graph' => 1, 'descr' => 'down'],
            ['value' => 4, 'generic' => 1, 'graph' => 1, 'descr' => 'warning'],
        ];
        create_state_index($state_name, $states);

        discover_sensor(null, 'state', $device, $current_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current_value);
    }
}
