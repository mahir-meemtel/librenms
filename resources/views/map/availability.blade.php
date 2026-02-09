@extends('layouts.obzorav1')

@section('title', __('Availability Map'))

@section('content')
    <div class="container-fluid availability-page">
        <div class="availability-header">
            <div class="availability-header-row">
                <div class="availability-controls">
                    <div class="control-group">
                        <label for="show_items" class="control-label">{{ __('Availability Map for') }}</label>
                        <select id="show_items" class="form-control modern-select" name="show_items" onchange="refreshMap()">
                            <option value="0" selected>{{ __('Only Devices') }}</option>
@if($services)
                            <option value="1">{{ __('Only Services') }}</option>
                            <option value="2">{{ __('Devices and Services') }}</option>
@endif
                        </select>
                    </div>
@if($use_groups)
                    <div class="control-group">
                        <label for="show_group" class="control-label">{{ __('Device group') }}</label>
                        <select id="show_group" class="form-control modern-select" name="show_group" onchange="refreshMap()">
                            <option value="0" selected>{{ __('Show all devices') }}</option>
@foreach($devicegroups as $g)
                            <option value="{{$g['id']}}">{{$g['name']}}</option>
@endforeach
                        </select>
                    </div>
@endif
                </div>
                
                <div class="availability-summary-section">
                    <div class="summary-card" id="devices-summary" style="display:none">
                        <div class="summary-title">{{ __('Total hosts') }}</div>
                        <div class="summary-badges">
                            <span class="badge badge-success-modern" id="devices-up"></span>
                            <span class="badge badge-warning-modern" id="devices-warn"></span>
                            <span class="badge badge-danger-modern" id="devices-down"></span>
                        </div>
                    </div>
                    <div class="summary-card" id="services-summary" style="display:none">
                        <div class="summary-title">{{ __('Total services') }}</div>
                        <div class="summary-badges">
                            <span class="badge badge-success-modern" id="services-up"></span>
                            <span class="badge badge-warning-modern" id="services-warn"></span>
                            <span class="badge badge-danger-modern" id="services-down"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="availability-content">
            <div id="device-list" class="availability-grid"></div>
            <div id="service-list" class="availability-grid"></div>
        </div>
    </div>
@endsection

@section('javascript')
@endsection

@section('scripts')
<script>
    function refreshMap() {
        group = null;
        if ($("#show_group").val()) {
            group = $("#show_group").val();
        }

        if ($("#show_items").val() == 0 || $("#show_items").val() == 2) {
            
            $.post( '{{ route('maps.getdevices') }}', {disabled: 0, disabled_alerts: null, group: group})
                .done(function( data ) {
                    var host_warn_count = 0;
                    var host_up_count = 0;
                    var host_down_count = 0;
                    var host_maintenance_count = 0;

                    function deviceSort(a,b) {
@if($sort == 'hostname')
                        return (data[a]["sname"] > data[b]["sname"]) ? 1 : -1;
@elseif($sort == 'status')
                        return (data[a]["status"] > data[b]["status"]) ? 1 : -1;
@else
                        // Sort not set, or unknown sort {{$sort}}
                        return 0;
@endif
                    }
                    var keys = Object.keys(data).sort(deviceSort);
                    var devicelist = document.createElement("div");

                    $.each( keys, function( key_idx, device_id ) {
                        var device = data[device_id];
                        var state, fullclass, compactclass;
                        if (device['status']) {
                            if (device['uptime'] && (device['uptime'] < {{$uptime_warn}})) {
                                state = 'warn';
                                fullclass = 'label-warning';
                                compactclass = 'availability-map-oldview-box-warn';
                                host_warn_count++;
                            } else {
                                state = 'up';
                                fullclass = 'label-success';
                                compactclass = 'availability-map-oldview-box-up';
                                host_up_count++;
                            }
                        } else if (device['maintenance']) {
                            state = 'maintenance';
                            fullclass = 'label-default';
                            compactclass = 'availability-map-oldview-box-ignored';
                            host_maintenance_count++;
                        } else {
                            state = 'down';
                            fullclass = 'label-danger';
                            compactclass = 'availability-map-oldview-box-down';
                            host_down_count++;
                        }

                        // create badge
                        var devhtml = document.createElement("a");
                        devhtml.href = device["url"];
                        devhtml.title = device["sname"] + ' - ' + device["updowntime"];
                    @if($compact)
                        var devcompact = document.createElement("div");
                        devcompact.classList.add("availability-tile-compact", compactclass);
                        devhtml.classList.add("availability-item-link");
                        devhtml.appendChild(devcompact);
                    @else
                        var devfull = document.createElement("div");
                        devfull.classList.add("availability-card", "device-card", state);
                        
                        var devheader = document.createElement("div");
                        devheader.classList.add("card-header");
                        
                        var devstatelabel = document.createElement("span");
                        devstatelabel.classList.add("status-badge", fullclass);
                        devstatelabel.textContent = state.toUpperCase();
                        devheader.appendChild(devstatelabel);
                        
                        devfull.appendChild(devheader);

                        var devbody = document.createElement("div");
                        devbody.classList.add("card-body");
                        
                        var devicon = document.createElement("div");
                        devicon.classList.add("device-icon-wrapper");
                        var deviconimage = document.createElement("img");
                        deviconimage.src = device["icon"];
                        deviconimage.alt = device["icontitle"];
                        deviconimage.title = device["icontitle"];
                        devicon.appendChild(deviconimage);
                        devbody.appendChild(devicon);

                        var devname = document.createElement("div");
                        devname.classList.add("device-name");
                        devname.textContent = device["sname"];
                        devbody.appendChild(devname);
                        
                        devfull.appendChild(devbody);
                        devhtml.classList.add("availability-item-link");
                        devhtml.appendChild(devfull);
                    @endif
                        devicelist.appendChild(devhtml);
                    });

                    document.getElementById("device-list").innerHTML = devicelist.innerHTML;
                    $("#devices-up").html('<span class="badge-value">' + host_up_count + '</span><span class="badge-label">UP</span>');
                    $("#devices-warn").html('<span class="badge-value">' + host_warn_count + '</span><span class="badge-label">WARN</span>');
                    $("#devices-down").html('<span class="badge-value">' + host_down_count + '</span><span class="badge-label">DOWN</span>');
                    $("#devices-summary").show();
                });
        } else {
            $("#device-list").html("");
            $("#devices-summary").hide();
        }
        if ($("#show_items").val() == 1 || $("#show_items").val() == 2) {
            $.post( '{{ route('maps.getservices') }}', {disabled: 0, disabled_alerts: null, device_group: group})
                .done(function( data ) {
                    var service_warn_count = 0;
                    var service_up_count = 0;
                    var service_down_count = 0;

                    function serviceSort(a,b) {
@if($sort == 'hostname')
                        return (a["device_name"] > b["device_name"]) ? 1 : -1;
@elseif($sort == 'status')
                        return (a["status"] > b["status"]) ? 1 : -1;
@else
                        // Sort not set, or unknown sort {{$sort}}
                        return 0;
@endif
                    }

                    var services = data.sort(serviceSort);
                    var servicelist = document.createElement("div");
                    $.each( services, function( svc_idx, service ) {
                        var fullclass,compactclass,state;

                        if (service['status'] == 0) {
                            fullclass = 'label-success';
                            compactclass = 'availability-map-oldview-box-up';
                            state = 'up';
                            service_up_count++;
                        } else if (service['status'] == 1) {
                            fullclass = 'label-warning';
                            compactclass = 'availability-map-oldview-box-warn';
                            state = 'warn';
                            service_warn_count++;
                        } else {
                            fullclass = 'label-danger';
                            compactclass = 'availability-map-oldview-box-down';
                            state = 'down';
                            service_down_count++;
                        }

                        // create badge
                        var svchtml = document.createElement("a");
                        svchtml.href = service["url"];
                        svchtml.title = service["device_name"] + ' - ' + service["updowntime"];
                    @if($compact)
                        var svccompact = document.createElement("div");
                        svccompact.classList.add("availability-tile-compact", compactclass);
                        svchtml.classList.add("availability-item-link");
                        svchtml.appendChild(svccompact);
                    @else
                        var svcfull = document.createElement("div");
                        svcfull.classList.add("availability-card", "service-card", state);
                        
                        var svcheader = document.createElement("div");
                        svcheader.classList.add("card-header");
                        
                        var svctypelabel = document.createElement("span");
                        svctypelabel.classList.add("service-type-badge", fullclass);
                        svctypelabel.textContent = service["type"];
                        svcheader.appendChild(svctypelabel);
                        
                        var svcstatelabel = document.createElement("span");
                        svcstatelabel.classList.add("status-badge", fullclass);
                        svcstatelabel.textContent = state.toUpperCase();
                        svcheader.appendChild(svcstatelabel);
                        
                        svcfull.appendChild(svcheader);

                        var svcbody = document.createElement("div");
                        svcbody.classList.add("card-body");
                        
                        var svcicon = document.createElement("div");
                        svcicon.classList.add("device-icon-wrapper");
                        var svciconimage = document.createElement("img");
                        svciconimage.src = service["icon"];
                        svciconimage.alt = service["icontitle"];
                        svciconimage.title = service["icontitle"];
                        svcicon.appendChild(svciconimage);
                        svcbody.appendChild(svcicon);

                        var svcname = document.createElement("div");
                        svcname.classList.add("device-name");
                        svcname.textContent = service["device_name"];
                        svcbody.appendChild(svcname);
                        
                        svcfull.appendChild(svcbody);
                        svchtml.classList.add("availability-item-link");
                        svchtml.appendChild(svcfull);
                    @endif
                        servicelist.appendChild(svchtml);
                    });
                    document.getElementById("service-list").innerHTML = servicelist.innerHTML;

                    $("#services-up").html('<span class="badge-value">' + service_up_count + '</span><span class="badge-label">UP</span>');
                    $("#services-warn").html('<span class="badge-value">' + service_warn_count + '</span><span class="badge-label">WARN</span>');
                    $("#services-down").html('<span class="badge-value">' + service_down_count + '</span><span class="badge-label">DOWN</span>');
                    $("#services-summary").show();
                });
        } else {
            $("#service-list").html("");
            $("#services-summary").hide();
        }
    }

    // initial data load
    $(document).ready(function () {
        refreshMap();
    });
</script>
<x-refresh-timer :refresh="$page_refresh" callback="refreshMap"></x-refresh-timer>

<style>
.availability-page {
    padding: 20px;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.availability-header {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.availability-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    flex-wrap: wrap;
}

.availability-controls {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    flex: 1;
}

.control-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 200px;
}

.control-label {
    font-size: 0.9em;
    font-weight: 600;
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modern-select {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: #fff;
}

.modern-select:hover {
    border-color: #adb5bd;
}

.modern-select:focus {
    outline: none;
    border-color: #2c539e;
    box-shadow: 0 0 0 3px rgba(44, 83, 158, 0.1);
}

.availability-summary-section {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-start;
}

.summary-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 6px;
    padding: 10px 14px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    min-width: 140px;
}

.summary-title {
    font-size: 0.75em;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.summary-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.badge-success-modern,
.badge-warning-modern,
.badge-danger-modern {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 45px;
    padding: 5px 8px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.badge-success-modern {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: #fff;
}

.badge-warning-modern {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: #212529;
}

.badge-danger-modern {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: #fff;
}

.badge-value {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 2px;
}

.badge-label {
    font-size: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
}

.availability-content {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.availability-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.availability-item-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.availability-item-link:hover {
    text-decoration: none;
}

.availability-card {
    background: #fff;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.availability-item-link:hover .availability-card {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.device-card.up {
    border-color: #28a745;
}

.device-card.down {
    border-color: #dc3545;
}

.device-card.warn {
    border-color: #ffc107;
}

.device-card.maintenance {
    border-color: #6c757d;
}

.service-card.up {
    border-color: #28a745;
}

.service-card.down {
    border-color: #dc3545;
}

.service-card.warn {
    border-color: #ffc107;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 10px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}

.status-badge {
    padding: 3px 6px;
    border-radius: 3px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.label-success {
    background: #28a745;
    color: #fff;
}

.status-badge.label-warning {
    background: #ffc107;
    color: #212529;
}

.status-badge.label-danger {
    background: #dc3545;
    color: #fff;
}

.status-badge.label-default {
    background: #6c757d;
    color: #fff;
}

.service-type-badge {
    padding: 3px 6px;
    border-radius: 3px;
    font-size: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.service-type-badge.label-success {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.service-type-badge.label-warning {
    background: rgba(255, 193, 7, 0.1);
    color: #f39c12;
}

.service-type-badge.label-danger {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.card-body {
    padding: 10px;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.device-icon-wrapper {
    margin-bottom: 8px;
}

.device-icon-wrapper img {
    width: 36px;
    height: 36px;
    object-fit: contain;
}

.device-name {
    font-size: 11px;
    font-weight: 600;
    color: #2c3e50;
    word-break: break-word;
    line-height: 1.3;
}

.availability-tile-compact {
    width: 16px;
    height: 16px;
    border-radius: 3px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.availability-item-link:hover .availability-tile-compact {
    transform: scale(1.2);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.availability-map-oldview-box-up {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.availability-map-oldview-box-warn {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
}

.availability-map-oldview-box-down {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}

.availability-map-oldview-box-ignored {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
}

.availability-map-oldview-box-ignored-up {
    background: linear-gradient(135deg, #5CB85C 0%, #4cae4c 100%) !important;
}

.availability-map-oldview-box-ignored-down {
    background: linear-gradient(135deg, #36393D 0%, #2a2d30 100%) !important;
}

@media (max-width: 768px) {
    .availability-page {
        padding: 10px;
    }
    
    .availability-header {
        padding: 15px;
    }
    
    .availability-header-row {
        flex-direction: column;
    }
    
    .availability-controls {
        flex-direction: column;
        gap: 15px;
        width: 100%;
    }
    
    .control-group {
        min-width: 100%;
    }
    
    .availability-summary-section {
        width: 100%;
        justify-content: flex-start;
    }
    
    .availability-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 10px;
    }
}

@media (max-width: 480px) {
    .availability-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 10px;
    }
    
    .device-icon-wrapper img {
        width: 40px;
        height: 40px;
    }
    
    .device-name {
        font-size: 12px;
    }
}
</style>
@endsection



