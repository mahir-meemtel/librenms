<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class DdWrt extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        // dd-wrt, cannot use exec with OID specified. Options are extend (w/OID), or exec (w/o OID)
        // -> extend seems to be the recommended approach, so use that (changes OID, which "spells out" name)
        [, $device->version] = explode(' ', snmp_get($this->getDeviceArray(), 'NET-SNMP-EXTEND-MIB::nsExtendOutput1Line."distro"', '-Osqnv'));
        $device->hardware = snmp_get($this->getDeviceArray(), 'NET-SNMP-EXTEND-MIB::nsExtendOutput1Line."hardware"', '-Osqnv');
    }
}
