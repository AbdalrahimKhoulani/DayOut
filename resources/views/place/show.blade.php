@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInRight">
                <div class="card-body">
                    <div class="tabulation" style="margin:50px">

                        <div style="padding:50px;">
                            <div class="row"><h1>{{$place->name}}</h1></div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6">

                                    <h5> {{$place->summary}}</h5>
                                    <p>{{$place->description}}</p>

                                </div>

                                <div class="col-sm-12 col-md-6">
                                    <!-- Swiper -->
                                    <div class="swiper-container">
                                        <div class="swiper-wrapper">
                                            @foreach($photos as $photo)
                                                <div class="swiper-slide">
                                                    <div>
                                                        <img class="img-responsive image"
                                                             src="{{asset($photo->path)}}"
                                                             alt="1">
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>

    </section>
@endsection
