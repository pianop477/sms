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
        .container {
            display: flex;
            padding: 5px;
            flex-direction: row;
            flex-wrap: wrap;
        }
        .logo {
            position: absolute;
            width: 70px;
            left: 7px;
            top: 20px;
            color: inherit;
        }

        .header h4, .header h5, .header h6 {
            margin: 2px 0; /* Kupunguza nafasi juu na chini */
            line-height: 1.2; /* Kufanya mistari iwe karibu */
            text-align: center;
            text-transform: uppercase;
        }
        .header {
            text-align: center;
            position: relative;
            top: 0;
            left: 40px;
            /* margin-bottom: 10px; */
            text-transform: uppercase;
            font-size: 24px;
            color: #343a40;
        }
        .table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
        }

        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 2px;
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
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-body">
                <div class="container">
                    <div class="logo">
                        <img src="{{public_path('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" style="max-width: 70px;">
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
                        <h6 class="text-center" style="text-transform: uppercase; border-bottom: 1px solid black;"></h6>
                    </div>
                </div>
                <div class="summary-content">
                    <div class="course-details">
                        <p class="" style="text-transform: uppercase; text-decoration:underline">class details</p>
                        <p>Attendance Date: <span class="float-right"><strong>{{\Carbon\Carbon::parse($attendanceRecords->first()->attendance_date)->format('d-F-Y')}}</strong></span></p>
                        <p>Class Teacher Name: <span class="float-right"><strong>{{ $attendanceRecords->first()->teacher_firstname }} {{ $attendanceRecords->first()->teacher_lastname }}</strong></span></p>
                        <p>Class: <span class="" style="text-transform: uppercase"><strong>{{ $attendanceRecords->first()->class_name }} - {{$attendanceRecords->first()->class_group}}</strong></span></p>
                        <p>Class Teacher Phone: <span class="float-right"><strong>{{ $attendanceRecords->first()->teacher_phone }}</strong></span></p>
                    </div>
                    <div class="grade-summary">
                        <p class="" style="text-transform: uppercase; text-decoration:underline">attendance details</p>
                        <table class="table table-hover table-bordered">
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
                                        <td style="text-align: center">Male</td>
                                        <td style="text-align: center">{{ $malePresent }}</td>
                                        <td style="text-align: center">{{ $maleAbsent }}</td>
                                        <td style="text-align: center">{{ $malePermission }}</td>
                                        <td style="text-align: center"><strong>{{$sumMale = $malePresent + $maleAbsent + $malePermission}}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center">Female</td>
                                        <td style="text-align: center">{{ $femalePresent }}</td>
                                        <td style="text-align: center">{{ $femaleAbsent }}</td>
                                        <td style="text-align: center">{{ $femalePermission }}</td>
                                        <td style="text-align: center"><strong>{{$sumFemale = $femalePresent  + $femaleAbsent + $femalePermission  }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center">Total</td>
                                        <td style="text-align: center"><strong>{{$malePresent +  $femalePresent}}</strong></td>
                                        <td style="text-align: center"><strong>{{$maleAbsent +  $femaleAbsent}}</strong></td>
                                        <td style="text-align: center"><strong>{{$malePermission +  $femalePermission}}</strong></td>
                                        <td style="text-align: center"><strong>{{$sumMale +  $sumFemale}}</strong></td>
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
                        <th class="text-center" style="text-align: center">Adm No</th>
                        <th>Student Name</th>
                        <th style="width: 10px" style="text-align: center" class="text-center">Gender</th>
                        <th style="width: 10px" style="text-align: center" class="text-center">Stream</th>
                        <th style="text-align: center">Status</th>
                    </thead>
                    <tbody>
                        @foreach ($attendanceRecords as $record )
                            <tr>
                                <td style="text-align: center">{{$loop->iteration}}</td>
                                <td style="text-align: center" class="text-center"><span style="text-transform: uppercase">{{$record->admission_number }}</td>
                                <td>{{ucwords(strtolower($record->first_name. ' '. $record->middle_name. ' '.$record->last_name  ))}}</td>
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
