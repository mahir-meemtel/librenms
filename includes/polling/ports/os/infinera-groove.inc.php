<?php
use ObzoraNMS\Util\Number;

echo 'Port types:';

foreach (['eth100g', 'eth40g', 'eth10g', 'fc16g', 'fc8g'] as $infineratype) {
    echo ' ' . $infineratype;
    preg_match('/[a-z]+(\d+)g/i', $infineratype, $matches);
    $infspeed = $matches[1];

    $cg_stats = snmpwalk_cache_multi_oid($device, $infineratype . 'Entry', [], 'CORIANT-GROOVE-MIB');
    $cg_stats = snmpwalk_cache_multi_oid($device, $infineratype . 'Statistics', $cg_stats, 'CORIANT-GROOVE-MIB');

    $required = [
        'ifAlias' => $infineratype . 'AliasName',
        'ifAdminStatus' => $infineratype . 'AdminStatus',
        'ifOperStatus' => $infineratype . 'OperStatus',
        'ifType' => 'Ethernet',
        'ifHCInBroadcastPkts' => $infineratype . 'StatisticsEntryInBroadcastPackets',
        'ifHCInMulticastPkts' => $infineratype . 'StatisticsEntryInMulticastPackets',
        'ifHCInOctets' => $infineratype . 'StatisticsEntryInOctets',
        'ifHCInUcastPkts' => $infineratype . 'StatisticsEntryInPackets',
        'ifHCOutBroadcastPkts' => $infineratype . 'StatisticsEntryOutBroadcastPackets',
        'ifHCOutMulticastPkts' => $infineratype . 'StatisticsEntryOutMulticastPackets',
        'ifHCOutOctets' => $infineratype . 'StatisticsEntryOutOctets',
        'ifHCOutUcastPkts' => $infineratype . 'StatisticsEntryOutPackets',
        'ifHighSpeed' => $infspeed * 1000,
    ];

    foreach ($cg_stats as $index => $tmp_stats) {
        $indexids = explode('.', $index);

        if (! isset($cg_stats[$index][$infineratype . 'AdminStatus'])) {
            continue;
        }

        // The CLI port name is not available in SNMP
        $descr = (strpos($infineratype, 'eth') === false) ? $infineratype : $infspeed . 'gbe';

        // 100g and 40g ports use shelfId, slotId, portId
        // 10g, fc16g and fc8g ports append the subportId with '.'
        $descr .= '-' . $indexids[0] . '/' . $indexids[1] . '/' . $indexids[3];

        if ($infspeed < 40) {
            $descr .= '.' . $indexids[4];
        }

        // obzora expects the index to be bigint(20) => we grab 3 decimal
        // spaces per indexid to make a numeric ifindex.  This is hacky.
        $lindex = '';
        for ($i = 0; $i <= 4; $i++) {
            $lindex .= sprintf('%03d', $indexids[$i]);
        }

        // convert to integer
        $lindex = Number::cast($lindex);

        $port_stats[$lindex]['ifName'] = $descr;
        $port_stats[$lindex]['ifDescr'] = $descr;

        foreach ($required as $normaloid => $infineraoid) {
            // this is a bit hacky
            if (preg_match('/^(eth|fc)\d+/i', $required[$normaloid])) {
                $port_stats[$lindex][$normaloid] = $cg_stats[$index][$infineraoid];
            } else {
                $port_stats[$lindex][$normaloid] = $required[$normaloid];
            }
        }
    }
}
