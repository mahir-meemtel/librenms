<?php
namespace ObzoraNMS\Modules;

use App\Models\Device;
use App\Models\Eventlog;
use App\Models\Location;
use App\Observers\DeviceObserver;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\Interfaces\Polling\OSPolling;
use ObzoraNMS\Polling\ModuleStatus;
use ObzoraNMS\Util\Url;

class Os implements Module
{
    /**
     * @inheritDoc
     */
    public function dependencies(): array
    {
        return [];
    }

    public function shouldDiscover(\ObzoraNMS\OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice());
    }

    public function discover(\ObzoraNMS\OS $os): void
    {
        $this->updateLocation($os);
        $this->sysContact($os);

        // null out values in case they aren't filled.
        $os->getDevice()->fill([
            'hardware' => null,
            'version' => null,
            'features' => null,
            'serial' => null,
            'icon' => null,
        ]);

        $os->discoverOS($os->getDevice());
        $this->handleChanges($os);
    }

    public function shouldPoll(\ObzoraNMS\OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice());
    }

    public function poll(\ObzoraNMS\OS $os, DataStorageInterface $datastore): void
    {
        $deviceModel = $os->getDevice(); /** @var Device $deviceModel */
        if ($os instanceof OSPolling) {
            $os->pollOS($datastore);
        } else {
            $device = $os->getDeviceArray();
            $location = null;

            if (is_file(base_path('/includes/polling/os/' . $device['os'] . '.inc.php'))) {
                // OS Specific
                Eventlog::log("Warning: OS {$device['os']} using deprecated polling method", $deviceModel, 'poller', Severity::Error);
                include base_path('/includes/polling/os/' . $device['os'] . '.inc.php');
            } elseif (! empty($device['os_group']) && is_file(base_path('/includes/polling/os/' . $device['os_group'] . '.inc.php'))) {
                // OS Group Specific
                Eventlog::log("Warning: OS {$device['os']} using deprecated polling method", $deviceModel, 'poller', Severity::Error);
                include base_path('/includes/polling/os/' . $device['os_group'] . '.inc.php');
            }

            // handle legacy variables, sometimes they are false
            $deviceModel->version = ($version ?? $deviceModel->version) ?: null;
            $deviceModel->hardware = ($hardware ?? $deviceModel->hardware) ?: null;
            $deviceModel->features = ($features ?? $deviceModel->features) ?: null;
            $deviceModel->serial = ($serial ?? $deviceModel->serial) ?: null;

            if (! empty($location)) { // legacy support, remove when no longer needed
                $deviceModel->setLocation($location);
                $deviceModel->location?->save();
            }
        }

        $this->handleChanges($os);
    }

    public function dataExists(Device $device): bool
    {
        return false; // data part of device
    }

    /**
     * @inheritDoc
     */
    public function cleanup(Device $device): int
    {
        return 0; // no cleanup needed
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        // get data fresh from the database
        return [
            'devices' => Device::where('device_id', $device->device_id)
            ->leftJoin('locations', 'location_id', 'id')
            ->select(['sysName', 'sysObjectID', 'sysDescr', 'sysContact', 'version', 'hardware', 'features', 'location', 'os', 'type', 'serial', 'icon'])
            ->get(),
        ];
    }

    private function handleChanges(\ObzoraNMS\OS $os): void
    {
        $device = $os->getDevice();

        $device->icon = basename(Url::findOsImage($device->os, $device->features, null, 'images/os/'));

        Log::info(trans('device.attributes.location') . ': ' . $device->location?->display());
        foreach (['hardware', 'version', 'features', 'serial'] as $attribute) {
            if (isset($device->$attribute)) {
                $device->$attribute = trim($device->$attribute);
            }
            Log::info(DeviceObserver::attributeChangedMessage($attribute, $device->$attribute, $device->getOriginal($attribute)));
        }

        $device->save();
    }

    private function updateLocation(\ObzoraNMS\OS $os): void
    {
        $device = $os->getDevice();
        $new_location = $device->override_sysLocation ? new Location() : $os->fetchLocation(); // fetch location data from device
        $device->setLocation($new_location, true); // set location and lookup coordinates if needed
        $device->location?->save();
    }

    private function sysContact(\ObzoraNMS\OS $os): void
    {
        $device = $os->getDevice();
        $device->sysContact = snmp_get($os->getDeviceArray(), 'sysContact.0', '-Ovq', 'SNMPv2-MIB');
        $device->sysContact = str_replace(['', '"', '\n', 'not set'], '', $device->sysContact);
        if (empty($device->sysContact)) {
            $device->sysContact = null;
        }
    }
}
