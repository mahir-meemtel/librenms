<?php
namespace App\Actions\Device;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use Illuminate\Support\Arr;
use ObzoraNMS\Enum\PortAssociationMode;
use ObzoraNMS\Exceptions\HostIpExistsException;
use ObzoraNMS\Exceptions\HostnameExistsException;
use ObzoraNMS\Exceptions\HostSysnameExistsException;
use ObzoraNMS\Exceptions\HostUnreachablePingException;
use ObzoraNMS\Exceptions\HostUnreachableSnmpException;
use ObzoraNMS\Exceptions\SnmpVersionUnsupportedException;
use ObzoraNMS\Modules\Core;
use SnmpQuery;

class ValidateDeviceAndCreate
{
    /**
     * @var Device
     */
    private $device;
    /**
     * @var bool
     */
    private $force;
    /**
     * @var bool
     */
    private $ping_fallback;
    /**
     * @var \ObzoraNMS\Polling\ConnectivityHelper
     */
    private $connectivity;

    public function __construct(Device $device, bool $force = false, bool $ping_fallback = false)
    {
        $this->device = $device;
        $this->force = $force;
        $this->ping_fallback = $ping_fallback;
        $this->connectivity = new \ObzoraNMS\Polling\ConnectivityHelper($this->device);
    }

    /**
     * @return bool
     *
     * @throws \ObzoraNMS\Exceptions\HostExistsException
     * @throws HostUnreachablePingException
     * @throws \ObzoraNMS\Exceptions\HostUnreachableException
     * @throws SnmpVersionUnsupportedException
     */
    public function execute(): bool
    {
        if ($this->device->exists) {
            return false;
        }

        $this->exceptIfHostnameExists();
        $this->fillDefaults();

        if (! $this->force) {
            $this->exceptIfIpExists();

            if (! $this->connectivity->isPingable()->success()) {
                throw new HostUnreachablePingException($this->device->hostname);
            }

            $this->detectCredentials();
            $this->cleanCredentials();

            if (! $this->device->snmp_disable) {
                $this->device->sysName = SnmpQuery::device($this->device)->get('SNMPv2-MIB::sysName.0')->value();
                $this->exceptIfSysNameExists();

                $this->device->os = Core::detectOS($this->device);
            }
        }

        return $this->device->save();
    }

    /**
     * @throws \ObzoraNMS\Exceptions\HostUnreachableException
     * @throws SnmpVersionUnsupportedException
     */
    private function detectCredentials(): void
    {
        if ($this->device->snmp_disable) {
            return;
        }

        $host_unreachable_exception = new HostUnreachableSnmpException($this->device->hostname);

        // which snmp version should we try (and in what order)
        $snmp_versions = $this->device->snmpver ? [$this->device->snmpver] : ObzoraConfig::get('snmp.version');

        $communities = Arr::where(Arr::wrap(ObzoraConfig::get('snmp.community')), function ($community) {
            return $community && is_string($community);
        });
        if ($this->device->community) {
            array_unshift($communities, $this->device->community);
        }
        $communities = array_unique($communities);

        $v3_credentials = ObzoraConfig::get('snmp.v3');
        array_unshift($v3_credentials, $this->device->only(['authlevel', 'authname', 'authpass', 'authalgo', 'cryptopass', 'cryptoalgo']));
        $v3_credentials = array_unique($v3_credentials, SORT_REGULAR);

        foreach ($snmp_versions as $snmp_version) {
            $this->device->snmpver = $snmp_version;

            if ($snmp_version === 'v3') {
                // Try each set of parameters from config
                foreach ($v3_credentials as $v3) {
                    $this->device->fill(Arr::only($v3, ['authlevel', 'authname', 'authpass', 'authalgo', 'cryptopass', 'cryptoalgo']));

                    if ($this->connectivity->isSNMPable()) {
                        return;
                    } else {
                        $host_unreachable_exception->addReason($snmp_version, $this->device->authname . '/' . $this->device->authlevel);
                    }
                }
            } elseif ($snmp_version === 'v2c' || $snmp_version === 'v1') {
                // try each community from config
                foreach ($communities as $community) {
                    $this->device->community = $community;
                    if ($this->connectivity->isSNMPable()) {
                        return;
                    } else {
                        $host_unreachable_exception->addReason($snmp_version, $this->device->community);
                    }
                }
            } else {
                throw new SnmpVersionUnsupportedException($snmp_version);
            }
        }

        if ($this->ping_fallback) {
            $this->device->snmp_disable = true;
            $this->device->os = 'ping';

            return;
        }

        throw $host_unreachable_exception;
    }

    private function cleanCredentials(): void
    {
        if ($this->device->snmpver == 'v3') {
            $this->device->community = null;
        } else {
            $this->device->authlevel = null;
            $this->device->authname = null;
            $this->device->authalgo = null;
            $this->device->cryptopass = null;
            $this->device->cryptoalgo = null;
        }
    }

    private function fillDefaults(): void
    {
        $this->device->port = $this->device->port ?: ObzoraConfig::get('snmp.port', 161);
        $this->device->transport = $this->device->transport ?: ObzoraConfig::get('snmp.transports.0', 'udp');
        $this->device->poller_group = $this->device->poller_group ?: ObzoraConfig::get('default_poller_group', 0);
        $this->device->os = $this->device->os ?: 'generic';
        $this->device->status_reason = '';
        $this->device->sysName = $this->device->sysName ?: $this->device->hostname;
        $this->device->port_association_mode = $this->device->port_association_mode ?: ObzoraConfig::get('default_port_association_mode', 'ifIndex');
        if (! is_int($this->device->port_association_mode)) {
            $this->device->port_association_mode = PortAssociationMode::getId($this->device->port_association_mode) ?? 1;
        }
    }

    /**
     * @throws \ObzoraNMS\Exceptions\HostExistsException
     */
    private function exceptIfHostnameExists(): void
    {
        if (Device::where('hostname', $this->device->hostname)->exists()) {
            throw new HostnameExistsException($this->device->hostname);
        }
    }

    /**
     * @throws \ObzoraNMS\Exceptions\HostExistsException
     */
    private function exceptIfIpExists(): void
    {
        if ($this->device->overwrite_ip) {
            $ip = $this->device->overwrite_ip;
        } elseif (ObzoraConfig::get('addhost_alwayscheckip')) {
            $ip = gethostbyname($this->device->hostname);
        } else {
            $ip = $this->device->hostname;
        }

        $existing = Device::findByIp($ip);

        if ($existing) {
            throw new HostIpExistsException($this->device->hostname, $existing->hostname, $ip);
        }
    }

    /**
     * Check if a device with match hostname or sysname exists in the database.
     * Throw and error if they do.
     *
     * @return void
     *
     * @throws \ObzoraNMS\Exceptions\HostExistsException
     */
    private function exceptIfSysNameExists(): void
    {
        if (ObzoraConfig::get('allow_duplicate_sysName')) {
            return;
        }

        if (Device::where('sysName', $this->device->sysName)
            ->when(ObzoraConfig::get('mydomain'), function ($query, $domain) {
                $query->orWhere('sysName', rtrim($this->device->sysName, '.') . '.' . $domain);
            })->exists()) {
            throw new HostSysnameExistsException($this->device->hostname, $this->device->sysName);
        }
    }
}
