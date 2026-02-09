<?php
return [

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'base' => [
            'driver' => 'local',
            'root' => base_path(),
        ],
    ],

];
