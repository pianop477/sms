<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Class Teacher General Attendance Report</title>
    <style>
        /* Global Styles */
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 11px;
            line-height: 1.2;
        }

        /* Print Specific Styles */
         @page {
            size: A3 landscape;
            margin: 0.5cm;
        }

        @media print {
            body {
                background-color: white;
                font-size: 11px;
            }

            .container {
                padding: 0;
                margin: 0;
                width: 100%;
            }

            .no-print {
                display: none;
            }

            .print-only {
                display: block;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
        }

        /* Layout Styles */
        .container {
            width: 100%;
            max-width: 100%;
            padding: 15px;
            box-sizing: border-box;
        }

        .header-section {
            text-align: center;
            margin-bottom: 15px;
            position: relative;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: 60px;
        }

        .logo img {
            width: 100%;
            height: auto;
            border-radius: 50%;
            border: 2px solid #ddd;
        }

        .header-text {
            margin: 0 auto;
            width: 80%;
        }

        .header-text h4 {
            margin: 3px 0;
            font-size: 16px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header-text h5 {
            margin: 3px 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        /* Summary Section */
        .summary-section {
            margin: 15px 0;
        }

        .summary-header {
            text-align: center;
            margin: 10px 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #333;
            padding-bottom: 3px;
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .course-details {
            width: 48%;
            font-size: 11px;
        }

        .course-details p {
            margin: 3px 0;
        }

        .grade-summary {
            width: 48%;
        }

        .grade-summary p.title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 11px;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
        }

        .table th {
            background-color: #343a40;
            color: white;
            padding: 5px 3px;
            text-align: center;
            border: 1px solid #ddd;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        .table td {
            padding: 4px 2px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        .table td.left {
            text-align: left;
            padding-left: 5px;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Attendance Status Symbols */
        .attendance-present {
            color: #28a745;
            font-size: 12px;
            font-weight: bold;
        }

        .attendance-absent {
            color: #dc3545;
            font-size: 12px;
            font-weight: bold;
        }

        .attendance-permission {
            color: #2f9bb9;
            font-size: 11px;
            font-weight: bold;
        }

        /* Footer */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4mm; /*urefu wa footer*/
            font-size: 10px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            background-color: white;
            z-index: 1000;
        }
        footer .page-number:after {
            content: "Page " counter(page);
        }
        footer .copyright {
            float: left;
            margin-left: 10px;
        }
        footer .printed {
            float: right;
            margin-right: 10px;
        }
        /* Clear floats */
        footer:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .mt-5 {
            margin-top: 5px;
        }

        .mb-5 {
            margin-bottom: 5px;
        }

        /* Page Break Control */
        .month-section {
            margin-bottom: 20px;
        }

        .time-duration-header {
            background-color: #f0f0f0;
            padding: 6px 10px;
            border-left: 4px solid #343a40;
            margin: 15px 0 10px 0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }

        /* Fixed column widths */
        .col-number {
            width: 5px;
        }

        .col-admission {
            width: 8px;
        }

        .col-name {
            width: 40px;
            text-align: left !important;
        }

        .col-gender {
            width: 5px;
        }

        .col-date {
            width: 8px;
        }

        /* Student summary row */
        .summary-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }

        .attendance-rate {
            text-align: center;
            font-size: 9px;
            padding: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section with Logo -->
        <div class="header-section">
            <div class="logo">
                <img src="{{ storage_path('app/public/logo/' . Auth::user()->school->logo) }}" alt="School Logo">
            </div>
            <div class="header-text">
                <h4>{{ Auth::user()->school->school_name }}</h4>
                <h5>{{ Auth::user()->school->postal_address }}, {{ Auth::user()->school->postal_name }}</h5>
                <h5>Class Attendance Report</h5>
            </div>
        </div>

        <!-- Attendance Data -->
        @forelse ($datas as $month => $attendances)
            @if (!$attendances->isEmpty())
                @php
                    // Get unique dates for this month
                    $dates = $attendances->unique('attendance_date')->pluck('attendance_date')->sort();

                    // Get unique students for this month
                    $students = $attendances->unique('studentID');

                    // Get teacher and class details from first record
                    $teacher = $attendances->first();

                    // Calculate attendance rate for the month
                    $totalRecords = $attendances->count();
                    $presentRecords = $attendances->where('attendance_status', 'present')->count();
                    $attendanceRate = $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100) : 0;
                @endphp

                <div class="month-section">
                    <!-- Time Duration Header -->
                    <div class="time-duration-header">
                        Attendance Report for: <strong>{{ \Carbon\Carbon::parse($month)->format('F Y') }}</strong>
                        | Attendance Rate: <strong>{{ $attendanceRate }}%</strong>
                    </div>

                    <!-- Summary Section -->
                    <div class="summary-section">
                        <div class="summary-content">
                            <div class="course-details">
                                <p><span class="bold">Class Teacher:</span> {{ ucwords(strtolower($teacher->teacher_firstname . ' '. $teacher->teacher_lastname ))}}</p>
                                <p><span class="bold">Class:</span> {{ ucwords(strtoupper($teacher->class_name)) }} <span class="text-uppercase">({{ $teacher->class_code }})</span></p>
                                <p><span class="bold">Stream:</span> {{ $teacher->group }}</p>
                                <p><span class="bold">Reporting Period:</span> {{\Carbon\Carbon::parse($dates->first())->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dates->last())->format('d/m/Y') }}</p>
                                <p><span class="bold">Total Students:</span> {{ $students->count() }}</p>
                            </div>

                            <div class="grade-summary">
                                <p class="title">Monthly Attendance Rate</p>
                                <div style="text-align: center; font-size: 24px; font-weight: bold; color: #28a745;">
                                    {{ $attendanceRate }}%
                                </div>
                                <div style="text-align: center; margin-top: 10px;">
                                    <div style="background-color: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden;">
                                        <div style="background-color: #28a745; height: 100%; width: {{ $attendanceRate }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Attendance Table -->
                    <h5 class="summary-header">Detailed Student Attendance Records</h5>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-number">#</th>
                                    <th class="col-admission">Admission#</th>
                                    <th class="col-name">Student's Name</th>
                                    <th class="col-gender">Gender</th>

                                    <!-- Date Columns -->
                                    @foreach ($dates as $date)
                                        <th class="col-date">{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                    @php
                                        $studentAttendances = $attendances->where('studentID', $student->studentID);
                                    @endphp

                                    <tr>
                                        <td class="col-number">{{ $loop->iteration }}</td>
                                        <td class="col-admission text-uppercase">{{ $student->admission_number }}</td>
                                        <td class="col-name left">{{ ucwords(strtolower($student->first_name . ' ' . $student->last_name)) }}</td>
                                        <td class="col-gender text-capitalize">{{ ucfirst($student->gender[0]) }}</td>

                                        <!-- Attendance status for each date -->
                                        @foreach ($dates as $date)
                                            @php
                                                $attendance = $studentAttendances->where('attendance_date', $date)->first();
                                                $status = $attendance ? $attendance->attendance_status : 'absent';

                                                if ($status === 'present') {
                                                    $symbol = 'P';
                                                    $class = 'attendance-present';
                                                } elseif ($status === 'absent') {
                                                    $symbol = 'A';
                                                    $class = 'attendance-absent';
                                                } elseif ($status === 'permission') {
                                                    $symbol = '*';
                                                    $class = 'attendance-permission';
                                                } else {
                                                    $symbol = '✗';
                                                    $class = 'attendance-absent';
                                                }
                                            @endphp
                                            <td class="{{ $class }}">{{ $symbol }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="mt-5" style="font-size: 9px;">
                        <strong>Key:</strong>
                        <span class="attendance-present">P = Present</span> |
                        <span class="attendance-absent">A = Absent</span> |
                        <span class="attendance-permission">* = Permission</span>
                    </div>
                </div>
            @endif
        @empty
            <div class="alert alert-warning mt-3 text-center" role="alert">
                <p>There is no attendance record for the selected time duration!</p>
            </div>
        @endforelse
    </div>

    <!-- Footer -->
    <footer>
        <span class="copyright">
        &copy; {{ ucwords(strtolower(Auth::user()->school->school_name)) }} – {{ date('Y') }}
        </span>
        <span class="page-number"></span>
        <span class="printed">
        Printed at: {{ now()->format('d-M-Y H:i') }}
        </span>
    </footer>
</body>
</html>
