<?php
namespace ObzoraNMS\Interfaces;

interface ValidationFixer
{
    /**
     * Fix the failed validation result. Take care not to break user installs.
     *
     * @return bool
     */
    public function fix(): bool;
}
