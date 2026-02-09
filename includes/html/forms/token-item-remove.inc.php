<?php
header('Content-type: text/plain');

if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

if (! is_numeric($_POST['token_id'])) {
    echo 'error with data';
    exit;
} else {
    if ($_POST['confirm'] == 'yes') {
        $delete = dbDelete('api_tokens', '`id` = ?', [$_POST['token_id']]);
        if ($delete > '0') {
            echo 'API token has been removed';
            exit;
        } else {
            echo 'An error occurred removing the API token';
            exit;
        }
    }
}//end if
