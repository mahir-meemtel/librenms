<?php
namespace App\Api\Controllers;

class LegacyApiController
{
    /**
     * Pass through api functions to api_functions.inc.php
     *
     * @param  string  $method_name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($method_name, $arguments)
    {
        $init_modules = ['web', 'alerts'];
        require base_path('/includes/init.php');
        require_once base_path('includes/html/api_functions.inc.php');

        return app()->call($method_name, $arguments);
    }
}
