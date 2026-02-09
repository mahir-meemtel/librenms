<?php
if (isset($vars['id'])) {
    $component = new ObzoraNMS\Component();
    $filter = [
        'filter' => [
            'type' => ['=', 'cisco-qfp'],
            'id' => ['=', $vars['id']],
        ],
    ];
    $components = $component->getComponents(null, $filter);
    /*
     * Fist (and only) key is the device ID
     */
    $device_id = key($components);
    /*
     * Check if component exists and we're authenticated
     */
    if ($components && isset($components[$device_id][$vars['id']]) && ($auth || device_permitted($device_id))) {
        $components = $components[$device_id][$vars['id']];
        $device = device_by_id_cache($device_id);

        /*
         * Data is split into just two RRD files, memory resources and utilization
         */
        if ($subtype == 'memory') {
            $rrd_filename = Rrd::name($device['hostname'], ['cisco-qfp', 'memory', $components['entPhysicalIndex']]);
        } else {
            $rrd_filename = Rrd::name($device['hostname'], ['cisco-qfp', 'util', $components['entPhysicalIndex']]);
        }

        /*
         * Build title with breadcrumbs for module's main subpage
         */
        $link_array = [
            'page' => 'device',
            'device' => $device['device_id'],
            'tab' => 'health',
        ];
        $title = generate_device_link($device);
        $title .= ' :: ' . generate_link('QFP', $link_array, ['metric' => 'qfp']);
        $title .= ' :: ' . $components['name'];

        $auth = true;
    }
}
