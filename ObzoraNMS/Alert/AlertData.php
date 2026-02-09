<?php
namespace ObzoraNMS\Alert;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Util\Time;

class AlertData extends \Illuminate\Support\Collection
{
    public function __get($name)
    {
        if ($this->has($name)) {
            return $this->get($name);
        }

        return "$name is not a valid \$alert data name";
    }

    public static function testData(Device $device, array $faults = []): array
    {
        return [
            'hostname' => $device->hostname,
            'device_id' => $device->device_id,
            'sysDescr' => $device->sysDescr,
            'sysName' => $device->sysName,
            'sysContact' => $device->sysContact,
            'os' => $device->os,
            'type' => $device->type,
            'ip' => $device->ip,
            'display' => $device->displayName(),
            'version' => $device->version,
            'hardware' => $device->hardware,
            'features' => $device->features,
            'serial' => $device->serial,
            'status' => $device->status,
            'status_reason' => $device->status_reason,
            'location' => (string) $device->location,
            'description' => $device->purpose,
            'notes' => $device->notes,
            'uptime' => $device->uptime,
            'uptime_short' => Time::formatInterval($device->uptime, true),
            'uptime_long' => Time::formatInterval($device->uptime),
            'title' => 'Testing transport from ' . ObzoraConfig::get('project_name'),
            'elapsed' => '11s',
            'alerted' => 0,
            'alert_id' => '000',
            'alert_notes' => 'This is the note for the test alert',
            'proc' => 'This is the procedure for the test alert',
            'rule_id' => '000',
            'id' => '000',
            'faults' => $faults,
            'uid' => '000',
            'severity' => 'critical',
            'rule' => 'macros.device = 1',
            'name' => 'Test-Rule',
            'string' => '#1: test => string;',
            'timestamp' => date('Y-m-d H:i:s'),
            'contacts' => AlertUtil::getContacts([$device->toArray()]),
            'state' => AlertState::ACTIVE,
            'msg' => 'This is a test alert',
            'builder' => '{}',
        ];
    }
}
