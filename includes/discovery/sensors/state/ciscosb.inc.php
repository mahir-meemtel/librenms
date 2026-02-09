<?php
use Illuminate\Support\Str;

$temp = SnmpQuery::hideMib()->walk('CISCOSB-rlInterfaces::swIfOperSuspendedStatus')->table(0);

$cur_oid = '.1.3.6.1.4.1.9.6.1.101.43.1.1.24.';

if (! empty($temp)) {
    //Create State Index
    $state_name = 'swIfOperSuspendedStatus';
    $states = [
        ['value' => 1, 'generic' => 2, 'graph' => 0, 'descr' => 'true'],
        ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'false'],
    ];
    create_state_index($state_name, $states);

    foreach ($temp[$state_name] as $index => $value) {
        $port = PortCache::getByIfIndex(preg_replace('/^\d+\./', '', $index), $device['device_id']);
        $descr = trim($port?->ifDescr . ' Suspended Status');

        if (Str::contains($descr, ['ethernet', 'Ethernet']) && $port?->ifOperStatus !== 'notPresent') {
            //Discover Sensors
            discover_sensor(null, 'state', $device, $cur_oid . $index, $index, $state_name, $descr, 1, 1, null, null, null, null, $value, 'snmp', $index);
        }
    }
}
