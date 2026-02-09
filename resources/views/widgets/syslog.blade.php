<div id="syslog_container-{{ $id }}" data-reload="false" class="syslog-widget-modern">
    <div class="syslog-table-container">
        <table id="syslog-{{ $id }}" class="table syslog-table">
            <thead>
            <tr>
                <th data-column-id="label"></th>
                <th data-column-id="timestamp" data-order="desc">{{ __('Timestamp') }}</th>
                <th data-column-id="level">{{ __('Level') }}</th>
                <th data-column-id="device_id">{{ __('Hostname') }}</th>
                <th data-column-id="program">{{ __('Program') }}</th>
                <th data-column-id="msg">{{ __('Message') }}</th>
                <th data-column-id="priority">{{ __('Priority') }}</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script type="application/javascript">
    (function () {
        var grid = $("#syslog-{{ $id }}").bootgrid({
            ajax: true,
            rowCount: [50, 100, 250, -1],
            navigation: ! {{ $hidenavigation }},
            post: function () {
                return {
                    device: '{{ $device ?: '' }}',
                    device_group: '{{ $device_group }}',
                    level: '{{ $level }}'
                };
            },
            url: "{{ url('/ajax/table/syslog') }}"
        });

        $('#syslog_container-{{ $id }}').on('refresh', function (event) {
            grid.bootgrid('reload');
        });
        $('#syslog_container-{{ $id }}').on('destroy', function (event) {
            grid.bootgrid('destroy');
            delete grid;
        });
    })();
</script>

<style>
.syslog-widget-modern {
    padding: 8px;
}

.syslog-table-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    overflow: hidden;
}

.syslog-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.syslog-table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #2c3e50;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 12px;
    border: none;
    border-bottom: 2px solid #e0e0e0;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.syslog-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: background 0.2s ease;
}

.syslog-table tbody tr:hover {
    background: #f8f9fa;
}

.syslog-table tbody tr:last-child {
    border-bottom: none;
}

.syslog-table tbody td {
    padding: 10px 12px;
    font-size: 13px;
    color: #2c3e50;
    vertical-align: middle;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.syslog-table + .bootgrid-footer {
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    padding: 8px 12px;
}

.syslog-table + .bootgrid-footer .infoBar,
.syslog-table + .bootgrid-footer .search {
    font-size: 12px;
    color: #6c757d;
}

.syslog-table + .bootgrid-footer .pagination > li > a {
    color: #2c539e;
    border-color: #e0e0e0;
    padding: 6px 10px;
    font-size: 12px;
}

.syslog-table + .bootgrid-footer .pagination > li > a:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.syslog-table + .bootgrid-footer .pagination > .active > a {
    background: #2c539e;
    border-color: #2c539e;
    color: #fff;
}

.syslog-table + .bootgrid-footer .actionBar .btn {
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    background: #fff;
    color: #2c539e;
    transition: all 0.2s ease;
}

.syslog-table + .bootgrid-footer .actionBar .btn:hover {
    background: #2c539e;
    color: #fff;
    border-color: #2c539e;
}

@media (max-width: 768px) {
    .syslog-table thead th,
    .syslog-table tbody td {
        padding: 8px 10px;
        font-size: 12px;
    }
}
</style>
