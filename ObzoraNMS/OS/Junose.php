<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Junose extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        if (is_string($device->sysDescr) && strpos($device->sysDescr, 'olive')) {
            $device->hardware = 'Olive';

            return;
        }

        $junose_hardware = \SnmpQuery::mibs(['Juniper-Products-MIB'])->translate($device->sysObjectID);
        $device->hardware = $this->rewriteHardware($junose_hardware) ?: null;

        $junose_version = \SnmpQuery::get('Juniper-System-MIB::juniSystemSwVersion.0')->value();
        preg_match('/\((.*)\)/', $junose_version, $matches);
        $device->version = $matches[1] ?? null;
        preg_match('/\[(.*)]/', $junose_version, $matches);
        $device->features = $matches[1] ?? null;
    }

    private function rewriteHardware(string $hardware): string
    {
        $rewrite_junose_hardware = [
            'Juniper-Products-MIB::' => 'Juniper ',
            'juniErx1400' => 'ERX-1400',
            'juniErx700' => 'ERX-700',
            'juniErx1440' => 'ERX-1440',
            'juniErx705' => 'ERX-705',
            'juniErx310' => 'ERX-310',
            'juniE320' => 'E320',
            'juniE120' => 'E120',
            'juniSsx1400' => 'SSX-1400',
            'juniSsx700' => 'SSX-700',
            'juniSsx1440' => 'SSX-1440',
        ];

        $hardware = str_replace(array_keys($rewrite_junose_hardware), array_values($rewrite_junose_hardware), $hardware);

        return $hardware;
    }
}
