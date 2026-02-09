<?php
foreach ($pre_cache['ciscoepc_docsIfSignalQualityTable'] as $index => $data) {
    if (is_numeric($data['docsIfSigQSignalNoise'])) {
        $descr = "Channel {$pre_cache['ciscoepc_docsIfDownstreamChannelTable'][$index]['docsIfDownChannelId']}";
        $oid = '.1.3.6.1.2.1.10.127.1.1.4.1.5.' . $index;
        $divisor = 10;
        $value = $data['docsIfSigQSignalNoise'];
        discover_sensor(null, 'snr', $device, $oid, 'docsIfSigQSignalNoise.' . $index, 'ciscoepc', $descr, $divisor, '1', null, null, null, null, $value);
    }
}
