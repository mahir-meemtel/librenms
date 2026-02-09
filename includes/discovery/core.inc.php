<?php
use ObzoraNMS\OS;
use ObzoraNMS\OS\Generic;

// start assuming no os
(new \ObzoraNMS\Modules\Core())->discover(Generic::make($device));

// then create with actual OS
$os = OS::make($device);
