<?php
namespace ObzoraNMS\Polling;

use App\Models\Device;

class ModuleStatus
{
    public function __construct(
        public ?bool $global,
        public ?bool $os = null,
        public ?bool $device = null,
        public ?bool $manual = null,
    ) {
    }

    public function isEnabled(): bool
    {
        if ($this->global === null) {
            return false; // this module does not have polling
        }

        if ($this->manual !== null) {
            return $this->manual;
        }

        if ($this->device !== null) {
            return $this->device;
        }

        if ($this->os !== null) {
            return $this->os;
        }

        return $this->global;
    }

    public function reason(): string
    {
        if ($this->manual !== null) {
            return 'manually';
        }

        if ($this->device !== null) {
            return 'by device';
        }

        if ($this->os !== null) {
            return 'by OS';
        }

        return 'globally';
    }

    public function isEnabledAndDeviceUp(Device $device, bool $check_snmp = true): bool
    {
        if ($check_snmp && $device->snmp_disable) {
            return false;
        }

        return $this->isEnabled() && $device->status;
    }

    public function __toString(): string
    {
        return sprintf('Module %s: Global %s | OS %s | Device %s | Manual %s',
            $this->isEnabled() ? 'enabled' : 'disabled',
            $this->global ? '+' : '-',
            $this->os === null ? ' ' : ($this->os ? '+' : '-'),
            $this->device === null ? ' ' : ($this->device ? '+' : '-'),
            $this->manual === null ? ' ' : ($this->manual ? '+' : '-'),
        );
    }
}
