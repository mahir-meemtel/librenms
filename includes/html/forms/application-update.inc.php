<?php
use App\Models\Device;
use App\Models\Eventlog;
use ObzoraNMS\Enum\Severity;

if (! Auth::user()->hasGlobalAdmin()) {
    $status = ['status' => 1, 'message' => 'You need to be admin'];
} else {
    $device = Device::find($_POST['device_id']);
    $app = $_POST['application'];

    if (! isset($app) && $device === null) {
        $status = ['status' => 1, 'message' => 'Error with data'];
    } else {
        $status = ['status' => 1, 'message' => 'Database update failed'];
        $app = $device->applications()->withTrashed()->firstOrNew(['app_type' => $app]);
        if ($_POST['state'] == 'true') {
            if ($app->trashed()) {
                $app->restore();
            }
            if ($app->save()) {
                Eventlog::log('Application enabled by user ' . Auth::user()->username . ': ' . $app, $device->device_id, 'application', Severity::Ok);
                $status = ['status' => 0, 'message' => 'Application enabled'];
            } else {
                $status = ['status' => 1, 'message' => 'Database update for enabling the application failed'];
            }
        } else {
            $app->delete();
            if ($app->save()) {
                Eventlog::log('Application disabled by user ' . Auth::user()->username . ': ' . $app, $device->device_id, 'application', Severity::Notice);
                $status = ['status' => 0, 'message' => 'Application disabled'];
            } else {
                $status = ['status' => 1, 'message' => 'Database update for disabling the application failed'];
            }
        }
    }
}
header('Content-Type: application/json');
echo json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
