<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\OS\Shared\Unix;

class Aix extends Unix
{
    public function discoverOS(Device $device): void
    {
        // don't support server hardware detection or extends
        $this->discoverYamlOS($device);
    }
}
