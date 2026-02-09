<?php
$multiplier = 1;
$divisor = 1000;

if (is_array($pre_cache['adva_fsp3kr7_Card'])) {
    foreach (array_keys($pre_cache['adva_fsp3kr7_Card']) as $index) {
        if ($pre_cache['adva_fsp3kr7_Card'][$index]['eqptPhysInstValuePsuVoltInp']) {
            $oid = '.1.3.6.1.4.1.2544.1.11.11.1.2.1.1.1.7.' . $index;
            $descr = strtoupper($pre_cache['adva_fsp3kr7_Card'][$index]['entityEqptAidString']) . ' Input';
            $rrd_filename = $descr;
            $current = $pre_cache['adva_fsp3kr7_Card'][$index]['eqptPhysInstValuePsuVoltInp'] / $divisor;

            discover_sensor(
                null,
                'voltage',
                $device,
                $oid,
                'eqptPhysInstValuePsuVoltInp' . $index,
                'adva_fsp3kr7',
                $descr,
                $divisor,
                $multiplier,
                null,
                null,
                null,
                null,
                $current
            );
        }
    }
}// ******** End If of FSP3000 R7
