<?php
$check_cmd = \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_dovecot ' . $service['service_param'];
