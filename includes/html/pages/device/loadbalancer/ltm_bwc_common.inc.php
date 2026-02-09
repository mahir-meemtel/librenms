<?php
$subtypes = [];
$subtypes['ltm_bwc_det'] = 'Bandwidth Controller Details';

if (! $vars['subtype']) {
    $vars['subtype'] = 'ltm_bwc_det';
}

// Determine a policy to show.
if (! isset($vars['bwcid'])) {
    foreach ($components as $id => $array) {
        if ($array['type'] != 'f5-ltm-bwc') {
            continue;
        }
        $vars['bwcid'] = $id;
    }
}

print_optionbar_start();
?>
    <div class='row' style="margin-bottom: 10px;">
        <div class='col-md-12'>
            <span style="font-size: 20px;">Bandwidth Controller - <?php echo $components[$vars['bwcid']]['label'] ?></span><br /> 
        </div>
    </div>
    <div class='row'>
        <div class='col-md-12'>
        </div>
    </div>
<?php
print_optionbar_end();
