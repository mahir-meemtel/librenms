<?php
use ObzoraNMS\OS;

if (! $os instanceof OS) {
    $os = OS::make($device);
}
(new \ObzoraNMS\Modules\Ospfv3())->poll($os, app('Datastore'));
