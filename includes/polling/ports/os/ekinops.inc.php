<?php
foreach ($port_stats as $index => $port) {
    /*
     * Split up ifName and drop the EKIPS/Chassis
     */
    $intName = preg_split("/[\/,\(,\)]/", $port['ifName']);

    // Make ifDescr slot/card/int
    $ifDescr = $intName[2] . '/' . $intName[3] . '/' . $intName[4];

    // Make ifAlias descr
    $ifAlias = $intName[5] ?? null;

    $port_stats[$index]['ifAlias'] = $ifAlias;
    $port_stats[$index]['ifDescr'] = $ifDescr;
}
