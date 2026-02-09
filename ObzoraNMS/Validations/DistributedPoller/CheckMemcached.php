<?php
namespace ObzoraNMS\Validations\DistributedPoller;

use App\Facades\ObzoraConfig;
use Illuminate\Support\Facades\Config;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\ValidationResult;

class CheckMemcached implements Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        if (! ObzoraConfig::get('distributed_poller_memcached_host')) {
            return ValidationResult::fail(trans('validation.validations.distributedpoller.CheckMemcached.not_configured_host'), 'lnms config:set distributed_poller_memcached_host <hostname>');
        }

        if (! ObzoraConfig::get('distributed_poller_memcached_port')) {
            return ValidationResult::fail(trans('validation.validations.distributedpoller.CheckMemcached.not_configured_port'), 'lnms config:set distributed_poller_memcached_port <port>');
        }

        $connection = @fsockopen(ObzoraConfig::get('distributed_poller_memcached_host'), ObzoraConfig::get('distributed_poller_memcached_port'));
        if (! is_resource($connection)) {
            return ValidationResult::fail(trans('validation.validations.distributedpoller.CheckMemcached.could_not_connect'));
        }

        fclose($connection);

        return ValidationResult::ok(trans('validation.validations.distributedpoller.CheckMemcached.ok'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return ObzoraConfig::get('distributed_poller') && Config::get('cache.default') == 'memcached';
    }
}
