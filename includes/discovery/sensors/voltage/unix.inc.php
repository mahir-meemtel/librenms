<?php
use ObzoraNMS\Util\Oid;

$snmpData = SnmpQuery::cache()->hideMib()->walk('LM-SENSORS-MIB::lmSensors')->table(1);
if (! empty($snmpData)) {
    echo 'LM-SENSORS-MIB: ' . PHP_EOL;
    foreach ($snmpData as $lmData) {
        $type = 'lmVoltSensors';
        $divisor = 1000;

        if (! isset($lmData[$type . 'Index'])) {
            continue;
        }

        $index = $lmData[$type . 'Index'];
        $descr = $lmData[$type . 'Device'];
        $value = intval($lmData[$type . 'Value']) / $divisor;
        if (! empty($descr)) {
            $oid = Oid::of('LM-SENSORS-MIB::' . $type . 'Value.' . $index)->toNumeric();
            discover_sensor(null, 'voltage', $device, $oid, $index, 'lmsensors', $descr, $divisor, 1, null, null, null, null, $value, 'snmp', null, null, null, 'lmsensors');
        }
    }
}

$snmpData = SnmpQuery::cache()->hideMib()->walk('NET-SNMP-EXTEND-MIB::nsExtendOutLine."ups-nut"')->table(3);
if (! empty($snmpData)) {
    echo 'UPS-NUT-MIB: ' . PHP_EOL;
    $snmpData = array_shift($snmpData); //drop [ups-nut]
    $upsnut = [
        4 => ['descr' => 'Battery Voltage', 'LL' => 0, 'LW' => 0, 'W' => null, 'H' => 60],
        5 => ['descr' => 'Battery Nominal', 'LL' => 0, 'LW' => 0, 'W' => null, 'H' => 60],
        6 => ['descr' => 'Line Nominal', 'LL' => null, 'LW' => null, 'W' => null, 'H' => null],
        7 => ['descr' => 'Input Voltage', 'LL' => 200, 'LW' => 0, 'W' => null, 'H' => 280],
    ];
    foreach ($snmpData as $index => $upsData) {
        if (isset($upsnut[$index])) {
            $value = $upsData['nsExtendOutLine'];
            if (is_numeric($value)) {
                $oid = Oid::of('NET-SNMP-EXTEND-MIB::nsExtendOutLine."ups-nut".' . $index)->toNumeric();
                discover_sensor(
                    null,
                    'voltage',
                    $device,
                    $oid,
                    $index,
                    'ups-nut',
                    $upsnut[$index]['descr'],
                    1,
                    1,
                    $upsnut[$index]['LL'],
                    $upsnut[$index]['LW'],
                    $upsnut[$index]['W'],
                    $upsnut[$index]['H'],
                    $value,
                    'snmp',
                    null,
                    null,
                    null,
                    'ups-nut'
                );
            }
        }
    }
}
