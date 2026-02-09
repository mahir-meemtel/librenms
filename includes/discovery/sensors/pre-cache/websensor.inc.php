<?php
$pre_cache['websensor_valuesInt'] = snmpwalk_group($device, 'valuesInt', 'T3610-MIB');
$pre_cache['websensor_settings'] = snmpwalk_group($device, 'settings', 'T3610-MIB');
