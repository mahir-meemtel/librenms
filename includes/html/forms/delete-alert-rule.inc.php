<?php
use App\Models\Alert;
use App\Models\AlertLog;
use App\Models\AlertTemplateMap;

header('Content-type: text/plain');

if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

if (! is_numeric($vars['alert_id'])) {
    echo 'ERROR: No alert selected';
    exit;
} else {
    $alert_name = dbFetchCell('SELECT name FROM alert_rules WHERE id=?', [$vars['alert_id']]);
    $alert_msg_prefix = 'Alert rule';
    if ($alert_name) {
        $alert_msg_prefix .= ' ' . $alert_name;
    }
    if (! $alert_name) {
        $alert_msg_prefix .= ' id ' . $vars['alert_id'];
    }
    if (dbDelete('alert_rules', '`id` =  ?', [$vars['alert_id']])) {
        Alert::where('rule_id', $vars['alert_id'])->delete();
        AlertLog::where('rule_id', $vars['alert_id'])->delete();
        dbDelete('alert_device_map', 'rule_id=?', [$vars['alert_id']]);
        dbDelete('alert_group_map', 'rule_id=?', [$vars['alert_id']]);
        dbDelete('alert_location_map', 'rule_id=?', [$vars['alert_id']]);
        dbDelete('alert_transport_map', 'rule_id=?', [$vars['alert_id']]);
        AlertTemplateMap::where('alert_rule_id', $vars['alert_id'])->delete();
        echo $alert_msg_prefix . ' has been deleted.';
        exit;
    } else {
        echo 'ERROR: ' . $alert_msg_prefix . ' has not been deleted.';
        exit;
    }
}
