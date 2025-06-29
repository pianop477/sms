@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12">
        <div class="card mt-4 mb-3">
            <div class="card-body">
                <h4 class="text-center text-capitalize">About {{$schools->school_name}}</h4>
                <hr>

                <!-- School Info Row - Responsive Layout -->
                <div class="row">
                    <!-- School Logo - Full width on mobile, 2 cols on larger screens -->
                    <div class="col-12 col-md-2 mb-3 mb-md-0 text-center text-md-start">
                        <img src="{{asset('assets/img/logo/'. $schools->logo)}}" alt="School Logo"
                             class="img-fluid border-radius-lg shadow-sm" style="max-width: 100px; height: auto; border-radius:50px">
                    </div>

                    <!-- School Details 1 - Full width on mobile, 4 cols on larger screens -->
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <ul class="list-unstyled">
                            <li class="mb-2">Registration Number: <span class="fw-bold text-uppercase">{{$schools->school_reg_no}}</span></li>
                            <li class="mb-2">School Manager: <span class="fw-bold text-uppercase">{{$managers->first()->first_name}} {{$managers->first()->last_name}}</span></li>
                            <li class="mb-2">Gender: <span class="fw-bold text-uppercase">{{$managers->first()->gender}}</span></li>
                            <li class="mb-2">Status:
                                @if ($managers->first()->status == 1)
                                    <span class="badge bg-success text-white">Active</span>
                                @else
                                    <span class="badge bg-secondary text-white">Blocked</span>
                                @endif
                            </li>
                        </ul>
                    </div>

                    <!-- School Details 2 - Full width on mobile, 4 cols on larger screens -->
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <ul class="list-unstyled">
                            <li class="mb-2">Phone Number: <span class="fw-bold text-uppercase">{{$managers->first()->phone}}</span></li>
                            <li class="mb-2">Email Address: <span class="fw-bold">{{$managers->first()->email}}</span></li>
                            <li class="mb-2">Address: <span class="fw-bold text-uppercase">P.O Box {{$schools->postal_address}} - {{$schools->postal_name}}</span></li>
                            <li class="mb-2">Country: <span class="fw-bold text-capitalize">{{$schools->country}}</span></li>
                        </ul>
                    </div>

                    <!-- Manager Image - Full width on mobile, 2 cols on larger screens -->
                    <div class="col-12 col-md-2 text-center">
                        @if ($managers->first()->image == NULL)
                            @if ($managers->first()->gender == 'male')
                                <img src="{{asset('assets/img/profile/avatar.jpg')}}" alt="Manager Avatar"
                                     class="img-fluid mb-2" style="max-width: 120px; height: auto; border-radius: 50px;">
                            @else
                                <img src="{{asset('assets/img/profile/avatar-female.jpg')}}" alt="Manager Avatar"
                                     class="img-fluid mb-2" style="max-width: 120px; height: auto; border-radius: 50px;">
                            @endif
                        @else
                            <img src="{{asset('assets/img/profile/' .$managers->first()->image)}}" alt="Manager Photo"
                                 class="img-fluid border-radius-lg shadow-sm mb-2" style="max-width: 150px; height: auto; border-radius: 50px;">
                        @endif
                        <p class="text-capitalize mb-1"><strong>{{$managers->first()->first_name}} {{$managers->first()->last_name}}</strong></p>
                        <p class="small">School Administrator</p>
                    </div>
                </div>

                <hr>

                <!-- Stats Row 1 -->
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="card h-100 p-3 bg-primary text-center text-white">
                            <h4>Teachers</h4>
                            <p class="text-white fs-4 mb-0">{{count($teachers)}}</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="card h-100 p-3 bg-info text-center text-white">
                            <h4>Parents</h4>
                            <p class="text-white fs-4 mb-0">{{count($parents)}}</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="card h-100 p-3 bg-success text-center text-white">
                            <h4>Students</h4>
                            <p class="text-white fs-4 mb-0">{{count($students)}}</p>
                        </div>
                    </div>
                </div>

                <!-- Stats Row 2 -->
                <div class="row">
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <div class="card h-100 p-3 bg-danger text-center text-white">
                            <h4>Classes</h4>
                            <p class="text-white fs-4 mb-0">{{count($classes)}}</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <div class="card h-100 p-3 bg-secondary text-center text-white">
                            <h4>Courses</h4>
                            <p class="text-white fs-4 mb-0">{{count($courses)}}</p>
                        </div>
                    </div>
                    <!-- Empty column to maintain alignment -->
                    <div class="col-12 col-md-4 d-none d-md-block"></div>
                </div>

                <hr>

                <!-- Generate Invoice Button -->
                <div class="row justify-content-center">
                    <div class="col-12 col-md-4 text-center">
                        <a href="{{route('admin.generate.invoice', ['school' => Hashids::encode($schools->id)])}}"
                           class="btn btn-primary w-100">
                           Generate Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
