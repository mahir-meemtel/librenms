<?php
foreach ($pre_cache['infineragroove_portTable'] as $index => $data) {
    if (isset($data['ochOsCD']) && is_numeric($data['ochOsCD']) && $data['ochOsCD'] != -99) {
        $descr = $data['portAlias'] . ' CD';
        $oid = '.1.3.6.1.4.1.42229.1.2.4.1.19.1.1.23.' . $index;
        $value = $data['ochOsCD'];
        discover_sensor(null, 'chromatic_dispersion', $device, $oid, 'ochOsCD.' . $index, 'infinera-groove', $descr, null, '1', null, null, null, null, $value);
    }
}
