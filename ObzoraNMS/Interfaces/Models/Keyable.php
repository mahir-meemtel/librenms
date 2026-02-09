<?php
namespace ObzoraNMS\Interfaces\Models;

interface Keyable
{
    /**
     * Get a string that can identify a unique instance of this model
     *
     * @return string|int
     */
    public function getCompositeKey();
}
