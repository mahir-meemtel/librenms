<?php
header('Content-type: application/json');

$status = 'error';
$message = 'unknown error';
$parameters = strip_tags($_POST['search_in_conf_textbox']);
if (isset($parameters)) {
    $message = 'Queried';
    if ($output = search_oxidized_config($parameters)) {
        $status = 'ok';
    }
} else {
    $message = 'ERROR: Could not query';
}
echo \ObzoraNMS\Util\Clean::html(json_encode([
    'status' => $status,
    'message' => $message,
    'search_in_conf_textbox' => $parameters,
    'output' => $output,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), []);
