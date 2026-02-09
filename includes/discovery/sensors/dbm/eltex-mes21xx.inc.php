<?php
$divisor = 1000;
$multiplier = 1;

$oids = SnmpQuery::cache()->hideMib()->walk('RADLAN-PHY-MIB::rlPhyTestGetResult')->table(1);

foreach ($oids as $ifIndex => $data) {
    if (isset($data['rlPhyTestGetResult']['rlPhyTestTableTxOutput'])) {
        $value = $data['rlPhyTestGetResult']['rlPhyTestTableTxOutput'] / $divisor;
        $low_limit = $low_warn_limit = -15;
        $high_warn_limit = $high_limit = 0;
        $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
        $descr = $port?->ifName;
        $oid = '.1.3.6.1.4.1.89.90.1.2.1.3.' . $ifIndex . '.8';
        $entPhysicalIndex = $ifIndex;
        $entPhysicalIndex_measured = 'ports';

        app('sensor-discovery')->discover(new \App\Models\Sensor([
            'poller_type' => 'snmp',
            'sensor_class' => 'dbm',
            'sensor_oid' => $oid,
            'sensor_index' => 'txdbm' . $ifIndex,
            'sensor_type' => 'rlPhyTestTableTxOutput',
            'sensor_descr' => 'SfpTxdBm-' . $descr,
            'sensor_divisor' => $divisor,
            'sensor_multiplier' => $multiplier,
            'sensor_limit_low' => $low_limit,
            'sensor_limit_low_warn' => $low_warn_limit,
            'sensor_limit_warn' => $high_warn_limit,
            'sensor_limit' => $high_limit,
            'sensor_current' => $value,
            'entPhysicalIndex' => $ifIndex,
            'entPhysicalIndex_measured' => 'port',
            'user_func' => null,
            'group' => 'transceiver',
        ]));
    }

    if (isset($data['rlPhyTestGetResult']['rlPhyTestTableRxOpticalPower'])) {
        $value = $data['rlPhyTestGetResult']['rlPhyTestTableRxOpticalPower'] / $divisor;
        $low_limit = $low_warn_limit = -15;
        $high_warn_limit = $high_limit = 0;
        $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
        $descr = $port?->ifName;
        $oid = '.1.3.6.1.4.1.89.90.1.2.1.3.' . $ifIndex . '.9';

        app('sensor-discovery')->discover(new \App\Models\Sensor([
            'poller_type' => 'snmp',
            'sensor_class' => 'dbm',
            'sensor_oid' => $oid,
            'sensor_index' => 'rxdbm' . $ifIndex,
            'sensor_type' => 'rlPhyTestTableRxOpticalPower',
            'sensor_descr' => 'SfpRxdBm-' . $descr,
            'sensor_divisor' => $divisor,
            'sensor_multiplier' => $multiplier,
            'sensor_limit_low' => $low_limit,
            'sensor_limit_low_warn' => $low_warn_limit,
            'sensor_limit_warn' => $high_warn_limit,
            'sensor_limit' => $high_limit,
            'sensor_current' => $value,
            'entPhysicalIndex' => $ifIndex,
            'entPhysicalIndex_measured' => 'port',
            'user_func' => null,
            'group' => 'transceiver',
        ]));
    }
}
