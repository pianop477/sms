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
            <div class="card">
                <div class="seo-fact sbg1">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="fas fa-user-tie"></i> Teachers</div>
                        <h2>{{count($teachers)}}</h2>
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
                        <h2>{{count($parents)}}</h2>
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
                        <h2>{{count($students)}}</h2>
                        <ul>
                            <li><span class="text-white">M: <strong>{{$maleStudents}}</strong></span></li>
                            <li><span class="text-white">F: <strong>{{$femaleStudents}}</strong></span></li>
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
                        <h2>{{count($subjects)}}</h2>
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
                        <h2>{{count($classes)}}</h2>
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
                        <h2>{{count($buses)}}</h2>
                    </div>
                    <canvas id="" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<hr class="dark horizontal py-0">

@endsection
