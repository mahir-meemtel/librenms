<?php
foreach ($pre_cache['infineragroove_portTable'] as $index => $data) {
    $portAliasIndex = preg_replace('/\.0$/', '', $index);
    $portAlias = (string) $pre_cache['infineragroove_portTable'][$portAliasIndex]['portAlias'];

    if (isset($data['bitErrorRatePreFecInstant']) && is_numeric($data['bitErrorRatePreFecInstant']) && in_array($pre_cache['infineragroove_portTable'][$portAliasIndex]['portAdminStatus'], ['up', '3'], true)) {
        $descr = $portAlias . ' PreFecBer';
        $oid = '.1.3.6.1.4.1.42229.1.2.13.1.1.1.1.' . $index;
        $value = $data['bitErrorRatePreFecInstant'];
        $divisor = 1;
        discover_sensor(null, 'ber', $device, $oid, 'bitErrorRatePreFecInstant.' . $index, 'infinera-groove', $descr, $divisor, '1', null, null, null, null, $value, 'snmp', null, null, null, $portAlias, 'GAUGE');
    }
    if (isset($data['bitErrorRatePostFecInstant']) && is_numeric($data['bitErrorRatePostFecInstant']) && in_array($pre_cache['infineragroove_portTable'][$portAliasIndex]['portAdminStatus'], ['up', '3'], true)) {
        $descr = $portAlias . ' PostFecBer';
        $oid = '.1.3.6.1.4.1.42229.1.2.13.2.1.1.1.' . $index;
        $value = $data['bitErrorRatePostFecInstant'];
        $divisor = 1;
        discover_sensor(null, 'ber', $device, $oid, 'bitErrorRatePostFecInstant.' . $index, 'infinera-groove', $descr, $divisor, '1', null, null, null, null, $value, 'snmp', null, null, null, $portAlias, 'GAUGE');
    }
}
