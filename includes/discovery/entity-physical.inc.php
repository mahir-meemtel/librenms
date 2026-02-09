<?php
use ObzoraNMS\Modules\EntityPhysical;
use ObzoraNMS\OS;

if (! isset($os) || ! $os instanceof OS) {
    $os = OS::make($device);
}
(new EntityPhysical())->discover($os);
