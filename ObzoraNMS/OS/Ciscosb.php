<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use ObzoraNMS\Interfaces\Discovery\OSDiscovery;
use ObzoraNMS\OS;

class Ciscosb extends OS implements OSDiscovery
{
    protected ?string $entityVendorTypeMib = 'CISCO-ENTITY-VENDORTYPE-OID-MIB';

    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $data = snmp_get_multi($this->getDeviceArray(), ['rlPhdUnitGenParamModelName.1', 'genGroupHWVersion.0', 'rlPhdUnitGenParamHardwareVersion.1', 'rlPhdUnitGenParamSoftwareVersion.1', 'rlPhdUnitGenParamFirmwareVersion.1', 'rndBaseBootVersion.0'], '-OQUs', 'CISCOSB-DEVICEPARAMS-MIB:CISCOSB-Physicaldescription-MIB');

        if (empty($device->hardware)) {
            if (preg_match('/\.1\.3\.6\.1\.4\.1\.9\.6\.1\.72\.(....).+/', $device->sysObjectID, $model)) {
                $hardware = 'SGE' . $model[1] . '-' . substr($device->sysDescr, 0, 2);
            } elseif ($device->sysObjectID == '.1.3.6.1.4.1.9.6.1.89.26.1') {
                $hardware = 'SG220-26';
            } else {
                $hardware = str_replace(' ', '', $data['1']['rlPhdUnitGenParamModelName'] ?? '');
            }
            $device->hardware = $hardware;
        }

        $hwversion = $data['0']['genGroupHWVersion'] ?? $data['1']['rlPhdUnitGenParamHardwareVersion'] ?? null;
        if ($hwversion) {
            $device->hardware = trim("$device->hardware $hwversion");
        }

        $device->version = isset($data['1']['rlPhdUnitGenParamSoftwareVersion']) ? ('Software ' . $data['1']['rlPhdUnitGenParamSoftwareVersion']) : null;
        $boot = $data['0']['rndBaseBootVersion'] ?? null;
        $firmware = $data['1']['rlPhdUnitGenParamFirmwareVersion'] ?? null;
        if ($boot) {
            $device->version .= ", Bootldr $boot";
        }
        if ($firmware) {
            $device->version .= ", Firmware $firmware";
        }
        if ($device->version) {
            $device->version = trim($device->version, ', ');
        }
    }
}
