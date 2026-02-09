<?php

$name = 'cucm';
$host = $device['hostname'] ?? null;

if (empty($host)) {
    return;
}

$cmd = "/opt/obzora/scripts/cucm.py " . escapeshellarg($host) . " 2>/dev/null";
$rawdata = trim(shell_exec($cmd));

if (empty($rawdata)) {
    return;
}

// Build the HTML Table
$lines = explode("\n", $rawdata);
$html = '<table class="table table-hover table-condensed table-striped"><thead>';
$registered_count = 0;

foreach ($lines as $index => $line) {
    $line = trim($line);
    if (empty($line) || stripos($line, 'Total devices') !== false) continue;

    $columns = str_getcsv($line);
    $html .= '<tr>';
    foreach ($columns as $col) {
        if ($index === 0) {
            $html .= "<th>" . htmlspecialchars($col) . "</th>";
        } else {
            $cell = htmlspecialchars($col);
            if ($col === 'Registered') {
                $cell = '<span class="label label-success">Registered</span>';
                $registered_count++;
            }
            $html .= "<td>$cell</td>";
        }
    }
    $html .= ($index === 0) ? '</tr></thead><tbody>' : '</tr>';
}
$html .= '</tbody></table>';

/**
 * 3. FORCE UPDATE
 */
// Some versions of the UI display 'app_status' as the main text.
// Others display 'data'. We will fill both.
dbUpdate(
    [
        'app_status' => $html, 
        'data'       => $html, 
        'app_state'  => 'OK',
        'timestamp'  => ['NOW()']
    ], 
    'applications', 
    'app_id=?', 
    [$app['app_id']]
);

// This ensures the App shows as "Up" in the dashboard
update_application($app, "Registered: $registered_count", ['registered' => $registered_count]);
