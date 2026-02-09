<?php
echo 'ifSfpParameterTable ';
$pre_cache['pbn_oids'] = snmpwalk_cache_multi_oid($device, '.1.3.6.1.4.1.11606.10.9.63.1.7', [], 'NMS-IF-MIB', 'pbn');
