<?php
echo 'TPDIN2-MIB::monitor ';
$pre_cache['tpdin_monitor'] = snmpwalk_cache_oid($device, 'monitor', [], 'TPDIN2-MIB');
