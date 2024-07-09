@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-2 logo">
                        <img src="{{asset('assets/img/logo/' .Auth::user()->school->logo)}}" alt="" style="max-width: 120px;">
                    </div>
                    <div class="col-8 text-center">
                        <h4 class="text-uppercase">{{Auth::user()->school->school_name}}</h4>
                        <h5 class="text-uppercase">class daily attendance report</h5>
                    </div>
                </div>
                <hr>
                @if ($attendanceRecords->isEmpty())
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning text-center mt-3" role="alert">
                                <p>Today report not submitted, please submit the attendance before a day to end!</p>
                            </div>
                        </div>
                    </div>
                @else
                <div class="row">
                    <div class="col-12 mt-2 border-bottom">
                        <h6 class="text-center">Attendance Summary</h6>
                    </div>
                </div>
                <div class="row border-bottom">
                    <div class="col-5">
                        <p class="text-center font-weight-bold text-capitalize p-2">class details</p>
                        <p class="text-capitalize border-bottom">Attendance Date: <span class="float-right"><strong>{{\Carbon\Carbon::parse($attendanceRecords->first()->attendance_date)->format('d-F-Y')}}</strong></span></p>
                        <p class="text-capitalize border-bottom">class teacher name: <span class="float-right"><strong>{{ $attendanceRecords->first()->teacher_firstname }} {{ $attendanceRecords->first()->teacher_lastname }}</strong></span></p>
                        <p class="text-capitalize border-bottom">Class name: <span class="text-uppercase float-right"><strong>{{ $attendanceRecords->first()->class_name }} ({{ $attendanceRecords->first()->class_code }})</strong></span></p>
                        <p class="text-capitalize border-bottom">class teacher phone: <span class="float-right"><strong>{{ $attendanceRecords->first()->teacher_phone }}</strong></span></p>
                        <p class="text-capitalize border-bottom">Class Stream: <span class="float-right"><strong>Stream: {{$attendanceRecords->first()->class_group}}</strong></span> </p>
                    </div>
                    <div class="col-7" style="border-left: 1px solid black;">
                        <p class="text-center font-weight-bold text-capitalize p-2">attendance details</p>
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Gender</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Permission</th>
                                    <th>Sum</th>
                                </tr>
                                <tbody>
                                    <tr>
                                        <td>Male</td>
                                        <td>{{ $malePresent }}</td>
                                        <td>{{ $maleAbsent }}</td>
                                        <td>{{ $malePermission }}</td>
                                        <th><strong>{{$sumMale = $malePresent + $maleAbsent + $malePermission}}</strong></th>
                                    </tr>
                                    <tr>
                                        <td>Female</td>
                                        <td>{{ $femalePresent }}</td>
                                        <td>{{ $femaleAbsent }}</td>
                                        <td>{{ $femalePermission }}</td>
                                        <td><strong>{{$sumFemale = $femalePresent  + $femaleAbsent + $femalePermission  }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Grand Total</td>
                                        <td><strong>{{$malePresent +  $femalePresent}}</strong></td>
                                        <td><strong>{{$maleAbsent +  $femaleAbsent}}</strong></td>
                                        <td><strong>{{$malePermission +  $femalePermission}}</strong></td>
                                        <td><strong>{{$sumMale +  $sumFemale}}</strong></td>
                                    </tr>
                                </tbody>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- Detailed Attendance Records -->
                <div class="row border-bottom">
                    <div class="col-12">
                        <h6 class="text-center text-capitalize p-2">Students Attendance Records</h6>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <th style="width: 5px">S/N</th>
                                <th class="text-center">Admission No</th>
                                <th>Student Name</th>
                                <th style="width: 10px" class="text-center">Gender</th>
                                <th style="width: 10px" class="text-center">Stream</th>
                                <th>Attendance Status</th>
                            </thead>
                            <tbody>
                                @foreach ($attendanceRecords as $record )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-center">{{ str_pad($record->studentId, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="text-capitalize">{{ $record->first_name }} {{ $record->middle_name }} {{ $record->last_name }}</td>
                                        <td class="text-capitalize text-center">{{ $record->gender[0] }}</td>
                                        <td class="text-capitalize text-center">{{ $record->class_group }}</td>
                                        <td>{{ ucfirst($record->attendance_status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mt-3">
                        <ul class="d-flex justify-content-center">
                            <li class="mr-3">
                                <a href="" class="btn btn-primary no-print" onclick="scrollToTopAndPrint(); return false;">Print Attendance</a>
                            </li>
                            <li>
                                <a href="{{url()->previous()}}" class="btn btn-danger no-print">Cancel</a>
                            </li>
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="footer mt-5" style="position: fixed; bottom: 0; width: 100%; border-top: 1px solid #ddd; padding-top: 10px;">
        <div class="row">
            <div class="col-8">
                <p class="">Printed by: {{ Auth::user()->email}}</p>
            </div>
            <div class="col-4">
                <p class="">{{ \Carbon\Carbon::now()->format('d/m/Y H:i A') }}</p>
            </div>
        </div>
        <script type="text/php">
            if ( isset($pdf) ) {
                $pdf->page_text(270, 770, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0,0,0));
            }
        </script>
    </div>
@endsection
@section('styles')
