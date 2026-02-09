<?php
$unitlen = 10;
$bigdescrlen = 9;
$smalldescrlen = 9;
$dostack = 0;
$printtotal = 0;
$unit_text = 'query/sec';
$colours = 'psychedelic';
$rrd_list = [];

$rrd_filename = Rrd::name($device['hostname'], ['app', 'bind', $app->app_id]);
$array = [
    'any',
    'a',
    'aaaa',
    'cname',
    'mx',
    'ns',
    'ptr',
    'soa',
    'srv',
    'spf',
];
if (Rrd::checkRrdExists($rrd_filename)) {
    foreach ($array as $ds) {
        $rrd_list[] = [
            'filename' => $rrd_filename,
            'descr' => strtoupper($ds),
            'ds' => $ds,
        ];
    }
} else {
    throw new \ObzoraNMS\Exceptions\RrdGraphException("No Data file $rrd_filename");
}

$rrd_filename = Rrd::name($device['hostname'], ['app', 'bind', $app->app_id, 'incoming']);
$array = [
    'afsdb',
    'apl',
    'caa',
    'cdnskey',
    'cds',
    'cert',
    'dhcid',
    'dlv',
    'dnskey',
    'ds',
    'ipseckey',
    'key',
    'kx',
    'loc',
    'naptr',
    'nsec',
    'nsec3',
    'nsec3param',
    'rrsig',
    'rp',
    'sig',
    'sshfp',
    'ta',
    'tkey',
    'tlsa',
    'tsig',
    'txt',
    'uri',
    'dname',
    'axfr',
    'ixfr',
    'opt',
];
foreach ($array as $ds) {
    $rrd_list[] = [
        'filename' => $rrd_filename,
        'descr' => strtoupper($ds),
        'ds' => $ds,
    ];
}

require 'includes/html/graphs/generic_multi_line.inc.php';
