<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\OS\Shared\Printer;

class DellLaser extends Printer
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // printer + yaml

        // SNMPv2-SMI::enterprises.253.8.51.10.2.1.7.2.28110202 = STRING: "MFG:Dell;CMD:PJL,RASTER,DOWNLOAD,PCLXL,PCL,POSTSCRIPT;MDL:Laser Printer
        // 3100cn;DES:Dell Laser Printer 3100cn;CLS:PRINTER;STS:AAAMAwAAAAAAAgJ/HgMKBigDCgY8AwAzcJqwggAAwAAACAAAAAAA/w==;"
        // SNMPv2-SMI::enterprises.674.10898.100.2.1.2.1.3.1 = STRING: "COMMAND SET:;MODEL:Dell Laser Printer 5310n"
        // SNMPv2-SMI::enterprises.641.2.1.2.1.3.1 = STRING: "COMMAND SET:;MODEL:Dell Laser Printer 1700n"
        $data = \SnmpQuery::get([
            '1.3.6.1.4.1.253.8.51.10.2.1.7.2.28110202',
            '1.3.6.1.4.1.674.10898.100.2.1.2.1.3.1',
            '1.3.6.1.4.1.641.2.1.2.1.3.1',
        ])->values();

        $dell_laser = $this->parseDeviceId(implode(PHP_EOL, $data));

        $device->hardware = $dell_laser['MDL'] ?? $dell_laser['MODEL'] ?? null;
    }
}
