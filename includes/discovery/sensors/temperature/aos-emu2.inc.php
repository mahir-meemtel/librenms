<?php
$oids = SnmpQuery::cache()->walk([
    'PowerNet-MIB::emsProbeStatusEntry',
])->table(1);

$scale = SnmpQuery::enumStrings()->get('PowerNet-MIB::emsStatusSysTempUnits.0')->value();

foreach ($oids as $id => $temp) {
    if (isset($temp['PowerNet-MIB::emsProbeStatusProbeTemperature']) && $temp['PowerNet-MIB::emsProbeStatusProbeTemperature'] > 0) {
        $index = $temp['PowerNet-MIB::emsProbeStatusProbeIndex'];
        $oid = '.1.3.6.1.4.1.318.1.1.10.3.13.1.1.3.' . $index;
        $descr = $temp['PowerNet-MIB::emsProbeStatusProbeName'];
        $low_limit = fahrenheit_to_celsius($temp['PowerNet-MIB::emsProbeStatusProbeMinTempThresh'], $scale);
        $low_warn_limit = fahrenheit_to_celsius($temp['PowerNet-MIB::emsProbeStatusProbeLowTempThresh'], $scale);
        $high_limit = fahrenheit_to_celsius($temp['PowerNet-MIB::emsProbeStatusProbeMaxTempThresh'], $scale);
        $high_warn_limit = fahrenheit_to_celsius($temp['PowerNet-MIB::emsProbeStatusProbeHighTempThresh'], $scale);
        $value = fahrenheit_to_celsius($temp['PowerNet-MIB::emsProbeStatusProbeTemperature'], $scale);

        app('sensor-discovery')->discover(new \App\Models\Sensor([
            'poller_type' => 'snmp',
            'sensor_class' => 'temperature',
            'sensor_oid' => $oid,
            'sensor_index' => $index,
            'sensor_type' => 'aos-emu2',
            'sensor_descr' => $descr,
            'sensor_divisor' => 1,
            'sensor_multiplier' => 1,
            'sensor_limit_low' => $low_limit,
            'sensor_limit_low_warn' => $low_warn_limit,
            'sensor_limit_warn' => $high_warn_limit,
            'sensor_limit' => $high_limit,
            'sensor_current' => $value,
            'entPhysicalIndex' => null,
            'entPhysicalIndex_measured' => null,
            'user_func' => null,
            'group' => null,
        ]));
    }
}
