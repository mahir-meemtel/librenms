<?php
$check_cmd = \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_graylog ' . $service['service_param'];
