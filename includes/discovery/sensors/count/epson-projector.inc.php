<?php
use Illuminate\Support\Str;

if (Str::startsWith($device['sysObjectID'], '.1.3.6.1.4.1.1248.4.1')) {
    discover_sensor(
        null,
        'count',
        $device,
        '.1.3.6.1.4.1.1248.4.1.1.1.1.0',
        0,
        'epson-projector',
        'Lamp Hours',
        1,
        1,
        null,
        null,
        null,
        null,
        snmp_get($device, '.1.3.6.1.4.1.1248.4.1.1.1.1.0', '-Ovq')
    );
}
