<?php
use ObzoraNMS\Interfaces\Plugins\Hooks\PortTabHook;

$pagetitle[] = 'Plugins';
$no_refresh = true;
?>

<h3>Plugins</h3>
<hr>
<?php
echo \ObzoraNMS\Plugins::call('port_container', [$device, $port]);
foreach (PluginManager::call(PortTabHook::class, ['port' => $port]) as $view) {
    echo $view;
};
