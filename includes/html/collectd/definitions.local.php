<?php
function load_graph_definitions_local($logarithmic = false, $tinylegend = false)
{
    global $GraphDefs, $MetaGraphDefs;

    // Define 1-rrd Graph definitions here
    $GraphDefs['local_type'] = [
        '-v', 'Commits',
        'DEF:avg={file}:value:AVERAGE',
        'DEF:min={file}:value:MIN',
        'DEF:max={file}:value:MAX',
        'AREA:max#B7B7F7',
        'AREA:min#FFFFFF',
        'LINE1:avg#0000FF:Commits',
        'GPRINT:min:MIN:%6.1lf Min,',
        'GPRINT:avg:AVERAGE:%6.1lf Avg,',
        'GPRINT:max:MAX:%6.1lf Max,',
        'GPRINT:avg:LAST:%6.1lf Last\l', ];

    // Define MetaGraph definition type -> function mappings here
    $MetaGraphDefs['local_meta'] = 'meta_graph_local';
}

function meta_graph_local($host, $plugin, $plugin_instance, $type, $type_instances, $opts = [])
{
    $sources = [];

    $title = "$host/$plugin" . (! is_null($plugin_instance) ? "-$plugin_instance" : '') . "/$type";
    if (! isset($opts['title'])) {
        $opts['title'] = $title;
    }
    $opts['rrd_opts'] = ['-v', 'Events'];
    /*  $opts['colors'] = array(
            'ham'     => '00e000',
            'spam'    => '0000ff',
            'malware' => '990000',

            'sent'     => '00e000',
            'deferred' => 'a0e000',
            'reject'   => 'ff0000',
            'bounced'  => 'a00050'
        );

        $type_instances = array('ham', 'spam', 'malware',  'sent', 'deferred', 'reject', 'bounced'); */
    foreach ($type_instances as $inst) {
        $file = '';
        foreach (\App\Facades\ObzoraConfig::get('datadirs') as $datadir) {
            if (is_file($datadir . '/' . $title . '-' . $inst . '.rrd')) {
                $file = $datadir . '/' . $title . '-' . $inst . '.rrd';
                break;
            }
        }
        if ($file == '') {
            continue;
        }

        $sources[] = ['name' => $inst, 'file' => $file];
    }

    //  return collectd_draw_meta_stack($opts, $sources);
    return collectd_draw_meta_line($opts, $sources);
}
