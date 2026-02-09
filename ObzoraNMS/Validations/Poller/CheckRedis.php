<?php
namespace ObzoraNMS\Validations\Poller;

use Illuminate\Support\Facades\Redis;
use ObzoraNMS\ValidationResult;

class CheckRedis implements \ObzoraNMS\Interfaces\Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        $driver = config('cache.default');
        if ($this->redisIsAvailable()) {
            if ($driver != 'redis') {
                return ValidationResult::warn(trans('validation.validations.poller.CheckRedis.bad_driver', ['driver' => $driver]));
            }

            return ValidationResult::ok(trans('validation.validations.poller.CheckRedis.ok'));
        }

        // cache driver set to redis, it is required. Otherwise, if there are 2 or more distrubted poller nodes, it is required
        if ($driver == 'redis' || (\App\Facades\ObzoraConfig::get('distributed_poller') && \App\Models\PollerCluster::isActive()->count() > 2)) {
            return ValidationResult::fail(trans('validation.validations.poller.CheckRedis.unavailable'));
        }

        return ValidationResult::ok(trans('validation.validations.poller.CheckRedis.unavailable'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return true;
    }

    private function redisIsAvailable(): bool
    {
        set_error_handler(null); // hide connection errors, we will send our own message

        try {
            Redis::command('ping');

            return true;
        } catch (\Exception $e) {
            return false;
        } finally {
            restore_error_handler();
        }
    }
}
