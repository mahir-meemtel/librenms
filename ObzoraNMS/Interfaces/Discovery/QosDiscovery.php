<?php
namespace ObzoraNMS\Interfaces\Discovery;

use Illuminate\Support\Collection;

interface QosDiscovery
{
    /**
     * Discover QoS queues
     *
     * @return Collection
     */
    public function discoverQos(): Collection;

    /**
     * Set QoS Parents given the output of discoverQos() after saving.
     * This ensures that all QoS objects have IDs
     */
    public function setQosParents(Collection $qos);
}
