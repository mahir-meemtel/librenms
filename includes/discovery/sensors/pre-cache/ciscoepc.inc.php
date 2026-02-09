<?php
echo 'docsIfDownstreamChannelTable ';
$pre_cache['ciscoepc_docsIfDownstreamChannelTable'] = snmpwalk_cache_oid($device, 'docsIfDownstreamChannelTable', [], 'DOCS-IF-MIB');

echo 'docsIfSignalQualityTable ';
$pre_cache['ciscoepc_docsIfSignalQualityTable'] = snmpwalk_cache_oid($device, 'docsIfSignalQualityTable', [], 'DOCS-IF-MIB');
