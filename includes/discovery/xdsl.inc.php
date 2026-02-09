<?php
use ObzoraNMS\OS;

if (! $os instanceof OS) {
    $os = OS::make($device);
}
(new \ObzoraNMS\Modules\Xdsl())->discover($os);
