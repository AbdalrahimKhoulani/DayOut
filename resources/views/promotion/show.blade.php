@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInRight">
                <div class="card-body">
                    <div class="tabulation" style="margin:10px">

                        <div style="padding:50px;">


                            @include('includes.messages')
                            <div class="row">
                                <div class="col-sm-12">
                                    <h3>{{$promotion->user->first_name. ' ' .$promotion->user->last_name}}</h3>
                                    <p>{{$promotion->description}}</p>

                                </div>


                            </div>
                            <div class="row">
                                <div class="col col-sm-12 col-md-6">
                                    <img class="img-responsive image" src="{{asset($promotion->credential_photo)}}"
                                         alt="credential">
                                </div>
                                <div class="col col-sm-12 col-md-6">

                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <div style="padding: 5px">
                                            <form action="" method="post">
                                                @csrf
                                                @method('put')
                                                <textarea name="admin_message" style="width:500px;height: 250px;" placeholder="Admin message ..."></textarea>
                                                <br>
                                                <button type="submit" class="btn btn-success"
                                                formaction="{{route('promotion.accept',['id'=>$promotion->id])}}">Accept</button>
                                                <button type="submit" class="btn btn-danger"
                                                formaction="{{route('promotion.reject',['id'=>$promotion->id])}}">Reject</button>
                                            </form>
                                        </div>
                                        <div style="padding: 5px">

                                        </div>
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
