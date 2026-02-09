<?php
namespace ObzoraNMS\OS;

use Illuminate\Support\Collection;
use ObzoraNMS\OS;

class Truenas extends OS
{
    public function discoverStorage(): Collection
    {
        // discover both yaml and HR storage
        return $this->discoverYamlStorage()->merge($this->discoverHrStorage());
    }
}
