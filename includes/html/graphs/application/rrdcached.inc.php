<?php
$rrd = Rrd::name($device['hostname'], ['app', 'rrdcached', $app->app_id]);
if (Rrd::checkRrdExists($rrd)) {
    $rrd_filename = $rrd;
}
