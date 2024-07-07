@extends('SRTDashboard.frame')
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table, .table th, .table td {
            border: 1px solid black;
        }
        .table th, .table td {
            padding: 8px;
            text-align: left;
        }
        .bg-info {
            background-color: #17a2b8;
        }
        .text-white {
            color: white;
        }
    </style>
    @section('content')
    <div class="attendance col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h3 class="text-uppercase text-center">{{ Auth::user()->school->school_name }}</h3>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-10">
                        <h3 class="">Attendance Report for: {{\Carbon\Carbon::parse($attendanceRecords->first()->attendance_date)->format('d-F-Y')}}</h3>
                    </div>
                    <div class="col-1">
                        <a href="" onclick="scrollToTopAndPrint(); return false;" class="no-print">
                            <i class="fas fa-print text-secondary" style="font-size: 1rem"></i>
                        </a>
                    </div>
                    <div class="col-1">
                        <a href="{{url()->previous()}}" class="no-print">
                            <i class="fas fa-arrow-circle-left text-secondary" style="font-size: 1rem"></i>
                        </a>
                    </div>
                </div>
                @if($attendanceRecords->isEmpty())
                    <div class="alert alert-warning mt-3 text-center" role="alert">
                        <h6>You have not submitted attendance report, kindly submit it before a day to end!</h6>
                    </div>
                @else
                    <!-- Display Teacher's Details -->
                    <div class="mt-3 teacher-details">
                        <h4 class="text-capitalize bg-info">Class Teacher Details</h4>
                        <div class="row">
                            <div class="col-6">
                                <p class="text-capitalize"><strong>Name:</strong> {{ $attendanceRecords->first()->teacher_firstname }} {{ $attendanceRecords->first()->teacher_lastname }}</p>
                                <p><strong>Gender:</strong> {{ ucfirst($attendanceRecords->first()->teacher_gender) }}</p>
                            </div>
                            <div class="col-4">
                                <p><strong>Phone:</strong> {{ $attendanceRecords->first()->teacher_phone }}</p>
                                <p class="text-uppercase"><strong>Class:</strong> {{ $attendanceRecords->first()->class_name }} ({{ $attendanceRecords->first()->class_code }})</p>
                                {{-- <p class="text-capitalize"><strong>Class Group:</strong> {{ $attendanceRecords->first()->group }}</p> --}}
                            </div>
                        </div>
                    </div>

                    <!-- Summary Table -->
                    <table class="table table-sm table-responsive-sm table-bordered mt-3">
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
                    </table>

                    <!-- Detailed Attendance Records -->
                    @php
                        $currentDate = '';
                    @endphp

                    @foreach ($attendanceRecords as $record)
                        @if ($currentDate !== $record->attendance_date)
                            @php
                                $currentDate = $record->attendance_date;
                            @endphp
                            <!-- Attendance Date Header -->
                            <h5 class="bg-info text-center mt-3 attendance-date">Date: {{ \Carbon\Carbon::parse($currentDate)->format('F d, Y') }}</h5>

                            <!-- Table Headers -->
                            <table class="table mb-4 table-bordered table-attendance">
                                <thead>
                                    <tr>
                                        <th>StudentID</th>
                                        <th>Student Name</th>
                                        <th class="text-center">Gender</th>
                                        <th class="text-center">Group</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @endif

                        <tr>
                            <td>{{ str_pad($record->studentId, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="text-capitalize">{{ $record->first_name }} {{ $record->middle_name }} {{ $record->last_name }}</td>
                            <td class="text-capitalize text-center">{{ $record->gender[0] }}</td>
                            <td class="text-capitalize text-center">{{ $record->class_group }}</td>
                            <td>{{ ucfirst($record->attendance_status) }}</td>
                        </tr>

                        @if ($loop->last || $attendanceRecords[$loop->index + 1]->attendance_date !== $currentDate)
                                </tbody>
                            </table>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <!-- Footer -->
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

