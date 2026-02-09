<?php
use App\Facades\ObzoraConfig;

if (isset($device['os_group']) && file_exists(ObzoraConfig::get('install_dir') . "/includes/polling/ntp/{$device['os_group']}.inc.php")) {
    include ObzoraConfig::get('install_dir') . "/includes/polling/ntp/{$device['os_group']}.inc.php";
}

if ($device['os'] == 'awplus') {
    include 'includes/polling/ntp/awplus.inc.php';
}

unset(
    $cntpPeersVarEntry,
    $atNtpAssociationEntry
);
