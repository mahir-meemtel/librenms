#!/usr/bin/env php
<?php
use ObzoraNMS\Alert\RunAlerts;
use ObzoraNMS\Util\Debug;

$init_modules = ['alerts', 'laravel'];
require __DIR__ . '/includes/init.php';

$options = getopt('fd::');

if (Debug::set(isset($options['d']))) {
    echo "DEBUG!\n";
}

$scheduler = \App\Facades\ObzoraConfig::get('schedule_type.alerting');
if (! isset($options['f']) && $scheduler != 'legacy' && $scheduler != 'cron') {
    if (Debug::isEnabled()) {
        echo "Alerts are not enabled for cron scheduling.  Add the -f command argument if you want to force this command to run.\n";
    }
    exit(0);
}

$alerts_lock = Cache::lock('alerts', \App\Facades\ObzoraConfig::get('service_alerting_frequency'));
if ($alerts_lock->get()) {
    $alerts = new RunAlerts();
    if (! defined('TEST') && \App\Facades\ObzoraConfig::get('alert.disable') != 'true') {
        echo 'Start: ' . date('r') . "\r\n";
        echo 'ClearStaleAlerts():' . PHP_EOL;
        $alerts->clearStaleAlerts();
        echo "RunFollowUp():\r\n";
        $alerts->runFollowUp();
        echo "RunAlerts():\r\n";
        $alerts->runAlerts();
        echo "RunAcks():\r\n";
        $alerts->runAcks();
        echo 'End  : ' . date('r') . "\r\n";
    }
    $alerts_lock->release();
}
