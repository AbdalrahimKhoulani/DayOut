@extends('layouts.dayout')

@section('content')
    <section>
        <div class="container">
            <div class=" form-card">
                <div class="card animated bounceInUp rubberBand">
                    <div class="card-body">
                        <div class="tabulation">
                            <div class="container">

                                @include('includes.messages')
                                @auth

                                    <div style="text-align: center; vertical-align: center; padding: 10px">
                                        <h1>{{ \Illuminate\Support\Facades\Auth::user()->first_name . ' ' . \Illuminate\Support\Facades\Auth::user()->last_name }}
                                        </h1>
                                    </div>
                                @else
                                    <div style="padding:50px;">
                                        <form method="POST" action="{{ route('login.perform') }}">
                                            @csrf

                                            <div style="text-align: center;">
                                                <h3>Login</h3>
                                            </div>
                                            <div class="dropdown-divider"></div>
                                            <div class="form-group">
                                                <input type="tel" class="form-control" aria-describedby="emailHelp"
                                                    placeholder="Enter phone number" name="phone_number"  required
                                                        pattern="09[3-9][0-9]{7}">
                                                <!--<small id="emailHelp" class="form-text text-muted">We'll never share your email with
                                            anyone else.</small>-->
                                            </div>
                                            <div class="form-group">
                                                <input type="password" class="form-control" placeholder="Password"
                                                    name="password" required>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>

                                    </div>
                                @endauth
                            </div>





                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection
