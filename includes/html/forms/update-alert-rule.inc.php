<?php
header('Content-type: text/plain');

if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

if (! is_numeric($_POST['alert_id'])) {
    echo 'ERROR: No alert selected';
    exit;
} else {
    if ($_POST['state'] == 'true') {
        $state = 0;
    } elseif ($_POST['state'] == 'false') {
        $state = 1;
    } else {
        $state = 1;
    }

    $update = dbUpdate(['disabled' => $state], 'alert_rules', '`id`=?', [$_POST['alert_id']]);
    if (! empty($update) || $update == '0') {
        echo 'Alert rule has been updated.';
        exit;
    } else {
        echo 'ERROR: Alert rule has not been updated.';
        exit;
    }
}
