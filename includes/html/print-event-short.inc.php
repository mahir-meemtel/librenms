<?php
use App\Models\Port;

unset($icon);
$severity_colour = eventlog_severity($entry['severity']);
$icon = '<span class="alert-status ' . $severity_colour . '"></span>';

echo '<tr>';
echo '<td>' . $icon . '</td>';
echo '<td>' . $entry['humandate'] . '</td>';

echo '<td style="white-space: nowrap;max-width: 100px;overflow: hidden;text-overflow: ellipsis;">';

if ($entry['type'] == 'interface') {
    echo '<b>' . \ObzoraNMS\Util\Url::portLink(Port::find($entry['reference'])) . '</b>';
}

echo '</td><td>' . htmlspecialchars($entry['message']) . '</td>';

echo '</tr>';
