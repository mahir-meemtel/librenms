<?php
if (strstr($device['sysObjectID'], '.1.3.6.1.4.1.6321.1.2.3')) { // E5-1xx Series
    echo 'Calix E5: ';

    if (strpos($device['sysObjectID'], '.1.3.6.1.4.1.6321.1.2.3.4') !== false) { // E5-121
        $oids = snmp_walk($device, 'iesSysTempCurValue', '-Osqn', 'E5-121-IESCOMMON-MIB', 'calix');
        $oids = trim($oids);
        $oids = str_replace('.1.3.6.1.4.1.6321.1.2.3.4.98.2.3.1.2.', '', $oids);
        foreach (explode("\n", $oids) as $data) {
            $data = trim($data);
            if ($data != '') {
                [$oid] = explode(' ', $data);
                $temperature_oid = ".1.3.6.1.4.1.6321.1.2.3.4.98.2.3.1.2.$oid";
                $descr_oid = ".1.3.6.1.4.1.6321.1.2.3.4.98.2.3.1.6.$oid";
                $descr = snmp_get($device, $descr_oid, '-Oqv', '');
                $temperature = snmp_get($device, $temperature_oid, '-Oqv', '');
                $descr = str_replace('"', '', $descr);
                $current = $temperature;

                discover_sensor(null, 'temperature', $device, $temperature_oid, $oid, 'calix', $descr, '1', '1', null, null, null, null, $current);
            }
        }
    }

    if (strpos($device['sysObjectID'], '.1.3.6.1.4.1.6321.1.2.3.3') !== false) { // E5-120
        $oids = snmp_walk($device, 'iesSysTempCurValue', '-Osqn', 'E5-120-IESCOMMON-MIB', 'calix');
        $oids = trim($oids);
        $oids = str_replace('.1.3.6.1.4.1.6321.1.2.3.3.98.2.3.1.2.', '', $oids);
        foreach (explode("\n", $oids) as $data) {
            $data = trim($data);
            if ($data != '') {
                [$oid] = explode(' ', $data);
                $temperature_oid = ".1.3.6.1.4.1.6321.1.2.3.3.98.2.3.1.2.$oid";
                $descr_oid = ".1.3.6.1.4.1.6321.1.2.3.3.98.2.3.1.6.$oid";
                $descr = snmp_get($device, $descr_oid, '-Oqv', '');
                $temperature = snmp_get($device, $temperature_oid, '-Oqv', '');
                $descr = str_replace('"', '', $descr);
                $current = $temperature;

                discover_sensor(null, 'temperature', $device, $temperature_oid, $oid, 'calix', $descr, '1', '1', null, null, null, null, $current);
            }
        }
    }

    if (strpos($device['sysObjectID'], '.1.3.6.1.4.1.6321.1.2.3.2') !== false) { // E5-111
        $oids = snmp_walk($device, 'iesSysTempCurValue', '-Osqn', 'E5-111-IESCOMMON-MIB', 'calix');
        $oids = trim($oids);
        $oids = str_replace('.1.3.6.1.4.1.6321.1.2.3.2.98.2.3.1.2.', '', $oids);
        foreach (explode("\n", $oids) as $data) {
            $data = trim($data);
            if ($data != '') {
                [$oid] = explode(' ', $data);
                $temperature_oid = ".1.3.6.1.4.1.6321.1.2.3.2.98.2.3.1.2.$oid";
                $descr_oid = ".1.3.6.1.4.1.6321.1.2.3.2.98.2.3.1.6.$oid";
                $descr = snmp_get($device, $descr_oid, '-Oqv', '');
                $temperature = snmp_get($device, $temperature_oid, '-Oqv', '');
                $descr = str_replace('"', '', $descr);
                $current = $temperature;

                discover_sensor(null, 'temperature', $device, $temperature_oid, $oid, 'calix', $descr, '1', '1', null, null, null, null, $current);
            }
        }
    }

    if (strpos($device['sysObjectID'], '.1.3.6.1.4.1.6321.1.2.3.1') !== false) { // E5-110
        $oids = snmp_walk($device, 'iesSysTempCurValue', '-Osqn', 'E5-110-IESCOMMON-MIB', 'calix');
        $oids = trim($oids);
        $oids = str_replace('.1.3.6.1.4.1.6321.1.2.3.1.98.2.3.1.2.', '', $oids);
        foreach (explode("\n", $oids) as $data) {
            $data = trim($data);
            if ($data != '') {
                [$oid] = explode(' ', $data);
                $temperature_oid = ".1.3.6.1.4.1.6321.1.2.3.1.98.2.3.1.2.$oid";
                $descr_oid = ".1.3.6.1.4.1.6321.1.2.3.1.98.2.3.1.6.$oid";
                $descr = snmp_get($device, $descr_oid, '-Oqv', '');
                $temperature = snmp_get($device, $temperature_oid, '-Oqv', '');
                $descr = str_replace('"', '', $descr);
                $current = $temperature;

                discover_sensor(null, 'temperature', $device, $temperature_oid, $oid, 'calix', $descr, '1', '1', null, null, null, null, $current);
            }
        }
    }
}
