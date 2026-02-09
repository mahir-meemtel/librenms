<?php
if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

foreach (['desc', 'name'] as $varname) {
    //sanitize description and name
    if (isset($vars[$varname])) {
        $$varname = strip_tags($vars[$varname]);
        $update['service_' . $varname] = $$varname;
    }
}
foreach (['ip', 'ignore', 'disabled', 'param', 'template_id'] as $varname) {
    if (isset($vars[$varname])) {
        $update['service_' . $varname] = $vars[$varname];
        $$varname = $vars[$varname];
    }
}
foreach (['stype', 'device_id', 'service_id'] as $varname) {
    if (isset($vars[$varname])) {
        $$varname = $vars[$varname];
    }
}

if (is_numeric($service_id) && $service_id > 0) {
    // Need to edit.
    if (is_numeric(edit_service($update, $service_id))) {
        $status = ['status' => 0, 'message' => 'Modified Service: <i>' . $service_id . ': ' . $stype . '</i>'];
    } else {
        $status = ['status' => 1, 'message' => 'ERROR: Failed to modify service: <i>' . $service_id . '</i>'];
    }
} else {
    // Need to add.
    $service_id = add_service($device_id, $stype, $desc, $ip, $param, $ignore, $disabled, 0, $name);
    if ($service_id == false) {
        $status = ['status' => 1, 'message' => 'ERROR: Failed to add Service: <i>' . $stype . '</i>'];
    } else {
        $status = ['status' => 0, 'message' => 'Added Service: <i>' . $service_id . ': ' . $stype . '</i>'];
    }
}
header('Content-Type: application/json');
echo json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
