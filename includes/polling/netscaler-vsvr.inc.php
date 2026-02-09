<?php
use ObzoraNMS\RRD\RrdDefinition;

if ($device['os'] == 'netscaler') {
    $oids_gauge = [
        'vsvrCurClntConnections',
        'vsvrCurSrvrConnections',
    ];

    $oids_counter = [
        'vsvrSurgeCount',
        'vsvrTotalRequests',
        'vsvrTotalRequestBytes',
        'vsvrTotalResponses',
        'vsvrTotalResponseBytes',
        'vsvrTotalPktsRecvd',
        'vsvrTotalPktsSent',
        'vsvrTotalSynsRecvd',
        'vsvrTotMiss',
        'vsvrTotHits',
        'vsvrTotSpillOvers',
        'vsvrTotalClients',
    ];

    $oids = array_merge($oids_gauge, $oids_counter);

    $rrd_def = new RrdDefinition();
    foreach ($oids_gauge as $oid) {
        $oid_ds = str_replace('vsvr', '', $oid);
        $rrd_def->addDataset($oid_ds, 'GAUGE', null, 100000000000);
    }
    foreach ($oids_counter as $oid) {
        $oid_ds = str_replace('vsvr', '', $oid);
        $rrd_def->addDataset($oid_ds, 'COUNTER', null, 100000000000);
    }

    $vsvr_array = snmpwalk_cache_oid($device, 'vserverEntry', [], 'NS-ROOT-MIB');

    $vsvr_db = dbFetchRows('SELECT * FROM `netscaler_vservers` WHERE `device_id` = ?', [$device['device_id']]);
    foreach ($vsvr_db as $vsvr) {
        $vsvrs[$vsvr['vsvr_name']] = $vsvr;
        print_r($vsvr);
    }

    d_echo($vsvrs);

    foreach ($vsvr_array as $index => $vsvr) {
        if (isset($vsvr['vsvrFullName'])) {
            $vsvr_exist[$vsvr['vsvrFullName']] = 1;
            $rrd_name = 'netscaler-vsvr-' . $vsvr['vsvrFullName'];

            $fields = [];
            foreach ($oids as $oid) {
                $oid_ds = str_replace('vsvr', '', $oid);
                if (is_numeric($vsvr[$oid])) {
                    $fields[$oid_ds] = $vsvr[$oid];
                } else {
                    $fields[$oid_ds] = 'U';
                }
            }

            $tags = [
                'vsvrFullName' => $vsvr['vsvrFullName'],
                'rrd_name' => $rrd_name,
                'rrd_def' => $rrd_def,
            ];
            app('Datastore')->put($device, 'netscaler-vsvr', $tags, $fields);

            echo str_pad($vsvr['vsvrFullName'], 25) . ' | ' . str_pad($vsvr['vsvrType'], 5) . ' | ' . str_pad($vsvr['vsvrState'], 6) . ' | ' . str_pad($vsvr['vsvrIpAddress'], 16) . ' | ' . str_pad($vsvr['vsvrPort'], 5);
            echo ' | ' . str_pad($vsvr['vsvrRequestRate'], 8) . ' | ' . str_pad($vsvr['vsvrRxBytesRate'] . 'B/s', 8) . ' | ' . str_pad($vsvr['vsvrTxBytesRate'] . 'B/s', 8);

            $db_update = [
                'vsvr_ip' => $vsvr['vsvrIpAddress'],
                'vsvr_port' => $vsvr['vsvrPort'],
                'vsvr_state' => $vsvr['vsvrState'],
                'vsvr_type' => $vsvr['vsvrType'],
                'vsvr_req_rate' => $vsvr['vsvrRequestRate'] ?? 0,
                'vsvr_bps_in' => $vsvr['vsvrRxBytesRate'] ?? 0,
                'vsvr_bps_out' => $vsvr['vsvrTxBytesRate'] ?? 0,
            ];

            if (! is_array($vsvrs[$vsvr['vsvrFullName']])) {
                $db_insert = array_merge(['device_id' => $device['device_id'], 'vsvr_name' => $vsvr['vsvrFullName']], $db_update);
                $vsvr_id = dbInsert($db_insert, 'netscaler_vservers');
                echo ' +';
            } else {
                $updated = dbUpdate($db_update, 'netscaler_vservers', '`vsvr_id` = ?', [$vsvrs[$vsvr['vsvrFullName']]['vsvr_id']]);
                echo ' U';
            }

            echo "\n";
        }//end if
    }//end foreach

    d_echo($vsvr_exist);

    foreach ($vsvrs as $db_name => $db_id) {
        if (! $vsvr_exist[$db_name]) {
            echo '-' . $db_name;
            dbDelete('netscaler_vservers', '`vsvr_id` =  ?', [$db_id['vsvr_id']]);
        }
    }
}//end if
