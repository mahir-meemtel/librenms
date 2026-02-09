<?php
if (strpos($device['sysObjectID'], '.1.3.6.1.4.1.7483.1.3.1.12') !== false) {
    d_echo('Nokia PSD DDM Current Sensors\n');
    $ifIndexToName = SnmpQuery::cache()->walk('IF-MIB::ifName')->pluck();
    $ifAdminStatus = SnmpQuery::cache()->enumStrings()->walk('IF-MIB::ifAdminStatus')->pluck();
    $ifDdmValues = SnmpQuery::cache()->hideMib()->walk('TROPIC-PSD-MIB::tnPsdDdmDataValue')->table(2);

    foreach ($ifDdmValues as $ifIndex => $ddmvalue) {
        $ifName = $ifIndexToName[$ifIndex] ?? $ifIndex;
        if (! empty($ddmvalue['ddmLaserBiasCurrent']['tnPsdDdmDataValue']) && $ifAdminStatus[$ifIndex] == 'up') {
            $divisor = 1000000;
            $descr = "$ifName Tx Bias";
            app('sensor-discovery')->discover(new \App\Models\Sensor([
                'poller_type' => 'snmp',
                'sensor_class' => 'current',
                'sensor_oid' => ".1.3.6.1.4.1.7483.2.2.7.3.1.4.1.2.$ifIndex.3",
                'sensor_index' => "$ifIndex.3",
                'sensor_type' => 'nokia-1830',
                'sensor_descr' => $descr,
                'sensor_divisor' => $divisor,
                'sensor_multiplier' => 1,
                'sensor_current' => $ddmvalue['ddmLaserBiasCurrent']['tnPsdDdmDataValue'] / $divisor,
                'entPhysicalIndex' => $ifIndex,
                'entPhysicalIndex_measured' => 'port',
                'group' => 'Transceivers',
            ]));
        }
    }
}   //  ************** End of Sensors for Nokia PSD **********
