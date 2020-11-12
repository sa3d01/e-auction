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
            تسجيل دخول
        </h4>
        <form method="POST" action="{{route('admin.login.submit')}}">
            @csrf
            <div class="form-group">
                <label for="">البريد الإلكترونى</label><input name="email" class="form-control" placeholder="admin@gmail.com" type="email">
                <div class="pre-icon os-icon os-icon-user-male-circle"></div>
            </div>
            <div class="form-group">
                <label for="">كلمة المرور</label><input name="password" class="form-control" placeholder="123456" type="password">
                <div class="pre-icon os-icon os-icon-fingerprint"></div>
            </div>
            <div class="buttons-w">
                <button class="btn btn-primary submit">دخول</button>
                <div class="form-check-inline">
                    <label class="form-check-label"><input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>تذكرنى</label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12" style="margin-top: 10px">
                    <a href="{{route('admin.password.request')}}" class="text-dark"><i class="fa fa-lock m-r-5"></i>نسيت كلمت المرور ؟</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="{{asset('panel/bower_components/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset('panel/bower_components/notify/notify.js')}}"></script>
@if($errors->any())
    <div style="visibility: hidden" id="errors" data-content="{{$errors->first()}}"></div>
    <script type="text/javascript">
        $(document).ready(function () {
            var errors=$('#errors').attr('data-content');
            $(".submit").notify(
                errors,
                'error',
                { position:"right" }
            );
        })
    </script>
@endif
</body>
</html>
