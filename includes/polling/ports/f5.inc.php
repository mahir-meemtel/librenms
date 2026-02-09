<?php
$f5_stats = snmpwalk_cache_oid($device, 'sysIfxStat', [], 'F5-BIGIP-SYSTEM-MIB');
unset($f5_stats[0]);

$tmp_port_stats = [];
foreach ($ifmib_oids as $oid) {
    echo "$oid ";
    $tmp_port_stats = snmpwalk_cache_oid($device, $oid, $tmp_port_stats, 'IF-MIB', null, '-OQUst');
}

$required = [
    'ifName' => 'sysIfxStatName',
    'ifHighSpeed' => 'sysIfxStatHighSpeed',
    'ifHCInOctets' => 'sysIfxStatHcInOctets',
    'ifHCOutOctets' => 'sysIfxStatHcOutOctets',
    'ifHCInUcastPkts' => 'sysIfxStatHcInUcastPkts',
    'ifHCOutUcastPkts' => 'sysIfxStatHcOutUcastPkts',
    'ifHCInMulticastPkts' => 'sysIfxStatHcInMulticastPkts',
    'ifHCOutMulticastPkts' => 'sysIfxStatHcOutMulticastPkts',
    'ifHCInBroadcastPkts' => 'sysIfxStatHcInBroadcastPkts',
    'ifHCOutBroadcastPkts' => 'sysIfxStatHcOutBroadcastPkts',
    'ifConnectorPresent' => 'sysIfxStatConnectorPresent',
    'ifAlias' => 'sysIfxStatAlias',
];

foreach ($tmp_port_stats as $index => $tmp_stats) {
    $descr = $tmp_port_stats[$index]['ifDescr'];
    $port_stats[$index] = $tmp_stats;
    $port_stats[$index]['ifDescr'] = $tmp_stats['ifDescr'];
    foreach ($required as $ifEntry => $IfxStat) {
        if (! isset($f5_stats[$descr]) || ! isset($f5_stats[$descr][$IfxStat])) {
            continue;
        }
        $port_stats[$index][$ifEntry] = $f5_stats[$descr][$IfxStat];
    }
}
