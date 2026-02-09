<?php
if (! array_key_exists($sensor['sensor_oid'], $snmp_data) && array_key_exists($sensor['sensor_oid'] . '.0', $snmp_data)) {
    $sensor_value = trim(str_replace('"', '', $snmp_data[$sensor['sensor_oid'] . '.0']));
}
