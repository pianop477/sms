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
            /* border-bottom: 2px solid gray; */
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
            width: 40%;
            line-height: 5px;
        }
        .grade-summary {
            position: absolute;
            width: 40.3%;
            left: 45%;
            top: 23%
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

    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{public_path('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" style="max-width: 100px;">
        </div>
        <div class="header">
            <h3>{{_('the united republic of tanzania')}}</h3>
            <h4>{{_("the president's office - ralg")}}</h4>
            <h4>{{ Auth::user()->school->school_name }} - P.O Box {{ Auth::user()->school->postal_address }}, {{ Auth::user()->school->postal_name }}</h4>
            <h5>{{ $results->first()->exam_type }} Results for {{ $month }} - {{ $year }}</h5>
        </div>
    </div>
    <hr>
    <div class="summary-header">
        <h5>Overall Summary</h5>
        <hr>
    </div>
    <div class="summary-content">
        <div class="course-details">
            <div>
                <p>Course Name: <strong>{{$courses->course_name}} - {{$courses->course_code}}</strong></p>
                <p>Course Instructor: <strong>{{$results->first()->teacher_firstname}}, {{$results->first()->teacher_lastname[0]}}</strong></p>
                <p>Examination: <strong>{{$results->first()->exam_type}}</strong></p>
                <p>Examination Date: <strong>{{\Carbon\Carbon::parse($results->first()->exam_date)->format('d-F-Y')}}</strong></p>
                <p>Examination Term: <strong><span style="text-transform: uppercase">{{$results->first()->Exam_term}}</span></strong></p>
            </div>
        </div>
        <div>
            <div class="grade-summary">
                <p style="">students overall performance</p>
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
                            <td>Number</td>
                            <td>{{$gradeCounts['A']}}</td>
                            <td>{{$gradeCounts['B']}}</td>
                            <td>{{$gradeCounts['C']}}</td>
                            <td>{{$gradeCounts['D']}}</td>
                            <td>{{$gradeCounts['E']}}</td>
                        </tr>
                    </table>
            </div>
        </div>
    </div>
    <hr>
    <h5 style="text-transform:capitalize; text-align:center; font-size:20px;">students examination results records</h5>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th style="text-align:center">Admission No.</th>
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
                    <td style="text-align:center"><span style="text-transform: uppercase">{{$result->school_reg_no}}</span>-{{Str_pad($result->studentId, 4, '0', STR_PAD_LEFT)}}</td>
                    <td>{{$result->first_name}} {{$result->middle_name}} {{$result->last_name}}</td>
                    <td style="text-align:center">{{$result->gender[0]}}</td>
                    <td style="text-align:center">{{$result->group}}</td>
                    <td style="text-align:center">{{$result->score}}</td>
                    <td style="text-align:center">{{$result->grade}}</td>
                    <td style="text-align:center">{{$result->position}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <div style="align-items: center">
        <span><strong>Course Average: {{number_format($averageScore, 2)}}</strong></span>,
        <span><strong>Grade: {{$averageGrade}}</strong></span>
    </div>
    <hr>
</body>
</html>
