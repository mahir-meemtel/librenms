<?php
use Illuminate\Support\Str;

if (Str::startsWith($device['sysObjectID'], '.1.3.6.1.4.1.8741.6')) {
    $licenses = snmp_get($device, 'SNWL-SSLVPN-MIB::userLicense.0', '-Ovq');
    $licenses = str_replace(' Users', '', $licenses);
    $current = snmp_get($device, '.1.3.6.1.4.1.8741.6.2.1.9.0', '-Ovq');

    discover_sensor(
        null,
        'count',
        $device,
        '.1.3.6.1.4.1.8741.6.2.1.9.0', // SNWL-SSLVPN-MIB::activeUserLicense.0
        0,
        'sonicwall',
        'SSL VPN clients',
        1,
        1,
        null,
        0,
        $licenses - 10,
        $licenses,
        $current
    );
}
