@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInRight">
                <div class="card-body">
                    <div class="tabulation" style="margin:10px">
                        <div style="padding: 25px;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col col-lg-3" style="text-align: center;">
                                            <img src="{{asset($target->photo)}}" alt="..."
                                                 class="img-thumbnail rounded-circle">
                                            <a href="{{route('user.show',['id'=>$target->id])}}">Open Profile</a>
                                        </div>
                                        <div class="col col-lg-8">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">{{$target->first_name}}</li>
                                                <li class="list-group-item">{{$target->last_name}}</li>
                                                <li class="list-group-item">{{$target->gender}}</li>
                                                <li class="list-group-item">{{$target->phone_number}}</li>
                                                <li class="list-group-item">{{$target->email}}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="list-group">
                                    @include('includes.messages')
                                    @foreach ($target->targets as $report)
                                        <div class="list-group-item list-group-item-action">
                                            <div class="col" style="padding-bottom: 10px">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">
                                                        {{$report->reporter->first_name.' '.$report->reporter->last_name}}
                                                    </h5>
                                                    <small>{{$report->created_at->diffForhumans()}}</small>
                                                </div>
                                                <p class="mb-1">{{$report->report}}</p>
                                                <div class="dropdown-divider"></div>
                                                <small>Contact with reporter
                                                    : {{$report->reporter->email .'  --  ' .$report->reporter->phone_number}}</small>
                                            </div>
                                            <form method="POST" action="">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                        formaction="{{route('report.accept',['id'=>$report->id])}}"
                                                        class="btn btn-primary">Accept
                                                </button>
                                                <button type="submit"
                                                        formaction="{{route('report.reject',['id'=>$report->id])}}"
                                                        class="btn btn-warning">Reject
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </section>
@endsection
