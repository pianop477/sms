@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12">
            <div class="card mt-4 mb-3">
                <div class="card-body">
                    <h4 class="text-center text-capitalize">About {{$school->school_name}}</h4>
                    <hr>
                    <div class="row">
                        <div class="col-2">
                            <img src="{{asset('assets/img/logo/'. $school->logo)}}" alt="" class="profile-img border-radius-lg shadow-sm" style="width: 150px; object-fit:cover;">
                        </div>
                        <div class="col-4">
                            <ul>
                                <li>Registration Number: <span class="font-weight-bold text-uppercase">{{$school->school_reg_no}}</span></li>
                                <li>School Manager: <span class="font-weight-bold text-uppercase">{{$managers->first()->first_name}} {{$managers->first()->last_name}}</span></li>
                                <li>Gender: <span class="font-weight-bold text-uppercase">{{$managers->first()->gender}}</span></li>
                                @if ($managers->first()->status == 1)
                                <li>Status: <span class="badge bg-success text-white">Active</span></li>
                                @else
                                <li>Status: <span class="badge bg-secondary text-white">Blocked</span></li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-4">
                            <ul>
                                <li>Phone Number: <span class="font-weight-bold text-uppercase">{{$managers->first()->phone}}</span></li>
                                <li>Email Address: <span class="font-weight-bold">{{$managers->first()->email}}</span></li>
                                <li>Address: <span class="font-weight-bold text-uppercase">P.O Box {{$school->postal_address}} - {{$school->postal_name}}</span></li>
                                <li>Country: <span class="font-weight-bold text-capitalize">{{$school->country}}</span></li>
                            </ul>
                        </div>
                        <div class="col-2">
                            @if ($managers->first()->image == NULL)
                                @if ($managers->first()->gender == 'male')
                                    <img src="{{asset('assets/img/profile/avatar.jpg')}}" alt="" class="profile-img" style="width: 120px; object-fit:cover; border: 1px solid black; border-radius: 4xp;">
                                    <p class="text-capitalize"><strong>{{$managers->first()->first_name}} {{$managers->first()->last_name}}</strong></p>
                                    <p>Manager</p>
                                @else
                                    <img src="{{asset('assets/img/profile/avatar-female.jpg')}}" alt="" class="profile-img" style="width: 120px; object-fit:cover; border: 1px solid black; border-radius: 4xp;">
                                    <p class="text-capitalize"><strong>{{$managers->first()->first_name}} {{$managers->first()->last_name}}</strong></p>
                                    <p>Manager</p>
                                @endif
                            @else
                                <img src="{{asset('assets/img/profile/' .$managers->first()->image)}}" alt="" class="profile-img border-radius-lg shadow-sm" style="width: 150px; object-fit:cover; border: 1px solid black; border-radius: 4px;">
                                <p class="text-capitalize"><strong>{{$managers->first()->first_name}} {{$managers->first()->last_name}}</strong></p>
                                <p>Manager</p>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-4">
                            <div class="card mb-3 p-3 bg-primary text-center text-white">
                                <h4>Teachers</h4>
                                <p class="text-white">{{count($teachers)}}</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card mb-3 p-3 bg-info text-center text-white">
                                <h4>Parents</h4>
                                <p class="text-white">{{count($parents)}}</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card mb-3 p-3 bg-success text-center text-white">
                                <h4>Students</h4>
                                <p class="text-white">{{count($students)}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="card mb-3 p-3 bg-danger text-center text-white">
                                <h4>Classes</h4>
                                <p class="text-white">{{count($classes)}}</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card mb-3 p-3 bg-secondary text-center text-white">
                                <h4>Courses</h4>
                                <p class="text-white">{{count($courses)}}</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row justify-content-center">
                        <a href="{{route('admin.generate.invoice', $school->id)}}" class="btn btn-primary">Genderate Invoice</a>
                    </div>
                </div>
            </div>
        </div>
    @endsection
