<?php
foreach ($pre_cache['enlogic_pdu_input'] as $index => $data) {
    if (is_array($data)) {
        $oid = '.1.3.6.1.4.1.38446.1.3.4.1.5.' . $index;
        $tmp_index = 'pduInputPhaseStatusCurrent.' . $index;
        $descr = "Input Phase $index";
        $divisor = 1;
        $type = 'enlogic-pdu';
        $low_limit = $data['pduInputPhaseConfigCurrentLowerCriticalThreshold'];
        $low_warn = $data['pduInputPhaseConfigCurrentLowerWarningThreshold'];
        $high_limit = $data['pduInputPhaseConfigCurrentUpperCriticalThreshold'];
        $high_warn = $data['pduInputPhaseConfigCurrentUpperWarningThreshold'];
        $current = $data['pduInputPhaseStatusCurrent'];
        if ($current > 0) {
            discover_sensor(null, 'current', $device, $oid, $tmp_index, $type, $descr, $divisor, '1', $low_limit, $low_warn, $high_warn, $high_limit, $current);
        }
    }
}

foreach ($pre_cache['enlogic_pdu_circuit'] as $index => $data) {
    if (is_array($data)) {
        $oid = '.1.3.6.1.4.1.38446.1.4.4.1.5.' . $index;
        $tmp_index = 'pduCircuitBreakerStatusCurrent.' . $index;
        $descr = "Input Phase {$data['pduCircuitBreakerLabel']}";
        $divisor = 1;
        $type = 'enlogic-pdu';
        $low_limit = $data['pduCircuitBreakerConfigLowerCriticalThreshold'];
        $low_warn = $data['pduCircuitBreakerConfigLowerWarningThreshold'];
        $high_limit = $data['pduCircuitBreakerConfigUpperCriticalThreshold'];
        $high_warn = $data['pduCircuitBreakerConfigUpperWarningThreshold'];
        $current = $data['pduCircuitBreakerStatusCurrent'];
        if ($current > 0) {
            discover_sensor(null, 'current', $device, $oid, $tmp_index, $type, $descr, $divisor, '1', $low_limit, $low_warn, $high_warn, $high_limit, $current);
        }
    }
}
