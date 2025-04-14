<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Subject results</title>

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
            width: 100%;
            line-height: 5px;
        }
        .grade-summary {
            position: absolute;
            width: 50%;
            left: 45%;
            top: 23%
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

    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{public_path('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" style="max-width: 70px;">
        </div>
        <div class="header">
            <h3>{{_('the united republic of tanzania')}}</h3>
            <h4>{{_("the president's office - ralg")}}</h4>
            <h4>{{ Auth::user()->school->school_name }} - {{ Auth::user()->school->postal_address }}, {{ Auth::user()->school->postal_name }}</h4>
            <h5>{{ $results->first()->exam_type }} Results for {{ $month }} - {{ $year }}</h5>
        </div>
    </div>
    <hr>
    <div class="summary-content">
        <div class="course-details">
            <p style="font-weight: bold">Course information Summary</p>
            <hr>
            <div>
                <p>Subject: <span style="text-transform: uppercase"><strong>{{$subjectCourse->course_name}} - {{$subjectCourse->course_code}}</strong></span></p>
                <p>Teacher: <strong>{{$results->first()->teacher_firstname}}, {{$results->first()->teacher_lastname[0]}}</strong></p>
                <p>Exam Type: <strong>{{$results->first()->exam_type}}</strong></p>
                <p>Date: <strong>{{\Carbon\Carbon::parse($results->first()->exam_date)->format('d-F-Y')}}</strong></p>
                <p>Term: <strong><span style="text-transform: uppercase">{{$results->first()->Exam_term}}</span></strong></p>
            </div>
        </div>
    </div>
    <hr>
    <div class="">
        <p style="">Performance Summary</p>
            <table class="table" style="border: 1px solid black; border-collapse:collapse; text-align:center">
                <tr>
                    <th>Grade</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>E</th>
                </tr>
                <tr>
                    <td style="text-align: center">Number</td>
                    <td style="text-align: center">{{$gradeCounts['A']}}</td>
                    <td style="text-align: center">{{$gradeCounts['B']}}</td>
                    <td style="text-align: center">{{$gradeCounts['C']}}</td>
                    <td style="text-align: center">{{$gradeCounts['D']}}</td>
                    <td style="text-align: center">{{$gradeCounts['E']}}</td>
                </tr>
            </table>
    </div>
    <div style="align-items: center">
        @php
            if($averageGrade == 'A') {
                $bgColor = 'rgb(117, 244, 48)';
                $comment = 'Excellent';
            }
            if($averageGrade == 'B') {
                $bgColor = 'rgb(12, 211, 184)';
                $comment = 'Good';
            }
            if($averageGrade == 'C') {
                $bgColor = 'rgb(237, 220, 113)';
                $comment = 'Pass';
            }
            if($averageGrade == 'D') {
                $bgColor = 'rgb(235, 75, 75)';
                $comment = 'Poor';
            }
            if($averageGrade == 'E') {
                $bgColor = 'rgb(182, 176, 176)';
                $comment = 'Fail';
            }
        @endphp
        <p><strong>Average: {{number_format($averageScore, 2)}}</strong></p>
        <p><strong>Grade: <span style="padding: 4px; background:{{$bgColor}}">{{$averageGrade}} - {{$comment}}</span></strong></p>
    </div>
    <hr>
    <p style="text-transform:capitalize; text-align:center;">students examination results records</p>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th style="text-align:center">Reg No.</th>
                <th>Student Name</th>
                <th style="text-align:center">Gender</th>
                <th style="text-align:center">Stream</th>
                <th style="text-align:center">Marks</th>
                <th style="text-align:center">Grade</th>
                <th style="text-align:center">Position</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $result)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td style="text-align:center"><span style="text-transform: uppercase">{{$result->admission_number}}</td>
                    <td>{{ucwords(strtolower($result->first_name. ' '. $result->middle_name. ' '.$result->last_name  ))}}</td>
                    <td style="text-align:center">{{$result->gender[0]}}</td>
                    <td style="text-align:center">{{$result->group}}</td>
                    <td style="text-align:center">
                        @if ( $result->score === null)
                            <span style="background:rgb(235, 75, 75); padding:2px 10px ">X</span>
                        @else
                            {{ $result->score }}
                        @endif
                    </td>
                    <td style="text-align:center">{{$result->grade}}</td>
                    <td style="text-align:center">
                        @if ($result->score === null)
                            <span style="background:rgb(235, 75, 75); padding:2px 10px ">X</span>
                        @else
                            {{$result->position}}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
