<?php
$pre_cache = snmpwalk_cache_multi_oid($device, 'ifDescr', [], 'IF-MIB', null, '-OQUbs');

foreach ($pre_cache as $index => $port) {
    /*
     * Replace the prefix node X interface from the interface description
     * It helps to have clearer graphs as $SHELF/$CARD/$IFNAME is more usable
     * than node $SHELF interface  $SHELF/$CARD/$IFNAME
     */
    $newDescr = preg_replace('/^node [0-9]+ interface (.+)/', '$1', $port['ifDescr']);
    $pre_cache[$index]['ifDescr'] = $newDescr;
}
