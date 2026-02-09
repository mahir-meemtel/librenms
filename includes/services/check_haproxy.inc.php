<?php
$check_cmd = \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_haproxy ' . $service['service_param'];
