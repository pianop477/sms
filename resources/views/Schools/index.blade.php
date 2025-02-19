
@extends('SRTDashboard.frame')
@section('content')
    <div class="col-12">
        <div class="card mt-5">
            <div class="card-title mt-2">
                <h4 class="text-uppercase text-center">School registration form</h4>
            </div>
            <div class="card-body">
                <h6 class="header-title">School's information</h6>
                    <form class="needs-validation" novalidate="" action="{{route('Schools.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">School name</label>
                                <input type="text" required name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="School Name" value="{{old('name')}}" required="">
                                @error('name')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom02">Registration No</label>
                                <input type="text" required name="reg_no" class="form-control text-uppercase" id="validationCustom02" placeholder="Registration Number" required="" value="{{old('reg_no')}}">
                                @error('reg_no')
                                <div class="invalid-feedback">
                                {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Postal Address</label>
                                <input type="text" required name="postal" class="form-control" id="userInput validationCustom01" placeholder="P.O Box 123" value="{{old('postal')}}" required="">
                                @error('postal')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Address Name</label>
                                <input type="text" required name="postal_name" class="form-control text-capitalize" id="validationCustom01" placeholder="Dodoma" value="{{old('postal_name')}}" required="">
                                @error('postal_name')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Abbreviation Code</label>
                                <input type="text" name="abbriv" class="form-control" id="userInput validationCustom01" onblur="" value="{{old('abbriv')}}" required>
                                @error('abbriv')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Sender Name</label>
                                <input type="text" name="sender_name" class="form-control" id="validationCustom01" placeholder="Enter Sender ID" value="{{old('sender_name')}}">
                                @error('sender_name')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Select Country</label>
                                <select name="country" id="validationCustom01" class="form-control text-capitalize" required>
                                    <option value="Tanzania" selected>Tanzania</option>
                                </select>
                                @error('country')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom02">School Logo</label>
                                <input type="file" required name="logo" class="form-control" id="validationCustom02" placeholder="Last name" required="" value="{{old('logo')}}">
                                @error('logo')
                                <div class="invalid-feedback">
                                {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <hr>
                        <h6 class="header-title">Manager's information</h6>
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">First name</label>
                                <input type="text" name="fname" class="form-control text-capitalize" id="validationCustom01" placeholder="First name" value="{{old('fname')}}" required="">
                                @error('fname')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom02">Last name</label>
                                <input type="text" name="lname" class="form-control text-capitalize" id="validationCustom02" placeholder="Last name" required="" value="{{old('lname')}}">
                                @error('lname')
                                <div class="invalid-feedback">
                                {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustomUsername">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                    </div>
                                    <input type="text" name="email" class="form-control" id="validationCustomUsername" placeholder="Email ID" aria-describedby="inputGroupPrepend" required="" value="{{old('email')}}">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01">Gender</label>
                                <select name="gender" id="validationCustom01" class="form-control text-capitalize" required>
                                    <option value="">-- select gender --</option>
                                    <option value="male">male</option>
                                    <option value="female">female</option>
                                </select>
                                @error('gender')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom02">Mobile Phone</label>
                                <input type="text" name="phone" class="form-control" id="validationCustom02" placeholder="Phone Number" required="" value="{{old('phone')}}">
                                @error('phone')
                                <div class="invalid-feedback">
                                {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <button class="btn btn-success" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
        {{-- schools list --}}
        <div class="card mt-5">
            <div class="card-body">
                <h4 class="header-title text-center text-uppercase">Registered institutions</h4>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">School Name</th>
                                    <th scope="col">sender id</th>
                                    <th scope="col">Abbreviation</th>
                                    <th scope="col">Registration No</th>
                                    <th class="text-center">School Logo</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schools as $school )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">
                                            {{$school->school_name}}
                                        </td>
                                        <td class="text-uppercase">
                                            {{$school->sender_id}}
                                        </td>
                                        <td class="text-uppercase">
                                            {{$school->abbriv_code}}
                                        </td>
                                        <td class="text-uppercase">{{$school->school_reg_no}}</td>
                                        <td class="text-center">
                                            <img src="{{asset('assets/img/logo/' .$school->logo)}}" alt="" class="profile-img rounded-circle" style="width: 50px; object-fit: cover;">
                                        </td>
                                        <td>
                                            @if ($school->status == 1)
                                            <span class="status-p bg-success">Open</span>
                                            @elseif($school->status == 0)
                                            <span class="status-p bg-danger">Closed</span>
                                            @else
                                            <span class="status-p bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                           @if ($school->status == 2)
                                                {{-- <a href="" class="btn btn-success btn-xs">Approve</a> --}}
                                                <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#exampleModal{{$school->id}}">
                                                    Manage
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModal{{$school->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel{{$school->id}}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Add Service Time Duration </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <p class="text-center text-danger">School Details</p>
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item text-uppercase">School Name: <strong>{{$school->school_name}}</strong></li>
                                                                        <li class="list-group-item text-uppercase">Regitration ID: <strong>{{$school->school_reg_no}}</strong></li>
                                                                      </ul>
                                                                </div>
                                                            </div>
                                                            <hr class="dark horizontal py-0">
                                                            <p class="text-center text-danger">Complete Approval Actions</p>
                                                            <form action="{{route('approve.school.request', $school->id)}}" method="POST" novalidate="" class="needs-validation" enctype="multipart/form-data" role="form">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <input type="hidden" name="school" value="{{$school->id}}">
                                                                            <label for="" class="control-label">Set Months</label>
                                                                            <input type="number" class="form-control" name="service_duration" id="validationCustom01" required>
                                                                            @error('service_duration')
                                                                                <span class="text-danger">{{$message}}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                            <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to send this request?')">Save changes</button>
                                                        </form>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                             @else
                                                {{-- <a href="{{route('schools.show', $school->id)}}" class="btn btn-warning btn-xs">View</a> --}}
                                                <button type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#exampleModal{{$school->id}}">
                                                    View
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModal{{$school->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel{{$school->id}}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Payment Status </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <p class="text-center text-danger">School Details</p>
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item text-capitalize">School Name: <strong>{{$school->school_name}}</strong></li>
                                                                        <li class="list-group-item text-capitalize">Regitration ID: <strong>{{$school->school_reg_no}}</strong></li>
                                                                        <li class="list-group-item">Sender ID: <strong>{{$school->sender_id ?? NULL}}</strong></li>
                                                                        <li class="list-group-item text-capitalize">Service Start Date: <strong>{{$school->service_start_date}}</strong></li>
                                                                        <li class="list-group-item text-capitalize">Service Expiry Date: <strong>{{$school->service_end_date}}</strong></li>
                                                                        <li class="list-group-item text-capitalize">Active Time Duration: <strong>{{$school->service_duration}} Months</strong></li>
                                                                      </ul>
                                                                </div>
                                                            </div>
                                                            <hr class="dark horizontal py-0">
                                                            <form action="" method="POST" novalidate="" class="needs-validation" enctype="multipart/form-data" role="form">

                                                                <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-success">Save changes</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                           @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
