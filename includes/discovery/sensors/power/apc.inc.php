<?php
foreach ($pre_cache['cooling_unit_analog'] as $index => $data) {
    $cur_oid = '.1.3.6.1.4.1.318.1.1.27.1.4.1.2.1.3.' . $index;
    $descr = $data['coolingUnitStatusAnalogDescription'];
    $scale = $data['coolingUnitStatusAnalogScale'] ?? null;
    $value = $data['coolingUnitStatusAnalogValue'] ?? null;
    if (preg_match('/Cool/', $descr) && $data['coolingUnitStatusAnalogUnits'] == 'kW' && $value >= 0) {
        discover_sensor(null, 'power', $device, $cur_oid, $cur_oid, 'apc', $descr, $scale, 1, null, null, null, null, $value);
    }
}
