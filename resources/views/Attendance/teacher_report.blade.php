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
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                border-top: 1px solid #ddd;
                padding-top: 10px;
                font-size: 12px;
                text-align: center;
                background-color: #fff;
            }
            thead {
                display: table-header-group;
                background-color: #343a40;
                color: #fff;
            }
            .table {
                width: 100%;
                border: 1px solid #000;
                border-collapse: collapse;
                margin-top: 10px;
            }
            .table th,
            .table td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            .table th {
                background-color: #343a40;
                color: #fff;
                text-align: center;
            }
            .table td {
                background-color: #fff;
                color: #333;
            }
        }

        /* Container and Layout */
        .container {
            padding: 20px;
        }

        .logo {
            position: absolute;
            width: 70px;
            left: 7px;
            top: 5px;
        }

        .header {
            text-align: center;
            margin-top: 10px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .header h4 {
            margin: 5px 0;
            font-size: 18px;
        }

        .header h5 {
            margin: 5px 0;
            font-size: 16px;
        }

        .summary-header {
            margin-top: 20px;
            text-align: center;
            text-transform: capitalize;
            font-size: 20px;
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .course-details {
            width: 50%;
        }

        .grade-summary {
            width: 45%;
        }

        .grade-summary table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .grade-summary th,
        .grade-summary td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .grade-summary th {
            background-color: #343a40;
            color: #fff;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; /* Reduced margin */
            font-size: 12px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #343a40;
            color: #fff;
            text-align: center;
        }

        .table td {
            background-color: #fff;
            color: #333;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 25px;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding: 4px 20px;
            text-align: center;
            background-color: white; /* Hakikisha footer ina background */
            z-index: 1000; /* Hakikisha footer iko juu ya content */
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

        .attendance-section:nth-child(n + 2) {
            page-break-before: always;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo and Header -->
        <div class="logo">
            <img src="{{ public_path('assets/img/logo/' . Auth::user()->school->logo) }}" alt="School Logo" style="max-width: 80px; border-radius: 50px;">
        </div>
        <div class="header">
            <h4>{{ Auth::user()->school->school_name }}</h4>
            <h5>P.O Box {{ Auth::user()->school->postal_address }}, {{ Auth::user()->school->postal_name }}</h5>
            <h5>Class Attendance Report</h5>
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
                    <div>
                        <div class="attendance-section">
                            <h6 style="text-transform: uppercase; border: 1px solid black; border-radius: 4px; padding: 5px;">
                                Time Duration: <strong>{{ \Carbon\Carbon::parse($month)->format('F Y') }}</strong>
                            </h6>
                        </div>

                        <div class="summary-header">
                            <p>Attendance Report Summary</p>
                        </div>
                        <div class="summary-content">
                            <div class="course-details">
                                <p>Attendance Date: <strong><u>{{ \Carbon\Carbon::parse($date)->format('d/F/Y') }}</u></strong></p>
                                <p>Class Teacher Name: <strong>{{ ucwords(strtolower($teacher->teacher_firstname . ' '. $teacher->teacher_lastname ))}}</strong></p>
                                <p>Class Name: <strong>{{ ucwords(strtoupper($teacher->class_name)) }} <span style="text-transform: uppercase">({{ ucwords(strtoupper($teacher->class_code)) }})</span></strong></p>
                                <p>Stream: <strong>{{ $teacher->group }}</strong></p>
                            </div>
                            <div class="grade-summary">
                                <p style="text-align: center; font-weight: bold;">Students Attendance Summary</p>
                                <table>
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
                                        <td><strong>{{ $sumMale = $malePresent + $maleAbsent + $malePermission }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Female</td>
                                        <td>{{ $femalePresent }}</td>
                                        <td>{{ $femaleAbsent }}</td>
                                        <td>{{ $femalePermission }}</td>
                                        <td><strong>{{ $sumFemale = $femalePresent + $femaleAbsent + $femalePermission }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td>{{ $malePresent + $femalePresent }}</td>
                                        <td>{{ $maleAbsent + $femaleAbsent }}</td>
                                        <td>{{ $malePermission + $femalePermission }}</td>
                                        <td><strong>{{ $sumMale + $sumFemale }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <h5 style="text-transform: capitalize; text-align: center; font-size: 20px;">Students Attendance Records</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="text-align: center;">Adm No</th>
                                    <th>Student's Name</th>
                                    <th style="text-align: center;">Gender</th>
                                    <th style="text-align: center;">Stream</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $attendance)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="text-align: center; text-transform: uppercase;">{{ $attendance->admission_number }}</td>
                                        <td>{{ ucwords(strtolower($attendance->first_name . ' ' . $attendance->middle_name . ' ' . $attendance->last_name)) }}</td>
                                        <td style="text-align: center; text-transform: capitalize;">{{ ucfirst($attendance->gender[0]) }}</td>
                                        <td style="text-align: center; text-transform: capitalize;">{{ ucfirst($attendance->class_group) }}</td>
                                        <td style="text-align: center; text-transform: capitalize;">{{ ucfirst($attendance->attendance_status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
