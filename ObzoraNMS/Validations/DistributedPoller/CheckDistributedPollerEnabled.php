<?php
namespace ObzoraNMS\Validations\DistributedPoller;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\Interfaces\ValidationFixer;
use ObzoraNMS\ValidationResult;

class CheckDistributedPollerEnabled implements Validation, ValidationFixer
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        if (! ObzoraConfig::get('distributed_poller')) {
            return ValidationResult::fail(trans('validation.validations.distributedpoller.CheckDistributedPollerEnabled.not_enabled'))
                ->setFix('lnms config:set distributed_poller true')
                ->setFixer(__CLASS__);
        }

        $db_config = \App\Models\Config::firstWhere('config_name', 'distributed_poller');
        if ($db_config === null || ! $db_config->config_value) {
            return ValidationResult::fail(trans('validation.validations.distributedpoller.CheckDistributedPollerEnabled.not_enabled_globally'))
                ->setFix('lnms config:set distributed_poller true')
                ->setFixer(__CLASS__);
        }

        return ValidationResult::ok(trans('validation.validations.distributedpoller.CheckDistributedPollerEnabled.ok'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return true;
    }

    public function fix(): bool
    {
        ObzoraConfig::persist('distributed_poller', true);

        return true;
    }
}
