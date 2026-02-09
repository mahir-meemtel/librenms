<?php
$check_cmd = \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_oracle ' . $service['service_param'];
