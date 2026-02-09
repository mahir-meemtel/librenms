<?php
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    'default' => env('LOG_CHANNEL', 'stack'),

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['log_file', 'flare'],
            'ignore_exceptions' => false,
        ],

        'console' => [
            'driver' => 'stack',
            'channels' => ['log_file', 'stdout', 'flare'],
            'ignore_exceptions' => false,
        ],

        'log_file' => [
            'driver' => 'single',
            'path' => env('APP_LOG', base_path('logs/obzora.log')),
            'formatter' => App\Logging\LogFileFormatter::class,
            'level' => env('LOG_LEVEL', 'warning'),
            'replace_placeholders' => true,
        ],

        'stdout' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => App\Logging\CliColorFormatter::class,
            'with' => [
                'stream' => 'php://output',
            ],
            'level' => env('STDOUT_LOG_LEVEL', 'info'),
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER', App\Logging\CliColorFormatter::class),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'deprecations_channel' => [ // don't name deprecations
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER', App\Logging\DeprecationDecorator::class),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'flare' => [
            'driver' => 'flare',
        ],
    ],

];
