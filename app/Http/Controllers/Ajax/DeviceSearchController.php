<?php
namespace App\Http\Controllers\Ajax;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use ObzoraNMS\Util\Color;

class DeviceSearchController extends SearchController
{
    public function buildQuery(string $search, Request $request): Builder
    {
        $baseQuery = Device::hasAccess($request->user())
            ->leftJoin('locations', 'locations.id', '=', 'devices.location_id')
            ->select(['devices.*', 'locations.location'])
            ->distinct()
            ->orderBy('devices.hostname');

        return $baseQuery
            ->where(function (Builder $query) use ($search, $baseQuery) {
                // search filter
                $like_search = "%$search%";
                $query->orWhere('hostname', 'LIKE', $like_search)
                    ->orWhere('sysName', 'LIKE', $like_search)
                    ->orWhere('display', 'LIKE', $like_search)
                    ->orWhere('location', 'LIKE', $like_search)
                    ->orWhere('purpose', 'LIKE', $like_search)
                    ->orWhere('serial', 'LIKE', $like_search)
                    ->orWhere('notes', 'LIKE', $like_search);

                if (\ObzoraNMS\Util\IPv4::isValid($search, false)) {
                    $baseQuery->leftJoin('ports', 'ports.device_id', '=', 'devices.device_id')
                        ->leftJoin('ipv4_addresses', 'ipv4_addresses.port_id', 'ports.port_id');

                    $query->orWhere('ipv4_addresses.ipv4_address', '=', $search)
                        ->orWhere('overwrite_ip', '=', $search)
                        ->orWhere('ip', '=', inet_pton($search));
                } elseif (\ObzoraNMS\Util\IPv6::isValid($search, false)) {
                    $baseQuery->leftJoin('ports', 'ports.device_id', '=', 'devices.device_id')
                        ->leftJoin('ipv6_addresses', 'ipv6_addresses.port_id', 'ports.port_id');

                    $query->orWhere('ipv6_addresses.ipv6_address', '=', $search)
                        ->orWhere('overwrite_ip', '=', $search)
                        ->orWhere('ip', '=', inet_pton($search));
                } elseif (ctype_xdigit($mac_search = str_replace([':', '-', '.'], '', $search))) {
                    $baseQuery->leftJoin('ports', 'ports.device_id', '=', 'devices.device_id');

                    $query->orWhere('ports.ifPhysAddress', 'LIKE', "%$mac_search%");
                }

                return $query;
            });
    }

    /**
     * @param  Device  $device
     * @return array
     */
    public function formatItem($device): array
    {
        $name = $device->displayName();
        if (! request()->get('map') && $name !== $device->sysName) {
            $name .= " ($device->sysName)";
        }

        return [
            'name' => $name,
            'device_id' => $device->device_id,
            'url' => \ObzoraNMS\Util\Url::deviceUrl($device),
            'colours' => Color::forDeviceStatus($device),
            'device_ports' => $device->ports()->count(),
            'device_image' => $device->icon,
            'device_hardware' => $device->hardware,
            'device_os' => ObzoraConfig::getOsSetting($device->os, 'text'),
            'version' => $device->version,
            'location' => $device->location,
        ];
    }
}
