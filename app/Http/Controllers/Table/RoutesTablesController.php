<?php
namespace App\Http\Controllers\Table;

use App\Models\Device;
use App\Models\Route;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use ObzoraNMS\Util\IP;
use ObzoraNMS\Util\Url;

class RoutesTablesController extends TableController
{
    protected $ipCache = [];

    protected function rules()
    {
        return [
            'device_id' => 'nullable|integer',
            'searchby' => 'in:inetCidrRouteNextHop,inetCidrRouteDest',
        ];
    }

    protected function filterFields($request)
    {
        return [
            'route.context_name' => 'context_name',
            'route.inetCidrRouteProto' => 'proto',
        ];
    }

    protected function sortFields($request)
    {
        return [
            'context_name',
            'inetCidrRouteDestType',
            'inetCidrRouteDest',
            'inetCidrRoutePfxLen',
            'inetCidrRouteNextHop',
            'inetCidrRouteIfIndex',
            'inetCidrRouteMetric1',
            'inetCidrRouteType',
            'inetCidrRouteProto',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Defines the base query for this resource
     *
     * @param  Request  $request
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    protected function baseQuery($request)
    {
        $join = function ($query) {
            $query->on('ports.port_id', 'route.port_id');
        };
        $showAllRoutes = trim(\Request::get('showAllRoutes'));
        $showProtocols = trim(\Request::get('showProtocols'));
        if ($showProtocols == 'all') {
            $protocols = ['ipv4', 'ipv6'];
        } else {
            $protocols = [$showProtocols];
        }
        if ($request->device_id && $showAllRoutes == 'false') {
            $query = Route::hasAccess($request->user())
                ->leftJoin('ports', $join)
                ->where('route.device_id', $request->device_id)
                ->whereIn('route.inetCidrRouteDestType', $protocols)
                ->where('updated_at', Route::hasAccess($request->user())
                    ->where('route.device_id', $request->device_id)
                    ->select('updated_at')
                    ->max('updated_at'));

            return $query;
        }
        if ($request->device_id && $showAllRoutes == 'true') {
            $query = Route::hasAccess($request->user())
                ->leftJoin('ports', $join)
                ->where('route.device_id', $request->device_id)
                ->whereIn('route.inetCidrRouteDestType', $protocols);

            return $query;
        }

        return Route::hasAccess($request->user())
            ->leftJoin('ports', $join);
    }

    /**
     * @param  string  $search
     * @param  Builder  $query
     * @param  array  $fields
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    protected function search($search, $query, $fields = [])
    {
        if ($search = trim(\Request::get('searchPhrase'))) {
            $searchLike = '%' . $search . '%';

            return $query->where(function ($query) use ($searchLike) {
                return $query->where('route.inetCidrRouteNextHop', 'like', $searchLike)
                    ->orWhere('route.inetCidrRouteDest', 'like', $searchLike);
            });
        }

        return $query;
    }

    /**
     * @param  Request  $request
     * @param  Builder  $query
     * @return Builder
     */
    public function sort($request, $query)
    {
        $sort = $request->get('sort');
        if (isset($sort['inetCidrRouteIfIndex'])) {
            $query->orderBy('ifDescr', $sort['inetCidrRouteIfIndex'])
                ->orderBy('inetCidrRouteIfIndex', $sort['inetCidrRouteIfIndex']);
        }
        // Simple fields to sort
        $s_fields = [
            'inetCidrRouteDestType',
            'inetCidrRouteType',
            'inetCidrRouteMetric1',
            'inetCidrRoutePfxLen',
            'inetCidrRouteNextHop',
            'updated_at',
            'created_at',
            'context_name',
            'inetCidrRouteDest',
        ];
        foreach ($s_fields as $s_field) {
            if (isset($sort[$s_field])) {
                $query->orderBy($s_field, $sort[$s_field]);
            }
        }

        return $query;
    }

    /**
     * @param  Route  $route_entry
     */
    public function formatItem($route_entry)
    {
        $item = [
            'updated_at' => $route_entry->updated_at ? $route_entry->updated_at->diffForHumans() : $route_entry->updated_at,
            'created_at' => $route_entry->created_at ? $route_entry->created_at->toDateTimeString() : $route_entry->created_at,
            'inetCidrRouteIfIndex' => $route_entry->inetCidrRouteIfIndex == 0 ? 'Undefined' : $route_entry->inetCidrRouteIfIndex,
            'inetCidrRouteMetric1' => $route_entry->inetCidrRouteMetric1,
            'inetCidrRoutePfxLen' => $route_entry->inetCidrRoutePfxLen,
            'inetCidrRouteDestType' => $route_entry->inetCidrRouteDestType,
        ];

        try {
            $obj_inetCidrRouteDest = IP::parse($route_entry->inetCidrRouteDest);
            $item['inetCidrRouteDest'] = $obj_inetCidrRouteDest->compressed();
        } catch (\Exception $e) {
            $item['inetCidrRouteDest'] = $route_entry->inetCidrRouteDest;
        }

        $item['inetCidrRouteIfIndex'] = $route_entry->inetCidrRouteIfIndex == 0 ? 'Undefined' : 'IfIndex ' . $route_entry->inetCidrRouteIfIndex;
        if ($port = $route_entry->port()->first()) {
            $item['inetCidrRouteIfIndex'] = Blade::render('<x-port-link :port="$port">{{ $port->getShortLabel() }}</x-port-link>', ['port' => $port]);
        }

        try {
            $obj_inetCidrRouteNextHop = IP::parse($route_entry->inetCidrRouteNextHop);
            $item['inetCidrRouteNextHop'] = $obj_inetCidrRouteNextHop->compressed();
        } catch (\Exception $e) {
            $item['inetCidrRouteNextHop'] = $route_entry->inetCidrRouteNextHop;
        }
        $device = Device::findByIp($route_entry->inetCidrRouteNextHop);
        if ($device) {
            if ($device->device_id == $route_entry->device_id || in_array($route_entry->inetCidrRouteNextHop, ['127.0.0.1', '::1'])) {
                $item['inetCidrRouteNextHop'] = Blade::render('<x-device-link :device="$device">localhost</x-device-link>', ['device' => $device]);
            } else {
                $item['inetCidrRouteNextHop'] = $item['inetCidrRouteNextHop'] . '<br>(' . rtrim(Blade::render('<x-device-link :device="$device"/>', ['device' => $device])) . ')';
            }
        }

        $item['inetCidrRouteProto'] = $route_entry->inetCidrRouteProto;
        if ($route_entry->inetCidrRouteProto && $route_entry::$translateProto[$route_entry->inetCidrRouteProto]) {
            $item['inetCidrRouteProto'] = $route_entry::$translateProto[$route_entry->inetCidrRouteProto];
        }

        $item['inetCidrRouteType'] = $route_entry->inetCidrRouteType;
        if ($route_entry->inetCidrRouteType && $route_entry::$translateType[$route_entry->inetCidrRouteType]) {
            $item['inetCidrRouteType'] = $route_entry::$translateType[$route_entry->inetCidrRouteType];
        }

        $item['context_name'] = '[global]';
        if ($route_entry->context_name != '') {
            $item['context_name'] = '<a href="' . Url::generate(['page' => 'routing', 'protocol' => 'vrf', 'vrf' => $route_entry->context_name]) . '">' . htmlspecialchars($route_entry->context_name) . '</a>';
        }

        return $item;
    }
}
