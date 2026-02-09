<?php
foreach ($pre_cache['ciscoepc_docsIfDownstreamChannelTable'] as $index => $data) {
    if (is_numeric($data['docsIfDownChannelPower'])) {
        $descr = "Channel {$data['docsIfDownChannelId']}";
        $oid = '.1.3.6.1.2.1.10.127.1.1.1.1.6.' . $index;
        $divisor = 10;
        $value = $data['docsIfDownChannelPower'];
        discover_sensor(null, 'dbm', $device, $oid, 'docsIfDownChannelPower.' . $index, 'ciscoepc', $descr, $divisor, '1', null, null, null, null, $value);
    }
}
