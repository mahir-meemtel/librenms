<?php
echo 'Comware ';

$multiplier = 1;
$divisor = 100;
$divisor_alarm = 10000;
$hh3cTransceiverInfoTable = SnmpQuery::cache()->enumStrings()->walk('HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverInfoTable')->table(1);
foreach ($hh3cTransceiverInfoTable as $index => $entry) {
    if (is_numeric($entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverVoltage']) && $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverVoltage'] != 2147483647 && isset($entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverDiagnostic'])) {
        $port = PortCache::getByIfIndex($index, $device['device_id']);
        if ($port?->ifAdminStatus != 'up') {
            continue;
        }

        $oid = '.1.3.6.1.4.1.25506.2.70.1.1.1.16.' . $index;
        $limit_low = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverVccLoAlarm'] / $divisor_alarm;
        $warn_limit_low = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverVccLoWarn'] / $divisor_alarm;
        $limit = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverVccHiAlarm'] / $divisor_alarm;
        $warn_limit = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverVccHiWarn'] / $divisor_alarm;
        $current = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverVoltage'] / $divisor;
        $entPhysicalIndex = $index;
        $entPhysicalIndex_measured = 'ports';

        $descr = $port->getShortLabel() . ' Supply Voltage';
        discover_sensor(null, 'voltage', $device, $oid, 'volt-' . $index, 'comware', $descr, $divisor, $multiplier, $limit_low, $warn_limit_low, $warn_limit, $limit, $current, 'snmp', $entPhysicalIndex, $entPhysicalIndex_measured, group: 'transceiver');
    }
}
