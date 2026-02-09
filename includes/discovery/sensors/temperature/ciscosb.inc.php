<?php
$oids = SnmpQuery::cache()->hideMib()->walk('CISCOSB-PHY-MIB::rlPhyTestGetResult')->table(1);

$multiplier = 1;
$divisor = 1;

foreach ($oids as $index => $ciscosb_data) {
    foreach ($ciscosb_data as $key => $value) {
        if (! isset($value['rlPhyTestTableTransceiverTemp'])) {
            continue;
        }

        $oid = '.1.3.6.1.4.1.9.6.1.101.90.1.2.1.3.' . $index . '.5';
        $port = PortCache::getByIfIndex(preg_replace('/^\d+\./', '', $index), $device['device_id']);
        $descr = trim($port?->ifDescr . ' Module');
        $temperature = $value['rlPhyTestTableTransceiverTemp'];

        if (is_numeric($temperature) && ($value['rlPhyTestTableTransceiverSupply'] != 0)) {
            app('sensor-discovery')->discover(new \App\Models\Sensor([
                'poller_type' => 'snmp',
                'sensor_class' => 'temperature',
                'sensor_oid' => $oid,
                'sensor_index' => $index,
                'sensor_type' => 'rlPhyTestTableTransceiverTemp',
                'sensor_descr' => $descr,
                'sensor_divisor' => $divisor,
                'sensor_multiplier' => $multiplier,
                'sensor_limit_low' => null,
                'sensor_limit_low_warn' => null,
                'sensor_limit_warn' => null,
                'sensor_limit' => null,
                'sensor_current' => $temperature,
                'entPhysicalIndex' => $index,
                'entPhysicalIndex_measured' => 'ports',
                'user_func' => null,
                'group' => null,
            ]));
        }
    }
}
