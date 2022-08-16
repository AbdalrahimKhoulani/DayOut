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
                                @if(count($suggestions)!=0)

                                    <div class="list-group">
                                        @foreach($suggestions as $suggestion)



                                            <div class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h4 class="mb-1">{{$suggestion->place_name}}</h4>
                                                    <form
                                                        action="{{route('place.proposed.destroy',['id'=>$suggestion->id])}}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-primary">Delete</button>
                                                    </form>
                                                </div>

                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">{{$suggestion->place_address}}</h6>
                                                </div>

                                                <p class="mb-1"> {{$suggestion->description}}</p>

                                                <small> {{$suggestion->organizer->user->first_name.' '.$suggestion->organizer->user->last_name}}</small>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{$suggestions->links()}}
                                @else
                                    <div class="alert alert-primary" role="alert">
                                        No proposed places
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
