<?php
foreach ($pre_cache['infineragroove_portTable'] as $index => $data) {
    if (isset($data['ochOsDGD']) && is_numeric($data['ochOsDGD']) && $data['ochOsDGD'] != 0) {
        $descr = $data['portAlias'] . ' Differential Group Delay';
        $oid = '.1.3.6.1.4.1.42229.1.2.4.1.19.1.1.22.' . $index;
        $value = $data['ochOsDGD'];
        $divisor = 1000000000000;
        discover_sensor(null, 'delay', $device, $oid, 'ochOsOSNR.' . $index, 'infinera-groove', $descr, $divisor, '1', null, null, null, null, $value);
    }
}
