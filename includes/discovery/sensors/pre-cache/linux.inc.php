<?php
echo 'RaspberryPi ';
$pre_cache['raspberry_pi_sensors'] = snmpwalk_cache_oid($device, '.1.3.6.1.4.1.8072.1.3.2.4.1.2.9.114.97.115.112.98.101.114.114.121', [], 'NET-SNMP-EXTEND-MIB');
