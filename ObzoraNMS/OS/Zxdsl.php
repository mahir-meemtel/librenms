<?php
namespace ObzoraNMS\OS;

use App\Models\Device;

class Zxdsl extends \ObzoraNMS\OS
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        if (preg_match('/^\.1\.3\.6\.1\.4\.1\.3902\.(1004|1015)\.(?<model>[^.]+)\.(?<variant>.*\.)1\.1\.1/', $device->sysObjectID, $matches)) {
            $device->hardware = 'ZXDSL ' . $matches['model'] . $this->parseVariant($matches['variant']);
        }
    }

    protected function parseVariant($oid)
    {
        $variant = ' ';
        $parts = explode('.', trim($oid, '.'));
        foreach ($parts as $part) {
            $variant .= chr(64 + (int) $part);
        }

        return trim($variant);
    }
}
