@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInUp rubberBand">
                <div class="card-body">
                    <div class=" tabulation" style="margin:50px">
                        <div class="form-group">
                            <a type="button" class="btn btn-success" href="{{route('place.create')}}">Add place</a>
                        </div>
                        <div style="padding:50px;">
                            <div class="container">
                                @include('includes.messages')
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col col-lg-9">#</th>
                                        <th scope="col col-lg-9">Name</th>
                                        <th scope="col col-lg-9">Address</th>
                                        <th scope="col col-lg-9">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($places as $place)
                                        <tr>
                                            <th scope="row">{{$place->id}}</th>
                                            <td>{{$place->name}}</td>
                                            <td>
                                                <div class="overflow-auto">{{$place->address}} </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <div style="padding-right: 10px"><a type="button"
                                                                                        class="btn btn-info"
                                                                                        href="{{route('place.show',['id'=>$place->id])}}">Details</a>
                                                    </div>

                                                    <div style="padding-right: 10px"><a type="button"
                                                                                        class="btn btn-warning"
                                                                                        href="{{route('place.edit',['id'=>$place->id])}}">Edit</a>
                                                    </div>

                                                    <div style="padding-right: 10px">
                                                        <form action="{{route('place.destroy',['id'=>$place->id])}}"
                                                              method="POST">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-danger">Delete
                                                            </button>
                                                        </form>
                                                    </div>


                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{$places->links()}}
                            </div>

                        </div>


                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
