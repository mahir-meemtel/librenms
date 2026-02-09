<?php
namespace ObzoraNMS\OS;

use ObzoraNMS\Interfaces\Discovery\ProcessorDiscovery;
use ObzoraNMS\Interfaces\Polling\ProcessorPolling;
use ObzoraNMS\OS;

class Quanta extends OS implements ProcessorDiscovery, ProcessorPolling
{
    use Traits\VxworksProcessorUsage;
}
