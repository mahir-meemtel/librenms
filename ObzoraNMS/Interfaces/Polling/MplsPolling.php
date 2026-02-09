<?php
namespace ObzoraNMS\Interfaces\Polling;

use Illuminate\Support\Collection;

interface MplsPolling
{
    /**
     * @return Collection MplsLsp objects
     */
    public function pollMplsLsps();

    /**
     * @param  Collection  $lsps  collecton of synchronized lsp objects from pollMplsLsps()
     * @return Collection MplsLspPath objects
     */
    public function pollMplsPaths($lsps);

    /**
     * @return Collection MplsSdp objects
     */
    public function pollMplsSdps();

    /**
     * @return Collection MplsService objects
     */
    public function pollMplsServices();

    /**
     * @param  Collection  $svcs  collecton of synchronized service objects from pollMplsServices()
     * @return Collection MplsSap objects
     */
    public function pollMplsSaps($svcs);

    /**
     * @param  Collection  $sdps  collecton of synchronized sdp objects from pollMplsSdps()
     * @param  Collection  $svcs  collecton of synchronized service objects from pollMplsServices()
     * @return Collection MplsSdpBind objects
     */
    public function pollMplsSdpBinds($sdps, $svcs);

    /**
     * @return Collection MplsTunnelArHop objects
     */
    public function pollMplsTunnelArHops($paths);

    /**
     * @return Collection MplsTunnelCHop objects
     */
    public function pollMplsTunnelCHops($paths);
}
