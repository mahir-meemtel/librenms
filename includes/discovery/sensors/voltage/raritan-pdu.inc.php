<?php
foreach ($pre_cache['raritan_inletTable'] as $index => $raritan_data) {
    for ($x = 1; $x <= $raritan_data['inletPoleCount']; $x++) {
        $tmp_index = "$index.$x";
        $new_index = "inletPoleVoltage.$tmp_index";
        $oid = '.1.3.6.1.4.1.13742.4.1.21.2.1.4.' . $tmp_index;
        $descr = 'Inlet ' . $pre_cache['raritan_inletPoleTable'][$index][$x]['inletPoleLabel'];
        $divisor = 1000;
        $low_limit = $raritan_data['inletVoltageUpperCritical'] / $divisor;
        $low_warn_limit = $raritan_data['inletVoltageUpperWarning'] / $divisor;
        $warn_limit = $raritan_data['inletVoltageLowerWarning'] / $divisor;
        $high_limit = $raritan_data['inletVoltageLowerCritical'] / $divisor;
        $current = $pre_cache['raritan_inletPoleTable'][$index][$x]['inletPoleVoltage'] / $divisor;
        discover_sensor(null, 'voltage', $device, $oid, $tmp_index, 'raritan', $descr, $divisor, 1, $low_limit, $low_limit, $warn_limit, $high_limit, $current);
    }
}
