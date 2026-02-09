<?php
$oids = SnmpQuery::walk([
    'DOCS-IF-MIB::docsIfSignalQualityTable',
])->table(1);

foreach ($oids as $index => $data) {
    if (is_numeric($data['DOCS-IF-MIB::docsIfSigQSignalNoise'])) {
        $port = PortCache::getByIfIndex($index, $device['device_id']);
        $descr = 'Channel ' . $port?->ifAlias . ' - ' . $port?->ifName;
        $oid = '.1.3.6.1.2.1.10.127.1.1.4.1.5.' . $index;
        $divisor = 10;
        $value = $data['DOCS-IF-MIB::docsIfSigQSignalNoise'];
        if (preg_match('/.0$/', $port?->ifName)) {
            app('sensor-discovery')->discover(new \App\Models\Sensor([
                'poller_type' => 'snmp',
                'sensor_class' => 'snr',
                'sensor_oid' => $oid,
                'sensor_index' => 'docsIfSigQSignalNoise.' . $index,
                'sensor_type' => 'cmts',
                'sensor_descr' => $descr,
                'sensor_divisor' => $divisor,
                'sensor_multiplier' => 1,
                'sensor_limit_low' => null,
                'sensor_limit_low_warn' => null,
                'sensor_limit_warn' => null,
                'sensor_limit' => null,
                'sensor_current' => $value,
                'entPhysicalIndex' => null,
                'entPhysicalIndex_measured' => null,
                'user_func' => null,
                'group' => null,
            ]));
        }
    }
}
