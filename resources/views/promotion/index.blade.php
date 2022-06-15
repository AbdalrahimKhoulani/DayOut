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
                                @if (count($promotions)>0)
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th scope="col col-lg-9">#</th>
                                            <th scope="col col-lg-9">First Name</th>
                                            <th scope="col col-lg-9">Last Name</th>
                                            <th scope="col col-lg-9">Gender</th>
                                            <th scope="col col-lg-9">PhoneNumber</th>
                                            <th scope="col col-lg-9">Date "D-M-Y"</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($promotions as $promotion)
                                            <tr>
                                                <th scope="row">{{$promotion->id}}</th>
                                                <td>{{$promotion->first_name}}</td>
                                                <td>{{$promotion->last_name}}</td>
                                                <td>{{$promotion->gender}}</td>
                                                <td>{{$promotion->phone_number}}</td>
                                                <td>{{date('d-m-y',strtotime($promotion->created_at))}}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <a type="button" class="btn btn-info" href="{{route('promotion.show',$promotion->id)}}">Details</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                @else
                                    <div class="alert alert-primary" role="alert">
                                        No Promotion Request
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
