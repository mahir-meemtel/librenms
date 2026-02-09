<?php
use ObzoraNMS\RRD\RrdDefinition;

/*
 * Check if port has one of the counters ('cieIfInRuntsErrs') from CISCO-IF-EXTENSION MIB
 */
if (isset($this_port['cieIfInRuntsErrs'])) {
    /*
     * Build interface RRD with filename in format of:
     * port-id<ifIndex>-cie.rrd
     */
    $rrd_name = Rrd::portName($port_id, 'cie');
    $rrdfile = Rrd::name($device['hostname'], $rrd_name);
    $rrd_def = RrdDefinition::make()
        ->addDataset('InRuntsErrs', 'DERIVE', 0)
        ->addDataset('InGiantsErrs', 'DERIVE', 0)
        ->addDataset('InFramingErrs', 'DERIVE', 0)
        ->addDataset('InOverrunErrs', 'DERIVE', 0)
        ->addDataset('InIgnored', 'DERIVE', 0)
        ->addDataset('InAbortErrs', 'DERIVE', 0)
        ->addDataset('InputQueueDrops', 'DERIVE', 0)
        ->addDataset('OutputQueueDrops', 'DERIVE', 0);

    /*
     * Populate data for RRD
     */
    $rrd_data = [];
    foreach ($cisco_if_extension_oids as $oid) {
        $ds_name = str_replace('cieIf', '', $oid);
        $rrd_data[$ds_name] = $this_port[$oid];
    }

    /*
     * Generate/update RRD
     */
    $ifName = $port['ifName'];
    $tags = ['ifName' => $ifName, 'rrd_name' => $rrd_name, 'rrd_def' => $rrd_def];
    app('Datastore')->put($device, 'drops', $tags, $rrd_data);
}
