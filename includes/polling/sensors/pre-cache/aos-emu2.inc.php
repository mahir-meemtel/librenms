<?php
if ($type == 'temperature') {
    $sensor_cache['scale'] = snmp_get($device, 'emsStatusSysTempUnits.0', '-OQv', 'PowerNet-MIB');
}
