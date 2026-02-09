<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class Ekinops extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $sysDescr = $device->sysDescr;
        $info = explode(',', $sysDescr);

        $device->hardware = trim($info[1]);
        $device->version = trim($info[2]);

        $mgmtCard = snmp_get($this->getDeviceArray(), 'mgnt2RinvHwPlatform.0', '-OQv', 'EKINOPS-MGNT2-MIB');
        $mgmtInfo = self::ekinopsInfo($mgmtCard);
        $device->serial = $mgmtInfo['Serial Number'];
    }

    /**
     * Parses Ekinops inventory returned in a tabular format within a single OID
     *
     * @param  string  $ekiInfo
     * @return array $inv
     */
    public static function ekinopsInfo($ekiInfo)
    {
        $info = explode("\n", $ekiInfo);
        unset($info[0]);
        $inv = [];
        foreach ($info as $line) {
            [$attr, $value] = explode(':', $line);
            $attr = trim($attr);
            $value = trim($value);
            $inv[$attr] = $value;
        }

        return $inv;
    }
}
