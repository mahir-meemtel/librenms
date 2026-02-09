<?php
namespace ObzoraNMS\Validations\Poller;

use App\Models\Device;
use App\Models\Poller;
use App\Models\PollerCluster;
use ObzoraNMS\DB\Eloquent;
use ObzoraNMS\ValidationResult;

class CheckActivePoller implements \ObzoraNMS\Interfaces\Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        $dispatcher_exists = PollerCluster::isActive()->exists();
        $wrapper_exists = Poller::isActive()->exists();
        if (! $dispatcher_exists && ! $wrapper_exists) {
            $interval = (int) \App\Facades\ObzoraConfig::get('rrd.step');

            return ValidationResult::fail(trans('validation.validations.poller.CheckActivePoller.fail', ['interval' => $interval]));
        }

        if ($dispatcher_exists && $wrapper_exists) {
            return ValidationResult::fail(trans('validation.validations.poller.CheckActivePoller.both_fail'));
        }

        return ValidationResult::ok(trans('validation.validations.poller.CheckActivePoller.ok'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return Eloquent::isConnected() && Device::exists();
    }
}
