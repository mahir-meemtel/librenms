<?php
if (! Auth::user()->hasGlobalAdmin()) {
    $status = ['status' => 1, 'message' => 'ERROR: You need to be admin to delete services'];
} else {
    if (! is_numeric($vars['service_id'])) {
        $status = ['status' => 1, 'message' => 'No Service has been selected'];
    } else {
        if (delete_service($vars['service_id'])) {
            $status = ['status' => 0, 'message' => 'Service: <i>' . $vars['service_id'] . ', has been deleted.</i>'];
        } else {
            $status = ['status' => 1, 'message' => 'Service: <i>' . $vars['service_id'] . ', has NOT been deleted.</i>'];
        }
    }
}
header('Content-Type: application/json');
echo json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
