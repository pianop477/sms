@extends('framework.frame')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card my-3">
        <div class="card-header">
            <div class="row">
                <div class="col-10"><h5 class="text-capitalize ps-3">Register New Bus Routine</h5></div>
                <div class="col-2"><a href="{{ route('Transportation.index') }}" class="btn btn-info"><i class="fas fa-chevron-left"></i> Back</a></div>
            </div>
        </div>
        <div class="card-body px-0 pb-2">
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">{{Session::get('success')}}</div>
            @endif
            <div class="container row">
                <form action="{{route('Transportation.store')}}" method="POST">
                    @csrf
                    <div class="col-12">
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Driver FullName</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="fullname" class="form-control" value="{{old('fullname')}}">
                                </div>
                                @error('fullname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Gender</label>
                                <div class="input-group input-group-outline mb-3">
                                    <select name="gender" id="" class="form-control">
                                        <option value="">--Select--</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                @error('gender')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Mobile Phone</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="phone" class="form-control" value="{{old('phone')}}">
                                </div>
                                @error('phone')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Bus Number</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="bus" class="form-control" value="{{old('bus')}}">
                                </div>
                                @error('bus')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label for="" class="form-label">Bus Routine</label>
                                <div class="input-group input-group-outline mb-3">
                                    <textarea name="routine" id="" cols="40" rows="3">{{old('routine')}}</textarea>
                                </div>
                                @error('routine') <span class="text-danger text-sm">{{$message}}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary float-end">Save</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
