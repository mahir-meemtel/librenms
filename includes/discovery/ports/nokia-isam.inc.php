<?php
SnmpQuery::context('ihub')->hideMib()->walk([
    'IF-MIB::ifDescr',
    'IF-MIB::ifName',
    'IF-MIB::ifAlias',
    'IF-MIB::ifType',
    'IF-MIB::ifOperStatus',
])->table(1, $port_stats);
