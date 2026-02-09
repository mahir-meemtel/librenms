<?php
use ObzoraNMS\Util\Oid;

$snmpData = SnmpQuery::cache()->hideMib()->walk('NET-SNMP-EXTEND-MIB::nsExtendOutLine."ups-nut"')->table(3);
if (! empty($snmpData)) {
    echo 'UPS-NUT-MIB: ' . PHP_EOL;
    $snmpData = array_shift($snmpData); //drop [ups-nut]
    $upsnut = [
        8 => ['descr' => 'Ups Load', 'LL' => 0, 'LW' => 0, 'W' => null, 'H' => 100],
    ];
    foreach ($snmpData as $index => $upsData) {
        if (isset($upsnut[$index])) {
            $value = intval($upsData['nsExtendOutLine']);
            if (! empty($value)) {
                $oid = Oid::of('NET-SNMP-EXTEND-MIB::nsExtendOutLine."ups-nut".' . $index)->toNumeric();
                discover_sensor(
                    null,
                    'load',
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
