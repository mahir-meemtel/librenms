<?php
namespace ObzoraNMS\Interfaces\Discovery;

use Illuminate\Support\Collection;

interface RouteDiscovery
{
    /**
     * Discover routes.
     *
     * @return Collection<\App\Models\Route>
     */
    public function discoverRoutes(): Collection;
}
