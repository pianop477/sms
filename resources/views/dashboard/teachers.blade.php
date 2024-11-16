@extends('SRTDashboard.frame')
@section('content')
<div class="col-lg-12">
    {{-- first argument======================================================== --}}
    @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2)
        {{-- school head teacher panel start here --}}
            <div class="row">
                <div class="col-md-4 mt-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg1">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-tie"></i> Teachers</div>
                                <h2>
                                    @if (count($teachers) > 29)
                                        30+
                                    @else
                                        {{count($teachers)}}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-md-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg2">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-shield"></i> Parents</div>
                                <h2>
                                    @if (count($parents) > 999)
                                        1000+
                                    @else
                                        {{count($parents)}}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-md-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Students</div>
                                <h2>
                                    @if (count($students) > 999)
                                        1000+
                                    @else
                                        {{count($students)}}
                                    @endif
                                </h2>
                                <ul>
                                    <li><span class="text-white">M: <strong>
                                        @if ($totalMaleStudents > 99)
                                            100+
                                        @else
                                            {{$totalMaleStudents}}
                                        @endif
                                    </strong></span></li>
                                    <li><span class="text-white">F: <strong>
                                        @if ($totalFemaleStudents > 99)
                                            100+
                                        @else
                                            {{$totalFemaleStudents}}
                                        @endif
                                    </strong></span></li>
                                </ul>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4 mt-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg2">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> Open Courses</div>
                                <h2>
                                    @if (count($subjects) > 19)
                                        20+
                                    @else
                                        {{count($subjects)}}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-md-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg1">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-blackboard"></i> Classes</div>
                                <h2>
                                    @if (count($classes) > 9)
                                        10+
                                    @else
                                        {{count($classes)}}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-md-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg4">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-bus"></i> School Buses</div>
                                <h2>
                                    @if (count($buses) > 19)
                                        20+
                                    @else
                                        {{count($buses)}}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- school head teacher panel end here --}}
        {{-- first argument end here ============================================= --}}
    @elseif (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 3)
        {{-- second argument start here =========================================== --}}
        {{-- academic teacher panel start here =================== --}}
            <div class="row">
                <div class="col-md-4 mt-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg1">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-tie"></i> Teachers</div>
                                <h2>
                                    @if (count($teachers) > 29)
                                        30+
                                    @else
                                        {{count($teachers)}}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-md-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Students</div>
                                <h2>
                                    @if count($students) > 999)
                                        1000+
                                    @else
                                        {{count($students)}}
                                    @endif
                                </h2>
                                <ul>
                                    <li><span class="text-white">M: <strong>
                                        @if ($totalMaleStudents > 99)
                                            100+
                                        @else
                                            {{$totalMaleStudents}}
                                        @endif
                                    </strong></span></li>
                                    <li><span class="text-white">F: <strong>
                                        @if ($totalFemaleStudents > 99)
                                            100+
                                        @else
                                            {{$totalFemaleStudents}}
                                        @endif
                                    </strong></span></li>
                                </ul>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg2">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> Open Courses</div>
                                <h2>
                                    @if (count($subjects) > 19)
                                        20+
                                    @else
                                        {{count($subjects)}}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4 mt-md-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg1">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-blackboard"></i> Classes</div>
                                <h2>
                                    @if (count($classes) > 9)
                                        10+
                                    @else
                                        {{count($classes)}}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-5 mb-3">
                    <div class="card bg-secondary">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> My Course</div>
                                <h2 class="text-white">
                                    @if ( count($courses) > 2)
                                        3+
                                    @else
                                        {{ count($courses) }}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        {{-- academic panel end here =========================================== --}}
        {{-- academic teacher its courses records start here ====================== --}}
        <div class="col-lg-10 mt-0">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10"><h4 class="header-title text-uppercase text-center"> My Courses</h4></div>
                        <div class="col-2">
                            <button type="button" class="btn btn-xs mb-3 btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-circle-plus text-secondary" style="font-size:1.5rem;"></i>
                            </button>
                            <div class="modal fade bd-example-modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Register New Courses</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="needs-validation" novalidate="" action="{{route('courses.store')}}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Course Name</label>
                                                        <input type="text" name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="Course Name" value="{{old('name')}}" required="">
                                                        @error('name')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Course Code</label>
                                                        <input type="text" name="code" class="form-control text-uppercase" id="validationCustom02" placeholder="Course Code" required="" value="{{old('code')}}">
                                                        @error('code')
                                                        <div class="invalid-feedback">
                                                           {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Class</label>
                                                        <select name="class" id="validationCustom01" class="form-control text-uppercase" required>
                                                            <option value="">-- select class --</option>
                                                            @foreach ($classes as $class)
                                                            <option value="{{$class->id}}">{{$class->class_name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('class')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- <input type="hidden" name="teacher_id" value="{{$teacher->id}}"> --}}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive-md">
                        <table class="table" id="">
                            <thead>
                                <tr class="text-capitalize">
                                    <th>#</th>
                                    <th>Course name</th>
                                    <th>Class</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($courses->isEmpty())
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-warning text-center">
                                                You dont have any subject/course
                                            </div>
                                        </td>
                                    </tr>
                                    @else
                                    @foreach ($courses as $course )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">
                                            {{$course->course_name}}
                                        </td>
                                        <td class="text-uppercase">{{$course->class_code}}</td>
                                        <td>
                                            @if ($course->status == 1)
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <a href="{{route('courses.edit', $course->id)}}" class="text-primary" onclick="return confirm('Are you sure you want to Edit this Course?')"><i class="ti-pencil"></i></a>
                                                </li>
                                                <li class="mr-3">
                                                    <a href="{{route('score.prepare.form', $course->id)}}" class="text-success" onclick="return confirm('Are you sure you want to VIEW or ENTER results?')"><i class="ti-pencil-alt"></i></a>
                                                </li>
                                                <li>
                                                    <form action="{{route('courses.remove', $course->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to remove {{strtoupper($course->course_name)}} course?')">
                                                            <i class="ti-trash text-danger"></i>
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                            @else
                                                <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- academic teacher courses records end here============================= --}}
        {{-- end of second argument ==================================================== --}}
    @elseif (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 4)
        {{-- third argument ============================================================ --}}
        {{-- class teacher panel start here ======================================= --}}
        <div class="row">
                <div class="col-md-4 mt-md-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg1">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-blackboard"></i> My Class</div>
                                <h2>{{ $myClass->count() }}</h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>

                {{-- second card --}}
                <div class="col-md-4 mt-md-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Student</div>
                                @foreach ($classData as $data )
                                <h2>{{$data['maleCount'] + $data['femaleCount']}}</h2>
                                <ul>
                                    <li><span class="text-white">Male: <strong>{{$data['maleCount']}}</strong></span></li>
                                    <li><span class="text-white">Female: <strong></strong>{{$data['femaleCount']}}</span></li>
                                </ul>
                                @endforeach
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-5 mb-3">
                    <div class="card">
                        <div class="seo-fact sbg2">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> My Course</div>
                                <h2>{{ count($courses) }}</h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
        </div>
        <div class="row">
                <div class="col-lg-6 mt-0">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title text-center text-uppercase"> My Attendance Class</h4>
                            <div class="table-responsive-md">
                                <table class="table">
                                    <thead>
                                        <tr class="text-capitalize">
                                            <th>Class</th>
                                            <th>Stream</th>
                                            <th class="">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($myClass as $class)
                                            <tr class="">
                                                <td class="text-uppercase">
                                                    <a href="{{ route('get.student.list', $class->id) }}">{{ $class->class_name }}</a>
                                                </td>
                                                <td class="text-uppercase text-center">{{ $class->group }}</td>
                                                <td>
                                                    <ul class="d-flex">
                                                        <li class="">
                                                            <a href="{{ route('attendance.get.form', $class->id) }}" class="btn btn-info btn-xs p-1">
                                                                <i class="ti-settings"> Generate Report</i>
                                                            </a>
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
            {{-- another table lies here --}}
            <div class="col-lg-6 mt-0">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-10"><h4 class="header-title text-center text-uppercase"> My Courses</h4></div>
                            <div class="col-2">
                                <button type="button" class="btn btn-xs mb-3 btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-circle-plus text-secondary" style="font-size:1.5rem;"></i>
                                </button>
                                <div class="modal fade bd-example-modal-lg">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Register New Courses</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <form class="needs-validation" novalidate="" action="{{route('courses.store')}}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom01">Course Name</label>
                                                            <input type="text" name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="Course Name" value="{{old('name')}}" required="">
                                                            @error('name')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom02">Course Code</label>
                                                            <input type="text" name="code" class="form-control text-uppercase" id="validationCustom02" placeholder="Course Code" required="" value="{{old('code')}}">
                                                            @error('code')
                                                            <div class="invalid-feedback">
                                                               {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom01">Class</label>
                                                            <select name="class" id="validationCustom01" class="form-control text-uppercase" required>
                                                                <option value="">-- select class --</option>
                                                                @foreach ($classes as $class)
                                                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('class')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- <input type="hidden" name="teacher_id" value="{{$teacher->id}}"> --}}
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive-md">
                            <table class="table" id="">
                                <thead>
                                    <tr class="text-capitalize">
                                        <th>#</th>
                                        <th>Course name</th>
                                        <th>Class</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($courses->isEmpty())
                                        <tr>
                                            <td colspan="5">
                                                <div class="alert alert-warning text-center">
                                                    You dont have any subject/course
                                                </div>
                                            </td>
                                        </tr>
                                        @else
                                        @foreach ($courses as $course )
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td class="text-uppercase">
                                                {{$course->course_name}}
                                            </td>
                                            <td class="text-uppercase">{{$course->class_code}}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <a href="{{route('courses.edit', $course->id)}}" class="text-primary" onclick="return confirm('Are you sure you want to Edit this Course?')"><i class="ti-pencil"></i></a>
                                                    </li>
                                                    <li class="mr-3">
                                                        <a href="{{route('score.prepare.form', $course->id)}}" class="text-success" onclick="return confirm('Are you sure you want to VIEW or ENTER results?')"><i class="ti-pencil-alt"></i></a>
                                                    </li>
                                                    <li>
                                                        <form action="{{route('courses.remove', $course->id)}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to remove {{strtoupper($course->course_name)}} course?')">
                                                                <i class="ti-trash text-danger"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                                @elseif ($course->status == 0)
                                                    <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- class teacher panel end here ========================================= --}}
        {{-- end of third argument ====================================================== --}}
    @else
        {{-- fourth argument start here ================================================ --}}
        {{-- normal teacher panel start here ========================================== --}}
        <div class="row">
            <div class="col-md-4 mt-5 mb-3">
                <div class="card">
                    <div class="seo-fact sbg2">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="ti-book"></i> My Course</div>
                            <h2>{{ count($courses) }}</h2>
                        </div>
                        <canvas id="" height="50"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mt-0">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-10"><h4 class="header-title text-center text-uppercase"> My Courses</h4></div>
                            <div class="col-2">
                                <button type="button" class="btn btn-xs mb-3 btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-circle-plus text-secondary" style="font-size:1.5rem;"></i>
                                </button>
                                <div class="modal fade bd-example-modal-lg">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Register New Courses</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <form class="needs-validation" novalidate="" action="{{route('courses.store')}}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom01">Course Name</label>
                                                            <input type="text" name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="Course Name" value="{{old('name')}}" required="">
                                                            @error('name')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom02">Course Code</label>
                                                            <input type="text" name="code" class="form-control text-uppercase" id="validationCustom02" placeholder="Course Code" required="" value="{{old('code')}}">
                                                            @error('code')
                                                            <div class="invalid-feedback">
                                                               {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom01">Class</label>
                                                            <select name="class" id="validationCustom01" class="form-control text-uppercase" required>
                                                                <option value="">-- select class --</option>
                                                                @foreach ($classes as $class)
                                                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('class')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- <input type="hidden" name="teacher_id" value="{{$teacher->id}}"> --}}
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive-md">
                            <table class="table" id="">
                                <thead>
                                    <tr class="text-capitalize">
                                        <th>#</th>
                                        <th>Course name</th>
                                        <th>Class</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($courses->isEmpty())
                                        <tr>
                                            <td colspan="5">
                                                <div class="alert alert-warning text-center">
                                                    You dont have any subject/course
                                                </div>
                                            </td>
                                        </tr>
                                        @else
                                        @foreach ($courses as $course )
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td class="text-uppercase">
                                                {{$course->course_name}}
                                            </td>
                                            <td class="text-uppercase">{{$course->class_code}}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <a href="{{route('courses.edit', $course->id)}}" class="text-primary" onclick="return confirm('Are you sure you want to Edit this Course?')"><i class="ti-pencil"></i></a>
                                                    </li>
                                                    <li class="mr-3">
                                                        <a href="{{route('score.prepare.form', $course->id)}}" class="text-success" onclick="return confirm('Are you sure you want to VIEW or ENTER results?')"><i class="ti-pencil-alt"></i></a>
                                                    </li>
                                                    <li>
                                                        <form action="{{route('courses.remove', $course->id)}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to remove {{strtoupper($course->course_name)}} course?')">
                                                                <i class="ti-trash text-danger"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                                @else
                                                    <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    {{-- normal teacher panel end here ============================================ --}}
    @endif
    {{-- end of argument end here ================================================== --}}
</div>
@endsection

