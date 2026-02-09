<?php
namespace ObzoraNMS\Interfaces\Discovery\Sensors;

interface WirelessCellDiscovery
{
    /**
     * Discover Wireless Cell in cell number. Type is cell.
     * Returns an array of ObzoraNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverWirelessCell();
}
