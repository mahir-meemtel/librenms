<?php
use ObzoraNMS\OS;

if (empty($os) || ! $os instanceof OS) {
    $os = OS::make($device);
}

(new \ObzoraNMS\Modules\Routes())->discover($os);
