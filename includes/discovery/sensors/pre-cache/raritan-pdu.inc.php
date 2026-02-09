<?php
echo 'inletTable ';
$pre_cache['raritan_inletTable'] = snmpwalk_group($device, 'inletTable', 'PDU-MIB');

echo 'inletPoleTable ';
$pre_cache['raritan_inletPoleTable'] = snmpwalk_group($device, 'inletPoleTable', 'PDU-MIB', 2);
