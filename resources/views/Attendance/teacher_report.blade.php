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
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 14px;
            line-height: 1.4;
        }

        /* Print Specific Styles */
        @page {
            size: A4;
            margin: 1.5cm;
            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 10px;
            }
        }

        @media print {
            body {
                background-color: white;
                font-size: 12px;
            }

            .container {
                padding: 0;
                margin: 0;
            }

            .page-break {
                page-break-after: always;
                break-after: page;
            }

            .no-print {
                display: none;
            }

            .print-only {
                display: block;
            }

            .table {
                page-break-inside: avoid;
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
            padding: 20px;
            box-sizing: border-box;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
            height: 80px;
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
            margin: 5px 0;
            font-size: 18px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header-text h5 {
            margin: 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        /* Summary Section */
        .summary-section {
            margin: 20px 0;
        }

        .summary-header {
            text-align: center;
            margin: 15px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .course-details {
            width: 55%;
            font-size: 13px;
        }

        .course-details p {
            margin: 5px 0;
        }

        .grade-summary {
            width: 42%;
        }

        .grade-summary p.title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
            page-break-inside: avoid;
        }

        .table th {
            background-color: #343a40;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }

        .table td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .table td.center {
            text-align: center;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Footer */
        @page {
            margin-top: 8mm;
            margin-bottom: 8mm; /* Ongeza nafasi ya chini kwa footer */
            margin-left: 10mm;
            margin-right: 10mm;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 8mm; /*urefu wa footer*/
            font-size: 10px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            background-color: white;
            z-index: 1000;
        }
        footer .page-number:after {
            /* content: "Page " counter(page); */
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

        .page-number:after {
            content: "Page " counter(page);
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
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

        .mt-10 {
            margin-top: 10px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        /* Page Break Control */
        .attendance-block {
            page-break-inside: avoid;
            margin-bottom: 30px;
        }

        .time-duration-header {
            background-color: #f0f0f0;
            padding: 8px 15px;
            border-left: 4px solid #343a40;
            margin: 20px 0 15px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section with Logo -->
        <div class="header-section">
            <div class="logo">
                <img src="{{ public_path('assets/img/logo/' . Auth::user()->school->logo) }}" alt="School Logo">
            </div>
            <div class="header-text">
                <h4>{{ Auth::user()->school->school_name }}</h4>
                <h5>P.O Box {{ Auth::user()->school->postal_address }}, {{ Auth::user()->school->postal_name }}</h5>
                <h5>Class Attendance Report</h5>
            </div>
        </div>

        <!-- Attendance Data -->
        @forelse ($datas as $month => $attendances)
            @if ($attendances->isEmpty())
                <div class="alert alert-warning text-center mt-3" role="alert">
                    <p>There is no attendance record for the selected date.</p>
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

                    <div class="attendance-block">
                        <!-- Time Duration Header -->
                        @if ($loop->first)
                            <div class="time-duration-header">
                                Time Duration: <strong>{{ \Carbon\Carbon::parse($month)->format('F Y') }}</strong>
                            </div>
                        @endif

                        <!-- Summary Section -->
                        <div class="summary-section">
                            <div class="summary-header">
                                Attendance Report Summary
                            </div>

                            <div class="summary-content">
                                <div class="course-details">
                                    <p><span class="bold">Attendance Date:</span> <strong class="underline">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</strong></p>
                                    <p><span class="bold">Class Teacher Name:</span> {{ ucwords(strtolower($teacher->teacher_firstname . ' '. $teacher->teacher_lastname ))}}</p>
                                    <p><span class="bold">Class Name:</span> {{ ucwords(strtoupper($teacher->class_name)) }} <span class="text-uppercase">({{ $teacher->class_code }})</span></p>
                                    <p><span class="bold">Stream:</span> {{ $teacher->group }}</p>
                                </div>

                                <div class="grade-summary">
                                    <p class="title">Students Attendance Summary</p>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Gender</th>
                                                <th>Present</th>
                                                <th>Absent</th>
                                                <th>Permission</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Male</td>
                                                <td class="center">{{ $malePresent }}</td>
                                                <td class="center">{{ $maleAbsent }}</td>
                                                <td class="center">{{ $malePermission }}</td>
                                                <td class="center bold">{{ $sumMale = $malePresent + $maleAbsent + $malePermission }}</td>
                                            </tr>
                                            <tr>
                                                <td>Female</td>
                                                <td class="center">{{ $femalePresent }}</td>
                                                <td class="center">{{ $femaleAbsent }}</td>
                                                <td class="center">{{ $femalePermission }}</td>
                                                <td class="center bold">{{ $sumFemale = $femalePresent + $femaleAbsent + $femalePermission }}</td>
                                            </tr>
                                            <tr>
                                                <td class="bold">Total</td>
                                                <td class="center bold">{{ $malePresent + $femalePresent }}</td>
                                                <td class="center bold">{{ $maleAbsent + $femaleAbsent }}</td>
                                                <td class="center bold">{{ $malePermission + $femalePermission }}</td>
                                                <td class="center bold">{{ $sumMale + $sumFemale }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Students Attendance Records -->
                        <h5 class="summary-header">Students Attendance Records</h5>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="12%" class="center">Adm No</th>
                                    <th width="38%">Student's Name</th>
                                    <th width="10%" class="center">Gender</th>
                                    <th width="15%" class="center">Stream</th>
                                    <th width="15%" class="center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $attendance)
                                    <tr>
                                        <td class="center">{{ $loop->iteration }}</td>
                                        <td class="center text-uppercase">{{ $attendance->admission_number }}</td>
                                        <td>{{ ucwords(strtolower($attendance->first_name . ' ' . $attendance->middle_name . ' ' . $attendance->last_name)) }}</td>
                                        <td class="center text-capitalize">{{ ucfirst($attendance->gender[0]) }}</td>
                                        <td class="center text-capitalize">{{ ucfirst($attendance->class_group) }}</td>
                                        <td class="center text-capitalize">{{ ucfirst($attendance->attendance_status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Add page break after each date except the last one -->
                        @if (!$loop->last)
                            <div class="page-break"></div>
                        @endif
                    </div>
                @endforeach
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
