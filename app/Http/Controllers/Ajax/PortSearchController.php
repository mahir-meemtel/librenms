<?php
namespace App\Http\Controllers\Ajax;

use App\Models\Port;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use ObzoraNMS\Util\Color;
use ObzoraNMS\Util\Url;

class PortSearchController extends SearchController
{
    public function buildQuery(string $search, Request $request): Builder
    {
        return Port::hasAccess($request->user())
            ->with('device')
            ->where('deleted', 0)
            ->where(function (Builder $query) use ($request) {
                $search = $request->get('search');
                $like_search = "%$search%";

                return $query->orWhere('ifAlias', 'LIKE', $like_search)
                    ->orWhere('ifDescr', 'LIKE', $like_search)
                    ->orWhere('ifName', 'LIKE', $like_search)
                    ->orWhere('port_descr_descr', 'LIKE', $like_search)
                    ->orWhere('portName', 'LIKE', $like_search);
            })
            ->orderBy('ifDescr');
    }

    /**
     * @param  Port  $port
     * @return array
     */
    public function formatItem($port): array
    {
        $description = $port->getDescription();
        $label = $port->getLabel();

        if ($description !== $port->ifDescr && $label !== $port->ifDescr) {
            $description .= " ($port->ifDescr)";
        }

        return [
            'url' => Url::portUrl($port),
            'name' => $label,
            'description' => $description,
            'colours' => Color::forPortStatus($port),
            'hostname' => $port->device?->displayName(),
            'port_id' => $port->port_id,
        ];
    }
}
