<?php
namespace ObzoraNMS\Interfaces\Discovery;

use Illuminate\Support\Collection;

interface MplsDiscovery
{
    /**
     * @return Collection MplsLsp objects
     */
    public function discoverMplsLsps();

    /**
     * @param  Collection  $lsps  collecton of synchronized lsp objects from discoverMplsLsps()
     * @return Collection MplsLspPath objects
     */
    public function discoverMplsPaths($lsps);

    /**
     * @return Collection MplsSdp objects
     */
    public function discoverMplsSdps();

    /**
     * @return Collection MplsService objects
     */
    public function discoverMplsServices();

    /**
     * @param  Collection  $svcs  collecton of synchronized lsp objects from discoverMplsServices()
     * @return Collection MplsSap objects
     */
    public function discoverMplsSaps($svcs);

    /**
     * @param  Collection  $sdps  collecton of synchronized sdp objects from discoverMplsSdps()
     * @param  Collection  $svcs  collecton of synchronized service objects from discoverMplsServices()
     * @return Collection MplsSdpBind objects
     */
    public function discoverMplsSdpBinds($sdps, $svcs);

    /**
     * @return Collection MplsTunnelArHop objects
     */
    public function discoverMplsTunnelArHops($paths);

    /**
     * @return Collection MplsTunnelCHop objects
     */
    public function discoverMplsTunnelCHops($paths);
}
