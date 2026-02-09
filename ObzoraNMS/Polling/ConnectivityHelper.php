<?php
namespace ObzoraNMS\Polling;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use App\Models\DeviceOutage;
use App\Models\Eventlog;
use ObzoraNMS\Data\Source\Fping;
use ObzoraNMS\Data\Source\FpingResponse;
use ObzoraNMS\Enum\MaintenanceStatus;
use ObzoraNMS\Enum\Severity;
use SnmpQuery;
use Symfony\Component\Process\Process;

class ConnectivityHelper
{
    /**
     * @var Device
     */
    private $device;
    /**
     * @var bool
     */
    private $saveMetrics = false;
    /**
     * @var string
     */
    private $family;
    /**
     * @var string
     */
    private $target;

    public function __construct(Device $device)
    {
        $this->device = $device;
        $this->target = $device->overwrite_ip ?: $device->hostname;
    }

    /**
     * After pinging the device, save metrics about the ping response
     */
    public function saveMetrics(): void
    {
        $this->saveMetrics = true;
    }

    /**
     * Check if the device is up.
     * Save availability and ping data if enabled with savePingPerf()
     */
    public function isUp(): bool
    {
        $previous = $this->device->status;
        $ping_response = $this->isPingable();

        // calculate device status
        if ($ping_response->success()) {
            if (! $this->canSnmp() || $this->isSNMPable()) {
                // up
                $this->device->status = true;
                $this->device->status_reason = '';
            } else {
                // snmp down
                $this->device->status = false;
                $this->device->status_reason = 'snmp';
            }
        } else {
            // icmp down
            $this->device->status = false;
            $this->device->status_reason = 'icmp';
        }

        if ($this->saveMetrics) {
            if ($this->canPing()) {
                $ping_response->saveStats($this->device);
            }
            $this->updateAvailability($previous, $this->device->status);

            $this->device->save(); // confirm device is saved
        }

        return $this->device->status;
    }

    /**
     * Check if the device responds to ICMP echo requests ("pings").
     */
    public function isPingable(): FpingResponse
    {
        if (! $this->canPing()) {
            return FpingResponse::artificialUp($this->target);
        }

        $status = app()->make(Fping::class)->ping($this->target, $this->ipFamily());

        if ($status->duplicates > 0) {
            Eventlog::log('Duplicate ICMP response detected! This could indicate a network issue.', $this->device, 'icmp', Severity::Warning);
            $status->ignoreFailure(); // when duplicate is detected fping returns 1. The device is up, but there is another issue. Clue admins in with above event.
        }

        return $status;
    }

    public function isSNMPable(): bool
    {
        $response = SnmpQuery::device($this->device)->get('SNMPv2-MIB::sysObjectID.0');

        return $response->getExitCode() === 0 || $response->isValid();
    }

    public function traceroute(): array
    {
        $command = [ObzoraConfig::get('traceroute', 'traceroute'), '-q', '1', '-w', '1', '-I', $this->target];
        if ($this->ipFamily() == 'ipv6') {
            $command[] = '-6';
        }

        $process = new Process($command);
        $process->setTimeout(120);
        $process->run();

        return [
            'traceroute' => $process->getOutput(),
            'traceroute_output' => $process->getErrorOutput(),
        ];
    }

    public function canSnmp(): bool
    {
        return ! $this->device->snmp_disable;
    }

    public function canPing(): bool
    {
        return ObzoraConfig::get('icmp_check') && ! ($this->device->exists && $this->device->getAttrib('override_icmp_disable') === 'true');
    }

    public function ipFamily(): string
    {
        if ($this->family === null) {
            $this->family = preg_match('/6$/', $this->device->transport ?? '') ? 'ipv6' : 'ipv4';
        }

        return $this->family;
    }

    private function updateAvailability(bool $previous, bool $status): void
    {
        // skip update if we are considering maintenance and skipping alerts
        if (ObzoraConfig::get('graphing.availability_consider_maintenance')
            && $this->device->getMaintenanceStatus() == MaintenanceStatus::SKIP_ALERTS) {
            return;
        }

        // check for open outage
        $open_outage = $this->device->getCurrentOutage();

        if ($status) {
            if ($open_outage) {
                $open_outage->up_again = time();
                $open_outage->save();
            }
        } elseif ($previous || $open_outage === null) {
            // status changed from up to down or there is no open outage
            // open new outage
            $this->device->outages()->save(new DeviceOutage(['going_down' => time()]));
        }
    }
}
