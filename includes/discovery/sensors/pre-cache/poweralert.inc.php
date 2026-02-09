<?php
echo 'tlUpsSnmpCardSerialNum ';
$pre_cache['poweralert_serial'] = trim(snmp_get($device, '.1.3.6.1.4.1.850.100.1.1.4.0', '-Ovq', 'TRIPPLITE-MIB'), '"');
