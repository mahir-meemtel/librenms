<?php
$oids = SnmpQuery::hideMib()->walk([
    'MARVELL-POE-MIB::rlPethPsePortPowerLimit',
    'MARVELL-POE-MIB::rlPethPsePortOutputPower',
])->table(2);
$divisor = 1000;

foreach ($oids as $unit => $indexData) {
    foreach ($indexData as $ifIndex => $data) {
        if (isset($data['rlPethPsePortOutputPower'])) {
            $value = $data['rlPethPsePortOutputPower'] / $divisor;
            if ($value) {
                $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
                $descr = $port?->ifName;
                $index = $unit . '.' . $ifIndex;
                $oid = '.1.3.6.1.4.1.89.108.1.1.5.' . $index;

                app('sensor-discovery')->discover(new \App\Models\Sensor([
                    'poller_type' => 'snmp',
                    'sensor_class' => 'power',
                    'sensor_oid' => $oid,
                    'sensor_index' => 'Poe' . $index,
                    'sensor_type' => 'rlPethPsePortOutputPower',
                    'sensor_descr' => 'PoE-' . $descr,
                    'sensor_divisor' => $divisor,
                    'sensor_multiplier' => 1,
                    'sensor_limit_low' => 0,
                    'sensor_limit_low_warn' => 0,
                    'sensor_limit_warn' => isset($data['rlPethPsePortPowerLimit']) ? ($data['rlPethPsePortPowerLimit'] / $divisor) * 0.8 : null,
                    'sensor_limit' => isset($data['rlPethPsePortPowerLimit']) ? ($data['rlPethPsePortPowerLimit'] / $divisor) : null,
                    'sensor_current' => $value,
                    'entPhysicalIndex' => null,
                    'entPhysicalIndex_measured' => null,
                    'user_func' => null,
                    'group' => 'PoE',
                ]));
            }
        }
    }
}
