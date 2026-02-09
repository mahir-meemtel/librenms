<?php
$isam_port_stats = snmpwalk_cache_oid($device, 'asamIfExtCustomerId', [], 'ITF-MIB-EXT', 'nokia-isam');
foreach ($isam_port_stats as $index => $value) {
    $port_stats[$index]['ifAlias'] = $isam_port_stats[$index]['asamIfExtCustomerId'];
}

// Now do the same as in ports.inc full ports
SnmpQuery::context('ihub')->hideMib()->walk(['IF-MIB::ifXEntry'])->table(1, $port_stats);
$hc_test = array_slice($port_stats, 0, 1);

// If the device doesn't have ifXentry data, fetch ifEntry instead.
if (! is_numeric($hc_test[0]['ifHCInOctets'] ?? null) || ! is_numeric($hc_test[0]['ifHighSpeed'] ?? null)) {
    $ifEntrySnmpFlags = ['-OQUst'];
    SnmpQuery::options($ifEntrySnmpFlags)->context('ihub')->hideMib()->walk(['IF-MIB::ifEntry'])->table(1, $port_stats);
} else {
    // For devices with ifXentry data, only specific ifEntry keys are fetched to reduce SNMP load
    foreach ($ifmib_oids as $oid) {
        echo "$oid ";
        SnmpQuery::options('-OQUst')->context('ihub')->hideMib()->walk(['IF-MIB::' . $oid])->table(1, $port_stats);
    }
}

unset($isam_ports_stats);
