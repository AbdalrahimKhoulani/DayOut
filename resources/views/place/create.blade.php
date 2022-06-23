@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInRight">
                <div class="card-body">
                    <div class="tabulation">
                        @include('includes.messages')
                        <div>
                            <form action="{{route('place.store')}}" method="POST"  enctype="multipart/form-data">
                                @csrf
                                <div style="text-align: center;"><h3>Add place</h3></div>
                                <div class="dropdown-divider"></div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="name" placeholder="Place name" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="address" placeholder="Place address" required>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="type_id">
                                        <option selected>select place type...</option>
                                        @foreach($place_types as $type)
                                            <option value="{{$type->id}}">{{$type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" rows="2" name="summary"
                                              required    placeholder="Summary"></textarea>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" id="" rows="4" name="description"
                                              placeholder="Description"></textarea>
                                </div>
                                <div class="form-group">
                                    <input type="file" class="form-control-file" name="photos[]" multiple>
                                </div>
                                <div class="dropdown-divider"></div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
