@extends('layouts.obzorav1')

@section('title', __('Overview'))

@section('content')
<div class="container-fluid">
@include('alerts.modals.ack')
@include('alerts.modals.notes')
@if (!$bare)
<div class="row collapse @if(!$hide_dashboard_editor)in @endif" id="dashboard-editor">
    <div class="col-md-12">
        <div class="btn-group btn-lg">
            <button class="btn btn-default disabled" style="min-width:160px;"><span class="pull-left">Dashboards</span></button>
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width:160px;">
                    <span class="pull-left">{{ $dashboard->user_id != Auth::id() ? ($dashboard->user->username ?? __('Deleted User')) . ':' : null}} {{ $dashboard->dashboard_name }}</span>
                <span class="pull-right">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
                </span>
                </button>
                <ul class="dropdown-menu">
                    @forelse ($user_dashboards as $dash)
                        @if($dash->dashboard_id != $dashboard->dashboard_id)
                        <li>
                            <a href="{{ route('dashboard.show', $dash->dashboard_id) }}">{{ $dash->dashboard_name }}</a>
                        </li>
                        @endif
                    @empty
                        <li><a>No other Dashboards</a></li>
                    @endforelse

                    @isset($shared_dashboards)
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Shared Dashboards</li>
                        @foreach ($shared_dashboards as $dash)
                            @if($dash->dashboard_id != $dashboard->dashboard_id)
                            <li>
                                <a href="{{ route('dashboard.show', $dash->dashboard_id) }}">
                                {{ ($dash->user->username ?? __('Deleted User')) . ':' . $dash->dashboard_name . ($dash->access == 1 ? ' (Read)' : '') }}</a>
                            </li>
                            @endif
                        @endforeach
                    @endisset
                </ul>
            </div>
            <button class="btn btn-default edit-dash-btn" href="#edit_dash" onclick="dashboard_collapse($(this).attr('href'))" data-toggle="tooltip" data-container="body" data-placement="top" title="Edit Dashboard"><i class="fa-solid fa-pen-to-square fa-fw"></i></button>
            <button class="btn btn-danger" href="#del_dash" onclick="dashboard_collapse($(this).attr('href'))" data-toggle="tooltip" data-container="body" data-placement="top" title="Remove Dashboard"><i class="fa-solid fa-trash fa-fw"></i></button>
            <button class="btn btn-success" href="#add_dash" onclick="dashboard_collapse($(this).attr('href'))" data-toggle="tooltip" data-container="body" data-placement="top" title="New Dashboard"><i class="fa-solid fa-plus fa-fw"></i></button>
        </div>
        <div class="dash-collapse" id="add_dash" style="display: none;" >
            <div class="row" style="margin-top:5px;">
                <div class="col-md-6">
                    <form class="form-inline" onsubmit="dashboard_add(this); return false;" name="add_form" id="add_form">
                        @csrf
                        <div class="col-sm-3 col-sx-6">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <a class="btn btn-default disabled" type="button" style="min-width:160px;"><span class="pull-left">New Dashboard</span></a>
                                </span>
                                <input class="form-control" type="text" placeholder="Name" name="dashboard_name" id="dashboard_name" style="min-width:160px;">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">Add</button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
        </div>
        <div class="dash-collapse" id="edit_dash" style="display: none;">
            <!-- Start Dashboard-Settings -->
            <div class="row" style="margin-top:5px;">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <form class="form-inline" onsubmit="dashboard_edit(this); return false;">
                            @csrf
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <a class="btn btn-default disabled" type="button" style="min-width:160px;"><span class="pull-left">Dashboard Name</span></a>
                                    </span>
                                    <input class="form-control" type="text" placeholder="Dashbord Name" name="dashboard_name" value="{{ $dashboard->dashboard_name }}" style="width:160px;">
                                    <select class="form-control" name="access" style="width:160px;">
                                    @foreach (array('Private','Shared (Read)','Shared (Admin RW)','Shared') as $k => $v)
                                        <option value="{{ $k }}" {{ $dashboard->access == $k ? 'selected' : null }}>{{ $v }}</option>
                                    @endforeach
                                    </select>
                                    <span class="input-group-btn pull-left">
                                        <button class="btn btn-primary" type="submit">Update</button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if (count($user_list) and auth()->user()->isAdmin())
                    <div class="btn-group btn-lg" style="margin-top:5px;position:absolute;right:0px;">
                        <div class="btn-group">
                        <select class="form-control" id="dashboard_copy_target" name="dashboard_copy_target" onchange="dashboard_copy_user_select()">
                            <option value="-1" selected> Copy Dashboard to </option>
                        @foreach ($user_list as $user_id => $username)
                            <option value="{{ $user_id }}">{{ $username }}</option>
                        @endforeach
                        </select>
                        </div>
                        <button disabled id="do_copy_dashboard" class="btn btn-primary" onclick="dashboard_copy(this)" data-toggle="tooltip" data-container="body" data-placement="top" title="Copy Dashboard"><i class="fa-solid fa-copy fa-fw"></i></button>
                    </div>
                    @endif
                </div>
            </div>
            <!-- End Dashboard-Settings -->
            <!-- Start Widget-Select -->
            <div class="row" style="margin-top:5px;">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="btn-group" role="group">
                            <a class="btn btn-default disabled" role="button" style="min-width:160px;"><span class="pull-left">Add Widgets</span></a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width:160px;"><span class="pull-left">Select Widget</span>
                                <span class="pull-right">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </span>
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach ($widgets as $type => $title)
                                    <li>
                                        <a href="#" onsubmit="return false;" class="place_widget" data-widget_type="{{ $type }}">{{ $title }}</a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Widget-Select -->
            <hr>
        </div>
        <div class="dash-collapse" id="del_dash" style="display: none;">
            <div class="row" style="margin-top:5px;">
                <div class="col-md-6">
                    <div class="col-md-6">
                        <button class="btn btn-danger" type="button" id="clear_widgets" name="clear_widgets" style="min-width:160px;"><span class="pull-left">Remove</span><strong class="pull-right">Widgets</strong></button>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:5px;">
                <div class="col-md-6">
                    <div class="col-md-6">
                        <button class="btn btn-danger" type="button" onclick="dashboard_delete(this); return false;" data-dashboard="{{ $dashboard->dashboard_id }}" style="min-width:160px;"><span class="pull-left">Delete</span><strong class="pull-right">Dashboard</strong></button>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </div>
</div>
@endif
<span class="message" id="message"></span>
<div class="dashboard-controls" style="margin-bottom: 10px; display: flex; justify-content: flex-end; align-items: center;">
    <button id="dashboard-fullscreen-btn" class="btn btn-default fullscreen-btn" data-toggle="tooltip" data-placement="left" title="{{ __('Toggle Fullscreen') }}">
        <i class="fa-solid fa-expand" aria-hidden="true"></i>
    </button>
</div>
<div class="gridster grid" id="dashboard-gridster">
    <ul></ul>
</div>
</div>
@endsection

@section('styles')
<style>
.dashboard-controls {
    padding: 0 15px;
}

.fullscreen-btn {
    border-radius: 4px;
    padding: 8px 12px;
    transition: all 0.2s ease;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    color: #2c539e;
}

.fullscreen-btn:hover {
    background: #2c539e;
    color: #ffffff;
    border-color: #2c539e;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(44, 83, 158, 0.2);
}

.fullscreen-btn i {
    font-size: 14px;
}

.widget-fullscreen-btn {
    cursor: pointer;
    color: #2c539e;
    transition: color 0.2s ease;
    padding: 2px 4px;
}

.widget-fullscreen-btn:hover {
    color: #1e3a6f;
}

.widget-fullscreen-active {
    background: #ffffff !important;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) !important;
    filter: brightness(1.15) contrast(1.05) !important;
    -webkit-filter: brightness(1.15) contrast(1.05) !important;
}

.widget-fullscreen-active header {
    background: #ffffff !important;
    border-bottom: 2px solid #2c539e;
    padding: 10px 15px;
    filter: brightness(1.1) !important;
    -webkit-filter: brightness(1.1) !important;
}

.widget-fullscreen-active .widget_body {
    padding: 15px 20px;
    overflow-y: auto;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
    max-height: calc(100vh - 60px);
    height: calc(100vh - 60px);
    box-sizing: border-box;
    background: #ffffff !important;
    filter: brightness(1.1) !important;
    -webkit-filter: brightness(1.1) !important;
}

.widget-fullscreen-active {
    overflow: hidden;
}

.widget-fullscreen-active .widget_body::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.widget-fullscreen-active .widget_body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.widget-fullscreen-active .widget_body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.widget-fullscreen-active .widget_body::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Fullscreen mode adjustments */
:fullscreen .dashboard-controls,
:-webkit-full-screen .dashboard-controls,
:-moz-full-screen .dashboard-controls,
:-ms-fullscreen .dashboard-controls {
    position: fixed;
    top: 10px;
    right: 10px;
    z-index: 10000;
    margin: 0;
}

:fullscreen #dashboard-gridster,
:-webkit-full-screen #dashboard-gridster,
:-moz-full-screen #dashboard-gridster,
:-ms-fullscreen #dashboard-gridster {
    padding: 20px;
    height: 100vh;
    overflow-y: auto;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
    background: #ffffff !important;
    filter: brightness(1.15) contrast(1.05) !important;
    -webkit-filter: brightness(1.15) contrast(1.05) !important;
}

:fullscreen,
:-webkit-full-screen,
:-moz-full-screen,
:-ms-fullscreen {
    background: #ffffff !important;
    filter: brightness(1.15) contrast(1.05) !important;
    -webkit-filter: brightness(1.15) contrast(1.05) !important;
}

/* COMPLETELY REMOVE dark backdrop/overlay in fullscreen - FORCE REMOVAL */
:fullscreen::backdrop,
:-webkit-full-screen::backdrop,
:-moz-full-screen::backdrop,
:-ms-fullscreen::backdrop {
    background: transparent !important;
    background-color: transparent !important;
    opacity: 0 !important;
    display: none !important;
    visibility: hidden !important;
    filter: none !important;
    -webkit-filter: none !important;
    pointer-events: none !important;
    z-index: -9999 !important;
}

/* Also target any potential overlay elements */
:fullscreen *[class*="backdrop"],
:fullscreen *[class*="overlay"],
:fullscreen *[id*="backdrop"],
:fullscreen *[id*="overlay"],
:-webkit-full-screen *[class*="backdrop"],
:-webkit-full-screen *[class*="overlay"],
:-moz-full-screen *[class*="backdrop"],
:-moz-full-screen *[class*="overlay"],
:-ms-fullscreen *[class*="backdrop"],
:-ms-fullscreen *[class*="overlay"] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    background: transparent !important;
}

/* Force white background on all fullscreen elements */
:fullscreen,
:-webkit-full-screen,
:-moz-full-screen,
:-ms-fullscreen {
    background: #ffffff !important;
    background-color: #ffffff !important;
    filter: brightness(1.15) contrast(1.05) !important;
    -webkit-filter: brightness(1.15) contrast(1.05) !important;
}

/* Force white on html and body in fullscreen */
:fullscreen html,
:fullscreen body,
:-webkit-full-screen html,
:-webkit-full-screen body,
:-moz-full-screen html,
:-moz-full-screen body,
:-ms-fullscreen html,
:-ms-fullscreen body {
    background: #ffffff !important;
    background-color: #ffffff !important;
}

/* Remove any dark overlays or pseudo-elements */
:fullscreen *::before,
:fullscreen *::after,
:-webkit-full-screen *::before,
:-webkit-full-screen *::after,
:-moz-full-screen *::before,
:-moz-full-screen *::after,
:-ms-fullscreen *::before,
:-ms-fullscreen *::after {
    background: transparent !important;
    box-shadow: none !important;
}

:fullscreen #dashboard-gridster::-webkit-scrollbar,
:-webkit-full-screen #dashboard-gridster::-webkit-scrollbar,
:-moz-full-screen #dashboard-gridster::-webkit-scrollbar,
:-ms-fullscreen #dashboard-gridster::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

:fullscreen #dashboard-gridster::-webkit-scrollbar-track,
:-webkit-full-screen #dashboard-gridster::-webkit-scrollbar-track,
:-moz-full-screen #dashboard-gridster::-webkit-scrollbar-track,
:-ms-fullscreen #dashboard-gridster::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 5px;
}

:fullscreen #dashboard-gridster::-webkit-scrollbar-thumb,
:-webkit-full-screen #dashboard-gridster::-webkit-scrollbar-thumb,
:-moz-full-screen #dashboard-gridster::-webkit-scrollbar-thumb,
:-ms-fullscreen #dashboard-gridster::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 5px;
}

:fullscreen #dashboard-gridster::-webkit-scrollbar-thumb:hover,
:-webkit-full-screen #dashboard-gridster::-webkit-scrollbar-thumb:hover,
:-moz-full-screen #dashboard-gridster::-webkit-scrollbar-thumb:hover,
:-ms-fullscreen #dashboard-gridster::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Prevent body scroll when in fullscreen and ensure bright background */
:fullscreen body,
:-webkit-full-screen body,
:-moz-full-screen body,
:-ms-fullscreen body {
    overflow: hidden;
    background: #ffffff !important;
}

/* Ensure all elements in fullscreen have bright backgrounds */
:fullscreen .gs_w,
:-webkit-full-screen .gs_w,
:-moz-full-screen .gs_w,
:-ms-fullscreen .gs_w {
    background: #ffffff !important;
}

:fullscreen .widget_header,
:-webkit-full-screen .widget_header,
:-moz-full-screen .widget_header,
:-ms-fullscreen .widget_header {
    background: #ffffff !important;
}

/* Ensure grid widget borders/shadows are visible after fullscreen exit */
#dashboard-gridster .gs-w {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
}

/* Force visibility of grid borders after exiting fullscreen */
body:not(:fullscreen):not(:-webkit-full-screen):not(:-moz-full-screen):not(:-ms-fullscreen) #dashboard-gridster .gs-w {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    visibility: visible !important;
    opacity: 1 !important;
}

@media (max-width: 768px) {
    .dashboard-controls {
        padding: 0 10px;
    }
    
    .fullscreen-btn {
        padding: 6px 10px;
    }
}
</style>
@endsection

@section('javascript')
<script src="{{ asset('js/jquery.gridster.min.js?ver=05072021') }}"></script>
<script src="{{ asset('js/raphael.min.js?ver=05072021') }}"></script>
<script src="{{ asset('js/justgage.min.js?ver=05072021') }}"></script>
@endsection

@push('scripts')
@include('map.custom-js')
<script type="text/javascript">
    var gridster;

    var serialization = @json($dash_config);

    serialization = Gridster.sort_by_row_and_col_asc(serialization);
    var gridster_state = 0;


    @if ($dashboard->dashboard_id > 0)
        var dashboard_id = {{ $dashboard->dashboard_id }};
    @else
        var dashboard_id = 0;
    @endif

    $('[data-toggle="tooltip"]').tooltip();
    dashboard_collapse();
    gridster = $(".gridster ul").gridster({
        widget_base_dimensions: ['auto', 100],
        autogenerate_stylesheet: true,
        widget_margins: [5, 5],
        avoid_overlapped_widgets: true,
        min_cols: 1,
        max_cols: 20,
        max_rows: 200,
        draggable: {
            handle: 'header, span',
            stop: function(e, ui, $widget) {
                updatePos(gridster);
            },
        },
        resize: {
            enabled: true,
            stop: function(e, ui, widget) {
                updatePos(gridster);
                widget_reload(widget.attr('id'), widget.data('type'));
            }
        },
        serialize_params: function(w, wgd) {
            return {
                id: $(w).attr('id'),
                col: wgd.col,
                row: wgd.row,
                size_x: wgd.size_x,
                size_y: wgd.size_y
            };
        }
    }).data('gridster');
    $('.gridster  ul').css({'width': $(window).width()});

    gridster.remove_all_widgets();
    gridster.disable();
    gridster.disable_resize();
    $.each(serialization, function() {
        widget_dom(this);
    });
    $(document).on('click','.edit-dash-btn', function() {
        if (gridster_state == 0) {
            gridster.enable();
            gridster.enable_resize();
            gridster_state = 1;
            $('.fade-edit').fadeIn();
        }
        else {
            gridster.disable();
            gridster.disable_resize();
            gridster_state = 0;
            $('.fade-edit').fadeOut();
        }
    });

    $(document).on('click','#clear_widgets', function() {
        if (dashboard_id > 0) {
            $.ajax({
                type: 'DELETE',
                url: '{{ route('dashboard.widget.clear', '?') }}'.replace('?', dashboard_id),
                dataType: "json",
                success: function (data) {
                    if (data.status == 'ok') {
                        gridster.remove_all_widgets();
                        toastr.success(data.message);
                    }
                    else {
                        toastr.error(data.message);
                    }
                },
                error: function (data) {
                    toastr.error(data.message);
                }
            });
        }
    });

    $('.place_widget').on('click',  function(event, state) {
        var widget_type = $(this).data('widget_type');
        event.preventDefault();
        if (dashboard_id > 0) {
            $.ajax({
                type: 'POST',
                url: '{{ route('dashboard.widget.add', '?') }}'.replace('?', dashboard_id),
                data: {
                    widget_type: widget_type
                },
                dataType: "json",
                success: function (data) {
                    if (data.status === 'ok') {
                        widget_dom(data.extra);
                        updatePos(gridster);
                        toastr.success(data.message);
                    }
                    else {
                        toastr.error(data.message);
                    }
                },
                error: function (data) {
                    toastr.error(data.message);
                }
            });
        }
    });

    $(document).on( "click", ".close-widget", function() {
        var widget_id = $(this).data('widget-id');
        $.ajax({
            type: 'DELETE',
            url: '{{ route('dashboard.widget.remove', '?') }}'.replace('?', widget_id),
            dataType: "json",
            success: function (data) {
                if (data.status == 'ok') {
                    gridster.remove_widget($('#'+widget_id));
                    updatePos(gridster);
                    toastr.success(data.message);
                }
                else {
                    toastr.error(data.message);
                }
            },
            error: function (data) {
                toastr.error(data.message);
            }
        });
    });

    $(document).on("click",".edit-widget",function() {
        obj = $(this).parent().parent().parent();
        if( obj.data('settings') == 1 ) {
            obj.data('settings','0');
        } else {
            obj.data('settings','1');
        }
        widget_reload(obj.attr('id'), obj.data('type'), true);
    });




    function updatePos(gridster) {
        @if ($dashboard->dashboard_id > 0)
            var dashboard_id = {{ $dashboard->dashboard_id }};
        @else
            var dashboard_id = 0;
        @endif

        if (dashboard_id > 0) {
            $.ajax({
                type: 'PUT',
                url: '{{ route('dashboard.widget.update', '?') }}'.replace('?', dashboard_id),
                data: {data: JSON.stringify(gridster.serialize())},
                dataType: "json",
                success: function (data) {
                    if (data.status == 'ok') {
                        toastr.success(data.message);
                    }
                    else {
                        toastr.error(data.message);
                    }
                },
                error: function (data) {
                    toastr.error(data.message);
                }
            });
        }
    }

    function dashboard_collapse(target) {
        if (target !== undefined) {
            $('.dash-collapse:not('+target+')').each(function() {
                $(this).fadeOut(0);
            });
            $(target).fadeToggle(300);
            if (target != "#edit_dash") {
                gridster.disable();
                gridster.disable_resize();
                gridster_state = 0;
                $('.fade-edit').fadeOut();
            }
        } else {
            $('.dash-collapse').fadeOut(0);
        }
    }

    function dashboard_delete(data) {
        $.ajax({
            type: 'DELETE',
            url: '{{ route('dashboard.destroy', '?') }}'.replace('?', $(data).data('dashboard')),
            dataType: "json",
            success: function (data) {
                if( data.status == "ok" ) {
                    toastr.success(data.message);
                    setTimeout(function (){
                        window.location.href = "{{ route('home') }}";
                    }, 500);

                } else {
                    toastr.error(data.message);
                }
            },
            error: function (data) {
                toastr.error(data.message);
            }
        });
    }

    function dashboard_edit(data) {
        @if ($dashboard->dashboard_id > 0)
            var dashboard_id = {{ $dashboard->dashboard_id }};
        @else
            var dashboard_id = 0;
        @endif
        datas = $(data).serializeArray();
        data = [];
        for( var field in datas ) {
            data[datas[field].name] = datas[field].value;
        }
        if (dashboard_id > 0) {
            $.ajax({
                type: 'PUT',
                url: '{{ route('dashboard.update', '?') }}'.replace('?', dashboard_id),
                data: {
                    dashboard_name: data['dashboard_name'],
                    access: data['access']
                },
                dataType: "json",
                success: function (data) {
                    if (data.status == "ok") {
                        toastr.success(data.message);
                        setTimeout(function (){
                            window.location.href = '{{ route('dashboard.show', '?') }}'.replace('?', dashboard_id);
                        }, 500);
                    }
                    else {
                        toastr.error(data.message);
                    }
                },
                error: function(data) {
                    toastr.error(data.message);
                }
            });
        }
    }

    function dashboard_add(data) {
        datas = $(data).serializeArray();
        data = [];
        for( var field in datas ) {
            data[datas[field].name] = datas[field].value;
        }
        $.ajax({
            type: 'POST',
            url: '{{ route('dashboard.store') }}',
            data: {dashboard_name: data['dashboard_name']},
            dataType: "json",
            success: function (data) {
                if( data.status == "ok" ) {
                    toastr.success(data.message);
                    setTimeout(function (){
                        window.location.href = '{{ route('dashboard.show', '?') }}'.replace('?', data.dashboard_id);
                    }, 500);
                }
                else {
                    toastr.error(data.message);
                }
            },
            error: function(data) {
                toastr.error(data.message);
            }
        });
    }

@if (auth()->user()->isAdmin())
    function dashboard_copy_user_select() {
        var button_disabled = true;
        if (document.getElementById("dashboard_copy_target").value > 0) {
            button_disabled = false;
        }
        $("#do_copy_dashboard").prop('disabled', button_disabled);
    }

    function dashboard_copy(data) {
        var target_user_id = document.getElementById("dashboard_copy_target").value;
        var dashboard_id = {{ $dashboard->dashboard_id }};
        var username = $("#dashboard_copy_target option:selected").text().trim();

        if (target_user_id == -1) {
            toastr.warning('No target selected to copy Dashboard to');
        } else {
            if (! confirm("Do you really want to copy this Dashboard to User '" + username + "'?")) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: '{{ route('dashboard.copy', '?') }}'.replace('?', dashboard_id),
                data: {target_user_id: target_user_id},
                dataType: "json",
                success: function (data) {
                    if( data.status == "ok" ) {
                        toastr.success(data.message);
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(data) {
                    toastr.error(data.message);
                }
            });
            $("#dashboard_copy_target option:eq(-1)").prop('selected', true);
            dashboard_copy_user_select();
        }
    }
@endif

    function widget_dom(data) {
        dom = '<li id="'+data.user_widget_id+'" data-type="'+data.widget+'" data-settings="0">'+
              '<header class="widget_header"><span id="widget_title_'+data.user_widget_id+'">'+data.title+
              '</span><span id="widget_title_counter_'+data.user_widget_id+'"></span>'+
              '<span class="fade-edit pull-right">'+
                        /*'<i class="fa-solid fa-expand widget-fullscreen-btn" data-widget-id="'+data.user_widget_id+'" aria-label="Fullscreen">&nbsp;</i>&nbsp;'+
                @if (
                        ($dashboard->access == 1 && Auth::id() === $dashboard->user_id) ||
                        ($dashboard->access == 0 || $dashboard->access >= 2)
                    )*/
                        '<i class="fa-solid fa-pen-to-square edit-widget" data-widget-id="'+data.user_widget_id+'" aria-label="Settings" data-toggle="tooltip" data-placement="top" title="Settings">&nbsp;</i>&nbsp;'+
                @endif
              '<i class="text-danger fa-solid fa-xmark close-widget" data-widget-id="'+data.user_widget_id+'" aria-label="Close" data-toggle="tooltip" data-placement="top" title="Remove">&nbsp;</i>&nbsp;'+
              '</span>'+
              '</header>'+
              '<div class="widget_body" id="widget_body_'+data.user_widget_id+'">'+data.widget+'</div>'+
              '\<script\>var timeout'+data.user_widget_id+' = grab_data('+data.user_widget_id+',\''+data.widget+'\');\<\/script\>'+
              '</li>';

        if (data.hasOwnProperty('col') && data.hasOwnProperty('row')) {
            gridster.add_widget(dom, parseInt(data.size_x), parseInt(data.size_y), parseInt(data.col), parseInt(data.row));
        } else {
            gridster.add_widget(dom, parseInt(data.size_x), parseInt(data.size_y));
        }
        if (gridster_state == 0) {
            $('.fade-edit').fadeOut(0);
        }
        $('[data-toggle="tooltip"]').tooltip();
    }

    function widget_settings(data) {
        var widget_settings = {};
        var widget_id = 0;
        var datas = $(data).serializeArray();
        for( var field in datas ) {
            var name = datas[field].name;
            if (name.substring(name.length - 2, name.length) === '[]') {
                name = name.slice(0, -2);
                if (widget_settings[name]) {
                    widget_settings[name].push(datas[field].value);
                } else {
                    widget_settings[name] = [datas[field].value];
                }
            } else {
                widget_settings[name] = datas[field].value;
            }
        }

        $('.gridster').find('div[id^=widget_body_]').each(function() {
            if(this.contains(data)) {
                widget_id = $(this).parent().attr('id');
                widget_type = $(this).parent().data('type');
                $(this).parent().data('settings', '0');
            }
        });
        if(widget_id > 0 && widget_settings != {}) {
            $.ajax({
                type: 'PUT',
                url: '{{ route('dashboard.widget.settings', '?') }}/'.replace('?', widget_id),
                data: { settings: widget_settings },
                dataType: "json",
                success: function (data) {
                    if( data.status == "ok" ) {
                        widget_reload(widget_id, widget_type, true);
                        toastr.success(data.message);
                    }
                    else {
                        toastr.error(data.message);
                    }
                },
                error: function (data) {
                    toastr.error(data.message);
                }
            });
        }
    return false;
    }

    function widget_reload(id, data_type, forceDomInject = false) {
        const $widget_body = $('#widget_body_' + id);
        const $widget = $widget_body.children().first();

        // skip html reload and sned refresh event instead
        if (!forceDomInject && $widget.data('reload') === false) {
            $widget.trigger('refresh', $widget); // send refresh event
            return; // skip html reload
        }

        $.ajax({
            type: 'POST',
            url: ajax_url + '/dash/' + data_type,
            data: {
                id: id,
                dimensions: {x: $widget_body.width(), y: $widget_body.height()},
                settings: $widget_body.parent().data('settings') == 1 ? 1 : 0
            },
            dataType: 'json',
            success: function (data) {
                if (data.status === 'ok') {
                    $widget.trigger('destroy', $widget); // send destroy event
                    $widget_body.children().unbind().html("").remove(); // clear old contents and unbind events

                    $('#widget_title_' + id).html(data.title);
                    $widget_body.html(data.html);
                    $widget_body.parent().data('settings', data.show_settings).data('refresh', data.settings.refresh);
                } else {
                    $widget_body.html('<div class="alert alert-info">' + data.message + '</div>');
                }
            },
            error: function (data) {
                $widget_body.html('<div class="alert alert-info">' + (data.responseJSON.error || '{{ __('Problem with backend') }}') + '</div>');
            }
        });
    }

    function grab_data(id, data_type) {
        const refresh = $('#widget_body_' + id).parent().data('refresh');
        widget_reload(id, data_type);

        setTimeout(function () {
            grab_data(id, data_type);
        }, (refresh > 0 ? refresh : 60) * 1000);
    }

    // make sure gridster stays disabled when the window is resized
    var resizeTrigger = null;
    addEvent(window, "resize", function(event) {
        // emit resize event, but only once every 100ms
        if (resizeTrigger === null) {
            resizeTrigger = setTimeout(() => {
                resizeTrigger = null;
                $('.widget_body').children().first().trigger('resize');
            }, 100);
        }

        setTimeout(function(){
            if(!gridster_state) {
                gridster.disable();
                gridster.disable_resize();
            }
        }, 100);
    });

    $('#new-widget').popover();

    // Fullscreen functionality
    function isFullscreen() {
        return !!(document.fullscreenElement || document.webkitFullscreenElement || 
                 document.mozFullScreenElement || document.msFullscreenElement);
    }

    function requestFullscreen(element) {
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    }

    function exitFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }

    // Dashboard fullscreen
    var dashboardFullscreen = false;
    $('#dashboard-fullscreen-btn').on('click', function() {
        var gridsterContainer = $('#dashboard-gridster');
        
        if (!dashboardFullscreen) {
            requestFullscreen(gridsterContainer[0]);
            $(this).find('i').removeClass('fa-expand').addClass('fa-compress');
            dashboardFullscreen = true;
            
            // Force remove dark backdrop and ensure bright background
            removeDarkBackdrop();
            setTimeout(function() {
                removeDarkBackdrop();
                
                // Force white backgrounds
                gridsterContainer.css({
                    'overflow-y': 'auto',
                    'overflow-x': 'auto',
                    '-webkit-overflow-scrolling': 'touch',
                    'scroll-behavior': 'smooth',
                    'background': '#ffffff',
                    'background-color': '#ffffff'
                });
                $('body, html').css({
                    'background': '#ffffff',
                    'background-color': '#ffffff'
                });
                
                // Force white on fullscreen element
                var fullscreenEl = document.fullscreenElement || 
                                  document.webkitFullscreenElement ||
                                  document.mozFullScreenElement ||
                                  document.msFullscreenElement;
                if (fullscreenEl) {
                    fullscreenEl.style.background = '#ffffff';
                    fullscreenEl.style.backgroundColor = '#ffffff';
                }
            }, 50);
            
            // Also try multiple times to catch any late-rendered backdrops
            setTimeout(removeDarkBackdrop, 100);
            setTimeout(removeDarkBackdrop, 200);
            setTimeout(removeDarkBackdrop, 300);
        } else {
            exitFullscreen();
            $(this).find('i').removeClass('fa-compress').addClass('fa-expand');
            dashboardFullscreen = false;
        }
    });

    // Function to completely remove dark backdrop/overlay
    function removeDarkBackdrop() {
        setTimeout(function() {
            // Remove backdrop completely
            var styles = document.createElement('style');
            styles.id = 'fullscreen-remove-backdrop';
            styles.textContent = `
                :fullscreen::backdrop,
                :-webkit-full-screen::backdrop,
                :-moz-full-screen::backdrop,
                :-ms-fullscreen::backdrop {
                    background: transparent !important;
                    background-color: transparent !important;
                    opacity: 0 !important;
                    display: none !important;
                    visibility: hidden !important;
                }
                :fullscreen,
                :-webkit-full-screen,
                :-moz-full-screen,
                :-ms-fullscreen {
                    background: #ffffff !important;
                    background-color: #ffffff !important;
                }
                :fullscreen html,
                :fullscreen body,
                :-webkit-full-screen html,
                :-webkit-full-screen body,
                :-moz-full-screen html,
                :-moz-full-screen body,
                :-ms-fullscreen html,
                :-ms-fullscreen body {
                    background: #ffffff !important;
                    background-color: #ffffff !important;
                }
            `;
            if (!document.getElementById('fullscreen-remove-backdrop')) {
                document.head.appendChild(styles);
            }
            
            // Try to find and remove backdrop element directly
            var fullscreenEl = document.fullscreenElement || 
                              document.webkitFullscreenElement ||
                              document.mozFullScreenElement ||
                              document.msFullscreenElement;
            if (fullscreenEl) {
                // Remove any overlay elements
                var overlays = document.querySelectorAll('[class*="overlay"], [class*="backdrop"], [id*="overlay"], [id*="backdrop"]');
                overlays.forEach(function(overlay) {
                    if (overlay.style && (overlay.style.background || overlay.style.backgroundColor)) {
                        var bg = window.getComputedStyle(overlay).background || window.getComputedStyle(overlay).backgroundColor;
                        if (bg && (bg.includes('rgba') || bg.includes('rgb(0') || bg.includes('#000'))) {
                            overlay.style.display = 'none';
                            overlay.style.visibility = 'hidden';
                            overlay.style.opacity = '0';
                        }
                    }
                });
            }
            
            // Force white on all elements
            $('body, html, #dashboard-gridster').css({
                'background': '#ffffff',
                'background-color': '#ffffff'
            });
        }, 10);
    }
    
    // Handle fullscreen change events - remove dark backdrop and restore grid borders
    document.addEventListener('fullscreenchange', function() {
        updateFullscreenButton();
        if (isFullscreen()) {
            removeDarkBackdrop();
            // Also try multiple times to catch any late-rendered backdrops
            setTimeout(removeDarkBackdrop, 50);
            setTimeout(removeDarkBackdrop, 100);
            setTimeout(removeDarkBackdrop, 200);
        } else {
            // Restore grid widget borders after exiting fullscreen
            restoreGridBorders();
        }
    });
    document.addEventListener('webkitfullscreenchange', function() {
        updateFullscreenButton();
        if (isFullscreen()) {
            removeDarkBackdrop();
            setTimeout(removeDarkBackdrop, 50);
            setTimeout(removeDarkBackdrop, 100);
            setTimeout(removeDarkBackdrop, 200);
        } else {
            restoreGridBorders();
        }
    });
    document.addEventListener('mozfullscreenchange', function() {
        updateFullscreenButton();
        if (isFullscreen()) {
            removeDarkBackdrop();
            setTimeout(removeDarkBackdrop, 50);
            setTimeout(removeDarkBackdrop, 100);
            setTimeout(removeDarkBackdrop, 200);
        } else {
            restoreGridBorders();
        }
    });
    document.addEventListener('MSFullscreenChange', function() {
        updateFullscreenButton();
        if (isFullscreen()) {
            removeDarkBackdrop();
            setTimeout(removeDarkBackdrop, 50);
            setTimeout(removeDarkBackdrop, 100);
            setTimeout(removeDarkBackdrop, 200);
        } else {
            restoreGridBorders();
        }
    });
    
    // Function to restore grid widget borders/shadows
    function restoreGridBorders() {
        setTimeout(function() {
            var $gridster = $('#dashboard-gridster');
            var $widgets = $gridster.find('.gs-w');
            
            // Force browser to recalculate and restore widget styles
            $widgets.each(function() {
                var $widget = $(this);
                // Trigger reflow to restore computed styles
                $widget.css('display', 'none');
                $widget.offset(); // Force reflow
                $widget.css('display', '');
            });
            
            // Trigger gridster resize to recalculate dimensions
            if (typeof gridster !== 'undefined' && gridster) {
                try {
                    gridster.resize_widget_dimensions();
                } catch(e) {
                    // Ignore errors
                }
            }
            
            // Force repaint by toggling visibility
            $gridster.hide().show(0);
            
            // Additional attempt: remove any inline styles that might hide borders
            $widgets.css({
                'box-shadow': '',
                'border': '',
                'outline': ''
            });
        }, 50);
        
        // Try again after a longer delay to ensure it works
        setTimeout(function() {
            var $widgets = $('#dashboard-gridster').find('.gs-w');
            $widgets.each(function() {
                var computedStyle = window.getComputedStyle(this);
                var boxShadow = computedStyle.boxShadow;
                var border = computedStyle.border;
                
                // If no shadow or border is visible, restore default
                if (boxShadow === 'none' || boxShadow === '') {
                    $(this).css('box-shadow', '0 2px 4px rgba(0, 0, 0, 0.1)');
                }
            });
        }, 200);
    }

    function updateFullscreenButton() {
        if (!isFullscreen()) {
            $('#dashboard-fullscreen-btn').find('i').removeClass('fa-compress').addClass('fa-expand');
            dashboardFullscreen = false;
            
            // Restore grid widget borders/shadows after exiting fullscreen
            setTimeout(function() {
                // Force re-render of grid widgets to restore borders/shadows
                var $gridster = $('#dashboard-gridster');
                var $widgets = $gridster.find('.gs-w');
                
                // Remove any inline styles that might hide borders
                $widgets.each(function() {
                    var $widget = $(this);
                    // Force browser to recalculate styles
                    $widget.css('display', 'none');
                    $widget.offset(); // Trigger reflow
                    $widget.css('display', '');
                    
                    // Ensure box-shadow is visible (if it was set before)
                    var computedShadow = window.getComputedStyle(this).boxShadow;
                    if (computedShadow === 'none' || !computedShadow) {
                        // Restore default shadow if it was removed
                        $widget.css('box-shadow', '0 2px 4px rgba(0, 0, 0, 0.1)');
                    }
                });
                
                // Also trigger a resize event to force gridster to recalculate
                if (typeof gridster !== 'undefined' && gridster) {
                    gridster.resize_widget_dimensions();
                }
                
                // Force browser repaint
                $gridster.hide().show(0);
            }, 100);
            
            // Exit any widget fullscreen and restore body scroll
            $('.widget-fullscreen-active').each(function() {
                var widgetId = $(this).attr('id');
                if (widgetId && widgetFullscreenData[widgetId]) {
                    var $widget = $('#' + widgetId);
                    var $widgetBody = $('#widget_body_' + widgetId);
                    $widget.removeClass('widget-fullscreen-active');
                    $widget.css(widgetFullscreenData[widgetId].styles);
                    $widgetBody.css({
                        'height': '',
                        'overflow-y': '',
                        'overflow-x': '',
                        '-webkit-overflow-scrolling': '',
                        'scroll-behavior': '',
                        'max-height': ''
                    });
                    $('.widget-fullscreen-btn[data-widget-id="' + widgetId + '"]').removeClass('fa-compress').addClass('fa-expand');
                    delete widgetFullscreenData[widgetId];
                }
            });
            
            // Restore body scroll
            $('body').css('overflow', '');
        }
    }

    // Widget fullscreen
    var widgetFullscreenData = {};
    
    $(document).on('click', '.widget-fullscreen-btn', function() {
        var widgetId = $(this).data('widget-id');
        var $widget = $('#' + widgetId);
        var $widgetBody = $('#widget_body_' + widgetId);
        var isFullscreen = $widget.hasClass('widget-fullscreen-active');
        
        if (!isFullscreen) {
            // Exit any other widget that might be in fullscreen
            $('.widget-fullscreen-active').each(function() {
                var otherWidgetId = $(this).attr('id');
                if (otherWidgetId && widgetFullscreenData[otherWidgetId]) {
                    var $otherWidget = $('#' + otherWidgetId);
                    var $otherWidgetBody = $('#widget_body_' + otherWidgetId);
                    $otherWidget.removeClass('widget-fullscreen-active');
                    $otherWidget.css(widgetFullscreenData[otherWidgetId].styles);
                    $otherWidgetBody.css({
                        'height': '',
                        'overflow-y': '',
                        'overflow-x': '',
                        '-webkit-overflow-scrolling': '',
                        'scroll-behavior': '',
                        'max-height': ''
                    });
                    $('.widget-fullscreen-btn[data-widget-id="' + otherWidgetId + '"]').removeClass('fa-compress').addClass('fa-expand');
                    delete widgetFullscreenData[otherWidgetId];
                }
            });
            
            // Store original styles
            var originalStyles = {
                position: $widget.css('position'),
                zIndex: $widget.css('z-index'),
                top: $widget.css('top'),
                left: $widget.css('left'),
                width: $widget.css('width'),
                height: $widget.css('height'),
                margin: $widget.css('margin')
            };
            
            widgetFullscreenData[widgetId] = {
                styles: originalStyles
            };
            
            // Enter widget fullscreen
            $widget.addClass('widget-fullscreen-active');
            $widget.css({
                'position': 'fixed',
                'top': '0',
                'left': '0',
                'width': '100vw',
                'height': '100vh',
                'z-index': '9999',
                'margin': '0',
                'background': '#ffffff'
            });
            
            $widgetBody.css({
                'height': 'calc(100vh - 60px)',
                'overflow-y': 'auto',
                'overflow-x': 'auto',
                '-webkit-overflow-scrolling': 'touch',
                'scroll-behavior': 'smooth',
                'max-height': 'calc(100vh - 60px)',
                'background': '#ffffff'
            });
            
            // Prevent body scroll when widget is in fullscreen and ensure bright background
            $('body').css({
                'overflow': 'hidden',
                'background': '#ffffff'
            });
            
            $(this).removeClass('fa-expand').addClass('fa-compress');
            
            // Trigger resize event for widgets
            setTimeout(function() {
                $widgetBody.children().first().trigger('resize');
            }, 100);
        } else {
            // Exit widget fullscreen
            if (widgetFullscreenData[widgetId]) {
                $widget.removeClass('widget-fullscreen-active');
                $widget.css(widgetFullscreenData[widgetId].styles);
                $widgetBody.css({
                    'height': '',
                    'overflow-y': '',
                    'overflow-x': '',
                    '-webkit-overflow-scrolling': '',
                    'scroll-behavior': '',
                    'max-height': '',
                    'background': ''
                });
                
                // Restore body scroll and background
                $('body').css({
                    'overflow': '',
                    'background': ''
                });
                
                $(this).removeClass('fa-compress').addClass('fa-expand');
                delete widgetFullscreenData[widgetId];
                
                // Trigger resize event for widgets
                setTimeout(function() {
                    $widgetBody.children().first().trigger('resize');
                }, 100);
            }
        }
    });

    // Close widget fullscreen on escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            var $activeWidget = $('.widget-fullscreen-active');
            if ($activeWidget.length > 0) {
                var activeWidgetId = $activeWidget.attr('id');
                if (activeWidgetId) {
                    $('.widget-fullscreen-btn[data-widget-id="' + activeWidgetId + '"]').trigger('click');
                }
            }
        }
    });

    @if (empty($dashboard->dashboard_id) && $default_dash == 0)
        $('#dashboard_name').val('Default');
        dashboard_add($('#add_form'));
    @endif
</script>
@endpush
