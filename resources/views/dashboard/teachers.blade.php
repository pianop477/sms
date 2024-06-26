@extends('SRTDashboard.frame')
@section('content')
<div class="col-lg-12">
    <div class="row">
        @if ($myClass->isNotEmpty())
            @php
                $maleCount = 0;
                $femaleCount = 0;

                foreach ($myClass as $class) {
                    $maleCount += App\Models\Student::where('class_id', $class->class_id)
                        ->where('group', $class->group)
                        ->where('gender', 'male')
                        ->count();

                    $femaleCount += App\Models\Student::where('class_id', $class->class_id)
                        ->where('group', $class->group)
                        ->where('gender', 'female')
                        ->count();
                }
            @endphp

            {{-- first card --}}
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
                            <h2>{{ $maleCount + $femaleCount }}</h2>
                            <ul>
                                <li><span class="text-white">Male: <strong>{{$maleCount}}</strong></span></li>
                                <li><span class="text-white">Female: <strong>{{$femaleCount}}</strong></span></li>
                            </ul>
                        </div>
                        <canvas id="" height="50"></canvas>
                    </div>
                </div>
            </div>
        @endif
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
        @if ($myClass->isNotEmpty())
            <div class="col-lg-6 mt-0">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title"> My Attendance Class</h4>
                        <div class="table-responsive-md">
                            <table class="table">
                                <thead>
                                    <tr class="text-capitalize">
                                        <th>Class</th>
                                        <th>Stream</th>
                                        <th>Action</th>
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
                                                            <i class="ti-settings"></i> Report
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
        @endif

        {{-- another table lies here --}}
        <div class="col-lg-6 mt-0">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10"><h4 class="header-title"> My Courses</h4></div>
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
                                    <th>Code</th>
                                    <th>Class</th>
                                    <th>Action</th>
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
                                        <td class="text-uppercase">{{$course->course_code}}</td>
                                        <td class="text-uppercase">{{$course->class_code}}</td>
                                        <td>
                                            @if ($course->status == 1)
                                            <ul class="d-flex justify-content-center">
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop" type="button" class="btn btn-success btn-xs p-1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        ACTION
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        <a class="dropdown-item" href="{{route('courses.edit', $course->id)}}"><i class="ti-pencil"></i> Edit</a>
                                                        <a class="dropdown-item" href="{{route('score.prepare.form', $course->id)}}"><i class="ti-file"></i> Score</a>
                                                        {{-- <a class="dropdown-item" href="{{route('courses.destroy', $course->id)}}" onclick="return confirm('Are you sure you want to delete this course permanently?')"><i class="ti-close"></i> Close</a> --}}
                                                    </div>
                                                </div>
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
</div>
@endsection

