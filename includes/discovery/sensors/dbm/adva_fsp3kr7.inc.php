<?php
$multiplier = 1;
$divisor = 10;

foreach ($pre_cache['adva_fsp3kr7'] as $index => $entry) {
    if ($entry['entityFacilityAidString'] and $entry['pmSnapshotCurrentInputPower']) {
        $oidRX = '.1.3.6.1.4.1.2544.1.11.7.7.2.3.1.2.' . $index;
        $descr = $entry['entityFacilityAidString'] . ' RX';
        $currentRX = $entry['pmSnapshotCurrentInputPower'] / $divisor;
        $descr = $entry['entityFacilityAidString'] . ' RX';

        discover_sensor(
            null,
            'dbm',
            $device,
            $oidRX,
            'pmSnapshotCurrentInputPower' . $index,
            'adva_fsp3kr7',
            $descr,
            $divisor,
            $multiplier,
            null,
            null,
            null,
            null,
            $currentRX
        );
    }//End if Input Power

    if ($entry['entityFacilityAidString'] and $entry['pmSnapshotCurrentOutputPower']) {
        $oidTX = '.1.3.6.1.4.1.2544.1.11.7.7.2.3.1.1.' . $index;
        $descr = $entry['entityFacilityAidString'] . ' TX';
        $currentTX = $entry['pmSnapshotCurrentOutputPower'] / $divisor;

        discover_sensor(
            null,
            'dbm',
            $device,
            $oidTX,
            'pmSnapshotCurrentOutputPower' . $index,
            'adva_fsp3kr7',
            $descr,
            $divisor,
            $multiplier,
            null,
            null,
            null,
            null,
            $currentTX
        );
    }//End if Output Power
}//End foreach entry
unset($entry);
//********* End of ADVA FSP3000 R7 Series
