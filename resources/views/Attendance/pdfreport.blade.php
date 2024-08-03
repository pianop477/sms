<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daily Attendance Report</title>
    <style>
        /* Inline your Bootstrap CSS styles here */
        body {
            font-family: Arial, sans-serif;
            /* line-height: 2px; */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
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

        .container {
            display: flex;
            padding: 10px;
            flex-direction: row;
            flex-wrap: wrap;
            border-bottom: 2px solid gray;
        }
        .logo {
            position: absolute;
            width: 50px;
            left: 7px;
            top: 5px;
            color: inherit;
        }
        .header {
            text-align: center;
            position: relative;
            top: 0;
            left: 40px;
            text-transform: uppercase;
            line-height: 1px;
        }
        .summary-header {
            margin-top: 5px;
            text-align: center;
            text-transform: capitalize;
            font-size: 20px;
        }
        .summary-content {
            display: flex;
            flex-direction: row;
            text-transform: capitalize
        }
        .course-details {
            position: relative;
            left: 5px;
            width: 50%;
            line-height: 5px;
        }
        .grade-summary {
            position: absolute;
            width: 50%;
            left: 50%;
            top: 17%
        }
        th, td {
            border: 1px solid black;
        }
        .table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            padding: 4px;
        }
        thead {
                display: table-header-group;
                background-color: gray; /* Adds a gray background to thead */
            }
            tbody {
                display: table-row-group;
            }
            .table th,
            .table td {
                /* border: 1px solid black; */
                text-transform: capitalize;
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
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-body">
                <div class="container">
                    <div class="logo">
                        <img src="{{public_path('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" style="max-width: 80px;">
                    </div>
                    <div class="header">
                        <h4 class="text-uppercase">{{Auth::user()->school->school_name}}</h4>
                        <h5 class="text-uppercase">class daily attendance report</h5>
                    </div>
                </div>
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
                    <div class="summary-header">
                        <h6 class="text-center" style="text-transform: uppercase; border-bottom: 1px solid black;">Attendance Summary</h6>
                    </div>
                </div>
                <div class="summary-content">
                    <div class="course-details">
                        <p class="text-center font-weight-bold text-capitalize p-2" style="text-transform: uppercase; border-bottom: 1px solid black;">class details</p>
                        <p>Attendance Date: <span class="float-right"><strong>{{\Carbon\Carbon::parse($attendanceRecords->first()->attendance_date)->format('d-F-Y')}}</strong></span></p>
                        <p>class teacher name: <span class="float-right"><strong>{{ $attendanceRecords->first()->teacher_firstname }} {{ $attendanceRecords->first()->teacher_lastname }}</strong></span></p>
                        <p>Class name: <span class="" style="text-transform: uppercase"><strong>{{ $attendanceRecords->first()->class_name }} ({{ $attendanceRecords->first()->class_code }})</strong></span></p>
                        <p>class teacher phone: <span class="float-right"><strong>{{ $attendanceRecords->first()->teacher_phone }}</strong></span></p>
                        <p>Class Stream: <span style="text-transform: capitalize"><strong>{{$attendanceRecords->first()->class_group}}</strong></span> </p>
                    </div>
                    <div class="grade-summary">
                        <p class="text-center font-weight-bold text-capitalize p-2" style="text-transform: uppercase; border-bottom:1px solid black;">attendance details</p>
                        <table class="table table-hover table-bordered" style="text-align:center">
                            <thead>
                                <tr>
                                    <th>Gender</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Permission</th>
                                    <th>Sum</th>
                                </tr>
                            </thead>
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
                                        <td>Total</td>
                                        <td><strong>{{$malePresent +  $femalePresent}}</strong></td>
                                        <td><strong>{{$maleAbsent +  $femaleAbsent}}</strong></td>
                                        <td><strong>{{$malePermission +  $femalePermission}}</strong></td>
                                        <td><strong>{{$sumMale +  $sumFemale}}</strong></td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
                </div>
                <!-- Detailed Attendance Records -->
                <div>
                    <p style="text-align:center; text-transform:uppercase">students attendance records</p>
                <table class="table table-bordered table-hover">
                    <thead>
                        <th style="width: 5px">#</th>
                        <th class="text-center" style="text-align: center">Admission No</th>
                        <th>Student Name</th>
                        <th style="width: 10px" style="text-align: center" class="text-center">Gender</th>
                        <th style="width: 10px" style="text-align: center" class="text-center">Stream</th>
                        <th style="text-align: center">Attendance Status</th>
                    </thead>
                    <tbody>
                        @foreach ($attendanceRecords as $record )
                            <tr>
                                <td style="text-align: center">{{$loop->iteration}}</td>
                                <td style="text-align: center" class="text-center"><span style="text-transform: uppercase">{{$record->school_reg_no}}</span>-{{ str_pad($record->studentId, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-capitalize">{{ $record->first_name }} {{ $record->middle_name }} {{ $record->last_name }}</td>
                                <td style="text-align: center" class="text-capitalize text-center">{{ $record->gender[0] }}</td>
                                <td style="text-align: center" class="text-capitalize text-center">{{ $record->class_group }}</td>
                                <td style="text-align: center">{{ ucfirst($record->attendance_status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @endif
            </div>
        </div>
    </div>
    <div class="footer">
        <footer>
            <div class="page-number"></div>
        </footer>
    </div>
</body>
</html>
