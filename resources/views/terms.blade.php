<!DOCTYPE html>
<html lang="ar">

<head>
    <!-- Meta-->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>E-auction</title>
    <link rel="shortcut icon" type="image/ico" href="{{asset('images/favIco.ico')}}" />
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/bootstrap-rtl.min.css')}}">
    <link rel="stylesheet" href="{{asset('fonts/fontawesome/css/all.css')}}">
    <link rel="stylesheet" href='{{asset('css/animate.css')}}'>
    <link rel="stylesheet" href='{{asset('css/owl.theme.default.min.css')}}'>
    <link rel="stylesheet" href='{{asset('css/owl.carousel.min.css')}}'>
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
</head>

<body>
    <header>
        <!-- navbar -->
        <nav class="navbar pt-3 navbar-expand-lg ">
            <div class="container ">
                <a href="{{route('home')}}">
                    <img src="{{asset('images/logo.png')}}" class="logo" alt="logo">
                </a>
                <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item mx-3 active">
                            <a class="nav-link" href="{{route('home','why')}}">
                                مميزاتنا
                            </a>
                        </li>
                        <li class="nav-item mx-3">
                            <a class="nav-link" href="{{route('home','buy')}}">
                                خدماتنا
                            </a>
                        </li>
                        <li class="nav-item mx-3 ">
                            <a class="nav-link" href="{{route('home','how')}}">
                                كيف ازايد
                            </a>
                        </li>
                        <li class="nav-item mx-3 ">
                            <a class="nav-link" href="{{route('home','download')}}">
                                حمل التطبيق
                            </a>
                        </li>
                        <li class="nav-item mx-3 ">
                            <a class="nav-link " href="{{route('home','contact')}}">
                                تواصل معنا
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    </header>
    <!--  -->
    <div class="container py-5">

        <h1 class="title w-700 py-3 my-3 p-relative">
            الشروط و الأحكام
        </h1>
        <div class="">
            <ul class="m-0 p-0 pt-4" id="terms">
                {{\App\Setting::value('licence')['ar']}}
            </ul>
        </div>
    </div>
    <!-- footer -->
    <footer>
        <div class="container text-white  py-5 text-center mt-5" id="contact">
            <div>
                <h3>
                    عندك أسئلة مالقيت لها جواب؟ شيك على الأسئلة الأكثر شيوعا و في حال مالقيت الجواب الفريق بالكامل بخدمتك
                </h3>
            </div>
            <div class="py-4">
                <div class="d-inline-block">
                    <img src="{{asset('images/icons/phone-call.png')}}" class="img-fluid my-2">
                    <p class="w-700 d-inline-block px-3">
                        {{\App\Setting::value('contacts')['mobile']}}
                    </p>
                </div>
                <div class="d-inline-block">
                    <img src="{{asset('images/icons/email.png')}}" class="img-fluid my-2">
                    <p class="w-700 d-inline-block px-3">
                        {{\App\Setting::value('contacts')['email']}}
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 my-3">
                    <img src="{{asset('images/icons/logo.png')}}" class="img-fluid ">
                </div>
                <div class="col-md-4 my-auto  my-3">
                    <ul class="m-0 p-0">
                        <li class="d-inline-block mx-2 my-3">
                            <a href="{{route('policy')}}" class="default-color w-700 ">
                                سياسة الخصوصية
                            </a>
                        </li>
                        <li class="d-inline-block mx-2 my-3">
                            <a href="{{route('terms')}}" class="default-color w-700 ">
                                الشروط و الأحكام
                            </a>
                        </li>
                        <li class="d-inline-block mx-2 my-3">
                            <a href="{{route('questions')}}" class="default-color w-700 ">
                                الأسئلة الشائعة
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 my-auto  my-3">
                    @php($socials=\App\Setting::value('socials'))
                    <ul class="m-0 p-0">
                        <li class="d-inline-block mx-2">
                            <a href="{{$socials['facebook']}}">
                                <img src="{{asset('images/icons/facebook.png')}}" class="img-fluid ">
                            </a>
                        </li>
                        <li class="d-inline-block mx-2">
                            <a href="{{$socials['snap']}}">
                                <img src="{{asset('images/icons/snapchat.png')}}" class="img-fluid ">
                            </a>
                        </li>
                        <li class="d-inline-block mx-2">
                            <a href="{{$socials['instagram']}}">
                                <img src="{{asset('images/icons/instagram.png')}}" class="img-fluid ">
                            </a>
                        </li>
                        <li class="d-inline-block mx-2">
                            <a href="{{$socials['twitter']}}">
                                <img src="{{asset('images/icons/twitter.png')}}" class="img-fluid ">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <!--scripts -->
    <script type="text/javascript" src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/wow.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/owl.carousel.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/scripts.js')}}"></script>
</body>

</html>
