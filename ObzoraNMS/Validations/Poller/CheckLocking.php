<?php
namespace ObzoraNMS\Validations\Poller;

use ObzoraNMS\ValidationResult;

class CheckLocking implements \ObzoraNMS\Interfaces\Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        set_error_handler(null); // hide connection errors, we will send our own message

        try {
            $lock = \Cache::lock('dist_test_validation', 5);
            $lock->get();
            $lock->release();

            return ValidationResult::ok(trans('validation.validations.poller.CheckLocking.ok'));
        } catch (\Exception $e) {
            return ValidationResult::fail(trans('validation.validations.poller.CheckLocking.fail', ['message' => $e->getMessage()]));
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return true;
    }
}
