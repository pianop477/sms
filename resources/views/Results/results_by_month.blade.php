<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>General Results</title>
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
            th.vertical {
            writing-mode: vertical-rl; /* or vertical-lr */
            text-orientation: upright;
            transform: rotate(180deg);
            white-space: nowrap;
        }

    </style>
</head>
<body>
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-body">
                <div class="container">
                    <div class="logo">
                        <img src="{{public_path('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" style="max-width: 100px;">
                    </div>
                    <div class="header">
                        <h3>the united republic of tanzania</h3>
                        <h4>the president's office - ralg</h4>
                        <h4>{{Auth::user()->school->school_name}}</h4>
                        <h4>{{$results->first()->exam_type}} Results - {{$month}}, {{$year}}</h4>
                        <h5>Class: {{$results->first()->class_name}}</h5>
                        <h6 style="text-align:end">Term: {{$results->first()->Exam_term}}</h6>
                    </div>
                </div>
                <hr>
                <div class="summary-header">
                    <h5 style="text-transform: uppercase">results evaluation summary</h5>
                </div>
                <div class="summary-content">
                    <p style="text-transform:capitalize">Subjectwise average Ranking</p>
                    <table class="table" style="width: 70%; text-align:center">
                        <tr class="text-center">
                            <th>Course Name</th>
                            <th>Average</th>
                            <th>Grade</th>
                            <th>Rank</th>
                        </tr>
                        @foreach ($sortedCourses as $course)
                            <tr class="text-center">
                                <td style="text-transform: capitalize">{{ $course['course_name'] }}</td>
                                <td>{{ number_format($course['average_score'], 2) }}</td>
                                @if ($course['grade'] == 'A')
                                    <td>{{ $course['grade'] }}</td>
                                @elseif ($course['grade'] == 'B')
                                    <td>{{ $course['grade'] }}</td>
                                @elseif ($course['grade'] == 'C')
                                    <td>{{ $course['grade'] }}</td>
                                @elseif ($course['grade'] == 'D')
                                    <td>{{ $course['grade'] }}</td>
                                @else
                                    <td>{{ $course['grade'] }}</td>
                                @endif
                                <td>{{ $course['position'] }}</td>
                            </tr>
                        @endforeach
                    </table>
                    <p style="text-transform: capitalize"><strong>Subjectwise grade Evaluation Summary</strong></p>
                    <table class="table" style="width: 70%; text-align:center">
                        <tr>
                            <th>Course</th>
                            <th>A</th>
                            <th>B</th>
                            <th>C</th>
                            <th>D</th>
                            <th>E</th>
                        </tr>
                        @foreach ( $evaluationScores as $courseId => $grades )
                            <tr>
                                <td class="text-uppercase">{{ $results->firstWhere('course_id', $courseId)->course_name }}</td>
                                <td>{{ $grades['A'] }}</td>
                                <td>{{ $grades['B'] }}</td>
                                <td>{{ $grades['C'] }}</td>
                                <td>{{ $grades['D'] }}</td>
                                <td>{{ $grades['E'] }}</td>
                            </tr>
                            @endforeach
                    </table>
                    <p style="text-transform: capitalize"><strong>class average performance</strong></p>
                    <table class="table" style="width: 40%; text-align:center">
                        <tr>
                            <td class="text-center">
                                <span>{{ number_format($totalAverageScore, 2) }}</span>
                                @if ($totalAverageScore >= 41 && $totalAverageScore <=  50 || $totalAverageScore >= 81 && $totalAverageScore <= 100)
                                    <span>A</span>
                                @elseif ($totalAverageScore >= 31 && $totalAverageScore >=  40 || $totalAverageScore >= 61 && $totalAverageScore <= 80)
                                    <span>B</span>
                                @elseif ($totalAverageScore >= 21 && $totalAverageScore >=  30 || $totalAverageScore >= 41 && $totalAverageScore <= 60)
                                    <span>C</span>
                                @elseif ($totalAverageScore >= 11 && $totalAverageScore >=  20 || $totalAverageScore >= 21 && $totalAverageScore <= 40)
                                    <span>D</span>
                                @else
                                    <span>E</span>
                                @endif
                            </td>
                            <td>
                                <h4 class="text-center">{{number_format($sumOfCourseAverages, 4)}}</h4>
                            </td>
                        </tr>
                    </table>
                </div>
                <h5 style="text-align: center; text-transform:uppercase">students results records</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="">#</th>
                            <th style="">Student Name</th>
                            <th style="" class="">sex</th>
                            @foreach ($results->groupBy('course_id')->keys() as $courseId)
                                <th style="text-transform: uppercase">{{ $results->firstWhere('course_id', $courseId)->course_code }}</th>
                            @endforeach
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Average</th>
                            <th style="text-align:center;">Grade</th>
                            <th style="text-align:center;">rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sortedStudentsResults as $index => $studentResult)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td style="text-transform:capitalize">{{ $studentResult['student_name'] }}</td>
                                <td style="text-align: center">{{ $studentResult['gender'][0] }}</td>
                                @foreach ($studentResult['courses'] as $course)
                                    <td style="text-align:center;">{{ $course['score'] }}</td>
                                @endforeach
                                <td style="text-align:center">{{ $studentResult['total_marks'] }}</td>
                                <td style="text-align:center">{{ number_format($studentResult['average'], 2) }}</td>
                                <td style="text-align:center; text-transform:uppercase">{{ $studentResult['grade'] }}</td>
                                <td style="text-align:center">{{ $studentResult['position'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
