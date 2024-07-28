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
            top: 24%;
            right: 0px;
            color: inherit;
        }

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
        hr {
            margin-top: 0px;
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
        .thanks {
            position: fixed;
            bottom: 0;
            align-content: center;
        }
    </style>
</head>
<body>
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-body">
                <div class="container">
                    <div class="logo">
                        <img src="{{public_path('assets/img/logo/'.$results->first()->logo)}}" alt="" style="max-width: 100px;">
                    </div>
                    <div class="header">
                        <h3>the united republic of tanzania</h3>
                        <h4>the president's office - ralg</h4>
                        <h4>{{$results->first()->school_name}}</h4>
                        <h6>P.O Box {{$results->first()->postal_address}} - {{$results->first()->postal_name}}, {{$results->first()->country}}</h6>
                        <h6>Student academic progress report</h6>
                    </div>
                    <div class="student-image">
                        <img src="{{public_path('assets/img/students/'.$student->image)}}" alt="" style="max-width: 100px; border:1px solid black; border-radius:4px;">
                        <p style="text-transform: uppercase; text-align:center; padding-top;0">{{$results->first()->school_reg_no}}-{{Str_pad($results->first()->studentId, 4, '0', STR_PAD_LEFT)}}</p>
                    </div>
                </div>
                <hr>
                <div class="info-row">
                    <h5 style="text-align: center;">Student Information</h5>
                    <div class="info-container student-info">
                        <p><strong>Student Name:</strong> <span style="text-transform: uppercase">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</span></p>
                        <p><strong>Class:</strong> <span style="text-transform: uppercase">{{ $student->gender}}</span></p>
                        <p><strong>Class:</strong> <span style="text-transform: uppercase">{{ $results->first()->class_name }}({{$results->first()->class_code}})</span></p>
                        <p><strong>Stream:</strong> <span style="text-transform: uppercase">{{ $student->group }}</span></p>
                    </div>
                    <hr>
                    <h5 style="text-align: center">Examination Details & Scores</h5>
                    <div class="info-container exam-info">
                        <p><strong>Exam Type:</strong> <span style="text-transform: uppercase">{{ $results->first()->exam_type }}</span></p>
                        <p><strong>Month:</strong> {{ $month }}</p>
                        <p><strong>Year:</strong> {{ $year }}</p>
                        <p><strong>Term:</strong> <span style="text-transform: uppercase">{{$results->first()->Exam_term}}</span></p>
                    </div>
                </div>
                <h5 style="text-align: center">Student Academic Performance</h5>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course Name</th>
                            <th>Course Code</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                            <th>Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $index => $result)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $result->course_name }}</td>
                                <td>{{ $result->course_code }}</td>
                                <td>{{ $result->score }}</td>
                                <td>{{ $result->grade }}</td>
                                <td>{{ $result->remarks }}</td>
                                <td>{{ $result->courseRank }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p>Total Marks: <strong>{{ $totalScore }}</strong></p>
                <p>Average: <strong>{{ number_format($averageScore, 2) }}</strong></p>
                <p>Position: <strong>{{ $studentRank }} out of {{ $rankings->count() }}</strong></p>
                <hr>
                <div class="thanks">
                    <i>End of Report. Thank you</i>
                </div>
        </div>
    </div>
</body>
</html>
