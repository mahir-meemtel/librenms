<?php
$no_refresh = true;
$device_id = $_POST['device_id'] ?? '';
$vars['fromdevice'] = false;
require_once 'includes/html/modal/alert_details.php';
require_once 'includes/html/common/alert-log.inc.php';
echo implode('', $common_output);
unset($device_id);
