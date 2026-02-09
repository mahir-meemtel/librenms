<?php
$airos_stats = snmpwalk_cache_oid($device, '.1.3.6.1.4.1.41112.1.10.1.6', [], 'UBNT-AFLTU-MIB');

foreach ($port_stats as $index => $afport_stats) {
    if ($afport_stats['ifDescr'] == 'eth0') {
        if (isset($airos_stats[0]['afLTUethConnected'])) {
            $port_stats[$index]['ifOperStatus'] = ($airos_stats[0]['afLTUethConnected'] == 'connected' ? 'up' : 'down');
            $port_stats[$index]['ifHCInOctets'] = $airos_stats[0]['afLTUethRxBytes'] ?? null;
            $port_stats[$index]['ifHCOutOctets'] = $airos_stats[0]['afLTUethTxBytes'] ?? null;
            $port_stats[$index]['ifHCInUcastPkts'] = $airos_stats[0]['afLTUethRxPps'] ?? null;
            $port_stats[$index]['ifHCOutUcastPkts'] = $airos_stats[0]['afLTUethTxPps'] ?? null;
            $port_stats[$index]['ifHighSpeed'] = '1000';
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

unset($airos_stats);
