@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInRight">
                <div class="card-body">
                    <div class=" tabulation" style="margin:50px">
                        <div style="padding:50px;">
                            <div class="container">
                                @include('includes.messages')
                                <div class="card" style="width: 18rem;">
                                    <div class="">
                                        <h5 class="card-title">Delete place</h5>
                                        <h6 class="card-subtitle mb-2">Are you sure to delete {{$place->name}} ?</h6>
                                        <p class="card-text">{{$place->summary}}</p>
                                        <div style="margin:10px;"><form action="{{route('place.destroy',['id'=>$place->id])}}"
                                                   method="POST">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-danger">Delete
                                                </button>
                                            </form></div>
                                       <div><a href="{{route('place.index')}}" class="btn btn-primary">Go to list</a></div>
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
