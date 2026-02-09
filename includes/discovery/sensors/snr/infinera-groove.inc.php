<?php
foreach ($pre_cache['infineragroove_portTable'] as $index => $data) {
    if (isset($data['ochOsOSNR']) && is_numeric($data['ochOsOSNR']) && $data['ochOsOSNR'] != -99) {
        $descr = $data['portAlias'] . ' Optical SNR';
        $oid = '.1.3.6.1.4.1.42229.1.2.4.1.19.1.1.24.' . $index;
        $value = $data['ochOsOSNR'];
        discover_sensor(null, 'snr', $device, $oid, 'ochOsOSNR.' . $index, 'infinera-groove', $descr, null, '1', null, null, null, null, $value);
    }
}
