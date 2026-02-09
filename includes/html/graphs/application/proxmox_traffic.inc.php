<?php
require 'includes/html/graphs/common.inc.php';

$rrd_filename = Rrd::proxmoxName($vars['cluster'], $vars['vmid'], $vars['port']);

$ds_in = 'INOCTETS';
$ds_out = 'OUTOCTETS';

require 'includes/html/graphs/generic_data.inc.php';
