@extends('SRTDashboard.frame')

@section('content')
<div class="col-lg-12">
    <div class="row">
        @php
            use App\Models\Student;
            use Illuminate\Support\Facades\Auth;

            $maleStudents = Student::where('gender', 'male')
                                    ->where('status', 1)
                                    ->where('school_id', Auth::user()->school_id)
                                    ->count();

            $femaleStudents = Student::where('gender', 'female')
                                    ->where('status', 1)
                                    ->where('school_id', Auth::user()->school_id)
                                    ->count();
        @endphp

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
            <div class="card" style="background: #c84fe0">
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
            <div class="card" style="background: #098ddf">
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
                            <li><span class="text-white text-sm">M:
                                <strong>
                                    @if ($maleStudents > 99)
                                        100+
                                    @else
                                        {{$maleStudents}}
                                    @endif
                                </strong></span></li>
                            <li><span class="text-white text-sm">F:
                                <strong>
                                    @if ($femaleStudents > 99)
                                        100+
                                    @else
                                        {{$femaleStudents}}
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
        <div class="col-md-4 mt-md-5 mb-3">
            <div class="card" style="background: #bf950a">
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
            <div class="card" style="background: #329688">
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
<hr class="dark horizontal py-0">

@endsection
