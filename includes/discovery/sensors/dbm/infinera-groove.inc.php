<?php
foreach ($pre_cache['infineragroove_portTable'] as $index => $data) {
    $portAlias = (string) $data['portAlias'];

    // Discover Rx Power
    if (isset($data['portRxOpticalPower']) && is_numeric($data['portRxOpticalPower']) && in_array($data['portAdminStatus'], ['up', '3'], true)) {
        $descr = $portAlias . ' Port Receive Power';
        $oid = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.4.' . $index;
        $value = $data['portRxOpticalPower'];
        discover_sensor(null, 'dbm', $device, $oid, 'portRxOpticalPower.' . $index, 'infinera-groove', $descr, $divisor, '1', null, null, null, null, $value, 'snmp', null, null, null, $portAlias, 'GAUGE');
    }

    // Discover Tx Power
    if (isset($data['portTxOpticalPower']) && is_numeric($data['portTxOpticalPower']) && in_array($data['portAdminStatus'], ['up', '3'], true)) {
        $descr = $portAlias . ' Port Transmit Power';
        $oid = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.5.' . $index;
        $value = $data['portTxOpticalPower'];
        discover_sensor(null, 'dbm', $device, $oid, 'portTxOpticalPower.' . $index, 'infinera-groove', $descr, $divisor, '1', null, null, null, null, $value, 'snmp', null, null, null, $portAlias, 'GAUGE');
    }
}
