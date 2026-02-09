<?php
$sensor_value = $snmp_data[$sensor['sensor_oid']] ?? $snmp_data[$sensor['sensor_oid'] . '.0'];
