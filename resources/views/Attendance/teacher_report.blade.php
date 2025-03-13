<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Class Teacher General Attendance Report</title>
    <link rel="stylesheet" href="{{public_path('assets/css/print_layout.css')}}">
    <style>
        .footer {
            position: fixed;
            bottom: -30px;
            align-content: space-around;
            font-size: 12px;
            /* border-top: 1px solid black; */
        }
        .page-number:before {
            content: "Page " counter(page);
        }

        /* Inline your Bootstrap CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        @media print {
            .no-print {
                display: none;
            }
            h1, h2, h4, h5, h6 {
                text-transform: uppercase;
                text-align: center
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
            }
            thead {
                display: table-header-group;
                background-color: gray; /* Adds a gray background to thead */
            }
            tbody {
                display: table-row-group;
            }
            .table {
                border: 1px solid black;
                border-collapse: collapse;
                width: 100%;
            }
            .table th,
            .table td {
                border: 1px solid black;
            }
        }

        .table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
        }

        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 2px;
            text-align: left;
            text-transform: capitalize
        }

        .table th {
            background-color: #343a40;
            color: #fff;
            text-align: center;
        }

        .table td {
            background-color: #fff;
        }

        .table img {
            display: block;
            margin: 0 auto;
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            align-content: space-around;
            font-size: 12px;
            /* border-top: 1px solid black; */
        }
        .page-number:before {
            content: "Page " counter(page);
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{public_path('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" style="max-width: 100px; border-radius:50px">
        </div>
        <div class="header">
            <h4>{{ Auth::user()->school->school_name }}</h4>
            <h5>P.O Box {{ Auth::user()->school->postal_address }}, {{ Auth::user()->school->postal_name }}</h5>
            <h5>class attendance report</h5>
            @forelse ( $datas as $month => $attendances )
        </div>
        @if ($attendances->isEmpty())
            <div class="alert alert-warning text-center mt-3" role="alert">
                <p>There is no attendance records for {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
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
                <div class="date-section">
                    <h6 style="text-transform: uppercase; border:1px solid black; border-radius:4px; padding: 2px;">time duration: <strong>{{ \Carbon\Carbon::parse($month)->format('F Y') }}</strong></h6>
                    <div class="summary-header">
                        <p style="line-height: 5px">attendance report summary</p>
                    </div>
                    <div class="summary-content">
                        <div class="course-details">
                            <div>
                                <p>Attendance Date: <strong><span style="text-decoration:underline">{{ \Carbon\Carbon::parse($date)->format('d/F/Y') }}</span></strong></p>
                                <p>Class Teacher Name: <strong>{{ $teacher->teacher_firstname }} {{ $teacher->teacher_lastname }}</strong></p>
                                <p>Class Name: <strong>{{ $teacher->class_name }} <span style="text-transform: uppercase">({{ $teacher->class_code }})</span></strong></p>
                                <p>Stream: <strong>{{ $teacher->group }}</strong></p>
                            </div>
                        </div>
                        <div class="grade-summary">
                            <p style="text-align:center; font-weight:bold">Students attendance summary</p>
                            <table class="table" style="text-align:center">
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
                        </div>
                    </div>
                    <hr>
                    <h5 style="text-transform:capitalize; text-align:center; font-size:20px;">students attendance records</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-center" style="text-align: center">Adm No</th>
                                <th>Student's Name</th>
                                <th class="text-center" style="text-align:center">Gender</th>
                                <th class="text-center" style="text-align:center">Stream</th>
                                <th style="text-align:center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( $records as $attendance )
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td class="text-center" style="text-align: center; text-transform:uppercase">{{$attendance->admission_number }}</td>
                                <td>{{ucwords(strtolower($attendance->first_name. ' '. $attendance->middle_name. ' '.$attendance->last_name ))}}</td>
                                <td style="text-align: center; text-transform:capitalize">{{ ucfirst($attendance->gender[0]) }}</td>
                                <td style="text-align: center; text-transform:capitalize">{{ucfirst($attendance->class_group)}}</td>
                                <td style="text-align: center; text-transform:capitalize">{{ ucfirst($attendance->attendance_status) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
        @empty
            <div class="alert alert-warning mt-3 text-center" role="alert">
                <p>There is no attendance records for the selected time duration!</p>
            </div>
        @endforelse
    </div>
    <div class="footer">
        <footer>
            <div class="page-number"></div>
        </footer>
    </div>
</body>
</html>
