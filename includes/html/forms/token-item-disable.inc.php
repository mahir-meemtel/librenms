<?php
header('Content-type: text/plain');

if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

if (! is_numeric($_POST['token_id'])) {
    echo 'error with data';
    exit;
} else {
    if ($_POST['state'] == 'true') {
        $state = 1;
    } elseif ($_POST['state'] == 'false') {
        $state = 0;
    } else {
        $state = 0;
    }

    $update = dbUpdate(['disabled' => $state], 'api_tokens', '`id` = ?', [$_POST['token_id']]);
    if (! empty($update) || $update == '0') {
        echo 'success';
        exit;
    } else {
        echo 'error';
        exit;
    }
}//end if
