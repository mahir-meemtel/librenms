<?php
header('Content-type: text/plain');

if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

if (! is_numeric($_POST['template_id'])) {
    echo 'ERROR: No template selected';
    exit;
} else {
    if (dbDelete('alert_templates', '`id` =  ?', [$_POST['template_id']])) {
        dbDelete('alert_template_map', 'alert_templates_id = ?', [$_POST['template_id']]);
        echo 'Alert template has been deleted.';
        exit;
    } else {
        echo 'ERROR: Alert template has not been deleted.';
        exit;
    }
}
