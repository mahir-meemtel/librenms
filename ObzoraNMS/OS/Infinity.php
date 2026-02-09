<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\OS;

class Infinity extends OS implements ProcessorDiscovery
{
    use Traits\FrogfootResources;
}
