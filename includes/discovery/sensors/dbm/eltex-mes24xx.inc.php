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

$divisor = 1000;
$multiplier = 1;

if (! empty($eltexPhyTransceiverDiagnosticTable['txOpticalPower'])) {
    foreach ($eltexPhyTransceiverDiagnosticTable['txOpticalPower'] as $ifIndex => $data) {
        $value = $data['eltexPhyTransceiverDiagnosticCurrentValue'] / $divisor;
        if ($value) {
            $high_limit = $data['eltexPhyTransceiverDiagnosticLowAlarmThreshold'] / -$divisor;
            $high_warn_limit = $data['eltexPhyTransceiverDiagnosticLowWarningThreshold'] / -$divisor;
            $low_warn_limit = $data['eltexPhyTransceiverDiagnosticHighWarningThreshold'] / -$divisor;
            $low_limit = $data['eltexPhyTransceiverDiagnosticHighAlarmThreshold'] / -$divisor;
            $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
            $descr = $port?->ifName;
            $oid = Oid::of('ELTEX-PHY-MIB::eltexPhyTransceiverDiagnosticCurrentValue.' . $ifIndex . '.4.1')->toNumeric();

            app('sensor-discovery')->discover(new \App\Models\Sensor([
                'poller_type' => 'snmp',
                'sensor_class' => 'dbm',
                'sensor_oid' => $oid,
                'sensor_index' => 'SfpTxDbm' . $ifIndex,
                'sensor_type' => 'ELTEX-PHY-MIB',
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
    }
}

if (! empty($eltexPhyTransceiverDiagnosticTable['rxOpticalPower'])) {
    foreach ($eltexPhyTransceiverDiagnosticTable['rxOpticalPower'] as $ifIndex => $data) {
        $value = $data['eltexPhyTransceiverDiagnosticCurrentValue'] / $divisor;
        if ($value) {
            $high_limit = $data['eltexPhyTransceiverDiagnosticLowAlarmThreshold'] / -$divisor;
            $high_warn_limit = $data['eltexPhyTransceiverDiagnosticLowWarningThreshold'] / -$divisor;
            $low_warn_limit = $data['eltexPhyTransceiverDiagnosticHighWarningThreshold'] / -$divisor;
            $low_limit = $data['eltexPhyTransceiverDiagnosticHighAlarmThreshold'] / -$divisor;
            $port = PortCache::getByIfIndex($ifIndex, $device['device_id']);
            $descr = $port?->ifName;
            $oid = Oid::of('ELTEX-PHY-MIB::eltexPhyTransceiverDiagnosticCurrentValue.' . $ifIndex . '.5.1')->toNumeric();

            app('sensor-discovery')->discover(new \App\Models\Sensor([
                'poller_type' => 'snmp',
                'sensor_class' => 'dbm',
                'sensor_oid' => $oid,
                'sensor_index' => 'SfpRxDbm' . $ifIndex,
                'sensor_type' => 'ELTEX-PHY-MIB',
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
}
