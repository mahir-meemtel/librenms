<?php
if (! Auth::user()->hasGlobalAdmin()) {
    $status = ['status' => 1, 'message' => 'You need to be admin'];
} else {
    $parent_ids = (array) $_POST['parent_ids'];
    $device_ids = (array) $_POST['device_ids'];

    foreach ($parent_ids as $parent) {
        if (! is_numeric($parent)) {
            $status = ['status' => 1, 'message' => 'Parent ID must be an integer!'];
            break;
        }
    }

    if (count($parent_ids) > 1 && in_array('0', $parent_ids)) {
        $status = ['status' => 1, 'message' => 'Multiple parents cannot contain None-Parent!'];
    }

    foreach ($device_ids as $device_id) {
        if (! is_numeric($device_id)) {
            $status = ['status' => 1, 'message' => 'Device ID must be an integer!'];
            break;
        } elseif (in_array($device_id, $parent_ids)) {
            $status = ['status' => 1, 'message' => 'A device cannot depend itself'];
            break;
        }

        \App\Models\Device::find($device_id)->parents()->sync($parent_ids);

        $status = ['status' => 0, 'message' => 'Device dependencies have been saved'];
    }
}
header('Content-Type: application/json');
echo json_encode($status);
