<?php
use ObzoraNMS\OS;

if (! $os instanceof OS) {
    $os = OS::make($device);
}
(new \ObzoraNMS\Modules\Ipv6Nd())->discover($os);
