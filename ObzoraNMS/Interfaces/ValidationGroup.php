<?php
namespace ObzoraNMS\Interfaces;

use ObzoraNMS\Validator;

interface ValidationGroup
{
    /**
     * Validate this module.
     * To return ValidationResults, call ok, warn, fail, or result methods on the $validator
     */
    public function validate(Validator $validator): void;

    /**
     * Returns if this test should be run by default or not.
     */
    public function isDefault(): bool;

    /**
     * Returns true if this group has been run
     */
    public function isCompleted(): bool;

    /**
     * Mark this group as completed
     */
    public function markCompleted(): void;
}
