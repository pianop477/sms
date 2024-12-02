@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">Change Password Form</h4>
            @if (Session::has('errors'))
                <div class="alert alert-danger" role="alert">
                    {{Session::get('errors')}}
                </div>
            @endif
            <form class="needs-validation" novalidate="" action="{{route('change.new.password')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Current Password</label>
                        <input type="password" name="current_password" class="form-control" id="validationCustom01" placeholder="Current Password" value="{{old('current_password')}}" required="">
                        @error('current_password')
                        <div class="invalid-feedback">
                            <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">New Password</label>
                        <input type="password" name="new_password" class="form-control" id="validationCustom02" placeholder="New Password" required="" value="{{old('new_password')}}">
                        @error('new_password')
                        <div class="invalid-feedback">
                           <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" id="validationCustom02" placeholder="Confirm Password" required="" value="{{old('confirm_password')}}">
                        @error('confirm_password')
                        <div class="invalid-feedback">
                           <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
@endsection
