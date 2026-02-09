<?php
$divisor = 10;
$multiplier = 1;

$oids = SnmpQuery::cache()->hideMib()->walk('TERRA-sdi480-MIB::sdi480status')->table(1);

if (is_array($oids)) {
    d_echo('Terra sdi480 SNR');
    for ($inputid = 1; $inputid <= 8; $inputid++) {
        $snr = $oids[0]['insnr' . $inputid];
        if ($snr) {
            $oid = '.1.3.6.1.4.1.30631.1.17.1.' . $inputid . '.3.0';
            $type = 'terra_snr';
            $descr = 'SNR# ' . sprintf('%02d', $inputid);
            $limit = 30;
            $limitwarn = 30;
            $lowwarnlimit = 14;
            $lowlimit = 12;
            $value = $snr / $divisor;

            app('sensor-discovery')->discover(new \App\Models\Sensor([
                'poller_type' => 'snmp',
                'sensor_class' => 'snr',
                'sensor_oid' => $oid,
                'sensor_index' => $inputid,
                'sensor_type' => $type,
                'sensor_descr' => $descr,
                'sensor_divisor' => $divisor,
                'sensor_multiplier' => $multiplier,
                'sensor_limit_low' => $lowlimit,
                'sensor_limit_low_warn' => $lowwarnlimit,
                'sensor_limit_warn' => $limitwarn,
                'sensor_limit' => $limit,
                'sensor_current' => $value,
                'entPhysicalIndex' => null,
                'entPhysicalIndex_measured' => null,
                'user_func' => null,
                'group' => 'Inputs',
            ]));
        }
    }
}
