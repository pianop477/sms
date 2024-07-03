@extends('SRTDashboard.frame')

@section('content')
<div class="col-lg-12">
    <div class="row">
        <div class="col-md-4 mt-md-5 mb-3">
            <div class="card">
                <div class="seo-fact sbg2">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="fas fa-user-graduate"></i> My Children</div>
                        <h2>{{count($students)}}</h2>
                    </div>
                    <canvas id="seolinechart2" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-8 mt-md-5 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <h4 class="header-title text-uppercase">My Children List</h4>
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fa-solid fa-user-plus text-secondary" style="font-size: 2rem"></i>
                            </button>
                            <div class="modal fade bd-example-modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-uppercase">Students Registration Form</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="needs-validation" novalidate="" action="{{route('register.student')}}" method="POST" enctype="multipart/form-data">
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
                                                        <label for="validationCustom02">Middle name</label>
                                                        <input type="text" name="middle" class="form-control" id="validationCustom02" placeholder="Middle name" required="" value="{{old('middle')}}">
                                                        @error('middle')
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
                                                        <label for="validationCustom02">Date of Birth</label>
                                                        <input type="date" name="dob" class="form-control" id="validationCustom02" placeholder="Enter Birth Date" required="" value="{{old('dob')}}">
                                                        @error('dob')
                                                        <div class="invalid-feedback">
                                                           {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Class</label>
                                                        <div class="input-group">
                                                            <select name="grade" id="" class="form-control text-uppercase" required>
                                                                <option value="">--select grade--</option>
                                                                @foreach ($classes as $class )
                                                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('grade')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Class Group</label>
                                                        <input type="text" name="group" id="validationCustomUsername" class="form-control" placeholder="Enter Group A, B or C" id="validationCustom02" value="{{old('group')}}" required>
                                                        @error('group')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Driver Name :<small class="text-sm text-muted">Select if using School bus</small></label>
                                                        <div class="input-group">
                                                            <select name="driver" id="" class="form-control text-uppercase">
                                                                <option value="">--Select bus driver--</option>
                                                                @foreach ($buses as $bus )
                                                                <option value="{{$bus->id}}">{{$bus->driver_name}}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('driver')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                        <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Passport Size Photo :<small class="text-sm text-danger"> (Optional)</small></label>
                                                        <div class="input-group">
                                                            <input type="file" name="image" id="validationCustomUsername" class="form-control" value="{{old('image')}}">
                                                            @error('image')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
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
                    {{-- table for students lies here --}}
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table" id="myTable">
                                <thead class="text-uppercase bg-dark">
                                    <tr class="text-white">
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Class</th>
                                        <th scope="col">action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student )
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td class="text-uppercase">
                                                <a href="{{route('Students.show', $student->id)}}">{{$student->first_name. ' '.$student->middle_name.' ' .$student->last_name}}</a>
                                            </td>
                                            <td class="text-uppercase">{{$student->gender[0]}}</td>
                                            <td class="text-uppercase">{{$student->class_code}}</td>
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop" type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            ACTION
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                            <a class="dropdown-item" href="{{route('students.modify', $student->id)}}"><i class="ti-pencil"></i> Edit</a>
                                                            <a class="dropdown-item" href="{{route('attendance.byYear', $student->id)}}"><i class="fa fa-list-check"></i> Attendance</a>
                                                            <a class="dropdown-item" href="{{route('results.index', $student->id)}}"><i class="ti-file"></i> Results</a>
                                                        </div>
                                                    </div>
                                                </ul>
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
</div>
<div class="col-lg-12">

</div>
<hr class="dark horizontal py-0">
@endsection
