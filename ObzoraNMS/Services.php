<?php
namespace ObzoraNMS;

use App\Facades\ObzoraConfig;

class Services
{
    /**
     * List all available services from nagios plugins directory
     *
     * @return array
     */
    public static function list()
    {
        $services = [];
        if (is_dir(ObzoraConfig::get('nagios_plugins'))) {
            foreach (scandir(ObzoraConfig::get('nagios_plugins')) as $file) {
                if (substr($file, 0, 6) === 'check_') {
                    $services[] = substr($file, 6);
                }
            }
        }

        return $services;
    }
}
