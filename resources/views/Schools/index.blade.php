
@extends('SRTDashboard.frame')
@section('content')
    <div class="col-12">
        <div class="card mt-5">
            <div class="card-title">
                <h4 class="text-uppercase text-center">Register school school</h4>
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
                                <input type="text" required name="postal" class="form-control" id="userInput validationCustom01" onblur="addPrefix()" placeholder="P.O Box 123" value="{{old('postal')}}" required="">
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
                                <input type="hidden" name="usertype" value="2">
                                <input type="hidden" name="password" value="shule@2024">
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
                                    <th scope="col">Registration No</th>
                                    <th scope="col">Address</th>
                                    <th class="text-center">School Logo</th>
                                    <th scope="col">status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schools as $school )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">
                                            {{$school->school_name}}
                                        </td>
                                        <td class="text-uppercase">{{$school->school_reg_no}}</td>
                                        <td class="text-uppercase">P.O Box {{$school->postal_address}} - {{$school->postal_name}}</td>
                                        <td class="text-center">
                                            <img src="{{asset('assets/img/logo/' .$school->logo)}}" alt="" class="profile-img rounded-circle" style="width: 50px; object-fit: cover;">
                                        </td>
                                        <td>
                                            @if ($school->status == 1)
                                            <span class="status-p bg-success">Open</span>
                                            @else
                                            <span class="status-p bg-secondary">Closed</span>
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
