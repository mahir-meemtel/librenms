<?php
$check_cmd = \App\Facades\ObzoraConfig::get('nagios_plugins') . '/check_hetzner_storagebox ' . $service['service_param'];
