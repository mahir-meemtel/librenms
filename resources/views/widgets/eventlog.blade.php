<div id="eventlog_container-{{ $id }}" data-reload="false" class="eventlog-widget-modern">
    <div class="eventlog-table-container">
        <table id="eventlog-{{ $id }}" class="table eventlog-table">
            <thead>
            <tr>
                <th data-column-id="datetime" data-order="desc">{{ __('Timestamp') }}</th>
                <th data-column-id="type">{{ __('Type') }}</th>
                <th data-column-id="device_id">{{ __('Hostname') }}</th>
                <th data-column-id="message">{{ __('Message') }}</th>
                <th data-column-id="username">{{ __('User') }}</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    $(function () {
        var grid = $("#eventlog-{{ $id }}").bootgrid({
            ajax: true,
            rowCount: [50, 100, 250, -1],
            navigation: ! {{ $hidenavigation }},
            post: function () {
                return {
                    device: "{{ $device }}",
                    device_group: "{{ $device_group }}",
                    eventtype: "{{ $eventtype }}"
                };
            },
            url: "{{ url('/ajax/table/eventlog') }}"
        });

        $('#eventlog_container-{{ $id }}').on('refresh', function (event) {
            grid.bootgrid('reload');
        });
        $('#eventlog_container-{{ $id }}').on('destroy', function (event) {
            grid.bootgrid('destroy');
            delete grid;
        });
    });
</script>

<style>
.eventlog-widget-modern {
    padding: 8px;
}

.eventlog-table-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    overflow: hidden;
}

.eventlog-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.eventlog-table thead th {
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

.eventlog-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: background 0.2s ease;
}

.eventlog-table tbody tr:hover {
    background: #f8f9fa;
}

.eventlog-table tbody tr:last-child {
    border-bottom: none;
}

.eventlog-table tbody td {
    padding: 10px 12px;
    font-size: 13px;
    color: #2c3e50;
    vertical-align: middle;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.eventlog-status {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 8px;
    vertical-align: middle;
}

.alert-status.label-success {
    background: #28a745;
}

.alert-status.label-info {
    background: #17a2b8;
}

.alert-status.label-primary {
    background: #007bff;
}

.alert-status.label-warning {
    background: #ffc107;
}

.alert-status.label-danger {
    background: #dc3545;
}

.alert-status.label-default {
    background: #6c757d;
}

.eventlog-table + .bootgrid-footer {
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    padding: 8px 12px;
}

.eventlog-table + .bootgrid-footer .infoBar,
.eventlog-table + .bootgrid-footer .search {
    font-size: 12px;
    color: #6c757d;
}

.eventlog-table + .bootgrid-footer .pagination > li > a {
    color: #2c539e;
    border-color: #e0e0e0;
    padding: 6px 10px;
    font-size: 12px;
}

.eventlog-table + .bootgrid-footer .pagination > li > a:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.eventlog-table + .bootgrid-footer .pagination > .active > a {
    background: #2c539e;
    border-color: #2c539e;
    color: #fff;
}

.eventlog-table + .bootgrid-footer .actionBar .btn {
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    background: #fff;
    color: #2c539e;
    transition: all 0.2s ease;
}

.eventlog-table + .bootgrid-footer .actionBar .btn:hover {
    background: #2c539e;
    color: #fff;
    border-color: #2c539e;
}

@media (max-width: 768px) {
    .eventlog-table thead th,
    .eventlog-table tbody td {
        padding: 8px 10px;
        font-size: 12px;
    }
}
</style>
