<?php
namespace ObzoraNMS\Validations\Rrd;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\ValidationResult;

class CheckRrdcachedConnectivity implements Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        [$host,$port] = explode(':', ObzoraConfig::get('rrdcached'));
        if ($host == 'unix') {
            // Using socket, check that file exists
            if (! file_exists($port)) {
                return ValidationResult::fail(trans('validation.validations.rrd.CheckRrdcachedConnectivity.fail_socket', ['socket' => $port]));
            }
        } else {
            $connection = @fsockopen($host, (int) $port);
            if (is_resource($connection)) {
                fclose($connection);
            } else {
                return ValidationResult::fail(trans('validation.validations.rrd.CheckRrdcachedConnectivity.fail_port', ['port' => $port]));
            }
        }

        return ValidationResult::ok(trans('validation.validations.rrd.CheckRrdcachedConnectivity.ok'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return (bool) ObzoraConfig::get('rrdcached');
    }
}
