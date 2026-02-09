<?php
if (! Auth::user()->hasGlobalAdmin()) {
    $status = ['status' => 1, 'message' => 'ERROR: You need to be admin to delete poller entries'];
} else {
    $id = $vars['id'];
    if (! is_numeric($id)) {
        $status = ['status' => 1, 'message' => 'No poller has been selected'];
    } else {
        $poller_name = dbFetchCell('SELECT `poller_name` FROM `pollers` WHERE `id`=?', [$id]);
        if (dbDelete('poller_cluster', 'id=?', [$id]) && dbDelete('poller_cluster_stats', 'parent_poller=?', [$id])) {
            $status = ['status' => 0, 'message' => "Poller: <i>$poller_name ($id), has been deleted.</i>"];
        } else {
            $status = ['status' => 1, 'message' => "Poller: <i>$poller_name ($id), has NOT been deleted.</i>"];
        }
    }
}
header('Content-Type: application/json');
echo json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
