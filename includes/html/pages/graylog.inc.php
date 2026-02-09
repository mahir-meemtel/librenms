<?php
$no_refresh = true;
$pagetitle[] = 'Graylog';

echo '<div class="panel panel-default panel-condensed">
    <div class="panel-heading">
        <strong>Graylog entries</strong>
    </div>';

require_once 'includes/html/common/graylog.inc.php';
echo implode('', $common_output);

echo '</div>';
