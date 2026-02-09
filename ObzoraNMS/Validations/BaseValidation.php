<?php
namespace ObzoraNMS\Validations;

use ObzoraNMS\Interfaces\Validation;
use ObzoraNMS\Interfaces\ValidationGroup;
use ObzoraNMS\Validator;

abstract class BaseValidation implements ValidationGroup
{
    /** @var bool */
    protected $completed = false;
    /** @var bool */
    protected static $RUN_BY_DEFAULT = true;
    /** @var string */
    protected $directory = null;
    /** @var string */
    protected $name = null;

    public function validate(Validator $validator): void
    {
        if ($this->directory) {
            foreach (glob(__DIR__ . "/$this->directory/*.php") as $file) {
                $base = basename($file, '.php');
                $class = __NAMESPACE__ . "\\$this->directory\\$base";
                $validation = new $class;
                if ($validation instanceof Validation && $validation->enabled()) {
                    $validator->result($validation->validate(), $this->name);
                }
            }
        }
    }

    /**
     * Returns if this test should be run by default or not.
     */
    public function isDefault(): bool
    {
        return static::$RUN_BY_DEFAULT;
    }

    /**
     * Returns true if this group has been run
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * Mark this group as completed
     */
    public function markCompleted(): void
    {
        $this->completed = true;
    }
}
