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
                                @if (count($reports)>0)
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
                                        @foreach($reports as $report)
                                            <tr>
                                                <th scope="row">{{$report->id}}</th>
                                                <td>{{$report->first_name}}</td>
                                                <td>{{$report->last_name}}</td>
                                                <td>{{$report->gender}}</td>
                                                <td>{{$report->phone_number}}</td>
                                                <td>{{date('d-m-y',strtotime($report->created_at))}}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <a type="button" class="btn btn-info" href="{{route('report.show',$report->id)}}">Details</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                @else
                                    <div class="alert alert-primary" role="alert">
                                        No Reports
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
