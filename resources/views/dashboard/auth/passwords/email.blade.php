<!DOCTYPE html>
<html>
<head>
    <title>{{config('app.name')}} - @yield('title','لوحة التحكم')</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="template language" name="keywords">
    <meta content="Tamerlan Soziev" name="author">
    <meta content="Admin dashboard html template" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="{{asset('/panel/favicon.png')}}" rel="shortcut icon">
    <link href="{{asset('/panel/apple-touch-icon.png')}}" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500" rel="stylesheet" type="text/css">
    <link href="{{asset('/panel/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('/panel/bower_components/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
    <link href="{{asset('/panel/bower_components/dropzone/dist/dropzone.css')}}" rel="stylesheet">
    <link href="{{asset('/panel/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/panel/bower_components/fullcalendar/dist/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('/panel/bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css')}}" rel="stylesheet">
    <link href="{{asset('/panel/bower_components/slick-carousel/slick/slick.css')}}" rel="stylesheet">
    <link href="{{asset('/panel/css/main.css?version=4.4.0')}}" rel="stylesheet">
</head>
<body class="auth-wrapper">
<div class="all-wrapper menu-side with-pattern">
    <div class="auth-box-w">
        <div class="logo-w">
            <a href="{{route('admin.home')}}"><img alt="" src="{{asset('/panel/img/logo-big.png')}}"></a>
        </div>
        <h4 class="auth-header">
            إستعادة كلمة المرور
        </h4>
        <form method="POST" action="{{route('admin.password.email')}}">
            @csrf
            <div class="alert alert-info alert-dismissable">
                قم بادخال <b>بريدك الالكترونى</b> ليتم ارسال رابط التفعيل اليك!
            </div>
            <div class="form-group">
                <label for="">البريد الإلكترونى</label><input name="email" class="form-control" value="{{ old('email') }}" type="email">
                <div class="pre-icon os-icon os-icon-user-male-circle"></div>
            </div>
            <div class="buttons-w">
                <button class="btn btn-primary submit">إرسال</button>
            </div>
        </form>
    </div>
</div>
<script src="{{asset('panel/bower_components/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset('panel/bower_components/notify/notify.js')}}"></script>
@if($errors->has('email'))
    <script type="text/javascript">
        $(document).ready(function () {
            $(".submit").notify(
                'هذا البريد غير مسجل من قبل',
                'error',
                { position:"right" }
            );
        })
    </script>
@endif
</body>
</html>
