@extends('SRTDashboard.frame')
@section('content')
<div class="container">
    <div class="attendance">
        <h4 class="text-center text-uppercase">{{Auth::user()->school->school_name. ' - '. Auth::user()->school->school_reg_no}}</h4>
    <div class="row">
        <div class="col-10">
            <h6 class="text-uppercase text-center">Attendance Report</h6>
        </div>
        <div class="col-2">
            <a href="" onclick="scrollToTopAndPrint(); return false;" class="no-print"><i class="fas fa-print text-secondary" style="font-size: 1rem;"></i></a>
        </div>
    </div>
    @forelse ($datas as $month => $attendances)
        <h6 class="text-center"><strong>Month: </strong> {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h6>
        @if ($attendances->isEmpty())
            <div class="alert alert-warning mt-3" role="alert">
                <h6 class="text-center">There is no attendance record for {{ \Carbon\Carbon::parse($month)->format('F Y') }}.</h6>
            </div>
        @else
            @php
                // Group attendances by date
                $groupedByDate = $attendances->groupBy('attendance_date');
            @endphp

            @foreach ($groupedByDate as $date => $records)
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

                <!-- Summary for each date -->
                <h6 class="attendance-date mt-3 bg-info">Date: {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h6>
                <!-- Teacher Details -->
                <div class="mt-3 teacher-details">
                    <h4 class="text-capitalize bg-info">Class Teacher Details</h4>
                    <div class="row">
                        <div class="col-6">
                            <p class="text-capitalize"><strong>Name:</strong> {{ $teacher->teacher_firstname }} {{ $teacher->teacher_lastname }}</p>
                            <p><strong>Gender:</strong> {{ ucfirst($teacher->teacher_gender) }}</p>
                        </div>
                        <div class="col-4">
                            <p><strong>Phone:</strong> {{ $teacher->teacher_phone }}</p>
                            <p class="text-uppercase"><strong>Class:</strong> {{ $teacher->class_name }} ({{ $teacher->class_code }})</p>
                            {{-- <p class="text-capitalize"><strong>Class Group:</strong> {{ $teacher->group }}</p> --}}
                        </div>
                    </div>
                </div>
                <table class="table table-responsive-sm table-sm table-bordered">
                    <tr>
                        <th>Gender</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Permission</th>
                        <th>Sum</th>
                    </tr>
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
                </table>

                <!-- Detailed Records for each date -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Attendance Date</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Gender</th>
                            <th>Group</th>
                            <th>Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $attendance)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d-m-Y') }}</td>
                                <td>{{ str_pad($attendance->studentID, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-capitalize">{{ $attendance->first_name }} {{$attendance->middle_name}} {{ $attendance->last_name }}</td>
                                <td class="text-capitalize">{{ ucfirst($attendance->gender[0]) }}</td>
                                <td class="text-capitalize">{{ucfirst($attendance->class_group)}}</td>
                                <td class="text-capitalize">{{ ucfirst($attendance->attendance_status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endif
    @empty
        <div class="alert alert-warning mt-3" role="alert">
            <h6 class="text-center">There are no attendance records for the selected month range.</h6>
        </div>
    @endforelse
    </div>
</div>
<div class="footer mt-5" style="position: fixed; bottom: 0; width: 100%; border-top: 1px solid #ddd; padding-top: 10px;">
    <div class="row">
        <div class="col-8">
            <p class="">Printed by: {{ Auth::user()->email}}</p>
        </div>
        <div class="col-4">
            <p class="">{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
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
<style>
    @media print {
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        @page {
            margin: 20mm;
        }
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
    }
</style>
@endsection
