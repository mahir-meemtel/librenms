<?php
foreach ($pre_cache['infineragroove_slotTable'] as $index => $data) {
    if (isset($data['cardFanSpeedRate']) && is_numeric($data['cardFanSpeedRate']) && $data['cardFanSpeedRate'] != -99) {
        $infinera_slot = 'slot-' . str_replace('.', '/', $index);
        $descr = 'Chassis fan ' . $infinera_slot;
        $oid = '.1.3.6.1.4.1.42229.1.2.3.3.1.1.7.' . $index;
        $value = $data['cardFanSpeedRate'];
        discover_sensor(null, 'load', $device, $oid, 'cardFanSpeedRate.' . $index, 'infinera-groove', $descr, null, '1', 0, 20, 80, 100, $value);
    }
}

unset($infinera_slot);
