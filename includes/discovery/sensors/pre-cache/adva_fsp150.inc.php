<?php
$pre_cache['adva_fsp150'] = snmpwalk_cache_multi_oid($device, 'cmEntityObjects', [], 'CM-ENTITY-MIB', null, '-OQUbs');
$pre_cache['adva_erp'] = snmpwalk_cache_multi_oid($device, 'f3ErpConfigObjects', [], 'F3-ERP-MIB', null, '-OQUbs');
$pre_cache['adva_fsp150_perfs'] = [];
$pre_cache['adva_fsp150_ports'] = [];

$neType = $pre_cache['adva_fsp150'][1]['neType'];
$pre_cache['adva_fsp150_ifName'] = snmpwalk_cache_multi_oid($device, 'ifName', [], 'IF-MIB', null, '-OQUbs');

if ($neType == 'ccxg116pro' || $neType == 'ccxg116proH' || $neType == 'ccxg120pro' || $neType == 'aggregation') {
    $pre_cache['adva_fsp150_ports'] = snmpwalk_cache_multi_oid($device, 'cmEthernetTrafficPortTable', $pre_cache['adva_fsp150_ports'], 'CM-FACILITY-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetTrafficPortStatsLBC', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetTrafficPortStatsOPR', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetTrafficPortStatsOPT', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetTrafficPortStatsTemp', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
} else {
    $pre_cache['adva_fsp150_ports'] = snmpwalk_cache_multi_oid($device, 'cmEthernetNetPortTable', $pre_cache['adva_fsp150_ports'], 'CM-FACILITY-MIB', null, '-OQUbs');

    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetNetPortStatsLBC', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetNetPortStatsOPR', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetNetPortStatsOPT', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetNetPortStatsTemp', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');

    $pre_cache['adva_fsp150_ports'] = snmpwalk_cache_multi_oid($device, 'cmEthernetAccPortTable', $pre_cache['adva_fsp150_ports'], 'CM-FACILITY-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetAccPortStatsLBC', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetAccPortStatsOPR', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetAccPortStatsOPT', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
    $pre_cache['adva_fsp150_perfs'] = snmpwalk_cache_multi_oid($device, 'cmEthernetAccPortStatsTemp', $pre_cache['adva_fsp150_perfs'], 'CM-PERFORMANCE-MIB', null, '-OQUbs');
}
