<?php
namespace ObzoraNMS\Modules;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use App\Models\Eventlog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;
use ObzoraNMS\RRD\RrdDefinition;
use ObzoraNMS\Util\Compare;
use ObzoraNMS\Util\Number;
use ObzoraNMS\Util\Time;
use Log;
use SnmpQuery;

class Core implements Module
{
    /**
     * @inheritDoc
     */
    public function dependencies(): array
    {
        return [];
    }

    public function shouldDiscover(OS $os, ModuleStatus $status): bool
    {
        return ! $os->getDevice()->snmp_disable && $os->getDevice()->status;
    }

    public function discover(OS $os): void
    {
        $snmpdata = SnmpQuery::numeric()->get(['SNMPv2-MIB::sysObjectID.0', 'SNMPv2-MIB::sysDescr.0', 'SNMPv2-MIB::sysName.0'])
            ->values();

        $device = $os->getDevice();
        $device->fill([
            'sysObjectID' => $snmpdata['.1.3.6.1.2.1.1.2.0'] ?? null,
            'sysName' => $snmpdata['.1.3.6.1.2.1.1.5.0'] ?? null,
            'sysDescr' => $snmpdata['.1.3.6.1.2.1.1.1.0'] ?? null,
        ]);

        foreach ($device->getDirty() as $attribute => $value) {
            Eventlog::log($value . ' -> ' . $device->$attribute, $device, 'system', Severity::Notice);
            $os->getDeviceArray()[$attribute] = $value; // update device array
        }

        // detect OS
        $device->os = self::detectOS($device, false);

        if ($device->isDirty('os')) {
            Eventlog::log('Device OS changed: ' . $device->getOriginal('os') . ' -> ' . $device->os, $device, 'system', Severity::Notice);
            $os->getDeviceArray()['os'] = $device->os;

            Log::info('OS Changed ');
        }

        // Set type to a predefined type for the OS if it's not already set
        $loaded_os_type = ObzoraConfig::get("os.$device->os.type");
        if (! $device->getAttrib('override_device_type') && $loaded_os_type != $device->type) {
            $device->type = $loaded_os_type;
            Log::debug("Device type changed to $loaded_os_type!");
        }

        $device->save();

        Log::notice('OS: ' . ObzoraConfig::getOsSetting($device->os, 'text') . " ($device->os)\n");
    }

    public function shouldPoll(OS $os, ModuleStatus $status): bool
    {
        return ! $os->getDevice()->snmp_disable && $os->getDevice()->status;
    }

    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        $device = $os->getDevice();
        $oids = [];

        // fill required fields if they are empty
        if (! isset($device->sysDescr)) {
            $oids[] = 'SNMPv2-MIB::sysDescr.0';
        }
        if (! isset($device->sysObjectID)) {
            $oids[] = 'SNMPv2-MIB::sysObjectID.0';
        }
        $oids[] = 'SNMPv2-MIB::sysUpTime.0'; // always poll uptime

        $snmpdata = SnmpQuery::numeric()->get($oids)->values();

        $device->fill([
            'sysDescr' => $snmpdata['.1.3.6.1.2.1.1.1.0'] ?? $device->sysDescr,
            'sysObjectID' => $snmpdata['.1.3.6.1.2.1.1.2.0'] ?? $device->sysObjectID,
        ]);

        $this->calculateUptime($os, $snmpdata['.1.3.6.1.2.1.1.3.0'] ?? null, $datastore);
        $device->save();
    }

    public function dataExists(Device $device): bool
    {
        return false; // no module specific data
    }

    public function cleanup(Device $device): int
    {
        return 0; // nothing to cleanup
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        return null; // all data here is stored in the devices table and covered by the os module
    }

    /**
     * Detect the os of the given device.
     *
     * @param  Device  $device  device to check
     * @param  bool  $fetch  fetch sysDescr and sysObjectID fresh from the device
     * @return string the name of the os
     *
     * @throws \Exception
     */
    public static function detectOS(Device $device, bool $fetch = true): string
    {
        if ($fetch) {
            // some devices act oddly when getting both OIDs at once
            $device->sysDescr = SnmpQuery::device($device)->get('SNMPv2-MIB::sysDescr.0')->value();
            $device->sysObjectID = SnmpQuery::device($device)->numeric()->get('SNMPv2-MIB::sysObjectID.0')->value();
        }

        Log::debug("| $device->sysDescr | $device->sysObjectID | \n");

        $deferred_os = [];
        $generic_os = [
            'airos',
            'freebsd',
            'linux',
        ];

        // check yaml files
        $os_defs = ObzoraConfig::get('os');

        foreach ($os_defs as $os => $def) {
            if (isset($def['discovery']) && ! in_array($os, $generic_os)) {
                if (self::discoveryIsSlow($def)) {
                    // defer all os that use snmpget or snmpwalk
                    $deferred_os[] = $os;
                    continue;
                }

                foreach ($def['discovery'] as $item) {
                    if (self::checkDiscovery($device, $item, $def['mib_dir'] ?? null)) {
                        return $os;
                    }
                }
            }
        }

        // check deferred os
        $deferred_os = array_merge($deferred_os, $generic_os);
        foreach ($deferred_os as $os) {
            foreach ($os_defs[$os]['discovery'] as $item) {
                if (self::checkDiscovery($device, $item, $os_defs[$os]['mib_dir'] ?? null)) {
                    return $os;
                }
            }
        }

        return 'generic';
    }

    /**
     * Check an array of conditions if all match, return true
     * sysObjectID if sysObjectID starts with any of the values under this item
     * sysDescr if sysDescr contains any of the values under this item
     * sysDescr_regex if sysDescr matches any of the regexes under this item
     * snmpget perform an snmpget on `oid` and check if the result contains `value`. Other subkeys: options, mib, mibdir
     *
     * Appending _except to any condition will invert the match.
     *
     * @param  Device  $device
     * @param  array  $array  Array of items, keys should be sysObjectID, sysDescr, or sysDescr_regex
     * @param  string|array  $mibdir  MIB directory for evaluated OS
     * @return bool the result (all items passed return true)
     */
    protected static function checkDiscovery(Device $device, array $array, $mibdir): bool
    {
        // all items must be true
        foreach ($array as $key => $value) {
            if ($check = Str::endsWith($key, '_except')) {
                $key = substr($key, 0, -7);
            }

            if ($key == 'sysObjectID') {
                if (Str::startsWith($device['sysObjectID'] ?? '', $value) == $check) {
                    return false;
                }
            } elseif ($key == 'sysDescr') {
                if (Str::contains($device['sysDescr'] ?? '', $value) == $check) {
                    return false;
                }
            } elseif ($key == 'sysDescr_regex') {
                if (preg_match_any($device['sysDescr'] ?? '', $value) == $check) {
                    return false;
                }
            } elseif ($key == 'sysObjectID_regex') {
                if (preg_match_any($device['sysObjectID'] ?? '', $value) == $check) {
                    return false;
                }
            } elseif ($key == 'snmpget') {
                $get_value = SnmpQuery::device($device)
                    ->options($value['options'] ?? null)
                    ->mibDir($value['mib_dir'] ?? $mibdir)
                    ->get(isset($value['mib']) ? "{$value['mib']}::{$value['oid']}" : $value['oid'])
                    ->value();
                if (Compare::values($get_value, $value['value'], $value['op'] ?? 'contains') == $check) {
                    return false;
                }
            } elseif ($key == 'snmpwalk') {
                $walk_value = SnmpQuery::device($device)
                    ->options($value['options'] ?? null)
                    ->mibDir($value['mib_dir'] ?? $mibdir)
                    ->walk(isset($value['mib']) ? "{$value['mib']}::{$value['oid']}" : $value['oid'])
                    ->raw;
                if (Compare::values($walk_value, $value['value'], $value['op'] ?? 'contains') == $check) {
                    return false;
                }
            }
        }

        return true;
    }

    private function calculateUptime(OS $os, ?string $sysUpTime, DataStorageInterface $datastore): void
    {
        $device = $os->getDevice();

        if (ObzoraConfig::get("os.$device->os.bad_uptime")) {
            return;
        }

        $agent_data = Cache::driver('array')->get('agent_data');
        if (! empty($agent_data['uptime'])) {
            $uptime = round((float) substr($agent_data['uptime'], 0, strpos($agent_data['uptime'], ' ')));
            Log::info("Using UNIX Agent Uptime ($uptime)");
        } else {
            $uptime_data = SnmpQuery::make()->get(['SNMP-FRAMEWORK-MIB::snmpEngineTime.0', 'HOST-RESOURCES-MIB::hrSystemUptime.0'])->values();

            $uptime = max(
                round(Number::cast($sysUpTime) / 100),
                ObzoraConfig::get("os.$device->os.bad_snmpEngineTime") ? 0 : Number::cast($uptime_data['SNMP-FRAMEWORK-MIB::snmpEngineTime.0'] ?? 0),
                ObzoraConfig::get("os.$device->os.bad_hrSystemUptime") ? 0 : round(Number::cast($uptime_data['HOST-RESOURCES-MIB::hrSystemUptime.0'] ?? 0) / 100)
            );
            Log::debug("Uptime seconds: $uptime\n");
        }

        // set it if unless it is wrong
        if ($uptime > 0) {
            if ($uptime < $device->uptime) {
                Eventlog::log('Device rebooted after ' . Time::formatInterval($device->uptime) . " -> {$uptime}s", $device, 'reboot', Severity::Warning, $device->uptime);
                if (ObzoraConfig::get('discovery_on_reboot')) {
                    $device->last_discovered = null;
                    $device->save();
                }
            }

            $datastore->put($os->getDeviceArray(), 'uptime', [
                'rrd_def' => RrdDefinition::make()->addDataset('uptime', 'GAUGE', 0),
            ], $uptime);

            $os->enableGraph('uptime');

            Log::info('Uptime: ' . Time::formatInterval($uptime));
            $device->uptime = $uptime;
        }
    }

    protected static function discoveryIsSlow(array $def): bool
    {
        foreach ($def['discovery'] as $item) {
            if (array_key_exists('snmpget', $item) || array_key_exists('snmpwalk', $item)) {
                return true;
            }
        }

        return false;
    }
}
