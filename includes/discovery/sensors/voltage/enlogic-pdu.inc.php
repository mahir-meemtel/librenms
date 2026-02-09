<?php
foreach ($pre_cache['enlogic_pdu_input'] as $index => $data) {
    if (is_array($data)) {
        $oid = '.1.3.6.1.4.1.38446.1.3.4.1.6.' . $index;
        $descr = "Input Phase $index";
        $divisor = 1;
        $type = 'enlogic-pdu';
        $low_limit = $data['pduInputPhaseConfigVoltageLowerCriticalThreshold'];
        $low_warn = $data['pduInputPhaseConfigVoltageLowerWarningThreshold'];
        $high_limit = $data['pduInputPhaseConfigVoltageUpperCriticalThreshold'];
        $high_warn = $data['pduInputPhaseConfigVoltageUpperWarningThreshold'];
        $current = $data['pduInputPhaseStatusVoltage'];
        if ($current > 0) {
            discover_sensor(null, 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', $low_limit, $low_warn, $high_warn, $high_limit, $current);
        }
    }
}
