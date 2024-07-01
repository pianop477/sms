@extends('layouts.frame')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card my-3">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-primary shadow-primary border-radius-lg pt-2 pb-2">
            <div class="row">
                <div class="col-10"><h6 class="text-white text-capitalize ps-3">Student Registration Form</h6></div>
                <div class="col-2"><a href="{{route('home')}}" class="btn btn-sm btn-info">Back</a></div>
            </div>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">{{Session::get('success')}}</div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-danger" role="alert">{{Session::get('error')}}</div>
            @endif
            <div class="container row">
                <form action="{{route('register.student')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-12">
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">First Name</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="fname" class="form-control" value="{{old('fname')}}">
                                </div>
                                @error('fname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Middle Name</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="middle" class="form-control" value="{{old('middle')}}">
                                </div>
                                @error('middle')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Last Name</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="lname" class="form-control" value="{{old('lname')}}">
                                </div>
                                @error('lname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Gender</label>
                                <div class="input-group input-group-outline mb-3">
                                    <select name="gender" id="" class="form-control">
                                        <option value="">--Select gender--</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                @error('gender')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Birth Date</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" id="customDatePicker" name="dob" class="form-control">
                                </div>
                                @error('dob')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                    <label class="form-label">Driver Name: <span class="text-sm text-primary">Select if using school bus</span></label>
                                    <div class="input-group input-group-outline mb-3">
                                        <select name="driver" id="" class="form-control text-uppercase">
                                            <option value="">--Select bus driver--</option>
                                            @foreach ($buses as $bus )
                                            <option value="{{$bus->id}}">{{$bus->driver_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('driver')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Grade </label>
                                <div class="input-group input-group-outline mb-3">
                                    <select name="grade" id="" class="form-control text-uppercase">
                                        <option value="">--select grade--</option>
                                        @foreach ($classes as $class )
                                        <option value="{{$class->id}}">{{$class->class_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('grade')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Class Group </label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="group" class="form-control text-uppercase">
                                </div>
                                @error('group')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Image </label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="file" name="image" class="form-control text-uppercase">
                                </div>
                                @error('image')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary float-end">Register</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
