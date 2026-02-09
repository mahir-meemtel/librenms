<?php
$check_cmd = 'sudo ' . \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_mailqueue ' . $service['service_param'];
