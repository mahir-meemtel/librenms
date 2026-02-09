<?php
require 'rrdcached.inc.php';
require 'includes/html/graphs/common.inc.php';

$ds = 'queue_length';

$colour_area = 'F37900';
$colour_line = 'FFA700';
$colour_area_max = 'F78800';

$unit_text = 'Queue Length';

require 'includes/html/graphs/generic_simplex.inc.php';
