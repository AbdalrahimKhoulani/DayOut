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
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col col-lg-9">#</th>
                                        <th scope="col col-lg-9">Name</th>
                                        <th scope="col col-lg-9">Address</th>
                                        <th scope="col col-lg-9">Organizer</th>
                                        <th scope="col col-lg-9">Date "D-M-Y"</th>
                                        <th scope="col col-lg-9">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($suggestions as $suggestion)
                                        <tr>
                                            <th scope="row">{{$suggestion->id}}</th>
                                            <td>{{$suggestion->place_name}}</td>
                                            <td>
                                                <div class="overflow-auto">{{$suggestion->place_address}} </div>
                                            </td>
                                            <td>
                                                {{$suggestion->organizer->user->first_name.' '.$suggestion->organizer->user->last_name}}
                                            </td>
                                            <td>
                                                {{date('d-m-y',strtotime($suggestion->created_at))}}
                                            </td>
                                            <td>


                                                    <div style="padding-right: 10px">
                                                        <form action="{{route('place.proposed.destroy',['id'=>$suggestion->id])}}"
                                                              method="POST">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-danger">Delete
                                                            </button>
                                                        </form>
                                                    </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{$suggestions->links()}}
                            </div>

                        </div>


                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
