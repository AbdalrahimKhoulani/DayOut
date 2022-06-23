@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInRight">
                <div class="card-body">
                    <div class="tabulation" style="margin:10px">

                        <div style="">


                            <div class="container d-flex align-items-center flex-column"
                                 style="background-color: #f2f2f2; padding: 25px; height: 100%;"><img
                                    class="mb-5 rounded-circle" src="{{asset($user->photo)}}" alt="..."
                                    style="width: 15rem;">
                                @if($user->deleted_at != null)
                                    <form method="post" action="{{route('user.unblock',['id'=>$user->id])}}">
                                       @csrf
                                        @method('PUT')
                                        <button type="submit">Unblock</button>
                                    </form>

                                    @endif
                                <!-- Masthead Heading-->
                                <h3 class="masthead-heading text-uppercase mb-0">{{$user->first_name.' '.$user->last_name}}</h3>
                                <div class="container">
                                    <div class="row">
                                        @if($user->email!=null)
                                            <div class="col-sm d-flex align-items-center flex-column "
                                                 style="padding:5px;">
                                                <h6 class="masthead-heading text-uppercase mb-0">Email
                                                    :{{ $user->email}}</h6>
                                            </div>
                                        @endif

                                        <div class="col-sm d-flex align-items-center flex-column " style="padding:5px;">
                                            <h6 class="masthead-heading text-uppercase mb-0">Phone
                                                : {{$user->phone_number}}</h6>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-sm d-flex align-items-center flex-column " style="padding:5px;">
                                            <h6 class="masthead-heading text-uppercase mb-0"> Gender
                                                : {{$user->gender}}</h6>
                                        </div>
                                        <div class="col-sm d-flex align-items-center flex-column " style="padding:5px;">
                                            <h6 class="masthead-heading text-uppercase mb-0">Follows
                                                : {{$user->follows}}</h6>
                                        </div>

                                    </div>
                                    @if($user->organizer!=null)
                                        <div class="row">
                                            <div class="col-sm d-flex align-items-center flex-column "
                                                 style="padding:5px;">
                                                <h6 class="masthead-heading text-uppercase mb-0">Rate
                                                    : {{$user->rate}}</h6>
                                            </div>
                                            <div class="col-sm d-flex align-items-center flex-column "
                                                 style="padding:5px;">
                                                <h6 class="masthead-heading text-uppercase mb-0">Folllower
                                                    : {{$user->followersCount}}</h6>
                                            </div>
                                        </div>

                                    @endif


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
