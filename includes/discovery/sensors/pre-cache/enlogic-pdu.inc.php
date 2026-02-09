<?php
echo 'pduUnitStatusTable ';
$pre_cache['enlogic_pdu_status'] = snmpwalk_cache_oid($device, 'pduUnitStatusTable', [], 'ENLOGIC-PDU-MIB');

echo 'pduInputPhaseConfigTable ';
$pre_cache['enlogic_pdu_input'] = snmpwalk_cache_oid($device, 'pduInputPhaseConfigTable', [], 'ENLOGIC-PDU-MIB');
echo 'pduInputPhaseStatusTable ';
$pre_cache['enlogic_pdu_input'] = snmpwalk_cache_oid($device, 'pduInputPhaseStatusTable', $pre_cache['enlogic_pdu_input'], 'ENLOGIC-PDU-MIB');

echo 'pduCircuitBreakerConfigTable ';
$pre_cache['enlogic_pdu_circuit'] = snmpwalk_cache_oid($device, 'pduCircuitBreakerConfigTable', [], 'ENLOGIC-PDU-MIB');
echo 'pduCircuitBreakerStatusTable ';
$pre_cache['enlogic_pdu_circuit'] = snmpwalk_cache_oid($device, 'pduCircuitBreakerStatusTable', $pre_cache['enlogic_pdu_circuit'], 'ENLOGIC-PDU-MIB');
