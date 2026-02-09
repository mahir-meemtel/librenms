<?php
echo 'Caching WIPIPE-MIB';
$pre_cache['wipipe_oids'] = snmpwalk_cache_multi_oid($device, 'mdmEntry', [], 'WIPIPE-MIB');
