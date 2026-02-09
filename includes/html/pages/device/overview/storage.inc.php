<?php
use Illuminate\Support\Str;
use ObzoraNMS\Util\Number;

$graph_type = 'storage_usage';

$drives = dbFetchRows('SELECT * FROM `storage` WHERE device_id = ? ORDER BY `storage_descr` ASC', [$device['device_id']]);

if (count($drives)) {
    echo '
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default panel-condensed">
                <div class="panel-heading">';
    echo '<a href="device/device=' . $device['device_id'] . '/tab=health/metric=storage/">';
    echo '<i class="fa fa-database fa-lg icon-theme" aria-hidden="true"></i> <strong>Storage</strong></a>';
    echo '    </div>
            <table class="table table-hover table-condensed table-striped">';

    foreach ($drives as $drive) {
        $skipdrive = 0;

        if ($device['os'] == 'junos') {
            foreach (\App\Facades\ObzoraConfig::get('ignore_junos_os_drives', []) as $jdrive) {
                if (preg_match($jdrive, $drive['storage_descr'])) {
                    $skipdrive = 1;
                }
            }

            $drive['storage_descr'] = preg_replace('/.*mounted on: (.*)/', '\\1', $drive['storage_descr']);
        }

        if ($device['os'] == 'freebsd') {
            foreach (\App\Facades\ObzoraConfig::get('ignore_bsd_os_drives', []) as $jdrive) {
                if (preg_match($jdrive, $drive['storage_descr'])) {
                    $skipdrive = 1;
                }
            }
        }

        if ($skipdrive) {
            continue;
        }

        $percent = round($drive['storage_perc']);
        $total = Number::formatBi($drive['storage_size']);
        $free = Number::formatBi($drive['storage_free']);
        $used = Number::formatBi($drive['storage_used']);
        $background = \ObzoraNMS\Util\Color::percentage($percent, $drive['storage_perc_warn']);

        $graph_array = [];
        $graph_array['height'] = '100';
        $graph_array['width'] = '210';
        $graph_array['to'] = \App\Facades\ObzoraConfig::get('time.now');
        $graph_array['id'] = $drive['storage_id'];
        $graph_array['type'] = $graph_type;
        $graph_array['from'] = \App\Facades\ObzoraConfig::get('time.day');
        $graph_array['legend'] = 'no';

        $link_array = $graph_array;
        $link_array['page'] = 'graphs';
        unset($link_array['height'], $link_array['width'], $link_array['legend']);
        $link = \ObzoraNMS\Util\Url::generate($link_array);

        $drive['storage_descr'] = Str::limit($drive['storage_descr'], 50);

        $overlib_content = generate_overlib_content($graph_array, $device['hostname'] . ' - ' . $drive['storage_descr']);

        $graph_array['width'] = 80;
        $graph_array['height'] = 20;
        $graph_array['bg'] = 'ffffff00';
        // the 00 at the end makes the area transparent.
        $minigraph = \ObzoraNMS\Util\Url::lazyGraphTag($graph_array);

        // Use modern storage colors
        $storage_bar = \ObzoraNMS\Util\Html::percentageBar(400, 20, $percent, "$used / $total ($percent%)", $free, $drive['storage_perc_warn'], null, null, 'storage');
        echo '<tr>
           <td class="col-md-4">' . \ObzoraNMS\Util\Url::overlibLink($link, $drive['storage_descr'], $overlib_content) . '</td>
           <td class="col-md-4">' . \ObzoraNMS\Util\Url::overlibLink($link, $minigraph, $overlib_content) . '</td>
           <td class="col-md-4">' . \ObzoraNMS\Util\Url::overlibLink($link, $storage_bar, $overlib_content) . '
           </a></td>
         </tr>';
    }//end foreach

    echo '</table>
        </div>
        </div>
        </div>';
}//end if

unset($drive_rows);
