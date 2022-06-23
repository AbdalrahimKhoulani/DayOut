@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInUp bounceInRight">
                <div class="card-body">
                    <div class=" tabulation" style="margin:50px">
                        <div style="padding:50px;">
                            <div class="container">
                                @include('includes.messages')
                                @if(count($reports))
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th scope="col col-lg-9">#</th>
                                            <th scope="col col-lg-9">First Name</th>
                                            <th scope="col col-lg-9">Last Name</th>
                                            <th scope="col col-lg-9">Count</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($reports as $report)
                                            <tr>
                                                <th scope="row">{{$report->target_id}}</th>
                                                <td>{{$report->first_name}}</td>
                                                <td>{{$report->last_name}}</td>
                                                <td>{{$report->count}}</td>

                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <a type="button" class="btn btn-info"
                                                           href="{{route('report.show',$report->target_id)}}">Details</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-primary" role="alert">
                                        NO reports
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
