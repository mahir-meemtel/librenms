@extends('layouts.obzorav1')

@section('title', __('About'))

@section('css')
<style>
    /* About page content visibility - white background for better readability */
    .about-page-content .container-fluid {
        background-color: #ffffff !important;
        padding: 20px !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        margin: 20px !important;
    }

    .about-page-content .container-fluid .row {
        background-color: transparent !important;
    }

    .about-page-content .container-fluid .col-md-6 {
        background-color: #ffffff !important;
        padding: 15px !important;
    }

    .about-page-content .container-fluid h3 {
        color: #333 !important;
        margin-top: 0 !important;
        margin-bottom: 15px !important;
    }

    .about-page-content .container-fluid table {
        background-color: #ffffff !important;
    }

    .about-page-content .container-fluid table td {
        color: #333 !important;
        background-color: #ffffff !important;
    }

    .about-page-content .container-fluid table tr {
        background-color: #ffffff !important;
    }

    .about-page-content .container-fluid table.table-hover tbody tr:hover {
        background-color: #f5f5f5 !important;
    }

    .about-page-content .container-fluid a {
        color: #2c539e !important;
    }

    .about-page-content .container-fluid a:hover {
        color: #1e3a6f !important;
    }

    .about-page-content .container-fluid label {
        color: #333 !important;
    }
</style>
@endsection

@section('content')
<div class="about-page-content">
<div class="modal fade" id="git_log" tabindex="-1" role="dialog" aria-labelledby="git_log_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('Local git log') }}</h4>
            </div>
            <div class="modal-body">
                <pre>{!! $git_log !!}</pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@Lang('Close')</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
    <div class="about-page-wrapper">
    <div class="about-page-box">
        <h2>About Obzora NMS</h2>

        <p>
            <strong>Obzora Network Monitoring Solution (NMS)</strong> is a unified monitoring platform 
            designed for real-time visibility and performance analysis. 
            It enables organizations and partners to efficiently monitor devices, networks, and services 
            from a single, intuitive interface.
        </p>

        <div class="about-info">
            <p><strong>Current Version:</strong> 1.0.0</p>
            <p><strong>Email:</strong> info@obzora.net</p>
            <p><strong>Website:</strong> www.obzora.net</p>
        </div>

        <p class="about-footer">
            © 2025 Obzora NMS. All rights reserved.
        </p>
    </div>
</div>


         <!--<div class="col-md-6">

            <h3>{{ __('ObzoraNMS is an autodiscovering network monitoring system') }}</h3>
           <table class='table table-condensed table-hover'>
                <tr>
                    <td><b>{{ __('Version') }}</b></td>
                    <td><a target="_blank" href='https://obzora.meemtel.com/'>{{ $version_local }}<span id='version_date' style="display: none;">{{ $git_date }}</span></a></td>
                </tr>
                <tr>
                    <td><b>{{ __('Database Schema') }}</b></td>
                    <td>{{ $db_schema }}</td>
                </tr>
                <tr>
                    <td><b>{{ __('Web Server') }}</b></td>
                    <td>{{ $version_webserver }}</td>
                </tr>
                <tr>
                    <td><b>{{ __('PHP') }}</b></td>
                    <td>{{ $version_php }}</td>
                </tr>
                <tr>
                    <td><b>{{ __('Python') }}</b></td>
                    <td>{{ $version_python }}</td>
                </tr>
                <tr>
                    <td><b>{{ __('Database') }}</b></td>
                    <td>{{ $version_database }}</td>
                </tr>
                <tr>
                    <td><a target="_blank" href="https://laravel.com/"><b>{{ __('Laravel') }}</b></a></td>
                    <td>{{ $version_laravel }}</td>
                </tr>
                <tr>
                    <td><a target="_blank" href="https://oss.oetiker.ch/rrdtool/"><b>{{ __('RRDtool') }}</b></a></td>
                    <td>{{ $version_rrdtool }}</td>
                </tr>
            </table>


      </div>-->
      <!--<div class="col-md-6">

        <h3>{{ __('Reporting & Statistics') }}</h3>

        <table class='table table-condensed'>

            @admin
            <tr>
                <td colspan='4'>
                    <div>
                        <label for="reporting.usage" class="bg-info">{{ __('Opt in to send anonymous reports to ObzoraNMS?') }}</label>
                    </div>
                    <div>
                        {{ __('Error reporting:') }} <input type="checkbox" id="reporting.error" name="reporting" data-size="small" @if($error_reporting_status) checked @endif>
                    </div>
                    <div class="tw:mt-2">
                    {{ __('Usage statistics:') }} <input type="checkbox" id="reporting.usage" name="reporting" data-size="small" @if($usage_reporting_status) checked @endif> <a target="_blank" href='https://stats.obzora.meemtel.com/'>stats.obzora.meemtel.com</a>
                    </div>
                    @if($reporting_clearable)
                        <div class="tw:mt-2">
                            <button class='btn btn-danger btn-xs' type='submit' name='clear-reporting' id='clear-reporting'>{{ __('Clear reporting data') }}</button>
                        </div>
                    @endif
                </td>
            </tr>
            @endadmin

            <tr>
                <td><i class='fa fa-fw fa-server fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Devices') }}</b></td>
                <td class='text-right'>{{ $stat_devices }}</td>
                <td><i class='fa fa-fw fa-link fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Ports') }}</b></td>
                <td class='text-right'>{{ $stat_ports }}</td>
            </tr>
            <tr>
                <td><i class='fa fa-fw fa-battery-empty fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('IPv4 Addresses') }}</b></td>
                <td class='text-right'>{{ $stat_ipv4_addy }}</td>
                <td><i class='fa fa-fw fa-battery-empty fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('IPv4 Networks') }}</b></td>
                <td class='text-right'>{{ $stat_ipv4_nets }}</td>
            </tr>
            <tr>
                <td><i class='fa fa-fw fa-battery-full fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('IPv6 Addresses') }}</b></td>
                <td class='text-right'>{{ $stat_ipv6_addy }}</td>
                <td><i class='fa fa-fw fa-battery-full fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('IPv6 Networks') }}</b></td>
                <td class='text-right'>{{ $stat_ipv6_nets }}</td>
            </tr>
            <tr>
                <td><i class='fa fa-fw fa-cogs fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Services') }}</b></td>
                <td class='text-right'>{{ $stat_services }}</td>
                <td><i class='fa fa-fw fa-cubes fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Applications') }}</b></td>
                <td class='text-right'>{{ $stat_apps }}</td>
            </tr>
            <tr>
                <td><i class='fa fa-fw fa-microchip fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Processors') }}</b></td>
                <td class='text-right'>{{ $stat_processors }}</td>
                <td><i class='fa-fw fas fa-memory fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Memory') }}</b></td>
                <td class='text-right'>{{ $stat_memory }}</td>
            </tr>
            <tr>
                <td><i class='fa fa-fw fa-database fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Storage') }}</b></td>
                <td class='text-right'>{{ $stat_storage }}</td>
                <td><i class='fa fa-fw fa-hdd-o fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Disk I/O') }}</b></td>
                <td class='text-right'>{{ $stat_diskio }}</td>
            </tr>
            <tr>
                <td><i class='fa fa-fw fa-cube fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('HR-MIB') }}</b></td>
                <td class='text-right'>{{ $stat_hrdev }}</td>
                <td><i class='fa fa-fw fa-cube fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Entity-MIB') }}</b></td>
                <td class='text-right'>{{ $stat_entphys }}</td>
            </tr>
            <tr>
                <td><i class='fa fa-fw fa-clone fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Syslog Entries') }}</b></td>
                <td class='text-right'>{{ $stat_syslog }}</td>
                <td><i class='fa fa-fw fa-bookmark fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Eventlog Entries') }}</b></td>
                <td class='text-right'>{{ $stat_events }}</td>
            </tr>
            <tr>
                <td><i class='fa fa-fw fa-dashboard fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('sensors.title') }}</b></td>
                <td class='text-right'>{{ $stat_sensors }}</td>
                <td><i class='fa fa-fw fa-wifi fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Wireless Sensors') }}</b></td>
                <td class='text-right'>{{ $stat_wireless }}</td>
            </tr>
            <tr>
                <td><i class='fa fa-fw fa-print fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('Toner') }}</b></td>
                <td class='text-right'>{{ $stat_toner }}</td>
                <td><i class='fa fa-fw fa-code-fork fa-lg icon-theme' aria-hidden='true'></i> <b>{{ __('QoS Queues') }}</b></td>
                <td class='text-right'>{{ $stat_qos }}</td>
            </tr>
        </table>

        </div>
    </div>
</div>-->
</div>
@endsection

@section('scripts')
<script>
    $("[name='reporting']").bootstrapSwitch('offColor','danger','size','mini');
    $('input[name="reporting"]').on('switchChange.bootstrapSwitch',  function(event, state) {
        event.preventDefault();
        const type = event.target.id;
        $.ajax({
            type: 'PUT',
            url: '{{ route('settings.update', '?') }}'.replace('?', type),
            data: JSON.stringify({value: state}),
            contentType: "application/json",
            success: function(data){},
            error:function(){
                return $("#" + type).bootstrapSwitch("toggle");
            }
        });
    });
    $('#clear-reporting').on("click", function(event) {
        event.preventDefault();
        $.ajax({
            type: 'DELETE',
            url: '{{ route('reporting.clear') }}',
            success: function(){
                $('#clear-reporting').remove();
                $("#callback").bootstrapSwitch('state', false);
            },
            error:function(){}
        });
    });

    var ver_date = $('#version_date');
    if (ver_date.text()) {
        ver_date.text(' - '.concat(moment(ver_date.text()))).show();
    }
</script>
@endsection
