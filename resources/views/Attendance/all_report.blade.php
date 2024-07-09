@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-body">
                @if (isset($datas) && $datas->isNotEmpty())
                    <div class="row">
                        <div class="col-2 logo">
                            <img src="{{asset('assets/img/logo/' .Auth::user()->school->logo)}}" alt="" style="max-width: 120px;">
                        </div>
                        <div class="col-8 text-center">
                            <h4 class="text-uppercase">{{Auth::user()->school->school_name}}</h4>
                            <h5 class="text-uppercase">class attendance report for {{$attendances->first()->class_name}}</h5>
                            <h5 class="text-uppercase">Academic year {{\Carbon\Carbon::parse($attendances->first()->attendance_date)->format('Y')}}</h5>
                        </div>
                    </div>
                    <hr>
                    @foreach ($datas as $date => $attendances)
                        <div class="row">
                            <div class="col-12 border-bottom">
                                <h6 class="text-center text-capitalize p-2">Attendance Summary</h6>
                                <p class="border-bottom font-weight-bold text-primary">Date: {{\Carbon\Carbon::parse($date)->format('d-F-Y')}}</p>
                                <table class="table table-hover table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Gender</th>
                                            <th>Present</th>
                                            <th>Absent</th>
                                            <th>Permision</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Male</td>
                                            <td>{{ $maleSummary[$date]['present'] }}</td>
                                            <td>{{ $maleSummary[$date]['absent'] }}</td>
                                            <td>{{ $maleSummary[$date]['permission'] }}</td>
                                            <td>{{ $maleSummary[$date]['present'] + $maleSummary[$date]['absent'] + $maleSummary[$date]['permission'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Female</td>
                                            <td>{{ $femaleSummary[$date]['present'] }}</td>
                                            <td>{{ $femaleSummary[$date]['absent'] }}</td>
                                            <td>{{ $femaleSummary[$date]['permission'] }}</td>
                                            <td>{{ $femaleSummary[$date]['present'] + $femaleSummary[$date]['absent'] + $femaleSummary[$date]['permission'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mt-2">
                                <h6 class="text-center font-weight-bold text-capitalize p-2">students attendance records</h6>
                                <table class="table table-hover table-bordered text-capitalize">
                                    <thead>
                                        <tr>
                                            <th style="width: 5px">S/N</th>
                                            <th class="text-center">Admission No.</th>
                                            <th>Student Name</th>
                                            <th style="width: 10px" class="text-center">Gender</th>
                                            <th style="width: 10px" class="text-center">Stream</th>
                                            <th>attendance Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attendances as $index => $attendance )
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="text-center">{{ str_pad($attendance->studentId, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td class="text-capitalize">{{ $attendance->first_name }} {{ $attendance->middle_name }} {{ $attendance->last_name }}</td>
                                                <td class="text-capitalize text-center">{{ $attendance->gender[0] }}</td>
                                                <td class="text-capitalize text-center">{{ $attendance->group }}</td>
                                                <td>{{ ucfirst($attendance->attendance_status) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                    <div class="row">
                        <div class="col-12 mt-4">
                            <ul class="d-flex justify-content-center">
                                <li class="mr-3">
                                    <a href="" class="btn btn-primary no-print" onclick="scrollToTopAndPrint(); return false;">Print Attendance</a>
                                </li>
                                <li>
                                    <a href="{{route('attendance.fill.form')}}" class="btn btn-danger no-print">Cancel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning text-center p-2">
                                <p>No attendance records for the selected Time duration</p>
                                <a href="{{route('attendance.fill.form')}}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
