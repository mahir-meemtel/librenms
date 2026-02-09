<?php
namespace ObzoraNMS\Validations\DistributedPoller;

use App\Facades\ObzoraConfig;
use ObzoraNMS\ValidationResult;
use ObzoraNMS\Validations\Rrd\CheckRrdcachedConnectivity;

class CheckRrdcached implements \ObzoraNMS\Interfaces\Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        if (! ObzoraConfig::get('rrdcached')) {
            return ValidationResult::fail(trans('validation.validations.distributedpoller.CheckRrdcached.fail'), 'lnms config:set rrdcached <rrdcached server ip:port>');
        }

        return (new CheckRrdcachedConnectivity)->validate();
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return (bool) ObzoraConfig::get('distributed_poller');
    }
}
