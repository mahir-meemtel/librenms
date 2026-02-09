<?php
$graphs = [
    'powerdns-dnsdist_latency' => 'Latency',
    'powerdns-dnsdist_cache' => 'Cache',
    'powerdns-dnsdist_downstream' => 'Downstream servers',
    'powerdns-dnsdist_dynamic_blocks' => 'Dynamic blocks',
    'powerdns-dnsdist_rules_stats' => 'Rules stats',
    'powerdns-dnsdist_queries_stats' => 'Queries stats',
    'powerdns-dnsdist_queries_latency' => 'Queries latency',
    'powerdns-dnsdist_queries_drop' => 'Queries drop',
];

foreach ($graphs as $key => $text) {
    $graph_type = $key;
    $graph_array['height'] = '100';
    $graph_array['width'] = '215';
    $graph_array['to'] = \App\Facades\ObzoraConfig::get('time.now');
    $graph_array['id'] = $app['app_id'];
    $graph_array['type'] = 'application_' . $key;

    echo '<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">' . $text . '</h3>
    </div>
    <div class="panel-body">
    <div class="row">';
    include 'includes/html/print-graphrow.inc.php';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
