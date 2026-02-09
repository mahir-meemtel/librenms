<?php
namespace ObzoraNMS\OS\Traits;

trait ServerHardware
{
    protected function discoverServerHardware()
    {
        $this->discoverDellHardware() || $this->discoverHpHardware() || $this->discoverSupermicroHardware();
    }

    protected function discoverDellHardware()
    {
        // Detect Dell hardware via OpenManage SNMP
        $hw = snmp_get_multi_oid($this->getDeviceArray(), [
            'MIB-Dell-10892::chassisModelName.1',
            'MIB-Dell-10892::chassisServiceTagName.1',
        ], '-OUQ', null, 'dell');

        if (empty($hw)) {
            return false;
        }

        $device = $this->getDevice();
        if (! empty($hw['MIB-Dell-10892::chassisModelName.1'])) {
            $device->hardware = 'Dell ' . $hw['MIB-Dell-10892::chassisModelName.1'];
        }

        $device->serial = $hw['MIB-Dell-10892::chassisServiceTagName.1'] ?? $device->serial;

        return true;
    }

    protected function discoverHpHardware()
    {
        $hw = snmp_get_multi_oid($this->getDeviceArray(), [
            'CPQSINFO-MIB::cpqSiProductName.0',
            'CPQSINFO-MIB::cpqSiSysSerialNum.0',
        ], '-OUQ', null, 'hp');

        if (empty($hw)) {
            return false;
        }

        $device = $this->getDevice();
        $device->hardware = $hw['CPQSINFO-MIB::cpqSiProductName.0'] ?? $device->hardware;
        $device->serial = $hw['CPQSINFO-MIB::cpqSiSysSerialNum.0'] ?? $device->serial;

        return true;
    }

    protected function discoverSupermicroHardware()
    {
        // Detect Supermicro hardware via Supermicro SuperDoctor 5
        $hw = snmp_get_multi_oid($this->getDeviceArray(), [
            'SUPERMICRO-SD5-MIB::mbProductName.1',
            'SUPERMICRO-SD5-MIB::mbSerialNumber.1',
        ], '-OUQ', null, 'supermicro');

        if (empty($hw)) {
            return false;
        }

        $device = $this->getDevice();
        if (! empty($hw['SUPERMICRO-SD5-MIB::mbProductName.1'])) {
            $device->hardware = 'Supermicro ' . $hw['SUPERMICRO-SD5-MIB::mbProductName.1'];
        }

        $device->serial = $hw['SUPERMICRO-SD5-MIB::mbSerialNumber.1'] ?? $device->serial;

        return true;
    }
}
