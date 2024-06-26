@extends('layouts.frame')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
            <div class="row">
                <div class="col-10"><h6 class="text-white text-capitalize ps-3">Register New School</h6></div>
                <div class="col-2"><a href="{{route('Schools.index')}}" class="btn btn-sm btn-info">Back</a></div>
            </div>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">{{Session::get('success')}}</div>
            @endif
            <div class="container row">
                <form action="{{route('Schools.store')}}" method="POST">
                    @csrf
                    <div class="col-12">
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">School Name</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="name" class="form-control text-uppercase" value="{{old('name')}}">
                                </div>
                                @error('name')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Registration No.</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="reg_no" class="form-control text-uppercase" value="{{old('reg_no')}}">
                                </div>
                                @error('reg_no')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
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
