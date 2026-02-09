<?php
if (DeviceCache::getPrimary()->transceivers->isNotEmpty()) {
    DeviceCache::getPrimary()->transceivers->load(['port']);
    echo view('device.overview.transceivers', [
        'transceivers' => DeviceCache::getPrimary()->transceivers,
        'transceivers_link' => route('device', ['device' => DeviceCache::getPrimary()->device_id, 'tab' => 'ports', 'vars' => 'transceivers']),
        'sensors' => DeviceCache::getPrimary()->sensors->where('group', 'transceiver'),
        // only temp and rx power to reduce information overload, click through to see all
        'filterSensors' => function (\App\Models\Sensor $sensor) {
            if ($sensor->sensor_class == 'temperature') {
                return true;
            }

            if ($sensor->sensor_class == 'dbm') {
                $haystack = strtolower($sensor->sensor_descr);

                return str_contains($haystack, 'rx') || str_contains($haystack, 'receive');
            }

            return false;
        },
    ]);
}
