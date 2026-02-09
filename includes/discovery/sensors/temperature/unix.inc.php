<?php
use ObzoraNMS\Util\Oid;

$snmpData = SnmpQuery::cache()->hideMib()->walk('LM-SENSORS-MIB::lmSensors')->table(1);
if (! empty($snmpData)) {
    echo 'LM-SENSORS-MIB: ' . PHP_EOL;
    foreach ($snmpData as $lmData) {
        $type = 'lmTempSensors';
        $divisor = 1000;

        if (! isset($lmData[$type . 'Index'])) {
            continue;
        }

        $index = $lmData[$type . 'Index'];
        $descr = $lmData[$type . 'Device'];
        $value = intval($lmData[$type . 'Value']) / $divisor;
        if (! empty($descr)) {
            $oid = Oid::of('LM-SENSORS-MIB::' . $type . 'Value.' . $index)->toNumeric();
            discover_sensor(null, 'temperature', $device, $oid, $index, 'lmsensors', $descr, $divisor, 1, null, null, null, null, $value, 'snmp', null, null, null, 'lmsensors');
        }
    }
}

$snmpData = SnmpQuery::cache()->hideMib()->walk('NET-SNMP-EXTEND-MIB::nsExtendOutLine."ups-nut"')->table(3);
if (! empty($snmpData)) {
    echo 'UPS-NUT-MIB: ' . PHP_EOL;
    $snmpData = array_shift($snmpData); //drop [ups-nut]
    $upsnut = [
        24 => ['descr' => 'Battery Temperature', 'LL' => 10, 'LW' => 15, 'W' => 35, 'H' => 40],
    ];
    foreach ($snmpData as $index => $upsData) {
        if (isset($upsnut[$index])) {
            $value = intval($upsData['nsExtendOutLine']);
            if (! empty($value)) {
                $oid = Oid::of('NET-SNMP-EXTEND-MIB::nsExtendOutLine."ups-nut".' . $index)->toNumeric();
                discover_sensor(
                    null,
                    'temperature',
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
