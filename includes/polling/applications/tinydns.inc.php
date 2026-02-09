<?php
use ObzoraNMS\RRD\RrdDefinition;

$name = 'tinydns';

if (! empty($agent_data['app'][$name]) && $app->app_id > 0) {
    echo ' tinydns';
    $rrd_def = RrdDefinition::make()
        ->addDataset('a', 'COUNTER', 0, 125000000000)
        ->addDataset('ns', 'COUNTER', 0, 125000000000)
        ->addDataset('cname', 'COUNTER', 0, 125000000000)
        ->addDataset('soa', 'COUNTER', 0, 125000000000)
        ->addDataset('ptr', 'COUNTER', 0, 125000000000)
        ->addDataset('hinfo', 'COUNTER', 0, 125000000000)
        ->addDataset('mx', 'COUNTER', 0, 125000000000)
        ->addDataset('txt', 'COUNTER', 0, 125000000000)
        ->addDataset('rp', 'COUNTER', 0, 125000000000)
        ->addDataset('sig', 'COUNTER', 0, 125000000000)
        ->addDataset('key', 'COUNTER', 0, 125000000000)
        ->addDataset('aaaa', 'COUNTER', 0, 125000000000)
        ->addDataset('axfr', 'COUNTER', 0, 125000000000)
        ->addDataset('any', 'COUNTER', 0, 125000000000)
        ->addDataset('total', 'COUNTER', 0, 125000000000)
        ->addDataset('other', 'COUNTER', 0, 125000000000)
        ->addDataset('notauth', 'COUNTER', 0, 125000000000)
        ->addDataset('notimpl', 'COUNTER', 0, 125000000000)
        ->addDataset('badclass', 'COUNTER', 0, 125000000000)
        ->addDataset('noquery', 'COUNTER', 0, 125000000000);

    [
        $a, $ns, $cname, $soa, $ptr, $hinfo, $mx, $txt, $rp, $sig, $key, $aaaa, $axfr, $any,
        $total, $other, $notauth, $notimpl, $badclass, $noquery
    ] = explode(':', $agent_data['app'][$name]);

    $fields = [
        'a' => $a,
        'ns' => $ns,
        'cname' => $cname,
        'soa' => $soa,
        'ptr' => $ptr,
        'hinfo' => $hinfo,
        'mx' => $mx,
        'txt' => $txt,
        'rp' => $rp,
        'sig' => $sig,
        'key' => $key,
        'aaaa' => $aaaa,
        'axfr' => $axfr,
        'any' => $any,
        'total' => $total,
        'other' => $other,
        'notauth' => $notauth,
        'notimpl' => $notimpl,
        'badclass' => $badclass,
        'noquery' => $noquery,
    ];

    $tags = [
        'name' => $name,
        'app_id' => $app->app_id,
        'rrd_name' => ['app', $name, $app->app_id],
        'rrd_def' => $rrd_def,
    ];
    app('Datastore')->put($device, 'app', $tags, $fields);
    update_application($app, $name, $fields);
}//end if
