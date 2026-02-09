<?php
if (! isset($pre_cache['infineragroove_portTable']) || ! is_array($pre_cache['infineragroove_portTable'])) {
    echo 'Caching OIDs:';
    $pre_cache['infineragroove_portTable'] = [];
    echo ' portTable';
    $portTable = SnmpQuery::options('-OQ')->numericIndex()->hideMib()->walk('CORIANT-GROOVE-MIB::portTable')->valuesByIndex();
    $pre_cache['infineragroove_portTable'] = array_merge_recursive($pre_cache['infineragroove_portTable'], $portTable);
    echo ' OchOsTable';
    $OchOsTable = SnmpQuery::options('-OQ')->numericIndex()->hideMib()->walk('CORIANT-GROOVE-MIB::ochOsTable')->valuesByIndex();
    $pre_cache['infineragroove_portTable'] = array_merge_recursive($pre_cache['infineragroove_portTable'], $OchOsTable);
    echo ' bitErrorRatePostFecTable';
    $bitErrorRatePostFecTable = SnmpQuery::options('-OQ')->numericIndex()->hideMib()->walk('CORIANT-GROOVE-MIB::bitErrorRatePostFecTable')->valuesByIndex();
    $pre_cache['infineragroove_portTable'] = array_merge_recursive($pre_cache['infineragroove_portTable'], $bitErrorRatePostFecTable);
    echo ' bitErrorRatePreFecTable';
    $bitErrorRatePreFecTable = SnmpQuery::options('-OQ')->numericIndex()->hideMib()->walk('CORIANT-GROOVE-MIB::bitErrorRatePreFecTable')->valuesByIndex();
    $pre_cache['infineragroove_portTable'] = array_merge_recursive($pre_cache['infineragroove_portTable'], $bitErrorRatePreFecTable);
}

foreach (array_keys($pre_cache['infineragroove_portTable']) as $index) {
    $indexids = explode('.', $index);

    if (isset($pre_cache['infineragroove_portTable'][$index]['ochOsAdminStatus'])) {
        $pre_cache['infineragroove_portTable'][$index]['portAlias'] = 'och-os-';
    } else {
        $pre_cache['infineragroove_portTable'][$index]['portAlias'] = 'port-';
    }
    $pre_cache['infineragroove_portTable'][$index]['portAlias'] .= $indexids[0] . '/' . $indexids[1] . '/' . $indexids[3];

    unset($indexids);
}

if (! isset($pre_cache['infineragroove_slotTable']) || ! is_array($pre_cache['infineragroove_slotTable'])) {
    $pre_cache['infineragroove_slotTable'] = [];
    echo ' slotTable';
    $slotTable = SnmpQuery::options('-OQ')->numericIndex()->hideMib()->walk('CORIANT-GROOVE-MIB::slotTable')->valuesByIndex();
    $pre_cache['infineragroove_slotTable'] = array_merge_recursive($pre_cache['infineragroove_slotTable'], $slotTable);
    echo ' cardTable';
    $cardTable = SnmpQuery::options('-OQ')->numericIndex()->hideMib()->walk('CORIANT-GROOVE-MIB::cardTable')->valuesByIndex();
    $pre_cache['infineragroove_slotTable'] = array_merge_recursive($pre_cache['infineragroove_slotTable'], $cardTable);
}
