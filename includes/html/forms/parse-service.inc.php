<?php
if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

$service_id = $vars['service_id'];

if (is_numeric($service_id) && $service_id > 0) {
    $service = service_get(null, $service_id);

    $output = [
        'stype' => $service[0]['service_type'],
        'ip' => $service[0]['service_ip'],
        'desc' => $service[0]['service_desc'],
        'param' => $service[0]['service_param'],
        'ignore' => $service[0]['service_ignore'],
        'disabled' => $service[0]['service_disabled'],
        'template_id' => $service[0]['service_template_id'],
        'name' => $service[0]['service_name'],
    ];

    header('Content-Type: application/json');
    echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
