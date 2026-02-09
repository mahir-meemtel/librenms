<?php
$ver = intval($device['version']);
d_echo('PORTS: Luminato v' . $ver);

// add IF-MIB::ifSpeed if missing
if (! array_key_exists('ifSpeed', Arr::first($port_stats))) {
    SnmpQuery::hideMib()->walk('IF-MIB::ifSpeed')->table(2, $port_stats);
}

foreach ($port_stats as $key => $data) {
    // emulate ifOperStatus if missing
    if (empty($data['ifOperStatus'])) {
        $port_stats[$key]['ifOperStatus'] = $data['ifConnectorPresent'] ? 'up' : 'down';
    }

    // ifHighSpeed is always broken and ver >= 20 ifSpeed is actually ifHighSpeed
    $port_stats[$key]['ifHighSpeed'] = ($ver < 20 ? $data['ifSpeed'] / 1000000 : $data['ifSpeed']);
}
