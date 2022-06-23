@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInUp rubberBand">
                <div class="card-body">
                    <div class=" tabulation" style="margin:50px">

                        <div style="padding:50px;">
                            <div class="container">
                                @include('includes.messages')
                                @if(count($users)!=0)
                                <div class="row">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th scope="col col-lg-9">#</th>
                                            <th scope="col col-lg-9">Name</th>
                                            <th scope="col col-lg-9">Address</th>
                                            <th scope="col col-lg-9">Gender</th>
                                            <th scope="col col-lg-9">Phone</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <th scope="row">{{$user->id}}</th>
                                                <td>{{$user->first_name.' '.$user->last_name}}</td>
                                                <td>
                                                    <div class="overflow-auto">{{$user->gender}} </div>
                                                </td>
                                                <td>
                                                    <div class="overflow-auto">{{$user->phone_number}} </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <div style="padding-right: 10px"><a type="button"
                                                                                            class="btn btn-info"
                                                                                            href="{{route('user.show',['id'=>$user->id])}}">Details</a>
                                                        </div>



                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table></div>

                                <div class="row">  {!! $users->links() !!}</div>
                                @else
                                    <div class="alert alert-primary" role="alert">
                                       No blocked users
                                    </div>
                                @endif

                            </div>

                        </div>


                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
