<?php
namespace ObzoraNMS\OS\Shared;

use App\Models\Device;
use Illuminate\Support\Str;

class Printer extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $device->serial = $device->serial ?? $this->getSerial() ?: null;
    }

    protected function getSerial()
    {
        return snmp_get($this->getDeviceArray(), 'prtGeneralSerialNumber.1', '-Oqv', 'Printer-MIB');
    }

    protected function parseDeviceId($data)
    {
        $vars = [];
        foreach (explode(';', $data) as $pair) {
            if (! Str::contains($pair, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $pair);
            $vars[trim($key)] = $value;
        }

        return $vars;
    }
}
