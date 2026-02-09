<?php
$cxr_stats = snmpwalk_cache_oid($device, 'portTable', [], 'CXR-TS-MIB');
$cxr_stats = snmpwalk_cache_oid($device, 'portStatsTable', $cxr_stats, 'CXR-TS-MIB');

d_echo($cxr_stats);

//We'll create dummy ifIndexes to add the Serial Ports to the ObzoraNMS port view.
//These devices are showing only a few interfaces, 1000 seems a sufficient offset.

$offset = 1000;

foreach ($cxr_stats as $index => $serialport_stats) {
    $curIfIndex = $offset + $index;
    $port_stats[$curIfIndex]['ifDescr'] = "SerialPort$index";
    $port_stats[$curIfIndex]['ifType'] = 'rs232'; //rs232
    $port_stats[$curIfIndex]['ifName'] = "Serial$index";
    $port_stats[$curIfIndex]['ifInOctets'] = $serialport_stats['bytesReceiveFromV24'];
    $port_stats[$curIfIndex]['ifOutOctets'] = $serialport_stats['bytesSendToV24'];
    $port_stats[$curIfIndex]['ifSpeed'] = preg_replace('/[^0-9.]/', '', $serialport_stats['baudRate']);
    $port_stats[$curIfIndex]['ifAdminStatus'] = 'up';
    $port_stats[$curIfIndex]['ifOperStatus'] = 'up';
    $port_stats[$curIfIndex]['ifAlias'] = "Port $index, " . $serialport_stats['terminalType'] . ', ' . $serialport_stats['mode'] . ', ' . $serialport_stats['baudRate'] . ' ' . $serialport_stats['nbParStop'];
    if ($serialport_stats['aliasIpAddress'] != '0.0.0.0') {
        $port_stats[$curIfIndex]['ifAlias'] .= ', Alias IP: ' . $serialport_stats['aliasIpAddress'] . ':' . $serialport_stats['tcpPort'];
    }
    if ($serialport_stats['remoteIpAddress'] != '0.0.0.0') {
        $port_stats[$curIfIndex]['ifAlias'] .= ', Remote IP: ' . $serialport_stats['remoteIpAddress'] . ':' . $serialport_stats['remoteTcpPort'];
    }
}
