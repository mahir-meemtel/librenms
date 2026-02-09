<div id="alertlog-stats_container-{{ $id }}" data-reload="false" class="alertlog-stats-widget-modern">
    <div class="alertlog-stats-table-container">
        <table id="alertlog-stats-{{ $id }}" class="table alertlog-stats-table">
            <thead>
            <tr>
                <th data-column-id="count">{{ __('Count') }}</th>
                <th data-column-id="hostname">{{ __('Device') }}</th>
                <th data-column-id="alert_rule">{{ __('Alert rule') }}</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    (function () {
        var grid = $("#alertlog-stats-{{ $id }}").bootgrid({
            ajax: true,
            rowCount: [50, 100, 250, -1],
            navigation: ! {{ $hidenavigation }},
            post: function () {
                return {
                    id: "alertlog-stats",
                    device_id: "",
                    min_severity: '{{ $min_severity }}',
                    time_interval: '{{ $time_interval }}'
                };
            },
            url: "ajax_table.php"
        });

        $('#alertlog-stats_container-{{ $id }}').on('refresh', function (event) {
            grid.bootgrid('reload');
        });
        $('#alertlog-stats_container-{{ $id }}').on('destroy', function (event) {
            grid.bootgrid('destroy');
            delete grid;
        });
    })();
</script>

<style>
.alertlog-stats-widget-modern {
    padding: 8px;
}

.alertlog-stats-table-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    overflow: hidden;
}

.alertlog-stats-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.alertlog-stats-table thead th {
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

.alertlog-stats-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: background 0.2s ease;
}

.alertlog-stats-table tbody tr:hover {
    background: #f8f9fa;
}

.alertlog-stats-table tbody tr:last-child {
    border-bottom: none;
}

.alertlog-stats-table tbody td {
    padding: 10px 12px;
    font-size: 13px;
    color: #2c3e50;
    vertical-align: middle;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.alertlog-stats-table + .bootgrid-footer {
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    padding: 8px 12px;
}

.alertlog-stats-table + .bootgrid-footer .infoBar,
.alertlog-stats-table + .bootgrid-footer .search {
    font-size: 12px;
    color: #6c757d;
}

.alertlog-stats-table + .bootgrid-footer .pagination > li > a {
    color: #2c539e;
    border-color: #e0e0e0;
    padding: 6px 10px;
    font-size: 12px;
}

.alertlog-stats-table + .bootgrid-footer .pagination > li > a:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.alertlog-stats-table + .bootgrid-footer .pagination > .active > a {
    background: #2c539e;
    border-color: #2c539e;
    color: #fff;
}

.alertlog-stats-table + .bootgrid-footer .actionBar .btn {
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    background: #fff;
    color: #2c539e;
    transition: all 0.2s ease;
}

.alertlog-stats-table + .bootgrid-footer .actionBar .btn:hover {
    background: #2c539e;
    color: #fff;
    border-color: #2c539e;
}

@media (max-width: 768px) {
    .alertlog-stats-table thead th,
    .alertlog-stats-table tbody td {
        padding: 8px 10px;
        font-size: 12px;
    }
}
</style>
