<?php
$divisor = 1000000;
$multiplier = 1;

$oids = SnmpQuery::cache()->hideMib()->walk('ELTEX-MES-PHYSICAL-DESCRIPTION-MIB::eltPhdTransceiverThresholdTable')->table(2);
$oids = SnmpQuery::cache()->hideMib()->walk('RADLAN-PHY-MIB::rlPhyTestGetResult')->table(1, $oids);

foreach ($oids as $ifIndex => $data) {
    if (isset($data['rlPhyTestGetResult']['rlPhyTestTableTxBias'])) {
        $value = $data['rlPhyTestGetResult']['rlPhyTestTableTxBias'] / $divisor;
        $high_limit = $data['txBias']['eltPhdTransceiverThresholdHighAlarm'] / $divisor;
        $high_warn_limit = $data['txBias']['eltPhdTransceiverThresholdHighWarning'] / $divisor;
        $low_warn_limit = $data['txBias']['eltPhdTransceiverThresholdLowWarning'] / $divisor;
        $low_limit = $data['txBias']['eltPhdTransceiverThresholdLowAlarm'] / $divisor;
        $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
        $descr = $port?->ifName;
        $oid = '.1.3.6.1.4.1.89.90.1.2.1.3.' . $ifIndex . '.7';

        app('sensor-discovery')->discover(new \App\Models\Sensor([
            'poller_type' => 'snmp',
            'sensor_class' => 'current',
            'sensor_oid' => $oid,
            'sensor_index' => 'SfpTxBias' . $ifIndex,
            'sensor_type' => 'rlPhyTestTableTxBias',
            'sensor_descr' => 'SfpTxBias-' . $descr,
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
