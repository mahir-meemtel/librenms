<?php
$ver = intval($device['version']);
d_echo('PORTS: Luminato v' . $ver);

if ($ver >= 20) {
    $ifmib = SnmpQuery::walk('IF-MIB::ifConnectorPresent')->table(2);
    foreach ($port_stats as $key => $data) {
        $port_stats[$key]['ifOperStatus'] = $ifmib[$key]['IF-MIB::ifConnectorPresent'] ? 'up' : 'down';
    }
}
