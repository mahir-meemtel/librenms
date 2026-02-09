<?php
if ($device['os'] == 'f5') {
    if (file_exists(Config::get('install_dir') . 'includes/polling/loadbalancers/f5-ltm.inc.php')) {
        include Config::get('install_dir') . 'includes/polling/loadbalancers/f5-ltm.inc.php';
    }
    if (file_exists(Config::get('install_dir') . 'includes/polling/loadbalancers/f5-gtm.inc.php')) {
        include Config::get('install_dir') . 'includes/polling/loadbalancers/f5-gtm.inc.php';
    }
    if (file_exists(Config::get('install_dir') . 'includes/polling/loadbalancers/f5-ltm-currconns.inc.php')) {
        include Config::get('install_dir') . 'includes/polling/loadbalancers/f5-ltm-currconns.inc.php';
    }
}
