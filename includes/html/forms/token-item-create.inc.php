<?php
header('Content-type: text/plain');

if (! Auth::user()->hasGlobalAdmin()) {
    exit('ERROR: You need to be admin');
}

$token = bin2hex(openssl_random_pseudo_bytes(16));

if (! is_numeric($_POST['user_id'])) {
    echo 'ERROR: error with data, please ensure a valid user and token have been specified.';
    exit;
} else {
    $create = dbInsert(['user_id' => $_POST['user_id'], 'token_hash' => $token, 'description' => $_POST['description']], 'api_tokens');
    if ($create > '0') {
        echo 'API token has been created';
        Session::put('api_token', true);
        exit;
    } else {
        echo 'ERROR: An error occurred creating the API token';
        exit;
    }
}//end if
