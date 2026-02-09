<?php
if (Str::startsWith($device['sysObjectID'], '.1.3.6.1.4.1.21362.100.')) {
    $pre_cache['ifoSysProductIndex'] = snmp_get($device, 'ifoSysProductIndex.0', '-Oqv', 'IFOTEC-SMI');

    if ($pre_cache['ifoSysProductIndex'] != null) {
        $virtual_tables = [
            'ifoTempName' => '/\.1\.3\.6\.1\.4\.1\.21362\.101\.2\.1\.1\.3\.' . $pre_cache['ifoSysProductIndex'] . '\.(\d+)/',
            'ifoTempDescr' => '/\.1\.3\.6\.1\.4\.1\.21362\.101\.2\.1\.1\.4\.' . $pre_cache['ifoSysProductIndex'] . '\.(\d+)/',
            'ifoTempValue' => '/\.1\.3\.6\.1\.4\.1\.21362\.101\.2\.1\.1\.5\.' . $pre_cache['ifoSysProductIndex'] . '\.(\d+)/',
            'ifoTempAlarmStatus' => '/\.1\.3\.6\.1\.4\.1\.21362\.101\.2\.1\.1\.6\.' . $pre_cache['ifoSysProductIndex'] . '\.(\d+)/',
            'ifoTempLowThldAlarm' => '/\.1\.3\.6\.1\.4\.1\.21362\.101\.2\.1\.1\.7\.' . $pre_cache['ifoSysProductIndex'] . '\.(\d+)/',
            'ifoTempHighThldAlarm' => '/\.1\.3\.6\.1\.4\.1\.21362\.101\.2\.1\.1\.8\.' . $pre_cache['ifoSysProductIndex'] . '\.(\d+)/',
            'ifoTempLowThldWarning' => '/\.1\.3\.6\.1\.4\.1\.21362\.101\.2\.1\.1\.9\.' . $pre_cache['ifoSysProductIndex'] . '\.(\d+)/',
            'ifoTempHighThldWarning' => '/\.1\.3\.6\.1\.4\.1\.21362\.101\.2\.1\.1\.10\.' . $pre_cache['ifoSysProductIndex'] . '\.(\d+)/',
        ];

        // .ifoTemperatureTable.ifoTemperatureEntry.<ifoSysProductIndex>
        $data = snmp_walk($device, 'ifoTemperatureEntry', '-OQn', 'IFOTEC-SMI');
        foreach (explode(PHP_EOL, $data) as $line) {
            if (! Str::contains($line, ' = ')) {
                continue;
            }
            [$oid, $value] = explode(' = ', $line);

            $processed = false;
            foreach ($virtual_tables as $vt_name => $vt_regex) {
                if (preg_match($vt_regex, $oid, $matches)) {
                    $index = $matches[1];

                    $pre_cache['ifoTemperatureTable'][$index][$vt_name] = ['value' => $value, 'oid' => $oid];

                    $processed = true;
                    break;  // skip rest
                }
            }

            if (! $processed) {
                $pre_cache[$oid] = [[$oid => $value]];
            }
        }
        var_dump($pre_cache['ifoTemperatureTable']);
    }
}
unset($data);
