<!DOCTYPE HTML>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $pagetitle }}</title>
    <base href="{{ ObzoraConfig::get('base_url') }}">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(!ObzoraConfig::get('favicon', false))
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" href="{{ asset('images/favicon-32x32.png') }}" sizes="32x32">
        <link rel="icon" type="image/png" href="{{ asset('images/favicon-16x16.png') }}" sizes="16x16">
        <link rel="mask-icon" href="{{ asset('images/safari-pinned-tab.svg') }}" color="#5bbad5">
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    @else
        <link rel="shortcut icon" href="{{ ObzoraConfig::get('favicon') }}">
    @endif

    <link rel="manifest" href="{{ asset('images/manifest.json') }}" crossorigin="use-credentials">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-config" content="{{ asset('images/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">

    @vite(['resources/js/app.js'])
    <!-- Flatpickr - Modern DateTime Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="{{ asset('css/bootstrap-switch.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery.bootgrid.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tagmanager.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mktree.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vis.min.css') }}" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('css/jquery.gridster.min.css?ver=09292021') }}" rel="stylesheet">
    <link href="{{ asset('css/leaflet.css') }}" rel="stylesheet">
    <link href="{{ asset('css/MarkerCluster.css') }}" rel="stylesheet">
    <link href="{{ asset('css/MarkerCluster.Default.css') }}" rel="stylesheet">
    <link href="{{ asset('css/L.Control.Locate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/leaflet.awesome-markers.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/query-builder.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset(ObzoraConfig::get('stylesheet', 'css/styles.css')) }}?ver=25052501" rel="stylesheet">
    <link href="{{ asset('css/tw_dark.css?ver=03052025') }}" rel="stylesheet">
    @if(!in_array(session('applied_site_style', 'light'), ['light', 'dark']))
    <link href="{{ asset('css/' . session('applied_site_style') . '.css?ver=732417643') }}" rel="stylesheet">
    @endif
    @foreach(ObzoraConfig::get('webui.custom_css', []) as $custom_css)
        <link href="{{ $custom_css }}" rel="stylesheet">
    @endforeach
    @yield('css')
    @stack('styles')

    <script src="{{ asset('js/polyfill.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js?ver=05072021') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js?ver=05072021') }}"></script>
    <script src="{{ asset('js/bootstrap-hover-dropdown.min.js?ver=05072021') }}"></script>
    <script src="{{ asset('js/bootstrap-switch.min.js?ver=05072021') }}"></script>
    <script src="{{ asset('js/hogan-2.0.0.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <!-- Bootstrap DateTimePicker - Required for alert-schedule -->
    <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js?ver=05072021') }}"></script>
    <!-- Flatpickr - Modern DateTime Picker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('js/typeahead.bundle.min.js?ver=05072021') }}"></script>
    <script src="{{ asset('js/tagmanager.js?ver=05072021') }}"></script>
    <script src="{{ asset('js/mktree.js') }}"></script>
    <script src="{{ asset('js/jquery.bootgrid.min.js') }}"></script>
    <script src="{{ asset('js/handlebars.min.js') }}"></script>
    <script data-pace-options='{ "eventLag": { "lagThreshold": 30 } }' src="{{ asset('js/pace.min.js') }}"></script>
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var ajax_url = "{{ url('/ajax') }}";
    </script>
    <script src="{{ asset('js/obzora.js?ver=30062025') }}"></script>
    <script type="text/javascript" src="{{ asset('js/overlib_mini.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/toastr.min.js?ver=05072021') }}"></script>
    <script type="text/javascript" src="{{ asset('js/boot.js?ver=10272021') }}"></script>
    <script>
        window.siteStyle = '{{ session('applied_site_style') }}';
        window.siteStylePreference = '{{ session('preferences.site_style') ?? session('applied_site_style', 'device') }}';

        // Apply color scheme
        applySiteStyle(window.siteStylePreference);

        // Listen for system theme changes in device mode
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
            if (window.siteStylePreference === 'device') {
                applySiteStyle(event.matches ? 'dark' : 'light');
            }
        });
    </script>
    @auth
        @if(session('preferences.timezone_static') == null || ! session('preferences.timezone_static'))
        <script>
            var tz = window.Intl.DateTimeFormat().resolvedOptions().timeZone;
            if(tz !== '{{ session('preferences.timezone') }}') {
                updateTimezone(tz, false);
            }
        </script>
        @endif
        <script src="{{ asset('js/register-service-worker.js') }}" defer></script>
    @endauth
    @yield('javascript')
    <style>
        /* Global 90% zoom for entire project */
        html {
            zoom: 0.9;
            -ms-zoom: 0.9;
        }
        
        /* For Firefox */
        @-moz-document url-prefix() {
            html {
                zoom: 0.9;
            }
        }
    </style>
</head>
<body>
@if(Auth::check())
    <script>
        // only update resolution if it doesn't match what is stored in the session
        if (document.documentElement.clientWidth !== {{ (int) session('screen_width') }} || document.documentElement.clientHeight !== {{ (int) session('screen_height') }}) {
            updateResolution(false);
        }
    </script>
@endif

@php
    \App\Facades\ObzoraConfig::invalidateAndReload(); // clear cache and reload fresh instance
    $lastCheck = (int) \App\Facades\ObzoraConfig::get('system_last_check');
    $old = strtotime('-30 days');
    $tooOld = $lastCheck < $old;
@endphp


@if($tooOld && request()->path() !== 'license-expired')
    <script>
        window.location.href = "{{ url('/license-expired.html') }}";
    </script>
@endif


@if(Request::get('bare') != 'yes' && $systemStatus === 'warning')
<div id="obzora-warning-banner"
     style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 9999;
        background-color: #FFB347;
        color: #2F2F2F;
        padding: 6px 12px;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        border-bottom: 2px solid #FFA500;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        line-height: 1.2;
    ">
    Your <strong>Obzora NMS is not licensed.</strong> Please contact
    <a href="mailto:info@obzora.net" style="color: #2F2F2F; text-decoration: underline;">info@obzora.net</a>
    to prevent service loss.
</div>




    {{-- Add top padding to avoid overlap with fixed banner --}}
    <style>
        body {
            padding-top: 30px !important;
            padding-bottom: 30px !important;
        }
        .blinker {
            animation: blinker 1s linear infinite;
        }
        @keyframes blinker {
            50% { opacity: 0; }
        }
    </style>
@endif


@if(Request::get('bare') == 'yes')
    <style>body { padding-top: 0 !important; padding-bottom: 0 !important; }</style>
@elseif($show_menu)
    @include('layouts.menu')
@endif

@yield('content')

@yield('scripts')

<x-toast />

@stack('scripts')
</body>
</html>
