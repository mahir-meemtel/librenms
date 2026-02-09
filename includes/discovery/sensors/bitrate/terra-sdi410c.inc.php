<?php
$divisor = 1;
$multiplier = 1000;

$oids = SnmpQuery::hideMib()->walk('TERRA-sdi410C-MIB::sdi410cstatus')->table(1);

if (is_array($oids)) {
    d_echo('Terra sdi410C Bitrates');
    for ($streamid = 1; $streamid <= 25; $streamid++) {
        $br = $oids[0]['outBr' . $streamid];
        if ($br) {
            $oid = '.1.3.6.1.4.1.30631.1.8.1.' . (1 + $streamid) . '.1.0';
            $type = 'terra_brout';
            $descr = 'Out# ' . sprintf('%02d', $streamid);
            $limit = 50 * 1000 * 1000; // 50 mbit/s
            $limitwarn = 49 * 1000 * 1000; // 49 mbit/s
            $lowwarnlimit = 1 * 1000 * 1000; // 1 mbit/s
            $lowlimit = 100 * 1000; // 100 kbit/s
            $value = $br * $multiplier;

            app('sensor-discovery')->discover(new \App\Models\Sensor([
                'poller_type' => 'snmp',
                'sensor_class' => 'bitrate',
                'sensor_oid' => $oid,
                'sensor_index' => $streamid,
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
                'group' => 'Streams',
            ]));
        }
    }
}
