<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class Brother extends OS implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        $data = snmp_get_multi($this->getDeviceArray(), [
            'brmDNSPrinterName.0', // Brother HL-2070N series
            'brInfoSerialNumber.0', // 000A5J431816
            'brpsFirmwareDescription.0', // Firmware Ver.1.33 (06.07.21)
            'brieee1284id.0', // MFG:Brother;CMD:HBP,PJL,PCL,PCLXL,POSTSCRIPT;MDL:MFC-8440;CLS:PRINTER;
        ], '-OQUs', 'BROTHER-MIB');

        $device->serial = $data[0]['brInfoSerialNumber'] ?? null;

        if (isset($data[0]['brmDNSPrinterName'])) {
            $device->hardware = str_replace(['Brother ', ' series'], '', $data[0]['brmDNSPrinterName']);
        } elseif (isset($data[0]['brieee1284id'])) {
            preg_match('/MDL:([^;]+)/', $data[0]['brieee1284id'], $matches);
            $device->hardware = $matches[1] ?? null;
        }

        if (isset($data[0]['brpsFirmwareDescription'])) {
            preg_match('/Ver\.([^ ]+)/', $data[0]['brpsFirmwareDescription'], $matches);
            $device->version = $matches[1] ?? null;
        }
    }
}
