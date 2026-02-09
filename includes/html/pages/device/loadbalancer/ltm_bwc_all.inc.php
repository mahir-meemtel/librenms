<?php
?>
<table id='grid' data-toggle='bootgrid' class='table table-condensed table-responsive table-striped'>
    <thead>
    <tr>
        <th data-column-id="bwcid" data-type="numeric" data-visible="false">bwcid</th>
        <th data-column-id="name">Name</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($components as $bwc_id => $array) {
        if ($array['type'] != 'f5-ltm-bwc') {
            continue;
        } ?>
        <tr>
            <td><?php echo $bwc_id; ?></td>
            <td><?php echo $array['label']; ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

<div class="panel panel-default" id="BitsDropped">
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
        $graph_array['type'] = 'device_bigip_ltm_allbwc_BitsDropped';
        require 'includes/html/print-graphrow.inc.php';
        ?>
    </div>
</div>
<div class="panel panel-default" id="Bitsin">
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
        $graph_array['type'] = 'device_bigip_ltm_allbwc_Bitsin';
        require 'includes/html/print-graphrow.inc.php';
        ?>
    </div>
</div>


<div class="panel panel-default" id="pktsin">
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
        $graph_array['type'] = 'device_bigip_ltm_allbwc_pktsin';
        require 'includes/html/print-graphrow.inc.php';
        ?>
    </div>
</div>

    <script type="text/javascript">
        $("#grid").bootgrid({
            caseSensitive: false,
            statusMappings: {
                2: "danger"
            },
        }).on("click.rs.jquery.bootgrid", function (e, columns, row) {
            var link = '<?php echo \ObzoraNMS\Util\Url::generate($vars, ['type' => 'ltm_bwc', 'subtype' => 'ltm_bwc_det']); ?>bwcid='+row['bwcid'];
            window.location.href = link;
        });
    </script>
