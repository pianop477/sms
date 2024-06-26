@extends('framework.frame')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card my-3">
        <div class="card-header">
            <div class="row">
                <div class="col-10"><h6 class="text-uppercase">Register New Teacher</h6></div>
                <div class="col-2"><a href="{{ route('Teachers.index') }}" class="btn btn-info"><i class="fas fa-chevron-left"></i> Back</a></div>
            </div>
        </div>
        <div class="card-body">
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">{{Session::get('success')}}</div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-danger" role="alert">{{Session::get('error')}}</div>
            @endif
            <div class="container row">
                <form action="{{route('Teachers.store')}}" method="POST">
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
                                <label class="form-label">Last Name</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="lname" class="form-control" value="{{old('lname')}}">
                                </div>
                                @error('lname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Email</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="email" name="email" class="form-control" value="{{old('email')}}">
                                </div>
                                @error('email')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="row">
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
                            <div class="col-4">
                                <label class="form-label">Birth Date</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="date" name="dob" class="form-control">
                                </div>
                                @error('dob')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Qualification</label>
                                <div class="input-group input-group-outline mb-3">
                                    <select name="qualification" id="" class="form-control">
                                        <option value="">--Select--</option>
                                        <option value="1">Masters</option>
                                        <option value="2">Degree</option>
                                        <option value="3">Diploma</option>
                                        <option value="4">Certificate</option>
                                    </select>
                                </div>
                                @error('qualification')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>

                            <div class="col-4">
                                <label class="form-label">Street/Village</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="street" class="form-control" value="{{old('street')}}">
                                </div>
                                @error('street')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                                <input type="hidden" name="usertype" value="3">
                                <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">
                                <input type="hidden" name="password" value="shule@123">
                        </div>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary float-end">Submit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
