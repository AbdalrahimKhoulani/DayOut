@extends('layouts.dashboard')
@section('dashboard.content')
<section>
    <div class=" form-card">
        <div class="card animated bounceInUp rubberBand">
            <div class="card-body">
                <div class=" tabulation" style="margin:50px">

                    <div style="padding:50px;">
                        <div class="container">
                            <div class="row">  <table class="table table-hover">
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
                                    @foreach($organizers as $organizer)
                                        <tr>
                                            <th scope="row">{{$organizer->id}}</th>
                                            <td>{{$customer->first_name.' '.$organizer->last_name}}</td>
                                            <td>
                                                <div class="overflow-auto">{{$organizer->gender}} </div>
                                            </td>
                                            <td>
                                                <div class="overflow-auto">{{$organizer->phone_number}} </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <div style="padding-right: 10px"><a type="button"
                                                                                        class="btn btn-info"
                                                                                        href="{{route('user.show',['id'=>$organizer->id])}}">Details</a>
                                                    </div>



                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table></div>

                            <div class="row">  {!! $customers->links() !!}</div>

                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>

</section>
@endsection
