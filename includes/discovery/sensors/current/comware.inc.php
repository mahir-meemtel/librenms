<?php
echo 'Comware ';

$multiplier = 1;
$divisor = 100000;
$divisor_alarm = 1000000;
$hh3cTransceiverInfoTable = SnmpQuery::cache()->enumStrings()->walk('HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverInfoTable')->table(1);
foreach ($hh3cTransceiverInfoTable as $index => $entry) {
    if (is_numeric($entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverBiasCurrent']) && $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverBiasCurrent'] != 2147483647 && isset($entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverDiagnostic'])) {
        $port = PortCache::getByIfIndex($index, $device['device_id']);
        if ($port?->ifAdminStatus != 'up') {
            continue;
        }

        $oid = '.1.3.6.1.4.1.25506.2.70.1.1.1.17.' . $index;
        $limit_low = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverBiasLoAlarm'] / $divisor_alarm;
        $warn_limit_low = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverBiasLoWarn'] / $divisor_alarm;
        $limit = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverBiasHiAlarm'] / $divisor_alarm;
        $warn_limit = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverBiasHiWarn'] / $divisor_alarm;
        $current = $entry['HH3C-TRANSCEIVER-INFO-MIB::hh3cTransceiverBiasCurrent'] / $divisor;
        $entPhysicalIndex = $index;
        $entPhysicalIndex_measured = 'ports';

        $descr = $port->getShortLabel() . ' Bias Current';
        discover_sensor(null, 'current', $device, $oid, 'bias-' . $index, 'comware', $descr, $divisor, $multiplier, $limit_low, $warn_limit_low, $warn_limit, $limit, $current, 'snmp', $entPhysicalIndex, $entPhysicalIndex_measured, group: 'transceiver');
    }
}
