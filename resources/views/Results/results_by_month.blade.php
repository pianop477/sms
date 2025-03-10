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
            flex-direction: row;
            flex-wrap: wrap;
            border-bottom: 2px solid gray;
            margin-top: 5px;
        }
        @page {
            margin: 8mm;
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
            /* margin-bottom: 10px; */
            font-size: 24px;
            color: #343a40;
        }
        .summary-header {
            margin-top: 2px;
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

            .table th {
                background-color: #343a40;
                color: #fff;
                text-align: center;
            }
            .table th,
            .table td {
                /* border: 1px solid black; */
                text-transform: capitalize;
            }

            .table td {
                background-color: #fff;
            }

            .details {
                text-transform: uppercase;
                line-height: 0.5mm;
                padding: 0;
                border-bottom: 2px dashed gray;
            }
            .total-summary {
                padding: 2px;
                border-bottom: 2px dashed gray;
            }
            .results {
                font-size: 12px;
                margin-top: 10px;
            }
            .final-summary {
                text-transform: uppercase;
            }
            footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            line-height: 35px;
            font-size: 12px;

        }
        .footer {
            position: fixed;
            bottom: -30px;
            align-content: space-around;
            font-size: 12px;
            border-top: 1px solid black;
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
                        <h3>the united republic of tanzania</h3>
                        <h4>the president's office - ralg</h4>
                        <h4>{{Auth::user()->school->school_name}}</h4>
                    </div>
                </div>
                <div class="details">
                    <h4>{{$results->first()->class_name}} {{$results->first()->exam_type}} Results - {{$month}}, {{$year}}</h4>
                    <h5 style="font-weight:normal">TERM: {{$results->first()->Exam_term}}</h5>
                    <h5 style="font-weight:normal">NUMBER OF CANDIDATES: {{$totalUniqueStudents}}</h5>
                    <h5 style="font-weight:normal">CLASS AVERAGE: <strong>{{number_format($sumOfCourseAverages, 4)}}</strong>
                            @if ($totalAverageScore >= 41 && $totalAverageScore <=  50 || $totalAverageScore >= 81 && $totalAverageScore <= 100)
                                    <span style="background:rgb(117, 244, 48); padding:2px 10px ">GRADE A (EXCELLENT)</span>
                                @elseif ($totalAverageScore >= 31 && $totalAverageScore >=  40 || $totalAverageScore >= 61 && $totalAverageScore <= 80)
                                    <span style="background:rgb(12, 211, 184); padding:2px 10px ">GRADE B (GOOD)</span>
                                @elseif ($totalAverageScore >= 21 && $totalAverageScore >=  30 || $totalAverageScore >= 41 && $totalAverageScore <= 60)
                                    <span style="background:rgb(237, 220, 113); padding:2px 10px ">GRADE C (PASS)</span>
                                @elseif ($totalAverageScore >= 11 && $totalAverageScore >=  20 || $totalAverageScore >= 21 && $totalAverageScore <= 40)
                                    <span style="background:rgb(235, 75, 75); padding:2px 10px "> GRADE D (UNSATISFACTORY)</span>
                                @else
                                    <span style="background:rgb(182, 176, 176); padding:2px 10px ">GRADE E (FAIL)</span>
                                @endif
                    </h5>
                </div>
                <div class="total-summary results">
                    <table class="table" style="text-align:center; width:60%">
                        <tr>
                            <td>Gender</td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                            <td>D</td>
                            <td>E</td>
                        </tr>
                        <tr>
                            <td>GIRLS</td>
                            <td>{{$totalFemaleGrades['A']}}</td>
                            <td>{{$totalFemaleGrades['B']}}</td>
                            <td>{{$totalFemaleGrades['C']}}</td>
                            <td>{{$totalFemaleGrades['D']}}</td>
                            <td>{{$totalFemaleGrades['E']}}</td>
                        </tr>
                        <tr>
                            <td>BOYS</td>
                            <td>{{$totalMaleGrades['A']}}</td>
                            <td>{{$totalMaleGrades['B']}}</td>
                            <td>{{$totalMaleGrades['C']}}</td>
                            <td>{{$totalMaleGrades['D']}}</td>
                            <td>{{$totalMaleGrades['E']}}</td>
                        </tr>
                        <tr>
                            <td>TOTAL</td>
                            <td>{{$totalFemaleGrades['A'] + $totalMaleGrades['A']}}</td>
                            <td>{{$totalFemaleGrades['B'] +$totalMaleGrades['B']}}</td>
                            <td>{{$totalFemaleGrades['C'] +$totalMaleGrades['C']}}</td>
                            <td>{{$totalFemaleGrades['D'] +$totalMaleGrades['D']}}</td>
                            <td>{{$totalFemaleGrades['E'] +$totalMaleGrades['E']}}</td>
                        </tr>
                    </table>
                </div>
                <table class="table results">
                    <thead>
                        <tr>
                            <th>Reg No.</th>
                            <th style="" class="">sex</th>
                            <th style="">Student Name</th>
                            @foreach ($results->groupBy('course_id')->keys() as $courseId)
                                <th style="text-transform: uppercase">{{ $results->firstWhere('course_id', $courseId)->course_code }}</th>
                            @endforeach
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Average</th>
                            <th style="text-align:center;">Grade</th>
                            <th style="text-align:center;">Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sortedStudentsResults as $index => $studentResult)
                            <tr>
                                <td style="text-align: center; text-transform:uppercase">{{$studentResult['admission_number']}}</td>
                                <td style="text-align: center">{{ $studentResult['gender'][0] }}</td>
                                <td style="text-transform:capitalize">{{ ucwords(strtolower($studentResult['student_name'])) }}</td>
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
                <div class="final-summary">
                    <table class="table results" style="width:100%">
                        <tr>
                            <th colspan="5">SUBJECTWISE PERFORMANCE</th>
                        </tr>
                        <tr style="">
                            <th style="text-transform: capitalize">Subject Name</th>
                            <th style="text-transform: capitalize">code</th>
                            <th style="text-align:center">average</th>
                            <th style="text-align:center">position</th>
                            <th style="text-align:center">grade</th>
                        </tr>
                        @foreach ($sortedCourses as $course)
                            <tr>
                                <td style="text-transform: uppercase">{{ $course['course_name'] }} </td>
                                <td style="text-transform: uppercase; text-align:center">{{ $course['course_code'] }} </td>
                                <td style="text-align:center">{{ number_format($course['average_score'], 2) }}</td>
                                <td style="text-align:center">{{ $course['position'] }}</td>
                                    @if ($course['grade']=='A')
                                        <td style="background:rgb(117, 244, 48); padding:2px 10px ">grade {{ $course['grade']}} (EXCELLENT)</td>
                                    @elseif ($course['grade']=='B')
                                        <td style="background:rgb(12, 211, 184); padding:2px 10px ">grade {{ $course['grade']}} (VERY GOOD)</td>
                                    @elseif ($course['grade']=='C')
                                        <td style="background:rgb(237, 220, 113); padding:2px 10px ">grade {{ $course['grade']}} (PASS)</td>
                                    @elseif ($course['grade']=='D')
                                        <td style="background:rgb(235, 75, 75); padding:2px 10px ">grade {{ $course['grade']}} (POOR)</td>
                                    @else
                                        <td style="background:rgb(182, 176, 176); padding:2px 10px ">grade {{ $course['grade']}} (FAIL)</td>
                                    @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="page-number"></div>
    </footer>
</body>
</html>
