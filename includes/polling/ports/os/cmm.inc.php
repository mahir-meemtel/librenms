<?php
$cmm_stats = SnmpQuery::hideMib()->abortOnFailure()->walk([
    'CMM3-MIB::cmmSwitchTable',
    'CMM3-MIB::cmmPortTable',
])->table(1);

$required = [
    'ifInOctets' => 'rxOctets',
    'ifOutOctets' => 'txOctets',
    'ifInUcastPkts' => 'rxUnicastPkts',
    'ifOutUcastPkts' => 'txUnicastPkts',
    'ifInErrors' => 'rxDropPkts',
    'ifOutErrors' => 'txDropPkts',
    'ifInBroadcastPkts' => 'rxBroadcastPkts',
    'ifOutBroadcastPkts' => 'txBroadcastPkts',
    'ifInMulticastPkts' => 'rxMulticastPkts',
    'ifOutMulticastPkts' => 'txMulticastPkts',
];

$cmm_ports = [];
foreach ($cmm_stats as $cmm_stat) {
    $cmm_port = array_map(function ($IfxStat) use ($cmm_stat) {
        return $cmm_stat[$IfxStat];
    }, $required);

    $cmm_port['ifName'] = 'CMM Port ' . $cmm_stat['portNumber'];
    $cmm_port['ifDescr'] = 'CMM Port ' . $cmm_stat['portNumber'];
    $cmm_port['ifType'] = 'ethernetCsmacd';

    if (isset($cmm_stat['duplexStatus'])) {
        $cmm_port['ifDuplex'] = ($cmm_stat['duplexStatus'] == 1 ? 'fullDuplex' : 'halfDuplex');
    }
    if (isset($cmm_stat['linkSpeed'])) {
        $cmm_port['ifSpeed'] = ($cmm_stat['linkSpeed'] == 1 ? '100000000' : '10000000');
    }
    if (isset($cmm_stat['linkStatus'])) {
        $cmm_port['ifOperStatus'] = ($cmm_stat['linkStatus'] == 1 ? 'up' : 'down');
    }

    $cmm_ports[] = $cmm_port;
}

$port_stats = array_replace_recursive($cmm_ports, $port_stats);

unset($cmm_stats, $cmm_ports, $cmm_stat, $cmm_port, $required);
