@extends('framework.frame')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header">
            <div class="row">
                <div class="col-10"><h5 class="text-capitalize ps-3">Register New Course</h5></div>
                <div class="col-2"><a href="{{ route('Subjects.index') }}" class="btn btn-info"><i class="fas fa-chevron-left"></i>Back</a></div>
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
                <form action="{{route('Subjects.store')}}" method="POST">
                    @csrf
                    <div class="col-12">
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Course Name</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="name" class="form-control text-uppercase" value="{{old('name')}}">
                                </div>
                                @error('name')<span class="text-danger text-sm">{{$message}}</span>@enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Course Code</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="code" class="form-control text-uppercase" value="{{old('code')}}">
                                </div>
                                @error('code')<span class="text-danger text-sm">{{$message}}</span>@enderror
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
