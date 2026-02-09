<?php
namespace ObzoraNMS\Interfaces;

use ObzoraNMS\ValidationResult;

interface Validation
{
    /**
     * Validate this module.
     */
    public function validate(): ValidationResult;

    /**
     * If this validation is enabled or not.
     *
     * @return bool
     */
    public function enabled(): bool;
}
