<?php
namespace ObzoraNMS\Interfaces\Discovery;

use ObzoraNMS\OS;

interface DiscoveryItem
{
    /**
     * Does this item represent an actual item or did it fail validation
     *
     * @return bool
     */
    public function isValid();

    /**
     * Generate an instance of this class from yaml data.
     * The data is processed and any snmp data is filled in
     *
     * @param  OS  $os
     * @param  int  $index  the index of the current entry
     * @param  array  $data
     * @return static
     */
    public static function fromYaml(OS $os, $index, array $data);
}
