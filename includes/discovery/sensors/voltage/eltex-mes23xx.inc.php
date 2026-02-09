<?php
$divisor = 1000000;
$multiplier = 1;

$oids = SnmpQuery::cache()->hideMib()->walk('ELTEX-MES-PHYSICAL-DESCRIPTION-MIB::eltPhdTransceiverThresholdTable')->table(2);
$oids = SnmpQuery::cache()->hideMib()->walk('RADLAN-PHY-MIB::rlPhyTestGetResult')->table(1, $oids);

foreach ($oids as $ifIndex => $data) {
    if (isset($data['rlPhyTestGetResult']['rlPhyTestTableTransceiverSupply'])) {
        $value = $data['rlPhyTestGetResult']['rlPhyTestTableTransceiverSupply'] / $divisor;
        $high_limit = $data['supply']['eltPhdTransceiverThresholdHighAlarm'] / $divisor;
        $high_warn_limit = $data['supply']['eltPhdTransceiverThresholdHighWarning'] / $divisor;
        $low_warn_limit = $data['supply']['eltPhdTransceiverThresholdLowWarning'] / $divisor;
        $low_limit = $data['supply']['eltPhdTransceiverThresholdLowAlarm'] / $divisor;
        $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
        $descr = $port?->ifName;
        $oid = '.1.3.6.1.4.1.89.90.1.2.1.3.' . $ifIndex . '.6';

        app('sensor-discovery')->discover(new \App\Models\Sensor([
            'poller_type' => 'snmp',
            'sensor_class' => 'voltage',
            'sensor_oid' => $oid,
            'sensor_index' => 'SfpVolt' . $ifIndex,
            'sensor_type' => 'rlPhyTestTableTransceiverSupply',
            'sensor_descr' => 'SfpVolt-' . $descr,
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
