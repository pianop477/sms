<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Student Academic Report</title>
    <style>
        .student-image {
            position: absolute;
            top: 20%;
            /* right: 0px; */
            left: 80%;
            color: inherit;
        }

        body {
            font-family: Arial, sans-serif;
            /* line-height: 2px; */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
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
                background-color: rgb(194, 191, 191); /* Adds a gray background to thead */
            }
            tbody {
                display: table-row-group;
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
            /* border-bottom: 2px solid gray; */
        }
        .logo {
            position: absolute;
            left: 0px;
            top: 5px;
            color: inherit;
        }
        .header {
            text-align: center;
            position: relative;
            top: 0;
            left: 0px;
            text-transform: uppercase;
            line-height: 1px;
        }
        .summary-header {
            margin-top: 3px;
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
        thead {
                display: table-header-group;
                background-color: rgb(202, 199, 199); /* Adds a gray background to thead */
            }
            tbody {
                display: table-row-group;
            }
            .table th,
            .table td {
                /* border: 1px solid black; */
                text-transform: capitalize;
            }
            th.vertical {
            writing-mode: vertical-rl; /* or vertical-lr */
            text-orientation: upright;
            transform: rotate(180deg);
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin-bottom: 30px;
        }

    .table th {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            }

    .table td {
            background-color: #fff;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #333; /* Rangi ya background (Dark Gray) */
            color: white; /* Rangi ya maandishi */
            font-size: 12px;
            text-align: center;
            padding: 5px 0; /* Padding juu na chini */
            box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.2); /* Kuongeza kivuli juu ya footer */
        }
    </style>
</head>
<body>
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-body">
                <div class="container">
                    <div class="logo">
                        <img src="{{public_path('assets/img/logo/'.$results->first()->logo)}}" alt="" style="max-width: 70px;">
                    </div>
                    <div class="header" style="">
                        <h3>united republic of tanzania</h3>
                        <h4>president office - TAMISEMI</h4>
                        <h4>{{$results->first()->school_name}}</h4>
                        <h6>{{$results->first()->postal_address}} - {{$results->first()->postal_name}}, {{$results->first()->country}}</h6>
                        <h6>academic progressive report</h6>
                    </div>
                    <div class="student-image">
                        @php
                            $imagePath = public_path('assets/img/students/' . $studentId->image);
                            $defaultImagePath = public_path('assets/img/students/student.jpg');
                        @endphp

                        @if(file_exists($imagePath) && !is_dir($imagePath))
                            <img src="{{ $imagePath }}" alt="Student Image" style="max-width: 100px; height:100px; border-radius:50px;">
                        @else
                            <img src="{{ $defaultImagePath }}" alt="" style="max-width: 100px; border-radius:50px;">
                        @endif
                        <p style="font-size:10px;">Admission No: <span style="text-decoration: underline;">{{ucwords(strtoupper($results->first()->admission_number))}}</span></p>
                    </div>
                </div>
                <div class="" style="border-bottom: 2px solid gray">
                    <div class="info-container student-info">
                        <p style="text-transform:capitalize; font-weight:bold; text-align:center">A. Student Information</p>
                        <p><strong>Student Full Name:</strong> <span style="text-decoration:underline">{{ ucwords(strtoupper($studentId->first_name. ' '.$studentId->middle_name . ' '. $studentId->last_name )) }}</span></p>
                        <p><strong>Gender:</strong> <span style="text-transform: uppercase; text-decoration:underline">{{ $studentId->gender}}</span></p>
                        <p><strong>Class:</strong> <span style="text-transform: uppercase; text-decoration:underline">{{ $results->first()->class_name }}</span></p>
                        <p><strong>Stream:</strong> <span style="text-transform: uppercase; text-decoration:underline">{{ $studentId->group }}</span></p>
                    </div>
                    <p style="border-bottom: 2px solid gray;"></p>
                    <p style="text-transform:capitalize; font-weight:bold; text-align:center">B. Examination Details</p>
                    <div class="info-container exam-info">
                        <p><strong>Examination Type:</strong> <span style="text-transform: uppercase; text-decoration:underline">{{ $results->first()->exam_type }}</span></p>
                        <p><strong>Exam Date:</strong> <span style="text-decoration:underline">{{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</span></p>
                        <p><strong>Term:</strong> <span style="text-transform: uppercase; text-decoration:underline">{{$results->first()->Exam_term}}</span></p>
                    </div>
                </div>
                <p style="text-transform:capitalize; font-weight:bold; text-align:center">C. Student overall performance </p>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Code</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                            <th>Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $index => $result)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td style="text-align: left; text-transform: capitalize">{{ $result->course_name }}</td>
                                <td style="text-transform: capitalize">{{ $result->teacher_first_name[0] }}.{{ $result->teacher_last_name }}</td>
                                <td style="text-transform: uppercase">{{ $result->course_code }}</td>
                                <td>{{ $result->score }}</td>
                                <td>{{ $result->grade }}</td>
                                <td>{{ $result->remarks }}</td>
                                <td>{{ $result->courseRank }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="cut" style="border-bottom: 2px dashed gray;">
                    <p>Total Marks: <strong>{{ $totalScore }}</strong></p>
                    <p>Overall Average: <strong>{{ number_format($averageScore, 2) }}</strong></p>
                    @php
                        $grade = '';
                        $gradeColor = '';
                        if ($averageScore >= 41 || $averageScore >= 81) {
                            $grade = "'A' - EXCELLENT";
                            $gradeColor = 'rgb(117, 244, 48)';
                        } elseif ($averageScore >= 31 || $averageScore >= 61) {
                            $grade = "'B' - GOOD";
                            $gradeColor = 'rgb(12, 211, 184)';
                        } elseif ($averageScore >= 21 || $averageScore >= 41) {
                            $grade = "'C' - PASS";
                            $gradeColor = 'rgb(237, 220, 113)';
                        } elseif ($averageScore >= 11 || $averageScore >= 21) {
                            $grade = "'D' - POOR";
                            $gradeColor = 'rgb(182, 176, 176)';
                        } elseif ($averageScore <= 10 || $averageScore <= 20) {
                            $grade = "'E' - FAIL";
                            $gradeColor = ' rgb(235, 75, 75)';
                        }
                    @endphp
                    <p>Grade Level: <strong><span style="background: {{$gradeColor}}; padding:1px 4px">{{$grade}}</span></strong></p>
                    <p>Ranked: <strong><span style="text-decoration: underline;">{{ $studentRank }}</span></strong> out of <strong><span style="text-decoration: underline">{{ $rankings->count() }}</span></strong> students</p>
                </div>
                <div class="footer" style="">
                    <p style="line-height: 1px;">
                        <i>&copy; Copyright {{$results->first()->school_name}} - {{\Carbon\Carbon::now()->format('Y')}}</i>
                    </p>
                </div>
        </div>
    </div>
</body>
</html>
