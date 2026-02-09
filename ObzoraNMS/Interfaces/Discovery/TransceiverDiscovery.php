<?php
namespace ObzoraNMS\Interfaces\Discovery;

use Illuminate\Support\Collection;

interface TransceiverDiscovery
{
    /**
     * Discover transceivers.
     * Distance is in meters.
     *
     * @return Collection<\App\Models\Transceiver>
     */
    public function discoverTransceivers(): Collection;
}
