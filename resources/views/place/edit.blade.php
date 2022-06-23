@extends('layouts.dashboard')
@section('dashboard.content')
    <section>
        <div class=" form-card">
            <div class="card animated bounceInRight">
                <div class="card-body">
                    <div class="tabulation">
                        @include('includes.messages')
                        <div>
                            <form action="{{route('place.update',['id'=>$place->id])}}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div style="text-align: center;"><h3>Update place</h3></div>
                                <div class="dropdown-divider"></div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="name" value="{{$place->name}}"
                                           placeholder="Place name" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="address" value="{{$place->address}}"
                                           placeholder="Place address" required>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="type_id">
                                        <option>select place type...</option>
                                        @foreach($place_types as $type)
                                            @if($type->id==$place->type_id)
                                                <option value="{{$type->id}}" selected> {{$type->name}} </option>
                                            @else
                                                <option value="{{$type->id}}"> {{$type->name}} </option>
                                            @endif


                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" rows="2" name="summary"
                                              required placeholder="Summary">{{$place->summary}}</textarea>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" id="" rows="4" name="description"
                                              placeholder="Description">{{$place->description}}</textarea>
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
