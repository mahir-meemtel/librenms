<?php
echo 'CradlePoint WiPipe';

$multiplier = 1;
$divisor = 1;

foreach ($pre_cache['wipipe_oids'] as $index => $entry) {
    // Modem Signal Strength
    if ($entry['mdmSignalStrength']) {
        $oid = '.1.3.6.1.4.1.20992.1.2.2.1.4.' . $index;
        // Get Modem Model & Phone Number for description
        $modemdesc = $entry['mdmDescr'];
        $modemmdn = $entry['mdmMDN'];
        $descr = 'Signal Strength - ' . $modemdesc . ' - ' . $modemmdn;
        $currentsignal = $entry['mdmSignalStrength'];
        // Discover Sensor
        discover_sensor(
            null,
            'dbm',
            $device,
            $oid,
            'mdmSignalStrength.' . $index,
            'wipipe',
            $descr,
            $divisor,
            $multiplier,
            null,
            null,
            null,
            null,
            $currentsignal,
            'snmp'
        );
    }
}
