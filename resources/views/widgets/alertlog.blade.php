<div id="alertlog_container-{{ $id }}" data-reload="false" class="alertlog-widget-modern">
    <div class="alertlog-table-container">
        <table id="alertlog_{{ $id }}" class="table alertlog-table">
            <thead>
            <tr>
                <th data-column-id="status" data-sortable="false"></th>
                <th data-column-id="time_logged" data-order="desc">{{ __('Timestamp') }}</th>
                <th data-column-id="details" data-sortable="false">&nbsp;</th>
                <th data-column-id="hostname">{{ __('Device') }}</th>
                <th data-column-id="alert">{{ __('Alert') }}</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    (function () {
        var grid = $("#alertlog_{{ $id }}").bootgrid({
            ajax: true,
            rowCount: [50, 100, 250, -1],
            navigation: ! {{ $hidenavigation }},
            post: function () {
                return {
                    id: "alertlog",
                    device_id: "",
                    device_group: "{{ $device_group }}",
                    state: '{{ $state }}',
                    min_severity: '{{ $min_severity }}',
                };
            },
            url: "ajax_table.php"
        }).on("loaded.rs.jquery.bootgrid", function () {
            // Set column widths
            grid.find('thead th:nth-child(1), tbody td:nth-child(1)').css({'width': '40px', 'min-width': '40px', 'max-width': '40px'});
            grid.find('thead th:nth-child(3), tbody td:nth-child(3)').css({'width': '50px', 'min-width': '50px', 'max-width': '50px'});
            grid.find('thead th:nth-child(5), tbody td:nth-child(5)').css({'min-width': '200px', 'width': 'auto'});
            
            grid.find(".incident-toggle").each(function () {
                $(this).parent().addClass('incident-toggle-td');
            }).on("click", function (e) {
                var target = $(this).data("target");
                $(target).collapse('toggle');
                $(this).toggleClass('fa-plus fa-minus');
            });
            grid.find(".incident").each(function () {
                $(this).parent().addClass('col-lg-4 col-md-4 col-sm-4 col-xs-4');
                $(this).parent().parent().on("mouseenter", function () {
                    $(this).find(".incident-toggle").fadeIn(200);
                }).on("mouseleave", function () {
                    $(this).find(".incident-toggle").fadeOut(200);
                }).on("click", "td:not(.incident-toggle-td)", function () {
                    var target = $(this).parent().find(".incident-toggle").data("target");
                    if ($(this).parent().find(".incident-toggle").hasClass('fa-plus')) {
                        $(this).parent().find(".incident-toggle").toggleClass('fa-plus fa-minus');
                        $(target).collapse('toggle');
                    }
                });
            });
        });

        $('#alertlog_container-{{ $id }}').on('refresh', function (event) {
            grid.bootgrid('reload');
        });
        $('#alertlog_container-{{ $id }}').on('destroy', function (event) {
            grid.bootgrid('destroy');
            delete grid;
        });
    })();
</script>

<style>
.alertlog-widget-modern {
    padding: 8px;
}

.alertlog-table-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    overflow: hidden;
}

.alertlog-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.alertlog-table thead th {
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

.alertlog-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: background 0.2s ease;
}

.alertlog-table tbody tr:hover {
    background: #f8f9fa;
}

.alertlog-table tbody tr:last-child {
    border-bottom: none;
}

.alertlog-table tbody td {
    padding: 10px 12px;
    font-size: 13px;
    color: #2c3e50;
    vertical-align: middle;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

/* Column width adjustments */
.alertlog-table thead th:nth-child(1),
.alertlog-table tbody td:nth-child(1) {
    width: 40px !important;
    min-width: 40px !important;
    max-width: 40px !important;
    padding: 10px 8px;
}

.alertlog-table thead th:nth-child(3),
.alertlog-table tbody td:nth-child(3) {
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    padding: 10px 8px;
}

.alertlog-table thead th:nth-child(5),
.alertlog-table tbody td:nth-child(5) {
    min-width: 200px !important;
    width: auto !important;
}

.alertlog-table + .bootgrid-footer {
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    padding: 8px 12px;
}

.alertlog-table + .bootgrid-footer .infoBar,
.alertlog-table + .bootgrid-footer .search {
    font-size: 12px;
    color: #6c757d;
}

.alertlog-table + .bootgrid-footer .pagination > li > a {
    color: #2c539e;
    border-color: #e0e0e0;
    padding: 6px 10px;
    font-size: 12px;
}

.alertlog-table + .bootgrid-footer .pagination > li > a:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.alertlog-table + .bootgrid-footer .pagination > .active > a {
    background: #2c539e;
    border-color: #2c539e;
    color: #fff;
}

.alertlog-table + .bootgrid-footer .actionBar .btn {
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    background: #fff;
    color: #2c539e;
    transition: all 0.2s ease;
}

.alertlog-table + .bootgrid-footer .actionBar .btn:hover {
    background: #2c539e;
    color: #fff;
    border-color: #2c539e;
}

@media (max-width: 768px) {
    .alertlog-table thead th,
    .alertlog-table tbody td {
        padding: 8px 10px;
        font-size: 12px;
    }
}
</style>
