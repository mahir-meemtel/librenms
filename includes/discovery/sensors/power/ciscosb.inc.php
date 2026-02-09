<?php
$oids = SnmpQuery::cache()->hideMib()->walk('CISCOSB-POE-MIB::rlPethPsePortTable')->table(2);

$divisor = '1000';
foreach ($oids as $unit => $unitData) {
    foreach ($unitData as $ifIndex => $data) {
        if (isset($data['rlPethPsePortOutputPower'])) {
            $value = $data['rlPethPsePortOutputPower'] / $divisor;
            if ($value) {
                $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
                $descr = $port?->ifDescr . ' PoE';
                $index = $unit . '.' . $ifIndex;
                $oid = '.1.3.6.1.4.1.9.6.1.101.108.1.1.5.' . $index;

                app('sensor-discovery')->discover(new \App\Models\Sensor([
                    'poller_type' => 'snmp',
                    'sensor_class' => 'power',
                    'sensor_oid' => $oid,
                    'sensor_index' => $index,
                    'sensor_type' => 'ciscosb',
                    'sensor_descr' => $descr,
                    'sensor_divisor' => $divisor,
                    'sensor_multiplier' => 1,
                    'sensor_limit_low' => null,
                    'sensor_limit_low_warn' => null,
                    'sensor_limit_warn' => null,
                    'sensor_limit' => isset($data['rlPethPsePortOperPowerLimit']) ? ($data['rlPethPsePortOperPowerLimit'] / $divisor) : null,
                    'sensor_current' => $value,
                    'entPhysicalIndex' => $index,
                    'entPhysicalIndex_measured' => null,
                    'user_func' => null,
                    'group' => null,
                ]));
            }
        }
    }
}
