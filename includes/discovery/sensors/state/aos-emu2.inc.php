<?php
$oids = SnmpQuery::walk([
    'PowerNet-MIB::emsInputContactStatusEntry',
])->table(1);

foreach ($oids as $id => $contact) {
    $index = $contact['PowerNet-MIB::emsInputContactStatusInputContactIndex'];
    $oid = '.1.3.6.1.4.1.318.1.1.10.3.14.1.1.3.' . $index;
    $descr = $contact['PowerNet-MIB::emsInputContactStatusInputContactName'];
    $currentstate = $contact['PowerNet-MIB::emsInputContactStatusInputContactState'];
    $normalstate = $contact['PowerNet-MIB::emsInputContactStatusInputContactNormalState'];
    if (is_array($oids) && $normalstate == '1') {
        $state_name = 'emsInputContactNormalState_NC';
        $states = [
            ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'Closed'],
            ['value' => 2, 'generic' => 1, 'graph' => 0, 'descr' => 'Open'],
        ];
        create_state_index($state_name, $states);
    } elseif (is_array($oids) && $normalstate == '2') {
        $state_name = 'emsInputContactNormalState_NO';
        $states = [
            ['value' => 1, 'generic' => 1, 'graph' => 0, 'descr' => 'Closed'],
            ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'Open'],
        ];
        create_state_index($state_name, $states);
    }

    app('sensor-discovery')->discover(new \App\Models\Sensor([
        'poller_type' => 'snmp',
        'sensor_class' => 'state',
        'sensor_oid' => $oid,
        'sensor_index' => $index,
        'sensor_type' => $state_name,
        'sensor_descr' => $descr,
        'sensor_divisor' => 1,
        'sensor_multiplier' => 1,
        'sensor_limit_low' => null,
        'sensor_limit_low_warn' => null,
        'sensor_limit_warn' => null,
        'sensor_limit' => null,
        'sensor_current' => $currentstate,
        'entPhysicalIndex' => $index,
        'entPhysicalIndex_measured' => null,
        'user_func' => null,
        'group' => null,
    ]));
}

// Output Relay discovery
$oids = SnmpQuery::walk([
    'PowerNet-MIB::emsOutputRelayStatusEntry',
])->table(1);

foreach ($oids as $id => $relay) {
    $index = $relay['PowerNet-MIB::emsOutputRelayStatusOutputRelayIndex'];
    $oid = '.1.3.6.1.4.1.318.1.1.10.3.15.1.1.3.' . $index;
    $descr = $relay['PowerNet-MIB::emsOutputRelayStatusOutputRelayName'];
    $currentstate = $relay['PowerNet-MIB::emsOutputRelayStatusOutputRelayState'];
    $normalstate = $relay['PowerNet-MIB::emsOutputRelayStatusOutputRelayNormalState'];
    if (is_array($oids) && $normalstate == '1') {
        $state_name = 'emsOutputRelayNormalState_NC';
        $states = [
            ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'Closed'],
            ['value' => 2, 'generic' => 1, 'graph' => 0, 'descr' => 'Open'],
        ];
        create_state_index($state_name, $states);
    } elseif (is_array($oids) && $normalstate == '2') {
        $state_name = 'emsOutputRelayNormalState_NO';
        $states = [
            ['value' => 1, 'generic' => 1, 'graph' => 0, 'descr' => 'Closed'],
            ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'Open'],
        ];
        create_state_index($state_name, $states);
    }

    app('sensor-discovery')->discover(new \App\Models\Sensor([
        'poller_type' => 'snmp',
        'sensor_class' => 'state',
        'sensor_oid' => $oid,
        'sensor_index' => $index,
        'sensor_type' => $state_name,
        'sensor_descr' => $descr,
        'sensor_divisor' => 1,
        'sensor_multiplier' => 1,
        'sensor_limit_low' => null,
        'sensor_limit_low_warn' => null,
        'sensor_limit_warn' => null,
        'sensor_limit' => null,
        'sensor_current' => $currentstate,
        'entPhysicalIndex' => $index,
        'entPhysicalIndex_measured' => null,
        'user_func' => null,
        'group' => null,
    ]));
}

// Outlet discovery
$oids = SnmpQuery::walk([
    'PowerNet-MIB::emsOutletStatusEntry',
])->table(1);

foreach ($oids as $id => $outlet) {
    $index = $outlet['PowerNet-MIB::emsOutletStatusOutletIndex'];
    $oid = '.1.3.6.1.4.1.318.1.1.10.3.16.1.1.3.' . $index;
    $descr = $outlet['PowerNet-MIB::emsOutletStatusOutletName'];
    $currentstate = $outlet['PowerNet-MIB::emsOutletStatusOutletState'];
    $normalstate = $outlet['PowerNet-MIB::emsOutletStatusOutletNormalState'];
    if (is_array($oids) && $normalstate == '1') {
        $state_name = 'emsOutletNormalState_ON';
        $states = [
            ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'On'],
            ['value' => 2, 'generic' => 1, 'graph' => 0, 'descr' => 'Off'],
        ];
        create_state_index($state_name, $states);
    } elseif (is_array($oids) && $normalstate == '2') {
        $state_name = 'emsOutletNormalState_OFF';
        $states = [
            ['value' => 1, 'generic' => 1, 'graph' => 0, 'descr' => 'On'],
            ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'Off'],
        ];
        create_state_index($state_name, $states);
    }

    app('sensor-discovery')->discover(new \App\Models\Sensor([
        'poller_type' => 'snmp',
        'sensor_class' => 'state',
        'sensor_oid' => $oid,
        'sensor_index' => $index,
        'sensor_type' => $state_name,
        'sensor_descr' => $descr,
        'sensor_divisor' => 1,
        'sensor_multiplier' => 1,
        'sensor_limit_low' => null,
        'sensor_limit_low_warn' => null,
        'sensor_limit_warn' => null,
        'sensor_limit' => null,
        'sensor_current' => $currentstate,
        'entPhysicalIndex' => $index,
        'entPhysicalIndex_measured' => null,
        'user_func' => null,
        'group' => null,
    ]));
}
