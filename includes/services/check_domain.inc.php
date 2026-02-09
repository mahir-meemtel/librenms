<?php
$check_cmd = \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_domain -d ';
if ($service['service_ip']) {
    $check_cmd .= $service['service_ip'];
} else {
    $check_cmd .= $service['hostname'];
}
$check_cmd .= ' ' . $service['service_param'];
