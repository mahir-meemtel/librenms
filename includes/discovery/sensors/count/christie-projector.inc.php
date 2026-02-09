<?php
use Illuminate\Support\Str;

if (Str::startsWith($device['sysObjectID'], '.1.3.6.1.4.1.25766')) {
    discover_sensor(
        null,
        'count',
        $device,
        '.1.3.6.1.4.1.25766.1.12.1.1.3.5.1.6.1',
        0,
        'christie-projector',
        'Lamp Hours',
        1,
        1,
        null,
        null,
        null,
        null,
        snmp_get($device, '.1.3.6.1.4.1.25766.1.12.1.1.3.5.1.6.1', '-Ovq')
    );
}
