<?php
foreach ($pre_cache['enlogic_pdu_status'] as $index => $data) {
    if (is_array($data)) {
        $current = $data['pduUnitStatusActivePower'];
        $descr = "Active power #$index";
        $oid = '.1.3.6.1.4.1.38446.1.2.4.1.4.' . $index;
        if ($current > 0) {
            discover_sensor(null, 'power', $device, $oid, $index, 'enlogic-pdu', $descr, 1, 1, null, null, null, null, $current);
        }
    }
}

foreach ($pre_cache['enlogic_pdu_input'] as $index => $data) {
    if (is_array($data)) {
        $current = $data['pduInputPhaseStatusActivePower'];
        $tmp_index = 'pduInputPhaseStatusActivePower.' . $index;
        $descr = 'Input Phase #' . $index;
        $oid = '.1.3.6.1.4.1.38446.1.3.4.1.7.' . $index;
        if ($current > 0) {
            discover_sensor(null, 'power', $device, $oid, $tmp_index, 'enlogic-pdu', $descr, 1, 1, null, null, null, null, $current);
        }
    }
}
