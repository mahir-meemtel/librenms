<?php
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Util\Mac;

$macTable = SnmpQuery::hideMib()->walk('BRIDGE-MIB::dot1dTpFdbAddress')->table();
$portTable = SnmpQuery::hideMib()->walk('BRIDGE-MIB::dot1dTpFdbPort')->table();
$basePortTable = SnmpQuery::hideMib()->walk('BRIDGE-MIB::dot1dBasePort')->table();
$basePortIfIndexTable = SnmpQuery::hideMib()->walk('BRIDGE-MIB::dot1dBasePortIfIndex')->table();

foreach ($macTable['dot1dTpFdbAddress'] as $dot1dTpFdbPort => $mac) {
    $fdbPort = $portTable['dot1dTpFdbPort'][$dot1dTpFdbPort];
    $dot1dBasePort = array_search($fdbPort, $basePortTable['dot1dBasePort']);
    $dot1dBasePortIfIndex = $basePortIfIndexTable['dot1dBasePortIfIndex'][$dot1dBasePort];

    $port_id = PortCache::getIdFromIfIndex($dot1dBasePortIfIndex);
    $vlan_id = 0; // Bug 9239914

    $mac_address = Mac::parse($mac)->hex(); // pad zeros and remove colons

    if ($port_id == null) {
        Log::debug("No port known for $mac\n");
        continue;
    }

    if (strlen($mac_address) != 12) {
        Log::debug("MAC address padding failed for $mac\n");
        continue;
    }

    $insert[$vlan_id][$mac_address]['port_id'] = $port_id;
    Log::debug("vlan $vlan_id mac $mac_address port ($dot1dBasePort) $port_id\n");
}

echo PHP_EOL;
