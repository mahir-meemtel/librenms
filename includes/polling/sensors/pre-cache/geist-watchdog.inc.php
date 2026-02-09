<?php
if ($type == 'temperature') {
    $sensor_cache['geist_temp_unit'] = snmp_get($device, 'temperatureUnits.0', '-Oqv', 'GEIST-V4-MIB');
}
