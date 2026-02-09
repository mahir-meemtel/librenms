<?php
use ObzoraNMS\Util\Oid;

$snmpData = SnmpQuery::cache()->hideMib()->walk('NET-SNMP-EXTEND-MIB::nsExtendOutLine."ups-nut"')->table(3);
if (! empty($snmpData)) {
    echo 'UPS-NUT-MIB: ' . PHP_EOL;
    $snmpData = array_shift($snmpData); //drop [ups-nut]
    $upsnut = [
        1 => ['descr' => 'Battery Charge', 'LL' => 0, 'LW' => 10, 'W' => null, 'H' => null],
    ];
    foreach ($snmpData as $index => $upsData) {
        if (isset($upsnut[$index])) {
            $value = intval($upsData['nsExtendOutLine']);
            if (! empty($value)) {
                $oid = Oid::of('NET-SNMP-EXTEND-MIB::nsExtendOutLine."ups-nut".' . $index)->toNumeric();
                discover_sensor(
                    null,
                    'charge',
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
