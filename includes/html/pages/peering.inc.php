<?php
$no_refresh = true;

switch ($vars['section']) {
    case 'ix-list':
        require_once 'includes/html/pages/peering/ix-list.inc.php';
        break;
    case 'ix-peers':
        require_once 'includes/html/pages/peering/ix-peers.inc.php';
        break;
    default:
        require_once 'includes/html/pages/peering/as-selection.inc.php';
}
