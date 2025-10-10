<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Monthly Class Attendance Report</title>
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
            size: A4 landscape;
            margin: 0.5cm;
        }

        @media print {
            body {
                background-color: white;
                font-size: 10px;
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

            /* Ensure table breaks properly across pages */
            table {
                /* page-break-inside: auto; */
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        /* Layout Styles */
        .container {
            width: 100%;
            max-width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        .header-section {
            text-align: center;
            margin-bottom: 10px;
            position: relative;
            border-bottom: 2px solid #343a40;
            padding-bottom: 8px;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 50px;
            height: 50px;
        }

        .logo img {
            width: 100%;
            height: auto;
            border-radius: 50%;
            border: 1px solid #ddd;
        }

        .header-text {
            margin: 0 auto;
            width: 80%;
        }

        .header-text h4 {
            margin: 2px 0;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header-text h5 {
            margin: 2px 0;
            font-size: 12px;
            text-transform: uppercase;
        }

        /* Summary Section */
        .summary-section {
            margin: 10px 0;
        }

        .summary-header {
            text-align: center;
            margin: 8px 0;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .course-details {
            width: 48%;
            font-size: 12px;
        }

        .course-details p {
            margin: 2px 0;
        }

        .grade-summary {
            width: 48%;
        }

        .grade-summary p.title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 11px;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 11px;
        }

        .table th {
            background-color: #343a40;
            color: white;
            padding: 4px 2px;
            text-align: center;
            border: 1px solid #ddd;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .table td {
            padding: 3px 2px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        .table td.left {
            text-align: left;
            padding-left: 3px;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Attendance Status Symbols */
        .attendance-present {
            color: #28a745;
            font-weight: bold;
        }

        .attendance-absent {
            color: #dc3545;
            font-weight: bold;
        }

        .attendance-permission {
            color: #2f9bb9;
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

        .mt-3 {
            margin-top: 3px;
        }

        .mb-3 {
            margin-bottom: 3px;
        }

        /* Page Break Control */
        .month-section {
            margin-bottom: 15px;
            /* page-break-inside: avoid; */
        }

        .time-duration-header {
            background-color: #f0f0f0;
            padding: 5px 8px;
            border-left: 3px solid #343a40;
            margin: 10px 0 8px 0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }

        /* Fixed column widths */
        .col-number {
            width: auto;
        }

        .col-admission {
            width: auto;
        }

        .col-name {
            width: auto;
            text-align: left !important;
        }

        .col-gender {
            width: auto;
        }

        .col-stream {
            width: auto;
        }

        .col-date {
            width: auto;
            min-width: 20px;
        }

        .col-total {
            width: auto;
            background-color: #e9ecef;
            font-weight: bold;
        }

        /* Student summary row */
        .summary-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }

        .attendance-rate {
            text-align: center;
            font-size: 12px;
            padding: 2px;
        }

        /* Scrollable container for wide tables */
        .table-container {
            overflow-x: auto;
            width: 100%;
            margin-bottom: 10px;
        }

        /* Progress bar styles */
        .progress {
            height: 15px;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: #28a745;
            text-align: center;
            line-height: 15px;
            font-size: 8px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section with Logo -->
        <div class="header-section">
            <div class="logo">
                <img src="{{ public_path('assets/img/logo/' . (Auth::user()->school->logo)) }}" alt="School Logo">
            </div>
            <div class="header-text">
                <h4>{{ Auth::user()->school->school_name }}</h4>
                <h5>{{ Auth::user()->school->postal_address }}, {{ Auth::user()->school->postal_name }}</h5>
                <h5>Class Attendance Report</h5>
            </div>
        </div>

        @if (isset($datas) && $datas->isNotEmpty())
            @php
                // Group by month first
                $monthlyData = [];
                foreach ($datas as $date => $attendances) {
                    $monthYear = \Carbon\Carbon::parse($date)->format('Y-m');
                    if (!isset($monthlyData[$monthYear])) {
                        $monthlyData[$monthYear] = [];
                    }
                    $monthlyData[$monthYear][$date] = $attendances;
                }

                ksort($monthlyData);
            @endphp

            @foreach ($monthlyData as $monthYear => $monthAttendances)
                @php
                    $monthName = \Carbon\Carbon::parse($monthYear . '-01')->format('F Y');
                    $datesInMonth = array_keys($monthAttendances);
                    sort($datesInMonth);

                    // Get all students for this month
                    $students = [];
                    foreach ($monthAttendances as $dateAttendances) {
                        foreach ($dateAttendances as $attendance) {
                            $studentId = $attendance->student_id;
                            if (!isset($students[$studentId])) {
                                $students[$studentId] = [
                                    'id' => $studentId,
                                    'admission_number' => $attendance->admission_number,
                                    'name' => ucwords(strtolower($attendance->first_name . ' ' .
                                                ($attendance->middle_name ? $attendance->middle_name . ' ' : '') .
                                                $attendance->last_name)),
                                    'gender' => $attendance->gender[0] ?? 'U',
                                    'group' => $attendance->group ?? 'N/A',
                                    'attendances' => []
                                ];
                            }
                        }
                    }

                    // Populate attendance data for each student
                    foreach ($monthAttendances as $date => $dateAttendances) {
                        foreach ($dateAttendances as $attendance) {
                            $studentId = $attendance->student_id;
                            if (isset($students[$studentId])) {
                                $students[$studentId]['attendances'][$date] = $attendance->attendance_status;
                            }
                        }
                    }

                    // Calculate monthly statistics
                    $totalRecords = 0;
                    $presentRecords = 0;

                    foreach ($monthAttendances as $dateAttendances) {
                        $totalRecords += $dateAttendances->count();
                        $presentRecords += $dateAttendances->where('attendance_status', 'present')->count();
                    }

                    $attendanceRate = $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100) : 0;
                    $firstRecord = reset($monthAttendances)[0];
                @endphp

                <div class="month-section">
                    <!-- Time Duration Header -->
                    <div class="time-duration-header">
                        Attendance Report for: <strong>{{ $monthName }}</strong>
                        | Attendance Rate: <strong>{{ $attendanceRate }}%</strong>
                    </div>

                    <!-- Summary Section -->
                    <div class="summary-section">
                        <div class="summary-content">
                            <div class="course-details">
                                <p style="text-transform: uppercase"><span class="bold">Class:</span> {{ $firstRecord->class_name ?? 'N/A' }}</p>
                                <p><span class="bold">Report Date:</span>
                                    {{ \Carbon\Carbon::parse($datesInMonth[0])->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($datesInMonth[count($datesInMonth)-1])->format('d/m/Y') }}
                                </p>
                                <p><span class="bold">Total Students:</span> {{ count($students) }}</p>
                            </div>

                            <div class="grade-summary">
                                <p class="title">Attendance Rate</p>
                                <div style="text-align: center; font-size: 18px; font-weight: bold; color: #28a745;">
                                    {{ $attendanceRate }}%
                                </div>
                                <div class="progress mt-3">
                                    <div class="progress-bar" style="width: {{ $attendanceRate }}%;">
                                        {{-- {{ $attendanceRate }}% --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Attendance Table -->
                    <h5 class="summary-header">Student Attendance Records - {{ $monthName }}</h5>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-number">#</th>
                                    <th class="col-name">Student's Name</th>
                                    <th class="col-gender">Sex</th>
                                    <th class="col-stream">Stm</th>
                                    <!-- Date Columns -->
                                    @foreach ($datesInMonth as $date)
                                        <th class="col-date">{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                    @php
                                        $presentCount = 0;
                                        $totalDays = count($datesInMonth);
                                    @endphp
                                    <tr>
                                        <td class="col-number">{{ $loop->iteration }}</td>
                                        <td class="col-name left">{{ $student['name'] }}</td>
                                        <td class="col-gender text-capitalize">{{ $student['gender'] }}</td>
                                        <td class="col-stream text-uppercase">{{ $student['group'] }}</td>

                                        <!-- Attendance status for each date -->
                                        @foreach ($datesInMonth as $date)
                                            @php
                                                $status = $student['attendances'][$date] ?? 'A';

                                                if ($status === 'present') {
                                                    $symbol = 'P';
                                                    $class = 'attendance-present';
                                                    $presentCount++;
                                                } elseif ($status === 'absent') {
                                                    $symbol = 'A';
                                                    $class = 'attendance-absent';
                                                } elseif ($status === 'permission') {
                                                    $symbol = '*';
                                                    $class = 'attendance-permission';
                                                } else {
                                                    $symbol = '?';
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
                    <div class="mt-3" style="font-size: 8px;">
                        <strong>Key:</strong>
                        <span class="attendance-present">P = Present</span> |
                        <span class="attendance-absent">A = Absent</span> |
                        <span class="attendance-permission">* = Permission</span>
                    </div>
                </div>

                <!-- Add page break if not the last month -->
                @if (!$loop->last)
                    <div style="page-break-after: always;"></div>
                @endif
            @endforeach
        @else
            <!-- Display message if no data is available -->
            <div class="text-center" style="padding: 20px;">
                <p>No attendance data found for the selected period.</p>
            </div>
        @endif
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
