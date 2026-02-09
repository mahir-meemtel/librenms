<?php
$advaports = SnmpQuery::walk([
    'FSP150-MIB::fsp150IfConfigUserString',
    'ENTITY-MIB::entPhysicalName',
])->table(1);

foreach ($advaports as $index => $entry) {
    // Indexes are the same as IfIndex and EntPhysicalIndex

    if (isset($port_stats[$index])) {
        if (isset($entry['FSP150-MIB::fsp150IfConfigUserString'])) {
            $port_stats[$index]['ifAlias'] = $entry['FSP150-MIB::fsp150IfConfigUserString'];
        }
        if (isset($entry['ENTITY-MIB::entPhysicalName'])) {
            $port_stats[$index]['ifName'] = $entry['ENTITY-MIB::entPhysicalName'];
        }
    }
}
