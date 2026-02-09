<?php
$no_refresh = true;
$page_title = 'Alerts';
?>

<div class="panel panel-default panel-condensed">
    <div class="panel-heading">
        <strong>Alerts</strong>
    </div>

    <?php
    $device['device_id'] = '-1';
    require_once 'includes/html/modal/alert_details.php';
    require_once 'includes/html/modal/alert_notes.inc.php';
    require_once 'includes/html/modal/alert_ack.inc.php';
    require_once 'includes/html/common/alerts.inc.php';
    echo implode('', $common_output);
    unset($device['device_id']);
    ?>
</div>
