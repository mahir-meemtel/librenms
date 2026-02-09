<?php
namespace App\Http\Controllers\Ajax;

use App\Models\BgpPeer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use ObzoraNMS\Util\Color;
use ObzoraNMS\Util\Url;

class BgpSearchController extends SearchController
{
    public function buildQuery(string $search, Request $request): Builder
    {
        return BgpPeer::hasAccess($request->user())
            ->with('device')
            ->where(function (Builder $query) use ($search) {
                $like_search = "%$search%";

                return $query->orWhere('astext', 'LIKE', $like_search)
                    ->orWhere('bgpPeerDescr', 'LIKE', $like_search)
                    ->orWhere('bgpPeerIdentifier', 'LIKE', $like_search)
                    ->orWhere('bgpPeerRemoteAs', 'LIKE', $like_search);
            })
            ->orderBy('astext');
    }

    /**
     * @param  BgpPeer  $peer
     * @return array
     */
    public function formatItem($peer): array
    {
        $bgp_image = $peer->bgpPeerRemoteAs == $peer->device->bgpLocalAs
            ? 'fa fa-square fa-lg icon-theme'
            : 'fa fa-external-link-square fa-lg icon-theme';

        return [
            'url' => Url::deviceUrl($peer->device, ['tab' => 'routing', 'proto' => 'bgp']),
            'name' => $peer->bgpPeerIdentifier,
            'description' => $peer->astext,
            'localas' => $peer->device->bgpLocalAs,
            'bgp_image' => $bgp_image,
            'remoteas' => $peer->bgpPeerRemoteAs,
            'colours' => Color::forBgpPeerStatus($peer),
            'hostname' => $peer->device->displayName(),
        ];
    }
}
