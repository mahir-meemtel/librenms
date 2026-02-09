<?php
$divisor = 10000;
$multiplier = 1;

$oids = SnmpQuery::cache()->hideMib()->walk('TERRA-sdi480-MIB::sdi480status')->table(1);

if (is_array($oids)) {
    d_echo('Terra sdi480 tv_signal');
    for ($inputid = 1; $inputid <= 8; $inputid++) {
        $signal = $oids[0]['inlevel' . $inputid];
        if ($signal) {
            $oid = '.1.3.6.1.4.1.30631.1.17.1.' . $inputid . '.2.0';
            $type = 'terra_tvsignal';
            $descr = 'Level# ' . sprintf('%02d', $inputid);
            $limit = 0.085;
            $limitwarn = 0.080;
            $lowwarnlimit = 0.050;
            $lowlimit = 0.045;
            $value = $signal / $divisor;

            app('sensor-discovery')->discover(new \App\Models\Sensor([
                'poller_type' => 'snmp',
                'sensor_class' => 'tv_signal',
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
