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
                        <h5 class="text-uppercase">class attendance report</h5>
                        @forelse ($datas as $month => $attendances )
                            <h6 class="text-capitalize">time duration: <strong>{{ \Carbon\Carbon::parse($month)->format('F Y') }}</strong></h6>
                    </div>
                </div>
                <hr>
                    @if ($attendances->isEmpty())
                        <div class="alert alert-warning text-center mt-3" role="alert">
                            <p>There is no attendance records for {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
                            <a href="{{url()->previous()}}" class="btn btn-danger no-print">Cancel</a>
                        </div>
                    @else
                        @php
                            // Group attendances by date
                            $groupedByDate = $attendances->groupBy('attendance_date');
                        @endphp
                            @foreach ($groupedByDate as $date => $records )
                                    @php
                                        // Initialize counters
                                        $malePresent = $maleAbsent = $malePermission = 0;
                                        $femalePresent = $femaleAbsent = $femalePermission = 0;

                                        // Get teacher details from the first record of the day
                                        $teacher = $records->first();

                                        // Count attendance status by gender
                                        foreach ($records as $record) {
                                            if ($record->gender == 'male') {
                                                if ($record->attendance_status == 'present') $malePresent++;
                                                elseif ($record->attendance_status == 'absent') $maleAbsent++;
                                                elseif ($record->attendance_status == 'permission') $malePermission++;
                                            } elseif ($record->gender == 'female') {
                                                if ($record->attendance_status == 'present') $femalePresent++;
                                                elseif ($record->attendance_status == 'absent') $femaleAbsent++;
                                                elseif ($record->attendance_status == 'permission') $femalePermission++;
                                            }
                                        }
                                    @endphp

                                    {{-- attendance summary --}}
                                    <div class="row border-bottom">
                                        <div class="col-12 text-center text-capitalize p-2">
                                            <h6>attendance report summary</h6>
                                        </div>
                                    </div>
                                    <div class="row border-bottom">
                                        <div class="col-5">
                                            <p class="text-center text-capitalize font-weight-bold p-2">class details</p>
                                            <p class="border-bottom">Attendance Date: <span class="float-right"><strong>{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</strong></span> </p>
                                            <p class="border-bottom text-capitalize">Class Teacher name: <span class="float-right"><strong>{{ $teacher->teacher_firstname }} {{ $teacher->teacher_lastname }}</strong></span></p>
                                            <p class="border-bottom text-capitalize">Class teacher phone: <span class="float-right"><strong>{{$teacher->teacher_phone }}</strong></span></p>
                                            <p class="border-bottom text-capitalize">attendance class: <span class="float-right text-uppercase"><strong>{{ $teacher->class_name }} ({{ $teacher->class_code }})</strong></span></p>
                                            <p class="border-bottom text-capitalize">Class Stream: <span class="float-right"><strong>Stream "{{ $teacher->group }}"</strong></span></p>
                                        </div>
                                        <div class="col-7 text-right" style="border-left: 1px solid black;">
                                            <p class="text-capitalize text-center p-2 font-weight-bold">Attendance details</p>
                                            <table class="table table-bordered table-hover text-center">
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
                                                            <td><strong>{{$sumMale = $malePresent + $maleAbsent + $malePermission}}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Female</td>
                                                            <td>{{ $femalePresent }}</td>
                                                            <td>{{ $femaleAbsent }}</td>
                                                            <td>{{ $femalePermission }}</td>
                                                            <td><strong>{{$sumFemale = $femalePresent + $femaleAbsent + $femalePermission  }}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Total</td>
                                                            <td>{{$malePresent + $femalePresent}}</td>
                                                            <td>{{$maleAbsent + $femaleAbsent}}</td>
                                                            <td>{{$malePermission + $femalePermission }}</td>
                                                            <td><strong>{{$sumMale + $sumFemale}}</strong></td>
                                                        </tr>
                                                    </tbody>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 border-bottom mt-3">
                                            <h6 class="text-capitalize text-center font-weight-bold">Students attendance records</h6>
                                            <table class="table table-hover table-bordered">
                                                <thead>
                                                    <th style="width: 5px">S/N</th>
                                                    <th class="text-center">Admission No</th>
                                                    <th>Student's Name</th>
                                                    <th class="text-center" style="width: 10px">Gender</th>
                                                    <th class="text-center" style="width: 10px">Stream</th>
                                                    <th>Attendance Status</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($records as $attendance )
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td class="text-center">{{ str_pad($attendance->studentID, 4, '0', STR_PAD_LEFT) }}</td>
                                                        <td class="text-capitalize">{{ $attendance->first_name }} {{$attendance->middle_name}} {{ $attendance->last_name }}</td>
                                                        <td class="text-capitalize text-center">{{ ucfirst($attendance->gender[0]) }}</td>
                                                        <td class="text-capitalize text-center">{{ucfirst($attendance->class_group)}}</td>
                                                        <td class="text-capitalize">{{ ucfirst($attendance->attendance_status) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                            @endforeach
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <ul class="d-flex justify-content-center">
                                        <li class="mr-3">
                                            <a href="" onclick="scrollToTopAndPrint(); return false;" class="btn btn-primary no-print">Print Attendance</a>
                                        </li>
                                        <li>
                                            <a href="{{url()->previous()}}" class="btn btn-danger no-print">Cancel</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                    @endif
                @empty
                    <div class="alert alert-warning mt-3 text-center" role="alert">
                        <p>There is no attendance records for the selected time duration!</p>
                        <a href="{{url()->previous()}}" class="btn btn-danger">Cancel</a>
                    </div>
                @endforelse
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
