<?php
$smokeping = new \ObzoraNMS\Util\Smokeping(DeviceCache::getPrimary());
$smokeping_files = $smokeping->findFiles();
