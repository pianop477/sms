@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-uppercase">Teachers list</h4>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-circle-plus text-secondary" style="font-size: 2rem;"></i>
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Teachers Registration Form</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('Teachers.store')}}" method="POST" enctype="multipart/form-data">
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
                                                        <input type="email" name="email" class="form-control" id="validationCustomUsername" placeholder="Email ID" aria-describedby="inputGroupPrepend" required="" value="{{old('email')}}">
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
                                                    <label for="validationCustomUsername">Qualification</label>
                                                    <div class="input-group">
                                                        <select name="qualification" id="validationCustomUsername" class="form-control text-uppercase" required>
                                                            <option value="">-- Select Qualification --</option>
                                                            <option value="1">Masters</option>
                                                            <option value="2">Degree</option>
                                                            <option value="3">Diploma</option>
                                                            <option value="4">Certificate</option>
                                                        </select>
                                                        @error('school')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Date of Birth</label>
                                                    <input type="date" name="dob" class="form-control" id="validationCustom02" value="{{old('dob')}}" required>
                                                    @error('dob')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Year Joined</label>
                                                    <select name="joined" id="" class="form-control" required>
                                                        <option value="">-- Select Year --</option>
                                                        @for ($year = date('Y'); $year >= 2000; $year--)
                                                            <option value="{{ $year }}">{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                    @error('joined')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Street/Village</label>
                                                    <div class="input-group">
                                                        <input type="text" name="street" class="form-control" id="validationCustom02" value="{{old('street')}}" placeholder="Street Address" required>
                                                        @error('street')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <input type="hidden" name="usertype" value="3">
                                                    <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">
                                                    <input type="hidden" name="password" value="shule123">
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Register</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Joined</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teachers as $teacher )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$teacher->first_name. ' '. $teacher->last_name}}</td>
                                        <td class="text-capitalize">{{$teacher->gender[0]}}</td>
                                        <td>{{$teacher->phone}}</td>
                                        <td>{{$teacher->email}}</td>
                                        <td>{{$teacher->joined}}</td>
                                        <td>
                                            @if ($teacher->status ==1)
                                                <span class="badge bg-success text-white">{{_('Active')}}</span>
                                                @else
                                                <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                            @endif
                                        </td>
                                        @if ($teacher->status == 1)
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3"><a href="{{route('Teachers.show.profile', $teacher->id)}}" class="text-primary"><i class="fa fa-eye"></i></a></li>
                                                <li class="mr-3">
                                                    <form action="{{route('update.teacher.status', $teacher->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Block {{strtoupper($teacher->first_name)}} {{strtoupper($teacher->last_name)}}?')"><i class="fas fa-ban text-info"></i></button>
                                                    </form>
                                                </li>
                                                <li><a href="{{route('Teachers.remove', $teacher->id)}}" onclick="return confirm('Are you sure you want to Delete {{ strtoupper($teacher->first_name) }} {{ strtoupper($teacher->last_name) }} Permanently?')" class="text-danger"><i class="ti-trash"></i></a></li>
                                            </ul>
                                        </td>
                                        @else
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <form action="{{route('teachers.restore', $teacher->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($teacher->first_name)}} {{strtoupper($teacher->last_name)}}?')"><i class="ti-share-alt text-success"></i></button>
                                                    </form>
                                                </li>
                                                <li><a href="{{route('Teachers.remove', $teacher->id)}}" onclick="return confirm('Are you sure you want to Delete {{ strtoupper($teacher->first_name) }} {{ strtoupper($teacher->last_name) }} Permanently?')" class="text-danger"><i class="ti-trash"></i></a></li>
                                            </ul>
                                        </td>
                                        @endif
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
