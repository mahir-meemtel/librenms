<?php
$airos_eth_stat = snmpwalk_cache_oid($device, 'afLTUethConnected', [], 'UBNT-AFLTU-MIB', null, '-OteQUsb');

foreach ($port_stats as $index => $afport_stats) {
    if ($afport_stats['ifDescr'] == 'eth0') {
        if (isset($airos_eth_stat[0]['afLTUethConnected'])) {
            $port_stats[$index]['ifOperStatus'] = ($airos_eth_stat[0]['afLTUethConnected'] == 1 ? 'up' : 'down');
        } else {
            /**
             * Ubiquiti uses separate OIDs for ethernet status. Sometimes the devices have difficulties to return
             * a value for the OID "afLTUethConnected".
             * Because "IF-MIB" reads wrong information we remove the existing entry for "eth0" if "afLTUethConnected"
             * could not be read to prevent wrong information from being stored.
             */
            unset($port_stats[$index]);
        }
        break;
    }
}

unset($airos_eth_stat);
