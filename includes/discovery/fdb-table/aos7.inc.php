<?php
$dot1d = snmpwalk_group($device, 'slMacAddressGblManagement', 'ALCATEL-IND1-MAC-ADDRESS-MIB', 0, [], 'nokia/aos7');
if (! empty($dot1d)) {
    echo 'AOS7+ MAC-ADDRESS-MIB:';
    $fdbPort_table = [];
    foreach ($dot1d['slMacAddressGblManagement'] as $slMacDomain => $data) {
        foreach ($data as $slLocaleType => $data2) {
            foreach ($data2 as $portLocal => $data3) {
                foreach ($data3 as $vlanLocal => $data4) {
                    if (! isset($fdbPort_table[$vlanLocal]['dot1qTpFdbPort'])) {
                        $fdbPort_table[$vlanLocal] = ['dot1qTpFdbPort' => []];
                    }
                    foreach ($data4[0] as $macLocal => $one) {
                        $fdbPort_table[$vlanLocal]['dot1qTpFdbPort'][$macLocal] = $portLocal;
                    }
                }
            }
        }
    }
}
include 'includes/discovery/fdb-table/aos6.inc.php';
