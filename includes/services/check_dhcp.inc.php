<?php
$check_cmd = \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_dhcp ' . $service['service_param'];
