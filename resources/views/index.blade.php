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
                            <a class="nav-link " href="#why">
                                مميزاتنا
                            </a>
                        </li>
                        <li class="nav-item mx-3">
                            <a class="nav-link " href="#buy">
                                خدماتنا
                            </a>
                        </li>
                        <li class="nav-item mx-3 ">
                            <a class="nav-link " href="#how">
                                كيف ازايد
                            </a>
                        </li>
                        <li class="nav-item mx-3 ">
                            <a class="nav-link " href="#download">
                                حمل التطبيق
                            </a>
                        </li>
                        <li class="nav-item mx-3 ">
                            <a class="nav-link " href="#contact">
                                تواصل معنا
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!--  -->
        <div class="container ltr py-5">
            <div class="row">
                <div class="col-md-6 text-center">
                    <img src="{{asset('images/header.png')}}" class="img-fluid header-img wow fadeInDown">
                </div>
                <div class="col-md-6 rtl m-auto pl-md-5">
                    <h1 class="w-700 pt-md-0 pt-4 title p-relative">
                        تطبيق E-Auction
                    </h1>
                    <p class="py-4 w-700">
                        مزاد علني إلكتروني يهدف لتسهيل عمليات البيع والشراء .لجميع أنواع المركبات
                    </p>
                </div>
            </div>
        </div>
    </header>
    <!-- why e-auction -->
    <div class="container py-5" id="why">
        <div class="row">
            <div class="col-md-6 mx-auto text-center">
                <img src="{{asset('images/why.png')}}" class="img-fluid p-5 wow zoomIn">
            </div>
            <div class="col-md-6 mx-auto my-auto why-img">
                <h1 class="title w-700 py-3 my-3 p-relative">
                    ليش E-Auction
                </h1>
                <p class="w-700 pt-4">
                    خدماتنا متاحة للجميع سواء كنت فرد او صاحب منشأة و عندك مركبات ترغب بعرضها للبيع، منصة E-Auction راح تساعدك بالتالي
                </p>
                <div class=" my-3 d-flex wow fadeIn">
                    <img src="{{asset('images/icons/car.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto mr-3">
                        حفظ المركبات في مستودعات E-Auction بشكل آمن
                    </p>
                </div>
                <div class=" my-3 d-flex wow fadeIn">
                    <img src="{{asset('images/icons/camera.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto mr-3">
                        تجهيز و تصوير المركبات بشكل احترافي
                    </p>
                </div>
                <div class=" my-3 d-flex wow fadeIn">
                    <img src="{{asset('images/icons/dollar.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto mr-3">
                        تخليص إجراءات المبايعة وتحصيل المبالغ بسهولة
                    </p>
                </div>
                <div class=" my-3 d-flex wow fadeIn">
                    <img src="{{asset('images/icons/growth.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto mr-3">
                        المزايدة ومتابعة السوم من خلال التطبيق
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- buy -->
    <div id="buy" class="">
        <div class="container mt-md-5">
            <h1 class="title-center w-700 pb-3 mb-3 p-relative text-center">
                في حال الرغبة بالشراء، راح نوفر لك التالي
            </h1>
            <div class="row">
                <div class="col-md-6 my-3 d-flex wow zoomIn">
                    <img src="{{asset('images/icons/stopwatch.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto d-inline-block px-3 m-0">
                        أوقات محددة لمعاينة المركبات
                    </p>
                </div>
                <div class="col-md-6 my-3 d-flex wow zoomIn">
                    <img src="{{asset('images/icons/clipboard.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto d-inline-block px-3 m-0">
                        تقرير عام عن حالة المركبة
                    </p>
                </div>
                <div class="col-md-6 my-3 d-flex wow zoomIn">
                    <img src="{{asset('images/icons/Group 3872.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto d-inline-block px-3 m-0">
                        آلية مزايده واضحة تحفظ حقوق كل الأطراف
                    </p>
                </div>
                <div class="col-md-6 my-3 d-flex wow zoomIn">
                    <img src="{{asset('images/icons/user.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto d-inline-block px-3 m-0">
                        نقل الملكية والتأمين بشكل ميسر
                    </p>
                </div>
                <div class="col-md-6 my-3 d-flex wow zoomIn">
                    <img src="{{asset('images/icons/Calculator.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto d-inline-block px-3 m-0">
                        حاسبة توضح السعر النهائي قبل تأكيد المزايدة
                    </p>
                </div>
                <div class="col-md-6 my-3 d-flex wow zoomIn">
                    <img src="{{asset('images/icons/delivery.png')}}" class="img-fluid my-2">
                    <p class="w-700 my-auto d-inline-block px-3 m-0">
                        خدمة شحن المركبة عند الطلب
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- how -->
    <div id="how">
        <div class="container text-center mt-5">
            <h1 class="title-center w-700 pb-3 mb-3 p-relative text-center">
                كيف تدخل المزاد
            </h1>
            <p class="my-5 w-700">
                شروطنا سهلة كل الى عليك
            </p>
            <div class="row">
                <div class="col-md-4 my-3 wow zoomIn">
                    <div class="step h-100">
                        <div>
                            <img src="{{asset('images/icons/account.png')}}" class="img-fluid mb-3">
                        </div>
                        <h3 class="m-0">
                            فتح حساب و تعبئة البيانات المطلوبة
                        </h3>
                    </div>
                </div>
                <div class="col-md-4 my-3 wow zoomIn">
                    <div class="step h-100">
                        <div>
                            <img src="{{asset('images/icons/dollar.png')}}" class="img-fluid mb-3">
                        </div>
                        <h3 class="m-0">
                            دفع عربون ابتداءً من ٥٠٠ ريال
                        </h3>
                    </div>
                </div>
                <div class="col-md-4 my-3 wow zoomIn">
                    <div class="step h-100">
                        <div>
                            <img src="{{asset('images/icons/Group.png')}}" class="img-fluid mb-3">
                        </div>
                        <h3 class="m-0">
                            زايد وراقب السوم وفالك التوفيق
                        </h3>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- download -->
    <div id="download">
        <div class="container mt-5 text-center">
            <h1 class="w-700">
                حاليًا انت جاهز للمزايدة ، حمل التطبيق و فالك التوفيق
            </h1>
            <div class="mt-5">
                <img src="{{asset('images/google_play.png')}}" class="store" alt="">
                <img src="{{asset('images/app-store.png')}}" class="store" alt="">
            </div>
        </div>
    </div>
    <!-- footer -->
    <footer>
        <div class="container text-white  py-5 text-center mt-5" id="contact">
            <div>
                <h3>
                    للأسئلة والإستفسارات نرجو مراجعة قسم الاسئلة الاكثر شيوعا، وفي حال وجود مشكلة في إيجاد الجواب، فريقنا بالكامل تحت خدمتكم.
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
