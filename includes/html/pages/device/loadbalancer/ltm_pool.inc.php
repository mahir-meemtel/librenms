<?php
$component = new ObzoraNMS\Component();
$components = $component->getComponents($device['device_id'], ['filter' => ['disabled' => ['=', 0]]]);

// We only care about our device id.
$components = $components[$device['device_id']];

// We extracted all the components for this device, now lets only get the LTM ones.
$keep = [];
$types = [$module, 'f5-ltm-pool', 'f5-ltm-poolmember'];
foreach ($components as $k => $v) {
    foreach ($types as $type) {
        if ($v['type'] == $type) {
            $keep[$k] = $v;
        }
    }
}
$components = $keep;

if (is_file('includes/html/pages/device/loadbalancer/' . $vars['subtype'] . '.inc.php')) {
    include 'includes/html/pages/device/loadbalancer/' . $vars['subtype'] . '.inc.php';
} else {
    include 'includes/html/pages/device/loadbalancer/ltm_pool_all.inc.php';
}//end if
