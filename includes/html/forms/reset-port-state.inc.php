<?php
use App\Models\Device;
use App\Models\Eventlog;

if (! Auth::user()->hasGlobalAdmin()) {
    $response = [
        'status' => 'error',
        'message' => 'Need to be admin',
    ];
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_POST['device_id'])) {
    if (! is_numeric($_POST['device_id'])) {
        $status = 'error';
        $message = 'Invalid device id ' . htmlspecialchars($_POST['device_id']);
    } else {
        $device = Device::find($_POST['device_id']);

        Eventlog::log('Port state history reset by ' . Auth::user()->username, $device);

        try {
            foreach ($device->ports()->get() as $port) {
                $port->ifSpeed_prev = null;
                $port->ifOperStatus_prev = null;
                $port->ifAdminStatus_prev = null;

                $port->save();
            }
            $status = 'ok';
            $message = 'Port state cleared successfully';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Clearing port state failed: $e';
        }
    }
} else {
    $status = 'Error';
    $message = 'Undefined POST keys received';
}

$output = [
    'status' => $status,
    'message' => $message,
];

header('Content-type: application/json');
echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
