<?php
$check_cmd = \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_procs ' . $service['service_param'];
