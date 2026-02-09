<?php
namespace App\Http\Middleware;

use Fruitcake\Cors\CorsService;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class HandleCors extends \Illuminate\Http\Middleware\HandleCors
{
    private $map = [
        'allowmethods' => 'allowed_methods',
        'origin' => 'allowed_origins',
        'allowheaders' => 'allowed_headers',
        'exposeheaders' => 'exposed_headers',
    ];

    public function __construct(Container $container, CorsService $cors)
    {
        // load legacy config settings before booting the CorsService
        if (\App\Facades\ObzoraConfig::get('api.cors.enabled')) {
            $laravel_config = $container['config']->get('cors');
            $legacy = \App\Facades\ObzoraConfig::get('api.cors');

            $laravel_config['paths'][] = 'api/*';

            foreach ($this->map as $config_key => $option_key) {
                if (isset($legacy[$config_key])) {
                    $laravel_config[$option_key] = Arr::wrap($legacy[$config_key]);
                }
            }
            $laravel_config['max_age'] = $legacy['maxage'] ?? $laravel_config['max_age'];
            $laravel_config['supports_credentials'] = $legacy['allowcredentials'] ?? $laravel_config['supports_credentials'];

            $container['config']->set('cors', $laravel_config);
        }

        parent::__construct($container, $cors);
    }
}
