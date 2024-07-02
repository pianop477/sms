@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase">{{$classId->class_name. ' Class'}}</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{route('classes.list', $classId->id)}}"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                    </div>
                    @if (Route::has('student.create'))
                    <div class="col-2">
                        <a type="#" class="btn p-0" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-plus-circle text-secondary" style="font-size:2rem;"></i>
                        </a>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-uppercase">{{$classId->class_name}} class Registration Form</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('student.store', $classId->id)}}" method="POST" enctype="multipart/form-data">
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
                                                    <input type="date" id="customDatePicker" name="dob" class="form-control" id="validationCustom02" placeholder="Enter your birth date" required="" value="{{old('dob')}}">
                                                    @error('dob')
                                                    <div class="invalid-feedback">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Parent Name/Guardian</label>
                                                    <div class="input-group">
                                                        <select name="parent" id="validationCustomUsername" class="form-control text-uppercase" required>
                                                            <option value="">-- Select Parent --</option>
                                                            @foreach ($parents as $parent)
                                                                <option value="{{$parent->id}}" class="text-uppercase">{{$parent->first_name. ' '. $parent->last_name}}</option>
                                                            @endforeach
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
                                                    <label for="validationCustom01">Class Group</label>
                                                    <input type="text" name="group" id="validationCustomUsername" class="form-control" placeholder="Enter Group A, B or C" id="validationCustom02" value="{{old('dob')}}" required>
                                                    @error('dob')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Driver Name :<small class="text-sm text-muted">Select if using School bus</small></label>
                                                    <div class="input-group">
                                                        <select name="driver" id="validationCustomUsername" class="form-control text-uppercase">
                                                            <option value="">-- select driver --</option>
                                                            @foreach ($buses as $bus)
                                                            <option value="{{$bus->id}}">{{$bus->driver_name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('street')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Student Photo :<small class="text-sm text-muted">Must not exceed 2 MB</small></label>
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
                    @endif
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Middle Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Date of Birth</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">{{$student->first_name}}</td>
                                        <td class="text-uppercase">{{$student->middle_name}}</td>
                                        <td class="text-uppercase">{{$student->last_name}}</td>
                                        <td class="text-center text-uppercase">{{$student->gender[0]}}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($student->dob)->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <a href="{{route('Students.show', $student->id)}}"><i class="ti-eye text-secondary"></i></a>
                                                </li>
                                                <li><a href="{{route('Students.destroy', $student->id)}}" onclick="return confirm('Are you sure you want to delete {{strtoupper($student->first_name)}} {{strtoupper($student->middle_name)}} {{strtoupper($student->last_name)}} Permanently?')" class="text-danger"><i class="ti-trash"></i></a></li>
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
@endsection
