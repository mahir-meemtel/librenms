<?php
include 'includes/html/pages/device/loadbalancer/ltm_bwc_common.inc.php';
if ($components[$vars['bwcid']]['type'] == 'f5-ltm-bwc') {
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="container-fluid">
                <div class='row'>
                    <div class="panel panel-default" id="Bits Dropped">
                        <div class="panel-heading">
                            <h3 class="panel-title">Traffic Dropped</h3>
                        </div>
                        <div class="panel-body">
                            <?php
                            $graph_array = [];
    $graph_array['device'] = $device['device_id'];
    $graph_array['height'] = '100';
    $graph_array['width'] = '215';
    $graph_array['legend'] = 'no';
    $graph_array['to'] = \App\Facades\ObzoraConfig::get('time.now');
    $graph_array['type'] = 'device_bigip_ltm_bwc_BitsDropped';
    $graph_array['id'] = $vars['bwcid'];
    require 'includes/html/print-graphrow.inc.php'; ?>
                        </div>
                    </div>

                    <div class="panel panel-default" id="Bits">
                        <div class="panel-heading">
                            <h3 class="panel-title">Traffic In</h3>
                        </div>
                        <div class="panel-body">
                            <?php
                            $graph_array = [];
    $graph_array['device'] = $device['device_id'];
    $graph_array['height'] = '100';
    $graph_array['width'] = '215';
    $graph_array['legend'] = 'no';
    $graph_array['to'] = \App\Facades\ObzoraConfig::get('time.now');
    $graph_array['type'] = 'device_bigip_ltm_bwc_Bits';
    $graph_array['id'] = $vars['bwcid'];
    require 'includes/html/print-graphrow.inc.php'; ?>
                        </div>
                    </div>

                    <div class="panel panel-default" id="pkts">
                        <div class="panel-heading">
                            <h3 class="panel-title">Packets In</h3>
                        </div>
                        <div class="panel-body">
                            <?php
                            $graph_array = [];
    $graph_array['device'] = $device['device_id'];
    $graph_array['height'] = '100';
    $graph_array['width'] = '215';
    $graph_array['legend'] = 'no';
    $graph_array['to'] = \App\Facades\ObzoraConfig::get('time.now');
    $graph_array['type'] = 'device_bigip_ltm_bwc_pkts';
    $graph_array['id'] = $vars['bwcid'];
    require 'includes/html/print-graphrow.inc.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
}
