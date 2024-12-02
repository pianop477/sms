@extends('SRTDashboard.frame')
@section('content')
<div class="col-lg-12">
    {{-- first argument======================================================== --}}
    @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2)
        {{-- school head teacher panel start here --}}
            <div class="row">
                <div class="col-md-4 mt-5 mb-3">
                    <div class="card" style="background: #e176a6;">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-tie"></i> Teachers</div>
                                <h2 class="text-white">
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
                    <div class="card" style="background: #c84fe0;">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-shield"></i> Parents</div>
                                <h2 class="text-white">
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
                    <div class="card" style="background: #098ddf;">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Students</div>
                                <h2 class="text-white">
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
                    <div class="card" style="background: #9fbc71;">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> Open Courses</div>
                                <h2 class="text-white">
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
                    <div class="card" style="background: #bf950a;">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-blackboard"></i> Classes</div>
                                <h2 class="text-white">
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
                    <div class="card" style="background: #329688;">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-bus"></i> School Buses</div>
                                <h2 class="text-white">
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
                    <div class="card" style="background: #e176a6">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-tie"></i> Teachers</div>
                                <h2 class="text-white">
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
                    <div class="card" style="background: #098ddf">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Students</div>
                                <h2 class="text-white">
                                    @if(count($students) > 999)
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
                    <div class="card" style="background: #9fbc71">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> Open Courses</div>
                                <h2 class="text-white">
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
                    <div class="card" style="background:#bf950a">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-blackboard"></i> Classes</div>
                                <h2 class="text-white">
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
                    <div class="card" style="background: #b14fbe">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> My Course</div>
                                <h2 class="text-white">
                                    @if ( $courses->where('status', 1)->count() > 2)
                                        3+
                                    @else
                                        {{ $courses->where('status', 1)->count() }}
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
                    </div>
                    <div class="table-responsive-md">
                        <table class="table table-hover text-center" id="">
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
                                                No any subject assigned for you
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
                                                    <a href="{{route('score.prepare.form', $course->id)}}" class="text-success" onclick="return confirm('Do you want to enter Examination score in {{strtoupper($course->course_name)}} subject?')"><i class="ti-pencil-alt"></i></a>
                                                </li>
                                                <li class="mr-3">
                                                    <a href="{{ route('results_byCourse', $course->id) }}" onclick="return confirm('Do you want to view results in {{strtoupper($course->course_name)}} subject?')">
                                                        <i class="ti-eye text-primary"></i>
                                                    </a>
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
                    <div class="card" style="background:#098ddf">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-blackboard"></i> My Class</div>
                                <h2 class="text-white">{{ $myClass->count() }}</h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>

                {{-- second card --}}
                <div class="col-md-4 mt-md-5 mb-3">
                    <div class="card" style="background:#c84fe0">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Student</div>
                                @foreach ($classData as $data )
                                <h2 class="text-white">{{$data['maleCount'] + $data['femaleCount']}}</h2>
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
                    <div class="card" style="background: #bf950a">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> My Course</div>
                                <h2 class="text-white">
                                    @if ($courses->where('status', 1)->count() > 2)
                                        3+
                                    @else
                                        {{$courses->where('status', 1)->count()}}
                                    @endif
                                </h2>
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
                                <table class="table table-hover text-center">
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
                        </div>
                        <div class="table-responsive-md">
                            <table class="table table-hover text-center" id="">
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
                                                    No any subject assigned for you
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
                                                        <a href="{{route('score.prepare.form', $course->id)}}" class="text-success" onclick="return confirm('Do you want to enter examination score in {{strtoupper($course->course_name)}} subject?')"><i class="ti-pencil-alt"></i></a>
                                                    </li>
                                                    <li class="mr-3">
                                                        <a href="{{ route('results_byCourse', $course->id) }}" onclick="return confirm('Do you want to view results in {{strtoupper($course->course_name)}} subject?')">
                                                            <i class="ti-eye text-primary"></i>
                                                        </a>
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
                <div class="card" style="background: #bf950a">
                    <div class="">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="ti-book"></i> My Course</div>
                            <h2 class="text-white">
                                @if ($courses->where('status', 1)->count() > 2)
                                    3+
                                @else
                                    {{$courses->where('status', 1)->count()}}
                                @endif
                            </h2>
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
                        </div>
                        <div class="table-responsive-md">
                            <table class="table table-hover text-center" id="">
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
                                                    No any subject assigned for you
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
                                                        <a href="{{route('score.prepare.form', $course->id)}}" class="text-success" onclick="return confirm('Do you want to enter examination score in {{strtoupper($course->course_name)}} subject?')"><i class="ti-pencil-alt"></i></a>
                                                    </li>
                                                    <li class="mr-3">
                                                        <a href="{{ route('results_byCourse', $course->id) }}" onclick="return confirm('Do you want to view results in {{strtoupper($course->course_name)}} subject?')">
                                                            <i class="ti-eye text-primary"></i>
                                                        </a>
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

