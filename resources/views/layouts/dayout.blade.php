<!DOCTYPE html>
<html lang="en">

<head>
    <title>Document</title>
    <link href="{{ asset('/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{asset('/css/swiper.min.css')}}" rel="stylesheet" >
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
</head>

<body>
<header class="header-section">
    <div style="padding: 15px;">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#dayoutNavbarSupportedContent" aria-controls="dayoutNavbarSupportedContent"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" style="color: #F78536;"></span>
                </button>
                <div class="collapse navbar-collapse" id="dayoutNavbarSupportedContent">
                    {{--                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">--}}
                    {{--                        <li class="nav-item">--}}
                    {{--                            <a class="nav-link active" aria-current="page" href="#">Home</a>--}}
                    {{--                        </li>--}}
                    {{--                        <li class="nav-item">--}}
                    {{--                            <a class="nav-link" href="#">Link 1</a>--}}
                    {{--                        </li>--}}
                    {{--                        <li class="nav-item">--}}
                    {{--                            <a class="nav-link" href="#">Link 2</a>--}}
                    {{--                        </li>--}}
                    {{--                        <li class="nav-item">--}}
                    {{--                            <a class="nav-link" href="#">Link 3</a>--}}
                    {{--                        </li>--}}
                    {{--                    </ul>--}}

                    @auth
                        <a class="btn login-btn-outline my-2 my-sm-0" href="{{route('logout.perform')}}">{{\Illuminate\Support\Facades\Auth::user()->first_name . ' ' .\Illuminate\Support\Facades\Auth::user()->last_name  }}</a>
                    @else
                        <a class="btn login-btn-outline my-2 my-sm-0">Login</a>
                    @endauth
                </div>
            </div>
        </nav>
    </div>

</header>


<section class="hero-section set-bg">
    <div class="container">


        <div class="row" style="padding-top: 150px;">

            <div class="col-sm-12 col-md-6">
                <div class="desc animate-box">

                    <h2 class="animated rubberBand">DayOut</h2>
                    <h3 class="animated bounceInLeft">The way to enjoy</h3>

                </div>
            </div>
            <div class="col-sm-12 col-md-6 ">
                <div class="text-center animated bounceInDown"><i class="fa fa-5x fa-car"></i></div>

            </div>


        </div>
    </div>


</section>

<section>
    @yield('content')
</section>

<footer class="footer-section ">
    <div class="footer-bottom">

        <div class="social">
            <a href=""><i class="fa fa-facebook"></i></a>
            <a href=""><i class="fa fa-twitter"></i></a>
            <a href=""><i class="fa fa-instagram"></i></a>
            <a href=""><i class="fa fa-youtube"></i></a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <p class="text-white  text-center" style="color:#111!important;">
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                Copyright ©
                <script>document.write(new Date().getFullYear());</script>
                 / 2019 All rights reserved
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            </p>
        </div>

    </div>
</footer>

<script src="{{asset('/js/jquery-3.4.1.min.js')}}"></script>
<script src="{{asset('/js/bootstrap.min.js')}}"></script>
<script src="{{asset('/js/fontawesome.min.js')}}"></script>
<script src="{{asset('/js/swiper.min.js')}}"></script>
<script src="{{asset('/js/app.js')}}"></script>
</body>

</html>
