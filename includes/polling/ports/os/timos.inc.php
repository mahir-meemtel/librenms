<?php
$timos_vrf_stats = SnmpQuery::enumStrings()->abortOnFailure()->walk([
    'TIMETRA-VRTR-MIB::vRtrIfName',
    'TIMETRA-VRTR-MIB::vRtrIfDescription',
    'TIMETRA-VRTR-MIB::vRtrIfSpeed',
    'TIMETRA-VRTR-MIB::vRtrIfType',
    'TIMETRA-VRTR-MIB::vRtrIfMtu',
    'TIMETRA-VRTR-MIB::vRtrIfRxBytes',
    'TIMETRA-VRTR-MIB::vRtrIfTxBytes',
    'TIMETRA-VRTR-MIB::vRtrIfRxPkts',
    'TIMETRA-VRTR-MIB::vRtrIfTxPkts',
    'TIMETRA-VRTR-MIB::vRtrIfAlias',
])->table(2);

// Merge all virtual routing ports into one
$timos_stats = [];
foreach ($timos_vrf_stats as $vrf) {
    foreach ($vrf as $index => $stats) {
        $timos_stats[$index] = $stats;
    }
}
unset($timos_vrf_stats);

$translate = [
    'ifName' => 'TIMETRA-VRTR-MIB::vRtrIfName',
    'ifAlias' => 'TIMETRA-VRTR-MIB::vRtrIfAlias',
    'ifDescr' => 'TIMETRA-VRTR-MIB::vRtrIfDescription',
    'ifSpeed' => 'TIMETRA-VRTR-MIB::vRtrIfSpeed',
    'ifType' => 'TIMETRA-VRTR-MIB::vRtrIfType',
    'ifMtu' => 'TIMETRA-VRTR-MIB::vRtrIfMtu',
    'ifHCInOctets' => 'TIMETRA-VRTR-MIB::vRtrIfRxBytes',
    'ifHCOutOctets' => 'TIMETRA-VRTR-MIB::vRtrIfTxBytes',
    'ifHCInUcastPkts' => 'TIMETRA-VRTR-MIB::vRtrIfRxPkts',
    'ifHCOutUcastPkts' => 'TIMETRA-VRTR-MIB::vRtrIfTxPkts',
];

$timos_ports = [];
foreach ($timos_stats as $index => $value) {
    foreach ($translate as $ifEntry => $ifVrtrEntry) {
        if (isset($value[$ifVrtrEntry])) {
            $timos_ports[$index][$ifEntry] = $value[$ifVrtrEntry];
        }
    }
    if (empty($timos_ports[$index]['ifDescr'])) {
        $timos_ports[$index]['ifDescr'] = $timos_ports[$index]['ifName'];
    }
}
$port_stats = array_replace_recursive($timos_ports, $port_stats);
unset($timos_ports);
