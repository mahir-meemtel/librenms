<?php
echo 'Adva FSP-150 dBm';

$multiplier = 1;
$divisor = 1;

// Adva Network Port dBm
foreach ($pre_cache['adva_fsp150_ports'] as $index => $entry) {
    if (isset($entry['cmEthernetNetPortMediaType']) && $entry['cmEthernetNetPortMediaType'] == 'fiber') {
        // Discover receive power level
        $oidRx = '.1.3.6.1.4.1.2544.1.12.5.1.5.1.34.' . $index . '.3';
        $oidTx = '.1.3.6.1.4.1.2544.1.12.5.1.5.1.33.' . $index . '.3';
        $currentRx = $pre_cache['adva_fsp150_perfs'][$index . '.3']['cmEthernetNetPortStatsOPR'];
        $currentTx = $pre_cache['adva_fsp150_perfs'][$index . '.3']['cmEthernetNetPortStatsOPT'];

        if ($currentRx != 0 || $currentTx != 0) {
            $entPhysicalIndex = $entry['cmEthernetNetPortIfIndex'];
            $entPhysicalIndex_measured = 'ports';
            $descrRx = ($pre_cache['adva_fsp150_ifName'][$entry['cmEthernetNetPortIfIndex']]['ifName'] ?? 'ifIndex ' . $entry['cmEthernetNetPortIfIndex']) . ' Rx Power';
            discover_sensor(
                null,
                'dbm',
                $device,
                $oidRx,
                'cmEthernetNetPortStatsOPR.' . $index,
                'adva_fsp150',
                $descrRx,
                $divisor,
                $multiplier,
                null,
                null,
                null,
                null,
                $currentRx,
                'snmp',
                $entPhysicalIndex,
                $entPhysicalIndex_measured
            );

            // Discover transmit power level
            $descrTx = ($pre_cache['adva_fsp150_ifName'][$entry['cmEthernetNetPortIfIndex']]['ifName'] ?? 'ifIndex ' . $entry['cmEthernetNetPortIfIndex']) . ' Tx Power';
            discover_sensor(
                null,
                'dbm',
                $device,
                $oidTx,
                'cmEthernetNetPortStatsOPT.' . $index,
                'adva_fsp150',
                $descrTx,
                $divisor,
                $multiplier,
                null,
                null,
                null,
                null,
                $currentTx,
                'snmp',
                $entPhysicalIndex,
                $entPhysicalIndex_measured
            );
        }
    }

    // Adva Access Ports dBm
    if (isset($entry['cmEthernetAccPortMediaType']) && $entry['cmEthernetAccPortMediaType'] == 'fiber') {
        //Discover receive power level
        $oidRx = '.1.3.6.1.4.1.2544.1.12.5.1.1.1.34.' . $index . '.3';
        $oidTx = '.1.3.6.1.4.1.2544.1.12.5.1.1.1.33.' . $index . '.3';
        $currentRx = $pre_cache['adva_fsp150_perfs'][$index . '.3']['cmEthernetAccPortStatsOPR'];
        $currentTx = $pre_cache['adva_fsp150_perfs'][$index . '.3']['cmEthernetAccPortStatsOPT'];

        if ($currentRx != 0 || $currentTx != 0) {
            $entPhysicalIndex = $entry['cmEthernetAccPortIfIndex'];
            $entPhysicalIndex_measured = 'ports';
            $descrRx = ($pre_cache['adva_fsp150_ifName'][$entry['cmEthernetAccPortIfIndex']]['ifName'] ?? 'ifIndex ' . $entry['cmEthernetAccPortIfIndex']) . ' Rx Power';

            discover_sensor(
                null,
                'dbm',
                $device,
                $oidRx,
                'cmEthernetAccPortStatsOPR.' . $index,
                'adva_fsp150',
                $descrRx,
                $divisor,
                $multiplier,
                null,
                null,
                null,
                null,
                $currentRx,
                'snmp',
                $entPhysicalIndex,
                $entPhysicalIndex_measured
            );

            $descrTx = ($pre_cache['adva_fsp150_ifName'][$entry['cmEthernetAccPortIfIndex']]['ifName'] ?? 'ifIndex ' . $entry['cmEthernetAccPortIfIndex']) . ' Tx Power';

            discover_sensor(
                null,
                'dbm',
                $device,
                $oidTx,
                'cmEthernetAccPortStatsOPT.' . $index,
                'adva_fsp150',
                $descrTx,
                $divisor,
                $multiplier,
                null,
                null,
                null,
                null,
                $currentTx,
                'snmp',
                $entPhysicalIndex,
                $entPhysicalIndex_measured
            );
        }
    }

    // Adva Traffic Port dBm
    if (isset($entry['cmEthernetTrafficPortMediaType']) && $entry['cmEthernetTrafficPortMediaType'] == 'fiber') {
        //Discover receivn power level
        $oidRx = '.1.3.6.1.4.1.2544.1.12.5.1.21.1.34.' . $index . '.3';
        $oidTx = '.1.3.6.1.4.1.2544.1.12.5.1.21.1.33.' . $index . '.3';
        $currentRx = $pre_cache['adva_fsp150_perfs'][$index . '.3']['cmEthernetTrafficPortStatsOPR'];
        $currentTx = $pre_cache['adva_fsp150_perfs'][$index . '.3']['cmEthernetTrafficPortStatsOPT'];

        if ($currentRx != 0 || $currentTx != 0) {
            $entPhysicalIndex = $entry['cmEthernetTrafficPortIfIndex'];
            $entPhysicalIndex_measured = 'ports';
            $descrRx = ($pre_cache['adva_fsp150_ifName'][$entry['cmEthernetTrafficPortIfIndex']]['ifName'] ?? 'ifIndex ' . $entry['cmEthernetTrafficPortIfIndex']) . ' Rx Power';
            discover_sensor(
                null,
                'dbm',
                $device,
                $oidRx,
                'cmEthernetTrafficPortStatsOPR.' . $index,
                'adva_fsp150',
                $descrRx,
                $divisor,
                $multiplier,
                null,
                null,
                null,
                null,
                $currentRx,
                'snmp',
                $entPhysicalIndex,
                $entPhysicalIndex_measured
            );

            $descrTx = ($pre_cache['adva_fsp150_ifName'][$entry['cmEthernetTrafficPortIfIndex']]['ifName'] ?? 'ifIndex ' . $entry['cmEthernetTrafficPortIfIndex']) . ' Tx Power';
            discover_sensor(
                null,
                'dbm',
                $device,
                $oidTx,
                'cmEthernetTrafficPortStatsOPT.' . $index,
                'adva_fsp150',
                $descrTx,
                $divisor,
                $multiplier,
                null,
                null,
                null,
                null,
                $currentTx,
                'snmp',
                $entPhysicalIndex,
                $entPhysicalIndex_measured
            );
        }
    }
}

unset($entry);
