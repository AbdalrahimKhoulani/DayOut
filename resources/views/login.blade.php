@extends('layouts.dayout')

@section('content')
    <section>
        <div class="container">
            <div class=" form-card">
                <div class="card animated bounceInUp rubberBand">
                    <div class="card-body">
                        <div class="tabulation">

                            <div style="padding:50px;">
                                <form method="POST" action="{{route('login')}}">
                                    @csrf

                                    <div style="text-align: center;"><h3>Login</h3></div>
                                    <div class="dropdown-divider"></div>
                                    <div class="form-group" >
                                        <input type="tel" class="form-control" aria-describedby="emailHelp"
                                               placeholder="Enter phone number" name="phone_number" required>
                                        <!--<small id="emailHelp" class="form-text text-muted">We'll never share your email with
                                    anyone else.</small>-->
                                    </div>
                                    <div class="form-group" >
                                        <input type="password" class="form-control" placeholder="Password" name="password" required>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>

                            </div>



                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection
