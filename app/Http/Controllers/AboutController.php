<?php
namespace App\Http\Controllers;

use App;
use App\Facades\ObzoraConfig;
use App\Models\Application;
use App\Models\Callback;
use App\Models\Device;
use App\Models\DiskIo;
use App\Models\EntPhysical;
use App\Models\Eventlog;
use App\Models\HrDevice;
use App\Models\Ipv4Address;
use App\Models\Ipv4Network;
use App\Models\Ipv6Address;
use App\Models\Ipv6Network;
use App\Models\Mempool;
use App\Models\Port;
use App\Models\PrinterSupply;
use App\Models\Processor;
use App\Models\Pseudowire;
use App\Models\Qos;
use App\Models\Sensor;
use App\Models\Service;
use App\Models\Sla;
use App\Models\Storage;
use App\Models\Syslog;
use App\Models\Vlan;
use App\Models\Vrf;
use App\Models\WirelessSensor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ObzoraNMS\Data\Store\Rrd;
use ObzoraNMS\Util\Http;
use ObzoraNMS\Util\Version;

class AboutController extends Controller
{
    public function index(Request $request)
    {
        $version = Version::get();

        return view('about.index', [
            'usage_reporting_status' => ObzoraConfig::get('reporting.usage'),
            'error_reporting_status' => ObzoraConfig::get('reporting.error'),
            'reporting_clearable' => Callback::whereIn('name', ['uuid', 'error_reporting_uuid'])->exists(),

            'db_schema' => $version->database(),
            'git_log' => $version->git->log(),
            'git_date' => $version->date(),
            'project_name' => ObzoraConfig::get('project_name'),

            'version_local' => $version->name(),
            'version_database' => $version->databaseServer(),
            'version_php' => phpversion(),
            'version_laravel' => App::version(),
            'version_python' => $version->python(),
            'version_webserver' => $request->server('SERVER_SOFTWARE'),
            'version_rrdtool' => Rrd::version(),
            'version_netsnmp' => str_replace('version: ', '', rtrim(shell_exec(ObzoraConfig::get('snmpget', 'snmpget') . ' -V 2>&1'))),

            'stat_apps' => Application::count(),
            'stat_devices' => Device::count(),
            'stat_diskio' => DiskIo::count(),
            'stat_entphys' => EntPhysical::count(),
            'stat_events' => Eventlog::count(),
            'stat_hrdev' => HrDevice::count(),
            'stat_ipv4_addy' => Ipv4Address::count(),
            'stat_ipv4_nets' => Ipv4Network::count(),
            'stat_ipv6_addy' => Ipv6Address::count(),
            'stat_ipv6_nets' => Ipv6Network::count(),
            'stat_memory' => Mempool::count(),
            'stat_qos' => Qos::count(),
            'stat_ports' => Port::count(),
            'stat_processors' => Processor::count(),
            'stat_pw' => Pseudowire::count(),
            'stat_sensors' => Sensor::count(),
            'stat_services' => Service::count(),
            'stat_slas' => Sla::count(),
            'stat_storage' => Storage::count(),
            'stat_syslog' => Syslog::count(),
            'stat_toner' => PrinterSupply::count(),
            'stat_vlans' => Vlan::count(),
            'stat_vrf' => Vrf::count(),
            'stat_wireless' => WirelessSensor::count(),
        ]);
    }

    public function clearReportingData(): JsonResponse
    {
        $usage_uuid = Callback::get('uuid');

        // try to clear usage data if we have a uuid
        if ($usage_uuid) {
            if (! Http::client()->post(ObzoraConfig::get('callback_clear'), ['uuid' => $usage_uuid])->successful()) {
                return response()->json([], 500); // don't clear if this fails to delete upstream data
            }
        }

        // clear all reporting ids
        Callback::truncate();

        return response()->json();
    }
}
