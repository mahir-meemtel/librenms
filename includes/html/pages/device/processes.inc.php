<?php
switch ($vars['order'] ?? '') {
    case 'vsz':
        $order = '`vsz`';
        break;

    case 'rss':
        $order = '`rss`';
        break;

    case 'cputime':
        $order = '`cputime`';
        break;

    case 'user':
        $order = '`user`';
        break;

    case 'command':
        $order = '`command`';
        break;

    default:
        $order = '`pid`';
        break;
}//end switch

if (isset($vars['by']) && $vars['by'] == 'desc') {
    $by = 'desc';
} else {
    $by = 'asc';
}

$heads = [
    'PID' => '',
    'VSZ' => 'Virtual Memory',
    'RSS' => 'Resident Memory',
    'cputime' => '',
    'user' => '',
    'command' => '',
];

echo "<div class='table-responsive'><table class='table table-hover'><thead><tr>";
foreach ($heads as $head => $extra) {
    unset($lhead, $bhead);
    $lhead = strtolower($head);
    $bhead = 'asc';
    $icon = '';
    if ('`' . $lhead . '`' == $order) {
        $icon = " class='fa fa-chevron-";
        if ($by == 'asc') {
            $bhead = 'desc';
            $icon .= 'up';
        } else {
            $icon .= 'down';
        }

        $icon .= "'";
    }

    echo '<th><a href="' . \ObzoraNMS\Util\Url::generate(['page' => 'device', 'device' => $device['device_id'], 'tab' => 'processes', 'order' => $lhead, 'by' => $bhead]) . '"><span' . $icon . '>&nbsp;';
    if (! empty($extra)) {
        echo "<abbr title='$extra'>$head</abbr>";
    } else {
        echo $head;
    }

    echo '</span></a></th>';
}//end foreach

echo '</tr></thead><tbody>';

foreach (dbFetchRows('SELECT * FROM `processes` WHERE `device_id` = ? ORDER BY ' . $order . ' ' . $by, [$device['device_id']]) as $entry) {
    echo '<tr>';
    echo '<td>' . $entry['pid'] . '</td>';
    echo '<td>' . \ObzoraNMS\Util\Number::formatSi($entry['vsz'] * 1024, 2, 0, '') . '</td>';
    echo '<td>' . \ObzoraNMS\Util\Number::formatSi($entry['rss'] * 1024, 2, 0, '') . '</td>';
    echo '<td>' . $entry['cputime'] . '</td>';
    echo '<td>' . $entry['user'] . '</td>';
    echo '<td>' . $entry['command'] . '</td>';
    echo '</tr>';
}

echo '</tbody></table></div>';
