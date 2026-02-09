<?php
use ObzoraNMS\RRD\RrdDefinition;

$data = snmpwalk_cache_oid($device, 'ipSystemStats', null, 'IP-MIB');

if ($data) {
    $oids = [
        'ipSystemStatsInReceives',
        'ipSystemStatsInHdrErrors',
        'ipSystemStatsInAddrErrors',
        'ipSystemStatsInUnknownProtos',
        'ipSystemStatsInForwDatagrams',
        'ipSystemStatsReasmReqds',
        'ipSystemStatsReasmOKs',
        'ipSystemStatsReasmFails',
        'ipSystemStatsInDiscards',
        'ipSystemStatsInDelivers',
        'ipSystemStatsOutRequests',
        'ipSystemStatsOutNoRoutes',
        'ipSystemStatsOutDiscards',
        'ipSystemStatsOutFragFails',
        'ipSystemStatsOutFragCreates',
        'ipSystemStatsOutForwDatagrams',
    ];

    foreach ($data as $af => $stats) {
        echo "$af ";

        // Use HC counters instead if they're available.
        if (isset($stats['ipSystemStatsHCInReceives'])) {
            $stats['ipSystemStatsInReceives'] = $stats['ipSystemStatsHCInReceives'];
        }

        if (isset($stats['ipSystemStatsHCInForwDatagrams'])) {
            $stats['ipSystemStatsInForwDatagrams'] = $stats['ipSystemStatsHCInForwDatagrams'];
        }

        if (isset($stats['ipSystemStatsHCInDelivers'])) {
            $stats['ipSystemStatsInDelivers'] = $stats['ipSystemStatsHCInDelivers'];
        }

        if (isset($stats['ipSystemStatsHCOutRequests'])) {
            $stats['ipSystemStatsOutRequests'] = $stats['ipSystemStatsHCOutRequests'];
        }

        if (isset($stats['ipSystemStatsHCOutForwDatagrams'])) {
            $stats['ipSystemStatsOutForwDatagrams'] = $stats['ipSystemStatsHCOutForwDatagrams'];
        }

        $rrd_name = ['ipSystemStats', $af];
        $rrd_def = new RrdDefinition();
        $fields = [];

        foreach ($oids as $oid) {
            $oid_ds = str_replace('ipSystemStats', '', $oid);
            $rrd_def->addDataset($oid_ds, 'COUNTER');
            if (! isset($stats[$oid]) || (strstr($stats[$oid], 'No') || strstr($stats[$oid], 'd') || strstr($stats[$oid], 's'))) {
                $stats[$oid] = '0';
            }
            $fields[$oid_ds] = $stats[$oid];
        }

        $tags = ['af' => $af, 'rrd_name' => $rrd_name, 'rrd_def' => $rrd_def];
        app('Datastore')->put($device, 'ipSystemStats', $tags, $fields);

        // FIXME per-AF?
        $os->enableGraph("ipsystemstats_$af");
        $os->enableGraph("ipsystemstats_{$af}_frag");
    }//end foreach
}//end if

unset($oids, $data);
echo "\n";
