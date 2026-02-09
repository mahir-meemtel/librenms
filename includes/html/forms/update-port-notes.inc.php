<?php
header('Content-type: application/json');

$status = 'error';
$message = 'unknown error';

$device_id = $_POST['device_id'];
$port_id_notes = $_POST['port_id_notes'];
$attrib_value = $_POST['notes'];

if (isset($attrib_value) && set_dev_attrib(['device_id' => $device_id], $port_id_notes, $attrib_value)) {
    $status = 'ok';
    $message = 'Updated';
} else {
    $status = 'error';
    $message = 'ERROR: Could not update';
}
exit(json_encode([
    'status' => $status,
    'message' => $message,
    'attrib_type' => $port_id_notes,
    'attrib_value' => $attrib_value,
    'device_id' => $device_id,

]));
