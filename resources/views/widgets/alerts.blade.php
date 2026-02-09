<div id="alerts_container-{{ $id }}" data-reload="false" class="alerts-widget-modern">
    <div class="alerts-table-container">
        <table id="alerts-{{ $id }}" class="table alerts-table">
            <thead>
            <tr>
                <th data-column-id="severity"></th>
                <th data-column-id="timestamp">{{ __('Timestamp') }}</th>
                <th data-column-id="rule">{{ __('Rule') }}</th>
                <th data-column-id="details" data-sortable="false"></th>
                <th data-column-id="hostname">{{ __('Hostname') }}</th>
                <th data-column-id="location" data-visible="{{ $location ? 'true' : 'false' }}">{{ __('Location') }}</th>
                <th data-column-id="ack_ico" data-sortable="false">{{ __('ACK') }}</th>
                <th data-column-id="notes" data-sortable="false">{{ __('Notes') }}</th>
                <th data-column-id="proc" data-sortable="false" data-visible="{{ $proc ? 'true' : 'false' }}">URL</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    (function () {
        var alerts_grid = $("#alerts-{{ $id }}").bootgrid({
            ajax: true,
            requestHandler: request => ({
                ...request,
                id: "alerts",
                acknowledged: '{{ $acknowledged }}',
                unreachable: '{{ $unreachable }}',
                fired: '{{ $fired }}',
                min_severity: '{{ $min_severity }}',
                group: '{{ $device_group }}',
                proc: '{{ $proc }}',
                sort: '{{ $sort }}',
                uncollapse_key_count: '{{ $uncollapse_key_count }}',
                device_id: '{{ $device }}'
            }),
            responseHandler: response => {
                $("#widget_title_counter_{{ $id }}").text(response.total ? ` (${response.total})` : '')

                return response
            },
            url: "ajax_table.php",
            navigation: ! {{ $hidenavigation }},
            rowCount: [50, 100, 250, -1]
        }).on("loaded.rs.jquery.bootgrid", function() {
            alerts_grid = $(this);
            
            // Set column widths
            alerts_grid.find('thead th:nth-child(1), tbody td:nth-child(1)').css({'width': '40px', 'min-width': '40px', 'max-width': '40px'});
            alerts_grid.find('thead th:nth-child(4), tbody td:nth-child(4)').css({'width': '50px', 'min-width': '50px', 'max-width': '50px'});
            alerts_grid.find('thead th:nth-child(3), tbody td:nth-child(3)').css({'min-width': '200px', 'width': 'auto'});
            
            alerts_grid.find(".incident-toggle").each( function() {
                $(this).parent().addClass('incident-toggle-td');
            }).on("click", function(e) {
                var target = $(this).data("target");
                $(target).collapse('toggle');
                $(this).toggleClass('fa-plus fa-minus');
            });
            alerts_grid.find(".incident").each( function() {
                $(this).parent().addClass('col-lg-4 col-md-4 col-sm-4 col-xs-4');
                $(this).parent().parent().on("mouseenter", function() {
                    $(this).find(".incident-toggle").fadeIn(200);
                }).on("mouseleave", function() {
                    $(this).find(".incident-toggle").fadeOut(200);
                });
            });
            alerts_grid.find(".command-ack-alert").on("click", function(e) {
                e.preventDefault();
                var alert_state = $(this).data("alert_state");
                var alert_id = $(this).data('alert_id');
                $('#ack_alert_id').val(alert_id);
                $('#ack_alert_state').val(alert_state);
                $('#ack_msg').val('');
                $("#alert_ack_modal").modal('show');
            });
            alerts_grid.find(".command-alert-note").on("click", function(e) {
                e.preventDefault();
                var alert_id = $(this).data('alert_id');
                $('#alert_id').val(alert_id);
                $("#alert_notes_modal").modal('show');
            });
        });

        $('#alerts_container-{{ $id }}').on('refresh', function (event) {
            alerts_grid.bootgrid('reload');
        });
        $('#alerts_container-{{ $id }}').on('destroy', function (event) {
            alerts_grid.bootgrid('destroy');
            delete alerts_grid;
        });
    })();
</script>

<style>
.alerts-widget-modern {
    padding: 8px;
}

.alerts-table-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    overflow: hidden;
}

.alerts-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.alerts-table thead th {
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

.alerts-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: background 0.2s ease;
}

.alerts-table tbody tr:hover {
    background: #f8f9fa;
}

.alerts-table tbody tr:last-child {
    border-bottom: none;
}

.alerts-table tbody td {
    padding: 10px 12px;
    font-size: 13px;
    color: #2c3e50;
    vertical-align: middle;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

/* Column width adjustments */
.alerts-table thead th:nth-child(1),
.alerts-table tbody td:nth-child(1) {
    width: 40px !important;
    min-width: 40px !important;
    max-width: 40px !important;
    padding: 10px 8px;
}

.alerts-table thead th:nth-child(4),
.alerts-table tbody td:nth-child(4) {
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    padding: 10px 8px;
}

.alerts-table thead th:nth-child(3),
.alerts-table tbody td:nth-child(3) {
    min-width: 200px !important;
    width: auto !important;
}

.alerts-table + .bootgrid-footer {
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    padding: 8px 12px;
}

.alerts-table + .bootgrid-footer .infoBar,
.alerts-table + .bootgrid-footer .search {
    font-size: 12px;
    color: #6c757d;
}

.alerts-table + .bootgrid-footer .pagination > li > a {
    color: #2c539e;
    border-color: #e0e0e0;
    padding: 6px 10px;
    font-size: 12px;
}

.alerts-table + .bootgrid-footer .pagination > li > a:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.alerts-table + .bootgrid-footer .pagination > .active > a {
    background: #2c539e;
    border-color: #2c539e;
    color: #fff;
}

.alerts-table + .bootgrid-footer .actionBar .btn {
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    background: #fff;
    color: #2c539e;
    transition: all 0.2s ease;
}

.alerts-table + .bootgrid-footer .actionBar .btn:hover {
    background: #2c539e;
    color: #fff;
    border-color: #2c539e;
}

@media (max-width: 768px) {
    .alerts-table thead th,
    .alerts-table tbody td {
        padding: 8px 10px;
        font-size: 12px;
    }
}
</style>
