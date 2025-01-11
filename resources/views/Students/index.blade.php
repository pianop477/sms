@extends('SRTDashboard.frame')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h4 class="header-title text-uppercase">{{$classId->class_name. ' Students list - ('.$classId->class_code.')'}}</h4>
                    </div>
                    @if ($students->isNotEmpty())
                        <div class="col-2">
                                <!-- Button trigger modal -->
                            <button type="button" class="btn btn-info btn-xs float-right" data-toggle="modal" data-target="#exampleModal">
                                <i class="fas fa-exchange-alt"></i> Promotes
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Promote Students to the Next class</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    <div class="modal-body">
                                        <span class="text-danger text-capitalize">select class you want to promote students</span>
                                        <form class="needs-validation" novalidate="" action="{{route('promote.student.class', $classId->id)}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="validationCustom01">Class name</label>
                                                    <select name="class_id" id="" class="form-control" required>
                                                        <option value="">--Select Class--</option>
                                                        @if ($classes->isEmpty())
                                                            <option value="" class="text-danger">No more classes found</option>
                                                            <option value="0" class="text-success font-weight-bold" style="font-size: 20px">🎓 Graduate Class 🎉</option>
                                                        @else
                                                            @foreach ($classes as $class)
                                                                <option value="{{$class->id}}" class="text-capitalize">{{$class->class_name}}</option>
                                                            @endforeach
                                                            <option value="0" class="text-success font-weight-bold" style="font-size: 20px">🎓 Graduate Class 🎉</option>
                                                        @endif
                                                    </select>
                                                    @error('name')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to promote this class?')">Upgrade</button>
                                        </div>
                                    </form>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <a href="{{route('export.student.pdf', $classId->id)}}" target="_blank" class="float-right btn btn-primary btn-xs"><i class="fas fa-cloud-arrow-down"></i> Export</a>
                        </div>
                        @endif
                    <div class="col-1">
                        <a href="{{route('classes.list', $classId->id)}}" class="float-right"><i class="fas fa-arrow-circle-left"></i> Back</a>
                    </div>
                    @if (Route::has('student.create'))
                    <div class="col-1">
                        <a type="#" class="btn p-0 float-right btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-plus-circle"></i> New
                        </a>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-uppercase">{{$classId->class_name}} Students Registration Form</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('student.store', $classId->id)}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">First name</label>
                                                    <input type="text" required name="fname" class="form-control" id="validationCustom01" placeholder="First name" value="{{old('fname')}}" required="">
                                                    @error('fname')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Middle name</label>
                                                    <input type="text" required name="middle" class="form-control" id="validationCustom02" placeholder="Middle name" required="" value="{{old('middle')}}">
                                                    @error('middle')
                                                    <div class="invalid-feedback">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Last name</label>
                                                    <input type="text" required name="lname" class="form-control" id="validationCustom02" placeholder="Last name" required="" value="{{old('lname')}}">
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
                                                    <select name="gender" required id="validationCustom01" class="form-control text-capitalize" required>
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
                                                    <input type="date" required id="customDatePicker" name="dob" class="form-control" id="validationCustom02" placeholder="Enter your birth date" required="" value="{{old('dob')}}">
                                                    @error('dob')
                                                    <div class="invalid-feedback">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Parent/Guardian Name</label>
                                                    <div class="input-group">
                                                        <select name="parent" id="parentSelect" class="form-control select2 text-capitalize" required>
                                                            <option value="">-- Select Parent --</option>
                                                            @if ($parents->isEmpty())
                                                                <option value="" disabled class="text-danger text-capitalize">No parents records found</option>
                                                            @else
                                                                @foreach ($parents as $parent)
                                                                    <option value="{{$parent->id}}" class="">
                                                                        {{$parent->first_name . ' ' . $parent->last_name}}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('parent')
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
                                                    <input type="text" name="group" required id="validationCustomUsername" class="form-control text-uppercase" placeholder="Enter Group A, B or C" id="validationCustom02" value="{{old('dob')}}" required>
                                                    @error('group')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Bus Number:<small class="text-sm text-muted">Select if using School bus</small></label>
                                                    <div class="input-group">
                                                        <select name="driver" id="validationCustomUsername" class="form-control text-uppercase">
                                                            <option value="">-- select bus number --</option>
                                                            @if ($buses->isEmpty())
                                                                <option value="" class="text-danger text-capitalize" disabled>No school bus records found</option>
                                                            @else
                                                                @foreach ($buses as $bus)
                                                                    <option value="{{$bus->id}}">Bus No. {{$bus->bus_no}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('driver')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Photo :<small class="text-sm text-danger">(Optional)</small></label>
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
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Save</button>
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
                                    <th scope="col" class="text-center">Admission No.</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Middle Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col" class="text-center">Gender</th>
                                    <th scope="col">Date of Birth</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase text-center">{{$student->school_reg_no}}/{{$student->admission_number}}</td>
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
                                                    <a href="{{route('students.modify', $student->id)}}"><i class="ti-pencil text-primary"></i></a>
                                                </li>
                                                <li class="mr-3">
                                                    <a href="{{route('Students.show', $student->id)}}"><i class="ti-eye text-secondary"></i></a>
                                                </li>

                                                <li>
                                                    <form action="{{route('Students.destroy', $student->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button class="btn btn-link p-0" onclick="return confirm('Are you sure you want to delete {{strtoupper($student->first_name)}} {{strtoupper($student->middle_name)}} {{strtoupper($student->last_name)}} Permanently?')">
                                                            <i class="ti-trash text-danger"></i>
                                                        </button>
                                                    </form>
                                                </li>
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
<script>
    $(document).ready(function() {
            $('#parentSelect').select2({
                placeholder: "-- Select Parent --",
                allowClear: true,
            });
        });

</script>
@endsection
