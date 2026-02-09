<?php

/**
 * CUCM Application Display Template
 * Features: 
 * - Summary Counters (Total, Registered, Unregistered)
 * - 3-Tab Interface
 * - Full Model Translation
 * - Independent DataTables per Tab
 */

$id = is_object($app) ? $app->app_id : $app['app_id'];
$cucm_table = dbFetchCell("SELECT `data` FROM `applications` WHERE `app_id` = ?", array($id));

if (!empty($cucm_table)) {
    // 1. FULL ENUM MAP - Translates Model IDs to Names
    $model_map = [
        15 => 'EMCC Base Phone', 20 => 'SCCP Phone', 30 => 'Analog Access', 40 => 'Digital Access',
        42 => 'Digital Access+', 43 => 'Digital Access WS-X6608', 47 => 'Analog Access WS-X6624',
        50 => 'Conference Bridge', 51 => 'Conference Bridge WS-X6608', 62 => 'H.323 Gateway',
        70 => 'Music On Hold', 71 => 'Device Pilot', 73 => 'CTI Route Point', 80 => 'Voice Mail Port',
        90 => 'Route List', 100 => 'Load Simulator', 110 => 'Media Termination Point',
        111 => 'Media Termination Point Hardware', 120 => 'MGCP Station', 121 => 'MGCP Trunk',
        122 => 'GateKeeper', 125 => 'Trunk', 126 => 'Tone Announcement Player',
        254 => 'Unknown MGCP Gateway', 255 => 'Unknown', 52 => 'Cisco IOS Conference Bridge (HDV2)',
        53 => 'Cisco Conference Bridge (WS-SVC-CMM)', 83 => 'Cisco IOS Software Media Termination Point (HDV2)',
        84 => 'Cisco Media Server (WS-SVC-CMM-MS)', 112 => 'Cisco IOS Media Termination Point (HDV2)',
        113 => 'Cisco Media Termination Point (WS-SVC-CMM)', 131 => 'SIP Trunk', 132 => 'SIP Gateway',
        133 => 'WSM Trunk', 85 => 'Cisco Video Conference Bridge (IPVC-35xx)', 522 => 'BlackBerry MVS VoWifi',
        640 => 'Usage Profile', 598 => 'Ascom IP-DECT Device', 599 => 'Cisco TelePresence Exchange System',
        36041 => 'Cisco TelePresence Conductor', 36219 => 'Interactive Voice Response', 36250 => 'Cisco Meeting Server',
        61 => 'H.323 Phone', 72 => 'CTI Port', 134 => 'Remote Destination Profile', 30027 => 'Analog Phone',
        30028 => 'ISDN BRI Phone', 36298 => 'SIP Station', 2 => 'Cisco 12 SP+', 3 => 'Cisco 12 SP',
        4 => 'Cisco 12 S', 1 => 'Cisco 30 SP+', 5 => 'Cisco 30 VIP', 9 => 'Cisco 7935', 6 => 'Cisco 7910',
        7 => 'Cisco 7960', 8 => 'Cisco 7940', 10 => 'Cisco VGC Phone', 11 => 'Cisco VGC Virtual Phone',
        48 => 'VGC Gateway', 12 => 'Cisco ATA 186', 124 => '7914 14-Button Line Expansion Module',
        336 => 'Third-party SIP Device (Basic)', 374 => 'Third-party SIP Device (Advanced)',
        115 => 'Cisco 7941', 119 => 'Cisco 7971', 20000 => 'Cisco 7905', 302 => 'Cisco 7985',
        307 => 'Cisco 7911', 308 => 'Cisco 7961G-GE', 309 => 'Cisco 7941G-GE', 335 => 'Motorola CN622',
        348 => 'Cisco 7931', 358 => 'Cisco Unified Personal Communicator', 365 => 'Cisco 7921',
        369 => 'Cisco 7906', 375 => 'Cisco TelePresence', 376 => 'Nokia S60', 30002 => 'Cisco 7920',
        30006 => 'Cisco 7970', 30007 => 'Cisco 7912', 30008 => 'Cisco 7902', 30016 => 'Cisco IP Communicator',
        30018 => 'Cisco 7961', 30019 => 'Cisco 7936', 30032 => 'SCCP gateway virtual phone', 30035 => 'IP-STE',
        404 => 'Cisco 7962', 412 => 'Cisco 3951', 431 => 'Cisco 7937', 434 => 'Cisco 7942', 435 => 'Cisco 7945',
        436 => 'Cisco 7965', 437 => 'Cisco 7975', 446 => 'Cisco 3911', 550 => 'Cisco ATA 187',
        631 => 'Third-party AS-SIP Endpoint', 36049 => 'BEKEM 36-Button Line Expansion Module',
        36263 => 'Cisco Collaboration Mobile Convergence', 86 => 'Cisco IOS Heterogeneous Video Conference Bridge',
        87 => 'Cisco IOS Guaranteed Audio Video Conference Bridge', 88 => 'Cisco IOS Homogeneous Video Conference Bridge',
        227 => '7915 12-Button Line Expansion Module', 228 => '7915 24-Button Line Expansion Module',
        229 => '7916 12-Button Line Expansion Module', 230 => '7916 24-Button Line Expansion Module',
        232 => 'CKEM 36-Button Line Expansion Module', 468 => 'Cisco Unified Mobile Communicator',
        478 => 'Cisco TelePresence 1000', 479 => 'Cisco TelePresence 3000', 480 => 'Cisco TelePresence 3200',
        481 => 'Cisco TelePresence 500-37', 484 => 'Cisco 7925', 493 => 'Cisco 9971', 495 => 'Cisco 6921',
        496 => 'Cisco 6941', 497 => 'Cisco 6961', 503 => 'Cisco Unified Client Services Framework',
        505 => 'Cisco TelePresence 1300-65', 520 => 'Cisco TelePresence 1100', 521 => 'Transnova S3',
        537 => 'Cisco 9951', 540 => 'Cisco 8961', 547 => 'Cisco 6901', 548 => 'Cisco 6911',
        557 => 'Cisco TelePresence 200', 558 => 'Cisco TelePresence 400', 562 => 'Cisco Dual Mode for iPhone',
        564 => 'Cisco 6945', 575 => 'Cisco Dual Mode for Android', 577 => 'Cisco 7926', 580 => 'Cisco E20',
        582 => 'Generic Single Screen Room System', 583 => 'Generic Multiple Screen Room System',
        584 => 'Cisco TelePresence EX90', 585 => 'Cisco 8945', 586 => 'Cisco 8941', 588 => 'Generic Desktop Video Endpoint',
        590 => 'Cisco TelePresence 500-32', 591 => 'Cisco TelePresence 1300-47', 592 => 'Cisco 3905',
        593 => 'Cisco Cius', 594 => 'VKEM 36-Button Line Expansion Module', 596 => 'Cisco TelePresence TX1310-65',
        597 => 'Cisco TelePresence MCU', 604 => 'Cisco TelePresence EX60', 606 => 'Cisco TelePresence Codec C90',
        607 => 'Cisco TelePresence Codec C60', 608 => 'Cisco TelePresence Codec C40', 609 => 'Cisco TelePresence Quick Set C20',
        610 => 'Cisco TelePresence Profile 42 (C20)', 611 => 'Cisco TelePresence Profile 42 (C60)',
        612 => 'Cisco TelePresence Profile 52 (C40)', 613 => 'Cisco TelePresence Profile 52 (C60)',
        614 => 'Cisco TelePresence Profile 52 Dual (C60)', 615 => 'Cisco TelePresence Profile 65 (C60)',
        616 => 'Cisco TelePresence Profile 65 Dual (C90)', 617 => 'Cisco TelePresence MX200',
        619 => 'Cisco TelePresence TX9000', 620 => 'Cisco TelePresence TX9200', 621 => 'Cisco 7821',
        622 => 'Cisco 7841', 623 => 'Cisco 7861', 626 => 'Cisco TelePresence SX20', 627 => 'Cisco TelePresence MX300',
        628 => 'IMS-integrated Mobile (Basic)', 632 => 'Cisco Cius SP', 633 => 'Cisco TelePresence Profile 42 (C40)',
        634 => 'Cisco VXC 6215', 642 => 'Carrier-integrated Mobile', 645 => 'Universal Device Template',
        647 => 'Cisco DX650', 648 => 'Cisco Unified Communications for RTX', 652 => 'Cisco Jabber for Tablet',
        659 => 'Cisco 8831', 681 => 'Cisco ATA 190', 682 => 'Cisco TelePresence SX10', 683 => 'Cisco 8841',
        684 => 'Cisco 8851', 685 => 'Cisco 8861', 688 => 'Cisco TelePresence SX80', 689 => 'Cisco TelePresence MX200 G2',
        690 => 'Cisco TelePresence MX300 G2', 253 => 'SPA8800', 36042 => 'Cisco DX80', 36043 => 'Cisco DX70',
        36207 => 'Cisco TelePresence MX700', 36208 => 'Cisco TelePresence MX800', 36210 => 'Cisco TelePresence IX5000',
        36213 => 'Cisco 7811', 36216 => 'Cisco 8821', 36217 => 'Cisco 8811', 36224 => 'Cisco 8845',
        36225 => 'Cisco 8865', 36227 => 'Cisco TelePresence MX800 Dual', 36232 => 'Cisco 8851NR',
        36235 => 'Cisco Spark Remote Device', 36239 => 'Cisco Webex DX80', 36241 => 'Cisco TelePresence DX70',
        36248 => 'Cisco 8865NR', 36251 => 'Cisco Webex Room Kit', 36254 => 'Cisco Webex Room 55',
        36255 => 'Cisco Webex Room Kit Plus', 36257 => 'CP-8800-Audio 28-Button Key Expansion Module',
        36256 => 'CP-8800-Video 28-Button Key Expansion Module', 36307 => 'Cisco Webex Desk Pro',
        635 => 'CTI Remote Device', 36312 => 'Cisco Webex Room Phone', 36309 => 'Cisco Webex Room 70 Panorama',
        36306 => 'Cisco Webex Board 85', 36265 => 'Cisco Webex Room 70 Dual', 36320 => 'Cisco 840',
        36322 => 'Cisco Webex Desk LE', 36304 => 'Cisco Webex Board 55', 36262 => 'Cisco ATA 191',
        36297 => 'Cisco Webex Room 70 Dual G2', 36299 => 'Cisco Webex Room Kit Mini', 36305 => 'Cisco Webex Board 70',
        36292 => 'Cisco Webex Room Kit Pro', 36319 => 'Cisco 860', 36247 => 'Cisco 7832',
        36308 => 'Cisco Webex Room Panorama', 36296 => 'Cisco Webex Room 70 Single G2', 36326 => 'Cisco Webex Desk Mini',
        36260 => 'Cisco 8832NR', 36258 => 'Cisco 8832', 36302 => 'Cisco Webex VDI Svc Framework',
        36327 => 'Cisco Webex Desk Hub', 36324 => 'Cisco Webex Desk', 36295 => 'Cisco Webex Room 55 Dual',
        36259 => 'Cisco Webex Room 70 Single',
    ];

    // 2. APPLY TRANSLATION
    foreach ($model_map as $code => $name) {
        $cucm_table = preg_replace('/<td>' . $code . '<\/td>/', '<td>' . $name . '</td>', $cucm_table);
    }

    // 3. PARSE DATA FOR CATEGORIZATION
    // Extract table rows using regex to categorize by status
    preg_match_all('/<tr>(.*?)<\/tr>/s', $cucm_table, $matches);
    
    // Safety check if no rows found
    if (empty($matches[0])) {
        echo '<div class="alert alert-warning">Data format unrecognized. Check poller output.</div>';
        return;
    }

    $header = $matches[0][0]; // The first row is the header
    $all_rows_temp = array_slice($matches[0], 1); // Everything else is data
    
    $reg_rows = [];
    $unreg_rows = [];


foreach ($all_rows_temp as $row) {

    // Registered
    if (stripos($row, '>Registered<') !== false) {

        $row = preg_replace(
            '/>Registered</i',
            '><span class="label label-success">Registered</span><',
            $row
        );

        $reg_rows[] = $row;

    } 
    // UnRegistered or Rejected
    elseif (
        stripos($row, '>UnRegistered<') !== false ||
        stripos($row, '>Rejected<') !== false
    ) {

        $row = preg_replace(
            '/>(UnRegistered|Rejected)</i',
            '><span class="label label-danger">$1</span><',
            $row
        );

        $unreg_rows[] = $row;

    } 
    // Anything else
    else {
        $unreg_rows[] = $row;
    }
    $all_rows[] = $row;
}



    // 4. DISPLAY SUMMARY COUNTERS
    echo '
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-4">
            <div class="well well-sm text-center" style="background:#f9f9f9; border-top: 3px solid #337ab7;">
                <h4 style="color:#777; font-size:12px; font-weight:bold; margin-bottom:5px;">TOTAL DEVICES</h4>
                <h2 style="margin:0; font-weight:bold; color:#337ab7;">' . count($all_rows) . '</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="well well-sm text-center" style="background:#f9f9f9; border-top: 3px solid #5cb85c;">
                <h4 style="color:#777; font-size:12px; font-weight:bold; margin-bottom:5px;">REGISTERED</h4>
                <h2 style="margin:0; font-weight:bold; color:#5cb85c;">' . count($reg_rows) . '</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="well well-sm text-center" style="background:#f9f9f9; border-top: 3px solid #d9534f;">
                <h4 style="color:#777; font-size:12px; font-weight:bold; margin-bottom:5px;">UNREGISTERED</h4>
                <h2 style="margin:0; font-weight:bold; color:#d9534f;">' . (count($all_rows) - count($reg_rows)) . '</h2>
            </div>
        </div>
    </div>';

    // 5. RENDER TABBED INTERFACE
    echo '
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#cucm_all" data-toggle="tab"><i class="fa fa-list"></i> All Phones</a></li>
            <li><a href="#cucm_reg" data-toggle="tab"><i class="fa fa-check-circle text-success"></i> Registered</a></li>
            <li><a href="#cucm_unreg" data-toggle="tab"><i class="fa fa-times-circle text-danger"></i> Unregistered</a></li>
        </ul>
        <div class="tab-content" style="padding: 15px 0;">
            <div class="tab-pane active" id="cucm_all">'.generateCucmHtmlTable('tableAll', $header, $all_rows).'</div>
            <div class="tab-pane" id="cucm_reg">'.generateCucmHtmlTable('tableReg', $header, $reg_rows).'</div>
            <div class="tab-pane" id="cucm_unreg">'.generateCucmHtmlTable('tableUnreg', $header, $unreg_rows).'</div>
        </div>
    </div>';

    // 6. JAVASCRIPT: DATA TABLES & TAB HANDLING
    ?>
    <script type="text/javascript">
    function runCucmInit() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.dataTable !== 'undefined') {
            
            // Helper to init specific table
            var setupTable = function(tableId) {
                var table = jQuery('#' + tableId).DataTable({
                    "stateSave": true,
                    "paging": true,
                    "pageLength": 100,
                    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>"
                });

                // Add individual column filtering
                jQuery('#' + tableId + ' thead th').each(function() {
                    var title = jQuery(this).text();
                    jQuery(this).append('<br><input type="text" class="cucm-col-filter" placeholder="Filter '+title+'" />');
                });

                table.columns().every(function() {
                    var column = this;
                    jQuery('input', this.header()).on('keyup change click', function(e) {
                        e.stopPropagation();
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                });
            };

            // Initialize all 3 tables
            setupTable('tableAll');
            setupTable('tableReg');
            setupTable('tableUnreg');

            // Fix for DataTables column alignment when switching tabs
            jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                jQuery.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            });

        } else {
            setTimeout(runCucmInit, 200);
        }
    }

    jQuery(document).ready(function() {
        runCucmInit();
    });
    </script>

    <style>
        .cucm-col-filter {
            width: 100%; 
            color: #333; 
            font-weight: normal; 
            margin-top: 5px; 
            border: 1px solid #ccc; 
            padding: 2px 4px; 
            font-size: 11px; 
            border-radius: 2px;
        }
        #tableAll_filter input, #tableReg_filter input, #tableUnreg_filter input {
            border: 2px solid #5bc0de;
            padding: 4px 8px;
            width: 200px;
            border-radius: 4px;
        }
        .nav-tabs-custom > .nav-tabs > li.active {
            border-top-color: #3c8dbc;
        }
    </style>
    <?php

} else {
    echo '<div class="alert alert-info">No data available. Please ensure the poller has run.</div>';
}

/**
 * Helper function to wrap rows in table structure
 */
function generateCucmHtmlTable($id, $header, $rows) {
    if (empty($rows)) {
        return '<div style="padding:20px;" class="text-muted">No devices found in this category.</div>';
    }
    $out = '<div class="table-responsive" style="padding:0 15px;">';
    $out .= '<table id="'.$id.'" class="table table-striped table-hover table-bordered" style="width:100%">';
    $out .= '<thead>' . $header . '</thead>';
    $out .= '<tbody>' . implode('', $rows) . '</tbody>';
    $out .= '</table></div>';
    return $out;
}
?>
