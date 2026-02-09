<?php
$common_output[] = '
<div class="table-responsive">
    <table id="outages" class="table table-hover table-condensed table-striped"
        data-url="' . route('table.outages') . '">
        <thead>
            <tr>
                <th data-column-id="status" data-sortable="false"></th>
                <th data-column-id="going_down" data-order="desc">Start</th>
                <th data-column-id="up_again">End</th>
                <th data-column-id="device_id">Hostname</th>
                <th data-column-id="duration" data-sortable="false">Duration</th>
            </tr>
        </thead>
    </table>
</div>
<script>

var outages_grid = $("#outages").bootgrid({
    ajax: true,
    rowCount: [50, 100, 250, -1],
    templates: {
        search: ""
    },
    post: function ()
    {
        return {
            device: ' . (empty($vars['device']) ? 'null' : (int) $vars['device']) . ',
            to: "' . htmlspecialchars($vars['to']) . '",
            from: "' . htmlspecialchars($vars['from']) . '",
        };
    },
});

</script>
';
