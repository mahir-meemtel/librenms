<?php
$rrd = Rrd::name($device['hostname'], ['app', 'powerdns-recursor', $app->app_id]);
if (Rrd::checkRrdExists($rrd)) {
    $rrd_filename = $rrd;
}
$simple_rrd = true;
