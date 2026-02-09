@extends('layouts.obzorav1')

@section('content')
<div style="display:flex; height:100vh; width:100%; overflow:hidden;">



<!-- LEFT BANNER -->
<div style="
    flex:1;
    background-color:#2F2F2F;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    color:white;
    padding:40px;
">

    <!-- Centered WELCOME CONTENT -->
    <div style="
        display:flex;
        flex-direction:column;
        align-items:center;
        text-align:center;
        flex:1;
        justify-content:center;
    ">
        <h1 style="font-size:36px; font-weight:600; margin-bottom:10px; color:white;">
            Welcome to Obzora NMS
        </h1>

        <p style="font-size:16px; opacity:0.9; color:#ffd369;">
            Monitor. Visualize. All in one place.
        </p>
    </div>

    <!-- FOOTER SECTION -->
    <div style="
        padding:20px 0;
        text-align:center;
        border-top:1px solid rgba(255,255,255,0.2);
        margin-top:0;
    ">
        <p style="font-size:16px; opacity:0.9; color:white; font-style:italic; margin:0;">
            For more details and enquiries, please visit our website or email us.
        </p>

        <p style="font-size:16px; opacity:0.9; font-style:italic; color:#ffd369; margin:8px 0 0 0;">
            website :
            <a href="https://www.obzora.net" style="color:white; text-decoration:underline;">
                www.obzora.net
            </a>
            &nbsp;|&nbsp;
            email :
            <a href="mailto:info@obzora.net" style="color:white; text-decoration:underline;">
                info@obzora.net
            </a>
        </p>
    </div>

</div>


<!-- RIGHT LOGIN FORM (UNCHANGED) -->
<div style="
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#ffffff;
    padding:40px;
">
    <div style="width:100%; max-width:380px; text-align:center;">

        <!-- Logo Here -->
        <div style="
            background:#ffffff;
            padding:16px 20px;
            border-radius:12px;
            display:flex;
            justify-content:center;
            margin-bottom:24px;
        ">
            <img src="{{ asset('images/obzora_logo_light.svg') }}"
                 alt="ObzoraNMS Logo"
                 style="height:60px; width:auto;">
        </div>
        <!-- END LOGO -->

        {{-- LOGIN FORM ONLY --}}
        @include('auth.login-form')

        @if($errors->any())
            <script>toastr.error('{{ $errors->first() }}')</script>
        @endif

    </div>
</div>

</div>
@endsection
