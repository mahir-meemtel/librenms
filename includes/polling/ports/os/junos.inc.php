<?php
$junos_vcp_stats = snmpwalk_cache_oid($device, 'jnxVirtualChassisPortTable', [], 'JUNIPER-VIRTUALCHASSIS-MIB');

d_echo($junos_vcp_stats);

foreach ($junos_vcp_stats as $index => $vcp_stats) {
    // VirtuallChassis MIB uses string indexes so dummy integer indexes for
    // VC ports need to be created.

    // Check if index string has expected format and decompose it to form
    // dummy integer index of it.

    if (preg_match('#^(\d{1,2})\.vcp-255/(\d)/(\d{1,2})$#', $index, $matches)) {
        $fpc = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $pic = $matches[2];
        $port = str_pad($matches[3], 2, '0', STR_PAD_LEFT);

        // The concatenation below starts VC port dummy indexes from 1000000
        // to protect against overlapping with IF-MIB.

        $nms_index = '100' . $fpc . $pic . $port;

        $port_stats[$nms_index]['ifDescr'] = "fpc$index";
        $port_stats[$nms_index]['ifType'] = 'vcp';
        $port_stats[$nms_index]['ifName'] = "fpc$index";
        $port_stats[$nms_index]['ifHCInOctets'] = $vcp_stats['jnxVirtualChassisPortInOctets'];
        $port_stats[$nms_index]['ifHCOutOctets'] = $vcp_stats['jnxVirtualChassisPortOutOctets'];
        $port_stats[$nms_index]['ifHCInUcastPkts'] = $vcp_stats['jnxVirtualChassisPortInPkts'];
        $port_stats[$nms_index]['ifHCOutUcastPkts'] = $vcp_stats['jnxVirtualChassisPortOutPkts'];
        $port_stats[$nms_index]['ifInMulticastPkts'] = $vcp_stats['jnxVirtualChassisPortInMcasts'];
        $port_stats[$nms_index]['ifOutMulticastPkts'] = $vcp_stats['jnxVirtualChassisPortOutMcasts'];
        $port_stats[$nms_index]['ifInErrors'] = $vcp_stats['jnxVirtualChassisPortInCRCAlignErrors'];
        $port_stats[$nms_index]['ifAdminStatus'] = $vcp_stats['jnxVirtualChassisPortAdminStatus'];
        $port_stats[$nms_index]['ifOperStatus'] = $vcp_stats['jnxVirtualChassisPortOperStatus'];
    }
}
