@extends('layouts.dayout')
@section('content')


    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#dashboardNavbarSupportedContent" aria-controls="dashboardNavbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="color: #F78536;"></span>
            </button>
            <div class="collapse navbar-collapse" id="dashboardNavbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('place.index')}}">Places</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('user.index')}}">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('organizer.index')}}">Organizers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('promotion.index')}}">Promotions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('report.index')}}">Reports</a>
                    </li>
                </ul>


            </div>
        </div>
    </nav>
    @yield('dashboard.content')
@endsection
