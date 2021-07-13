<html>
<head>
    <title>{{config('app.name')}} - @yield('title','لوحة التحكم')</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta content="template language" name="keywords">
    <meta content="https://sa3d01.com" name="author">
    <meta content="Admin dashboard html template" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="{{asset('panel/favicon.png')}}" rel="shortcut icon">
    <link href="{{asset('panel/apple-touch-icon.png')}}" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500" rel="stylesheet" type="text/css">
    {{--    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">--}}
    {{--    <link rel="stylesheet" media="screen" href="https://fontlibrary.org/face/droid-arabic-kufi" type="text/css"/>--}}
    <link href="{{asset('panel/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('panel/bower_components/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
    <link href="{{asset('panel/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('panel/bower_components/fullcalendar/dist/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('panel/bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css')}}" rel="stylesheet">
    <link href="{{asset('panel/bower_components/slick-carousel/slick/slick.css')}}" rel="stylesheet">
    <link href="{{asset('panel/css/main.css?version=4.4.0')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    @yield('style')
    <style>
        body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .breadcrumb li, .btn, .all-wrapper .fc-button{
            font-family: Cairo;
        }
        .btn, .all-wrapper .fc-button {
            font-family: Cairo;
            font-weight: 400;
            outline: none;
            line-height: 3;
            color: #fff;
            padding: 0 2em;
        }
    </style>
</head>
<body class="menu-position-top full-screen">
<div class="all-wrapper solid-bg-all">
    <div class="layout-w">
        <div class="menu-mobile menu-activated-on-click color-scheme-dark">
            <div class="mm-logo-buttons-w">
                <a class="mm-logo" href="{{route('admin.home')}}"><img src="{{asset('media/images/logo.jpeg')}}"><span>لوحة التحكم</span></a>
                <div class="mm-buttons">
                    <div class="content-panel-open">
                        <div class="os-icon os-icon-grid-circles"></div>
                    </div>
                    <div class="mobile-menu-trigger">
                        <div class="os-icon os-icon-hamburger-menu-1"></div>
                    </div>
                </div>
            </div>
            <div class="menu-and-user">
                <div class="logged-user-w">
                    <div class="avatar-w">
                        <img alt="" src="{{Auth::user()->image}}">
                    </div>
                    <div class="logged-user-info-w">
                        <div class="logged-user-name">
                            {{Auth::user()->name}}
                        </div>
                        <div class="logged-user-role">
                            {{auth()->user()->getRoleArabicName()}}
                        </div>
                    </div>
                </div>
                @include('dashboard.master.mobile-main-menu')
            </div>
        </div>
        <!--------------------
        END - Mobile Menu
        --------------------><!--------------------
            START - Main Menu
            -------------------->
{{--        style="background-image: linear-gradient(to bottom, #ffe187 0%, #fbe4a0 100%)"--}}
        <div class="menu-w color-scheme-dark color-style-bright menu-position-top menu-layout-compact sub-menu-style-over sub-menu-color-bright selected-menu-color-light menu-activated-on-hover menu-has-selected-link">
            <div class="logo-w">
                <a class="logo" href="{{route('admin.home')}}">
                    <div><img alt="" src="{{asset('media/images/logo.jpeg')}}"></div>
                </a>
            </div>
            <div class="logged-user-w avatar-inline">
                <div class="logged-user-i">
                    <div class="avatar-w">
                        <img alt="" src="{{Auth::user()->image}}">
                    </div>
                    <div class="logged-user-info-w">
                        <div class="logged-user-name">
                            {{auth()->user()->name}}
                        </div>
                        <div class="logged-user-role">
                            {{auth()->user()->getRoleArabicName()}}
                        </div>
                    </div>
                    <div class="logged-user-toggler-arrow">
                        <div class="os-icon os-icon-chevron-down"></div>
                    </div>
                    <div class="logged-user-menu color-style-bright">
                        <div class="logged-user-avatar-info">
                            <div class="avatar-w">
                                <img alt="" src="{{Auth::user()->image}}">
                            </div>
                            <div class="logged-user-info-w">
                                <div class="logged-user-name">
                                    {{auth()->user()->name}}
                                </div>
                                <div class="logged-user-role">
                                    {{auth()->user()->getRoleArabicName()}}
                                </div>
                            </div>
                        </div>
                        <div class="bg-icon">
                            <i class="os-icon os-icon-wallet-loaded"></i>
                        </div>
                        <ul>
                            <li>
                                <a href="{{route('admin.profile')}}"><i class="os-icon os-icon-user-male-circle2"></i><span>الملف الشخصى</span></a>
                            </li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <a href="javascript:;" onclick="parentNode.submit();">
                                        <i type="submit" class="os-icon os-icon-signs-11"></i><span>تسجيل خروج</span>
                                    </a>
                                </form>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
            @include('dashboard.master.notification')
            @include('dashboard.master.main-menu')
        </div>
        <div class="content-w">
            @if(isset($title))
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.home')}}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{$title}}
                    </li>
                </ul>
            @endif
            @yield('content')
        </div>
    </div>
    @include('dashboard.master.custom-buttons')
    <div class="display-type"></div>
</div>
<!--copyRight-->
<div class="text-center">
    <p class="m-0 py-3 text-white">
        حقوق ملكية محفوظة @
    </p>
</div>
{{--script--}}

<script src="{{asset('panel/bower_components/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset('panel/bower_components/notify/notify.js')}}"></script>
<script src="{{asset('panel/bower_components/popper.js/dist/umd/popper.min.js')}}"></script>
<script src="{{asset('panel/bower_components/moment/moment.js')}}"></script>
<script src="{{asset('panel/bower_components/chart.js/dist/Chart.min.js')}}"></script>
<script src="{{asset('panel/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="{{asset('panel/bower_components/jquery-bar-rating/dist/jquery.barrating.min.js')}}"></script>
<script src="{{asset('panel/bower_components/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap-validator/dist/validator.min.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('panel/bower_components/ion.rangeSlider/js/ion.rangeSlider.min.js')}}"></script>
<script src="{{asset('panel/bower_components/editable-table/mindmup-editabletable.js')}}"></script>
<script src="{{asset('panel/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('panel/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('panel/bower_components/fullcalendar/dist/fullcalendar.min.js')}}"></script>
<script src="{{asset('panel/bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js')}}"></script>
<script src="{{asset('panel/bower_components/tether/dist/js/tether.min.js')}}"></script>
<script src="{{asset('panel/bower_components/slick-carousel/slick/slick.min.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/util.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/alert.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/button.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/carousel.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/collapse.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/dropdown.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/modal.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/tab.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/tooltip.js')}}"></script>
<script src="{{asset('panel/bower_components/bootstrap/js/dist/popover.js')}}"></script>
<script src="{{asset('panel/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('panel/js/demo_customizer.js?version=4.4.0')}}"></script>
<script src="{{asset('panel/js/main.js?version=4.4.0')}}"></script>
<script>
    // message-input
    $.ajaxSetup({
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#message-input").keypress(function(e){
        if (e.which == 13) {
            // e.preventDefault();
            let note = $("#message-input").val();
            let receiver_id = $(this).data("receiver");
            $.ajax({
                dataType: 'json',
                type: 'GET',
                url: "/e-auction/public/admin/send_single_notify/"+receiver_id+"/"+note,
                success: function(res) {
                    console.log(res)
                },error:function (msg){
                    console.log(msg.responseJSON['message'])
                }
            })
        }
    });
</script>
@include('dashboard.master.alerts')
@yield('script')
</body>
</html>
