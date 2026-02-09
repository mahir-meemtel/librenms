<?php
return [

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'legacy',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],

        'token' => [
            'driver' => 'token_driver',
            'provider' => 'token_provider',
            'hash' => false,
        ],
    ],

    'providers' => [
        'legacy' => [
            'driver' => 'legacy',
            'model' => App\Models\User::class,
        ],
    ],

];
