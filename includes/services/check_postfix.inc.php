<?php
$check_cmd = 'sudo ' . \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_postfix ' . $service['service_param'];
