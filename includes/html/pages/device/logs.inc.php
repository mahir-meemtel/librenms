<?php
if (! isset($vars['section'])) {
    $vars['section'] = 'eventlog';
}

echo '<br>';
echo '<div class="panel panel-default">';
echo '<div class="panel-heading">';
echo '<strong>Logging</strong>  &#187; ';

if ($vars['section'] == 'outages') {
    echo '<span class="pagemenu-selected">';
}

echo generate_link('Outages', $vars, ['section' => 'outages']);
if ($vars['section'] == 'outages') {
    echo '</span>';
}

echo ' | ';

if ($vars['section'] == 'eventlog') {
    echo '<span class="pagemenu-selected">';
}

echo generate_link('Event Log', $vars, ['section' => 'eventlog']);
if ($vars['section'] == 'eventlog') {
    echo '</span>';
}

if (\App\Facades\ObzoraConfig::get('enable_syslog') == 1) {
    echo ' | ';

    if ($vars['section'] == 'syslog') {
        echo '<span class="pagemenu-selected">';
    }

    echo generate_link('Syslog', $vars, ['section' => 'syslog']);
    if ($vars['section'] == 'syslog') {
        echo '</span>';
    }
}

if (\App\Facades\ObzoraConfig::get('graylog.server')) {
    echo ' | ';
    if ($vars['section'] == 'graylog') {
        echo '<span class="pagemenu-selected">';
    }
    echo generate_link('Graylog', $vars, ['section' => 'graylog']);
    if ($vars['section'] == 'graylog') {
        echo '</span>';
    }
}

echo '</div><br>';
echo '<div style="width:99%;margin:0 auto;">';

switch ($vars['section']) {
    case 'syslog':
        $vars['fromdevice'] = true;
        include 'includes/html/pages/syslog.inc.php';
        break;
    case 'eventlog':
        $vars['fromdevice'] = true;
        include 'includes/html/pages/eventlog.inc.php';
        break;
    case 'graylog':
        include 'includes/html/pages/device/logs/' . $vars['section'] . '.inc.php';
        break;
    case 'outages':
        $vars['fromdevice'] = true;
        include 'includes/html/pages/outages.inc.php';
        break;

    default:
        echo '</div>';
        echo 'Unknown section';
        break;
}

echo '</div>';
