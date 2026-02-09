<?php
use ObzoraNMS\Util\Oid;

echo 'eltexPhyTransceiverDiagnosticTable' . PHP_EOL;
$snmpData = SnmpQuery::cache()->hideMib()->walk('ELTEX-PHY-MIB::eltexPhyTransceiverDiagnosticTable')->table(3);
if (! empty($snmpData)) {
    foreach ($snmpData as $index => $typeData) {
        foreach ($typeData as $type => $data) {
            $eltexPhyTransceiverDiagnosticTable[$type][$index] = array_shift($data);
        }
    }
}

$divisor = 1;
$multiplier = 1;

if (isset($eltexPhyTransceiverDiagnosticTable['lossOfSignal'])) {
    //Create State Index
    $state_name = 'mes24xx_sfpLoss';
    $states = [
        ['value' => 0, 'generic' => 0, 'graph' => 1, 'descr' => 'false'],
        ['value' => 1, 'generic' => 2, 'graph' => 1, 'descr' => 'true'],
    ];
    create_state_index($state_name, $states);

    foreach ($eltexPhyTransceiverDiagnosticTable['lossOfSignal'] as $ifIndex => $data) {
        $value = $data['eltexPhyTransceiverDiagnosticCurrentValue'] / $divisor;
        $high_limit = 1;
        $high_warn_limit = null;
        $low_warn_limit = null;
        $low_limit = null;
        $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
        $descr = $port?->ifName;
        $oid = Oid::of('ELTEX-PHY-MIB::eltexPhyTransceiverDiagnosticCurrentValue.' . $ifIndex . '.6.1')->toNumeric();

        app('sensor-discovery')->discover(new \App\Models\Sensor([
            'poller_type' => 'snmp',
            'sensor_class' => 'state',
            'sensor_oid' => $oid,
            'sensor_index' => 'SfpLoss' . $ifIndex,
            'sensor_type' => 'ELTEX-PHY-MIB',
            'sensor_descr' => 'SfpLoss-' . $descr,
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
