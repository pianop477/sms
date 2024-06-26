
@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">User Registration Form</h4>
            <form class="needs-validation" novalidate="" action="{{route('manager.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">First name</label>
                        <input type="text" name="fname" class="form-control" id="validationCustom01" placeholder="First name" value="{{old('fname')}}" required="">
                        @error('fname')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Last name</label>
                        <input type="text" name="lname" class="form-control" id="validationCustom02" placeholder="Last name" required="" value="{{old('lname')}}">
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
                        <label for="validationCustomUsername">Institution Name</label>
                        <div class="input-group">
                            <select name="school" id="validationCustomUsername" class="form-control text-uppercase">
                                <option value="">-- Select Institution --</option>
                                @foreach ($schools as $school)
                                    <option value="{{$school->id}}">{{$school->school_name}}</option>
                                @endforeach
                            </select>
                            @error('school')
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                        <input type="hidden" name="usertype" value="2">
                        <input type="hidden" name="password" value="shule@123">
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">Submit</button>
            </form>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-center text-uppercase">managers list</h4>
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table table-hover progress-table" id="myTable">
                                <thead class="text-uppercase">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">School Manager</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($managers as $manager )
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td class="text-capitalize">
                                                {{$manager->first_name. ' '. $manager->last_name}}
                                            </td>
                                            <td class="text-capitalize">{{$manager->gender[0]}}</td>
                                            <td> {{$manager->phone}}</td>
                                            <td>{{$manager->email}}</td>
                                            <td>
                                                @if ($manager->status == 1)
                                                <span class="status-p bg-success">Active</span>
                                                @else
                                                <span class="status-p bg-danger">Blocked</span>
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
    </div>
@endsection
