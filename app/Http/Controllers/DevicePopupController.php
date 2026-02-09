<?php
namespace App\Http\Controllers;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use ObzoraNMS\Util\Graph;

class DevicePopupController
{
    public function __invoke(Device $device)
    {
        if (! ObzoraConfig::get('web_mouseover', true)) {
            return response('Disabled');
        }

        // Check access permissions
        if (! $device->canAccess(auth()->user())) {
            return response('Unauthorized', 403);
        }

        // Build graphs HTML using existing graph-row component
        $graphs = [];
        foreach (Graph::getOverviewGraphsForDevice($device) as $graph) {
            if (isset($graph['text'], $graph['graph'])) {
                $graphs[] = [
                    'device' => $device,
                    'type' => $graph['graph'],
                    'title' => $graph['text'],
                    'graphs' => [['from' => '-1d'], ['from' => '-7d']],
                ];
            }
        }

        return view('device.popup', [
            'device' => $device,
            'osText' => ObzoraConfig::getOsSetting($device->os ?? '', 'text'),
            'href' => route('device', ['device' => $device->device_id]),
            'graphs' => $graphs,
        ]);
    }
}
