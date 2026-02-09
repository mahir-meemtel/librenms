<?php
$exa_stats = snmpwalk_cache_oid($device, 'fusionPortTable', [], 'EXALINK-FUSION-MIB');
unset($exa_stats[0]);

$obj_map = [
    'ifName' => 'fusionPortName',
    'ifAlias' => 'fusionPortAlias',
    'ifOperStatus' => 'fusionPortHasSignal',
    'ifAdminStatus' => 'fusionPortEnabled',
    'ifHighSpeed' => 'fusionPortSpeed',
    'ifHCInOctets' => 'fusionPortRXBytes',
    'ifHCOutOctets' => 'fusionPortTXBytes',
    'ifInErrors' => 'fusionPortRXErrors',
    'ifConnectorPresent' => 'fusionPortPresent',
];

// Rename these to use "up" and "down"
$tf_rename_map = [
    'fusionPortHasSignal',
    'fusionPortEnabled',
];
$orig_tf = ['true', 'false'];
$std_tf = ['up', 'down'];

// Only supports ethernet
$ifType = 'ethernetCsmacd';

foreach ($exa_stats as $name => $tmp_stats) {
    $e_name = explode('.', $name);
    $index = (((int) $e_name[0]) - 1) * 16 + (int) $e_name[1];
    $port_stats[$index] = [];
    $port_stats[$index]['ifName'] = $name;
    $port_stats[$index]['ifType'] = $ifType;
    foreach ($obj_map as $ifEntry => $IfxStat) {
        if (in_array($IfxStat, $tf_rename_map)) {
            $val = str_replace($orig_tf, $std_tf, $exa_stats[$name][$IfxStat]);
        } else {
            $val = $exa_stats[$name][$IfxStat];
        }
        $port_stats[$index][$ifEntry] = $val;
    }
    $port_stats[$index]['ifDescr'] = $port_stats[$index]['ifName'];
}
