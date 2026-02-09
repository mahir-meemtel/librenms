<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use Illuminate\Support\Str;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class ThreeCom extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        if (Str::contains($device->sysDescr, 'Software')) {
            $device->hardware = str_replace('3Com ', '', substr($device->sysDescr, 0, strpos($device->sysDescr, 'Software')));
            // Version is the last word in the sysDescr's first line
            [$device->version] = explode("\n", substr($device->sysDescr, strpos($device->sysDescr, 'Version') + 8));

            return;
        }

        $device->hardware = str_replace('3Com ', '', $device->sysDescr);
        // Old Stack Units
        if (Str::startsWith($device->sysObjectID ?? '', '.1.3.6.1.4.1.43.10.27.4.1.')) {
            $oids = ['stackUnitDesc.1', 'stackUnitPromVersion.1', 'stackUnitSWVersion.1', 'stackUnitSerialNumber.1', 'stackUnitCapabilities.1'];
            $data = snmp_get_multi($this->getDeviceArray(), $oids, ['-OQUs', '--hexOutputLength=0'], 'A3COM0352-STACK-CONFIG');
            $device->hardware = trim($device->hardware . ' ' . ($data[1]['stackUnitDesc'] ?? ''));
            $device->version = $data[1]['stackUnitSWVersion'] ?? null;
            $device->serial = $data[1]['stackUnitSerialNumber'] ?? null;
            $device->features = $data[1]['stackUnitCapabilities'] ?? null;
        }
    }
}
