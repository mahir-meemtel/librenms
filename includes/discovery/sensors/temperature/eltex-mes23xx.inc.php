<?php
$divisor = 1;
$multiplier = 1;

$oids = SnmpQuery::cache()->hideMib()->walk('ELTEX-MES-PHYSICAL-DESCRIPTION-MIB::eltPhdTransceiverThresholdTable')->table(2);
$oids = SnmpQuery::cache()->hideMib()->walk('RADLAN-PHY-MIB::rlPhyTestGetResult')->table(1, $oids);

foreach ($oids as $ifIndex => $data) {
    if (isset($data['rlPhyTestGetResult']['rlPhyTestTableTransceiverTemp'])) {
        $value = $data['rlPhyTestGetResult']['rlPhyTestTableTransceiverTemp'] / $divisor;
        $high_limit = $data['temperature']['eltPhdTransceiverThresholdHighAlarm'] / $divisor;
        $high_warn_limit = $data['temperature']['eltPhdTransceiverThresholdHighWarning'] / $divisor;
        $low_warn_limit = $data['temperature']['eltPhdTransceiverThresholdLowWarning'] / $divisor;
        $low_limit = $data['temperature']['eltPhdTransceiverThresholdLowAlarm'] / $divisor;
        $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
        $descr = $port?->ifName;
        $oid = '.1.3.6.1.4.1.89.90.1.2.1.3.' . $ifIndex . '.5';

        app('sensor-discovery')->discover(new \App\Models\Sensor([
            'poller_type' => 'snmp',
            'sensor_class' => 'temperature',
            'sensor_oid' => $oid,
            'sensor_index' => 'SfpTemp' . $ifIndex,
            'sensor_type' => 'rlPhyTestTableTransceiverTemp',
            'sensor_descr' => 'SfpTemp-' . $descr,
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
