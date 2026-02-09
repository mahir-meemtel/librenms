<?php
header('Content-type: text/plain');

if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

$ok = '';
$error = '';
$group_id = $_POST['group_id'];
$group_name = $_POST['group_name'];
$descr = $_POST['descr'];
if (! empty($group_name)) {
    if (is_numeric($group_id)) {
        if (dbUpdate(['group_name' => $group_name, 'descr' => $descr], 'poller_groups', 'id = ?', [$group_id]) >= 0) {
            $ok = 'Updated poller group';
        } else {
            $error = 'Failed to update the poller group';
        }
    } else {
        if (dbInsert(['group_name' => $group_name, 'descr' => $descr], 'poller_groups') >= 0) {
            $ok = 'Added new poller group';
        } else {
            $error = 'Failed to create new poller group';
        }
    }
} else {
    $error = "You haven't given your poller group a name, it feels sad :( - $group_name";
}

if (! empty($ok)) {
    exit("$ok");
} else {
    exit('ERROR: ' . htmlspecialchars($error));
}
