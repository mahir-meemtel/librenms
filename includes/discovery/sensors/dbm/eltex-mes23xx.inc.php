<?php
$divisor = 1000;
$multiplier = 1;

$oids = SnmpQuery::cache()->hideMib()->walk('ELTEX-MES-PHYSICAL-DESCRIPTION-MIB::eltPhdTransceiverThresholdTable')->table(2);
$oids = SnmpQuery::cache()->hideMib()->walk('RADLAN-PHY-MIB::rlPhyTestGetResult')->table(1, $oids);

foreach ($oids as $ifIndex => $data) {
    if (isset($data['rlPhyTestGetResult']['rlPhyTestTableTxOutput'])) {
        $value = $data['rlPhyTestGetResult']['rlPhyTestTableTxOutput'] / $divisor;
        $high_limit = $data['txOutput']['eltPhdTransceiverThresholdHighAlarm'] / $divisor;
        $high_warn_limit = $data['txOutput']['eltPhdTransceiverThresholdHighWarning'] / $divisor;
        $low_warn_limit = $data['txOutput']['eltPhdTransceiverThresholdLowWarning'] / $divisor;
        $low_limit = $data['txOutput']['eltPhdTransceiverThresholdLowAlarm'] / $divisor;
        $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
        $descr = $port?->ifName;
        $oid = '.1.3.6.1.4.1.89.90.1.2.1.3.' . $ifIndex . '.8';
        $entPhysicalIndex = $ifIndex;
        $entPhysicalIndex_measured = 'ports';

        app('sensor-discovery')->discover(new \App\Models\Sensor([
            'poller_type' => 'snmp',
            'sensor_class' => 'dbm',
            'sensor_oid' => $oid,
            'sensor_index' => 'SfpTxDbm' . $ifIndex,
            'sensor_type' => 'rlPhyTestTableTxOpticalPower',
            'sensor_descr' => 'SfpTxDbm-' . $descr,
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
        $high_limit = $data['rxOpticalPower']['eltPhdTransceiverThresholdHighAlarm'] / $divisor;
        $high_warn_limit = $data['rxOpticalPower']['eltPhdTransceiverThresholdHighWarning'] / $divisor;
        $low_warn_limit = $data['rxOpticalPower']['eltPhdTransceiverThresholdLowWarning'] / $divisor;
        $low_limit = $data['rxOpticalPower']['eltPhdTransceiverThresholdLowAlarm'] / $divisor;
        $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
        $descr = $port?->ifName;
        $oid = '.1.3.6.1.4.1.89.90.1.2.1.3.' . $ifIndex . '.9';

        app('sensor-discovery')->discover(new \App\Models\Sensor([
            'poller_type' => 'snmp',
            'sensor_class' => 'dbm',
            'sensor_oid' => $oid,
            'sensor_index' => 'SfpRxDbm' . $ifIndex,
            'sensor_type' => 'rlPhyTestTableRxOpticalPower',
            'sensor_descr' => 'SfpRxDbm-' . $descr,
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
