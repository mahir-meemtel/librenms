<?php
namespace ObzoraNMS\Validations;

use App\Facades\ObzoraConfig;

class DistributedPoller extends BaseValidation
{
    protected $directory = 'DistributedPoller';
    protected $name = 'distributedpoller';

    public function isDefault(): bool
    {
        // run by default if distributed polling is enabled
        return ObzoraConfig::get('distributed_poller');
    }
}
