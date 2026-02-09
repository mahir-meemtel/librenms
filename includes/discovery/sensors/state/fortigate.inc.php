<?php
$systemMode = SnmpQuery::enumStrings()->get('FORTINET-FORTIGATE-MIB::fgHaSystemMode.0')->value(0);

// Verify that the device is clustered
if ($systemMode == 'activePassive' || $systemMode == 'activeActive') {
    // Indexes of all the members
    $fgHaStatsIndex_num = '.1.3.6.1.4.1.12356.101.13.2.1.1.1';

    // Fetch the cluster members
    $haStatsEntries = SnmpQuery::hideMIB()->walk('FORTINET-FORTIGATE-MIB::fgHaStatsIndex')->table(1);

    // As of July 2024:
    // FortiNet considers a single-node cluster a valid setup. Therefor, if a cluster node is removed or
    // defective (lost power, defective hardware) no error message can be seen. The FortiGate silently
    // removes the cluster member from the configuration after a few minutes and the cluster status becomes OK

    // Additionally, a FortiGate cluster is always in-sync with the primary.
    // So fgHaStatsSyncStatus.1 (primary node) will *always* return 1. FortiNet does not consider this
    // a bug (reference: Fortinet ticket 9671105)

    // So if we have a 2-node cluster and either one of the nodes goes offline, no alert or warning
    // can be seen in the UI. The sync status will remain 1 (In-Sync) for node/index 1 and index 2
    // will cease to exist. Again, Fortinet considers this expected behavior.

    // Use the 'Cluster State' sensor to create an alert when this value drops to 1.
    // -- Rudy Broersma

    // Per node sync status
    $stateName = 'fgHaStatsSyncStatus';

    $states = [
        ['value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'Out Of Sync'],
        ['value' => 1, 'generic' => 0, 'graph' => 1, 'descr' => 'In Sync'],
    ];

    create_state_index($stateName, $states);

    // Per-node loop
    foreach ($haStatsEntries as $index => $entry) {
        // Get current value (for issue #16544)
        $sensor_value = SnmpQuery::get('FORTINET-FORTIGATE-MIB::fgHaStatsSyncStatus.' . $index)->value(0);

        // Get name of cluster member
        $cluster_member_name = SnmpQuery::get('FORTINET-FORTIGATE-MIB::fgHaStatsHostname.' . $index)->value(0);

        $descr = 'HA sync status ' . $cluster_member_name;

        // Setup a sensor for the cluster sync state

        discover_sensor(
            null,
            'state',
            $device,
            '.1.3.6.1.4.1.12356.101.13.2.1.1.12.' . $index,
            $index,
            $stateName,
            $descr,
            1,
            1,
            null,
            null,
            null,
            null,
            $sensor_value,
            'snmp',
            $index,
            null,
            null,
            'HA'
        );
    }
}

unset(
    $systemMode,
    $haStatsEntries,
    $stateName,
    $descr,
    $states,
    $entry
);
