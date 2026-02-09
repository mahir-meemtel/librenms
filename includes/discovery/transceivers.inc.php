<?php
use ObzoraNMS\OS;

if (! isset($os) || ! $os instanceof OS) {
    $os = OS::make($device);
}

(new \ObzoraNMS\Modules\Transceivers())->discover($os);
