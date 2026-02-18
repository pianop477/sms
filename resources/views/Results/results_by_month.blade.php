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
            background-color: white;
        }

        @media print {
            .no-print {
                display: none;
            }

            h1,
            h2,
            h4,
            h5,
            h6 {
                text-transform: uppercase;
                text-align: center
            }

            .print-only {
                display: block;
            }

            thead {
                display: table-header-group;
                background-color: gray;
                /* Adds a gray background to thead */
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

            footer {
                display: block !important;
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
            margin-top: 6mm;
            margin-bottom: 6mm;
            /* Ongeza nafasi ya chini kwa footer */
            margin-left: 6mm;
            margin-right: 6mm;
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

        th,
        td {
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
            /* background-color: gray; Adds a gray background to thead */
        }

        tbody {
            display: table-row-group;
        }

        .table th {
            /* background-color: #343a40; */
            /* color: #fff; */
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
            bottom: 0;
            left: 0;
            right: 0;
            height: 4mm;
            /*urefu wa footer*/
            font-size: 10px;
            /* padding-top: 3px; */
            border-top: 1px solid #ddd;
            text-align: center;
            background-color: white;
            z-index: 1000;
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

        /* Division background colors */
        .division-i {
            /* background-color: rgb(117, 244, 48) !important; */
        }

        .division-ii {
            /* background-color: rgb(153, 250, 237) !important; */
        }

        .division-iii {
            /* background-color: rgb(237, 220, 113) !important; */
        }

        .division-iv {
            /* background-color: rgb(182, 176, 176) !important; */
        }

        .division-zero {
            /* background-color: rgb(235, 75, 75) !important; */
        }
    </style>
</head>

<body>
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-body">
                <div class="container">
                    <div class="logo">
                        <img src="{{ storage_path('app/public/logo/' . Auth::user()->school->logo) }}" alt=""
                            style="max-width: 80px;">
                    </div>
                    <div class="header">
                        <h3>the united republic of tanzania</h3>
                        <h4>the president's office - ralg</h4>
                        <h4>{{ Auth::user()->school->school_name }}</h4>
                    </div>
                </div>
                <div class="details">
                    <h4>{{ $results->first()->class_name }} {{ $results->first()->exam_type }} Results -
                        {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</h4>
                    <h5 style="font-weight:normal">TERM: {{ $results->first()->Exam_term }}</h5>
                    <h5 style="font-weight:normal">NUMBER OF CANDIDATES: {{ $totalUniqueStudents }}</h5>

                    @if ($marking_style == 3)
                        <!-- Display nothing for marking style 3 -->
                    @else
                        <h5 style="font-weight:normal">CLASS AVERAGE:
                            <strong>{{ number_format($sumOfCourseAverages, 4) }}</strong>
                            @if ($marking_style == 1)
                                @if ($generalClassAvg >= 40.5)
                                    <span style="background:rgb(117, 244, 48); padding:2px 10px; ">GRADE A
                                        (EXCELLENT)</span>
                                @elseif ($generalClassAvg >= 30.5)
                                    <span style="background:rgb(153, 250, 237); padding:2px 10px;">GRADE B (GOOD)</span>
                                @elseif ($generalClassAvg >= 20.5)
                                    <span style="background:rgb(237, 220, 113); padding:2px 10px;">GRADE C (PASS)</span>
                                @elseif ($generalClassAvg >= 10.5)
                                    <span style="background:rgb(182, 176, 176); padding:2px 10px;"> GRADE D
                                        (POOR)</span>
                                @elseif($generalClassAvg <= 10.4)
                                    <span style="background:rgb(235, 75, 75); padding:2px 10px;">GRADE E (FAIL)</span>
                                @endif
                            @else
                                @if ($generalClassAvg >= 80.5)
                                    <span style="background:rgb(117, 244, 48); padding:2px 10px;">GRADE A
                                        (EXCELLENT)</span>
                                @elseif ($generalClassAvg >= 60.5)
                                    <span style="background:rgb(153, 250, 237); padding:2px 10px;">GRADE B (GOOD)</span>
                                @elseif ($generalClassAvg >= 40.5)
                                    <span style="background:rgb(237, 220, 113); padding:2px 10px">GRADE C (PASS)</span>
                                @elseif ($generalClassAvg >= 20.5)
                                    <span style="background:rgb(182, 176, 176); padding:2px 10px;"> GRADE D
                                        (POOR)</span>
                                @elseif($generalClassAvg <= 20.4)
                                    <span style="background:rgb(235, 75, 75); padding:2px 10px;">GRADE E (FAIL)</span>
                                @endif
                            @endif
                        </h5>
                        <h5 style="font-weight:normal">AVERAGE OF:
                            <strong>{{ number_format($generalClassAvg, 2) }}</strong>
                    @endif
                </div>

                @if ($marking_style == 3)
                    <!-- DIVISION PERFORMANCE SUMMARY FOR MARKING STYLE 3 -->
                    <div class="total-summary results">
                        <table class="table" style="text-align:center; width:60%">
                            <tr style="background: rgb(187, 163, 56); color:black">
                                <th colspan="6">DIVISION PERFORMANCE SUMMARY</th>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>I</td>
                                <td>II</td>
                                <td>III</td>
                                <td>IV</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>Girls</td>
                                @php
                                    $girlsDivisions = ['I' => 0, 'II' => 0, 'III' => 0, 'IV' => 0, '0' => 0];
                                    foreach ($sortedStudentsResults as $student) {
                                        if ($student['gender'] == 'female' && isset($student['division'])) {
                                            $girlsDivisions[$student['division']]++;
                                        }
                                    }
                                @endphp
                                <td>{{ $girlsDivisions['I'] }}</td>
                                <td>{{ $girlsDivisions['II'] }}</td>
                                <td>{{ $girlsDivisions['III'] }}</td>
                                <td>{{ $girlsDivisions['IV'] }}</td>
                                <td>{{ $girlsDivisions['0'] }}</td>
                            </tr>
                            <tr>
                                <td>Boys</td>
                                @php
                                    $boysDivisions = ['I' => 0, 'II' => 0, 'III' => 0, 'IV' => 0, '0' => 0];
                                    foreach ($sortedStudentsResults as $student) {
                                        if ($student['gender'] == 'male' && isset($student['division'])) {
                                            $boysDivisions[$student['division']]++;
                                        }
                                    }
                                @endphp
                                <td>{{ $boysDivisions['I'] }}</td>
                                <td>{{ $boysDivisions['II'] }}</td>
                                <td>{{ $boysDivisions['III'] }}</td>
                                <td>{{ $boysDivisions['IV'] }}</td>
                                <td>{{ $boysDivisions['0'] }}</td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td>{{ $divisionAggregate['I'] ?? 0 }}</td>
                                <td>{{ $divisionAggregate['II'] ?? 0 }}</td>
                                <td>{{ $divisionAggregate['III'] ?? 0 }}</td>
                                <td>{{ $divisionAggregate['IV'] ?? 0 }}</td>
                                <td>{{ $divisionAggregate['0'] ?? 0 }}</td>
                            </tr>
                        </table>
                    </div>
                @else
                    <!-- OVERALL GRADE SUMMARY FOR MARKING STYLES 1 & 2 -->
                    <div class="total-summary results">
                        <table class="table" style="text-align:center; width:60%">
                            <tr style="background: rgb(187, 163, 56); color:black">
                                <th colspan="6">OVERALL GRADE SUMMARY</th>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>A</td>
                                <td>B</td>
                                <td>C</td>
                                <td>D</td>
                                <td>E</td>
                            </tr>
                            <tr>
                                <td>Girls</td>
                                <td>{{ $totalFemaleGrades['A'] ?? 0 }}</td>
                                <td>{{ $totalFemaleGrades['B'] ?? 0 }}</td>
                                <td>{{ $totalFemaleGrades['C'] ?? 0 }}</td>
                                <td>{{ $totalFemaleGrades['D'] ?? 0 }}</td>
                                <td>{{ $totalFemaleGrades['E'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Boys</td>
                                <td>{{ $totalMaleGrades['A'] ?? 0 }}</td>
                                <td>{{ $totalMaleGrades['B'] ?? 0 }}</td>
                                <td>{{ $totalMaleGrades['C'] ?? 0 }}</td>
                                <td>{{ $totalMaleGrades['D'] ?? 0 }}</td>
                                <td>{{ $totalMaleGrades['E'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td>{{ ($totalFemaleGrades['A'] ?? 0) + ($totalMaleGrades['A'] ?? 0) }}</td>
                                <td>{{ ($totalFemaleGrades['B'] ?? 0) + ($totalMaleGrades['B'] ?? 0) }}</td>
                                <td>{{ ($totalFemaleGrades['C'] ?? 0) + ($totalMaleGrades['C'] ?? 0) }}</td>
                                <td>{{ ($totalFemaleGrades['D'] ?? 0) + ($totalMaleGrades['D'] ?? 0) }}</td>
                                <td>{{ ($totalFemaleGrades['E'] ?? 0) + ($totalMaleGrades['E'] ?? 0) }}</td>
                            </tr>
                        </table>
                    </div>
                @endif

                <div style="background: rgb(187, 163, 56);">
                    <p style="text-align:center; font-size:14px; font-weight:bold" colspan="">STUDENTS WISE
                        PERFORMANCE</p>
                </div>

                @if ($marking_style == 3)
                    <!-- TABLE FOR MARKING STYLE 3 -->
                    <table class="table results">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Adm.No#.</th>
                                <th class="">sex</th>
                                <th>Student Name</th>
                                <th>AGGT</th>
                                <th>DIV</th>
                                @foreach ($results->groupBy('course_id')->keys() as $courseId)
                                    <th style="text-transform: uppercase">
                                        {{ $results->firstWhere('course_id', $courseId)->course_code }}</th>
                                @endforeach
                                <th style="text-align:center;">Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortedStudentsResults as $index => $studentResult)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td style="text-align: center; text-transform:uppercase">
                                        {{ $studentResult['admission_number'] }}</td>
                                    <td style="text-align: center">{{ $studentResult['gender'][0] }}</td>
                                    <td style="text-transform:capitalize">
                                        {{ ucwords(strtolower($studentResult['student_name'])) }}</td>
                                    <td style="text-align:center">{{ $studentResult['aggregate_points'] ?? 'N/A' }}
                                    </td>
                                    <td style="text-align:center; text-transform:uppercase"
                                        class="division-{{ strtolower($studentResult['division']) }}">
                                        {{ $studentResult['division'] === '0' ? '0' : $studentResult['division'] }}
                                    </td>
                                    @foreach ($studentResult['courses'] as $course)
                                        <td style="text-align:center;">
                                            @if ($course['score'] === null)
                                                <span style="background:rgb(235, 75, 75); padding:2px 10px ">X</span>
                                            @else
                                                {{ $course['grade'] ?? 'X' }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="text-align:center">
                                        @if ($studentResult['grade'] === 'ABS')
                                            <span style="background: rgb(235, 75, 75); padding: 2px; 10px">X</span>
                                        @else
                                            {{ $studentResult['position'] }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <!-- TABLE FOR MARKING STYLES 1 & 2 -->
                    <table class="table results">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Adm.No#.</th>
                                <th style="" class="">sex</th>
                                <th style="">Student Name</th>
                                @foreach ($results->groupBy('course_id')->keys() as $courseId)
                                    <th style="text-transform: uppercase">
                                        {{ $results->firstWhere('course_id', $courseId)->course_code }}</th>
                                @endforeach
                                <th style="text-align:center;">Total</th>
                                <th style="text-align:center;">Avg</th>
                                <th style="text-align:center;">Grade</th>
                                <th style="text-align:center;">Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortedStudentsResults as $index => $studentResult)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td style="text-align: center; text-transform:uppercase">
                                        {{ $studentResult['admission_number'] }}</td>
                                    <td style="text-align: center">{{ $studentResult['gender'][0] }}</td>
                                    <td style="text-transform:capitalize">
                                        {{ ucwords(strtolower($studentResult['student_name'])) }}</td>
                                    @foreach ($studentResult['courses'] as $course)
                                        <td style="text-align:center;">
                                            @if ($course['score'] === null)
                                                <span style="background:rgb(235, 75, 75); padding:2px 10px ">X</span>
                                            @else
                                                {{ $course['score'] }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="text-align:center">{{ $studentResult['total_marks'] }}</td>
                                    <td style="text-align:center">{{ number_format($studentResult['average'], 2) }}
                                    </td>
                                    <td style="text-align:center; text-transform:uppercase">
                                        {{ $studentResult['grade'] === 'ABS' ? 'ABS' : $studentResult['grade'] }}
                                    </td>
                                    <td style="text-align:center">
                                        @if ($studentResult['grade'] === 'ABS')
                                            <span style="background: rgb(235, 75, 75); padding: 2px; 10px">X</span>
                                        @else
                                            {{ $studentResult['position'] }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <hr>
                <div class="final-summary">
                    <table class="table results" style="width:100%">
                        <tr style="background: rgb(187, 163, 56); color:black">
                            <th colspan="5">SUBJECTWISE RANKINGS</th>
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
                                <td style="text-transform: uppercase; text-align:center">{{ $course['course_code'] }}
                                </td>
                                <td style="text-align:center">{{ number_format($course['average_score'], 2) }}</td>
                                <td style="text-align:center">{{ $course['position'] }}</td>
                                @if ($marking_style == 1 || $marking_style == 2)
                                    @if ($course['grade'] == 'A')
                                        <td style="background:rgb(117, 244, 48); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>EXCELLENT</i></td>
                                    @elseif ($course['grade'] == 'B')
                                        <td style="background:rgb(153, 250, 237); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>GOOD</i></td>
                                    @elseif ($course['grade'] == 'C')
                                        <td style="background:rgb(237, 220, 113); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>PASS</i></td>
                                    @elseif ($course['grade'] == 'D')
                                        <td style="background:rgb(182, 176, 176); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>POOR</i></td>
                                    @else
                                        <td style="background:rgb(235, 75, 75); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>FAIL</i></td>
                                    @endif
                                @else
                                    <!-- For marking style 3 -->
                                    @if ($course['grade'] == 'A')
                                        <td style="background:rgb(117, 244, 48); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>EXCELLENT</i></td>
                                    @elseif ($course['grade'] == 'B')
                                        <td style="background:rgb(153, 250, 237); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>GOOD</i></td>
                                    @elseif ($course['grade'] == 'C')
                                        <td style="background:rgb(237, 220, 113); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>PASS</i></td>
                                    @elseif ($course['grade'] == 'D')
                                        <td style="background:rgb(182, 176, 176); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>UNSATISFACTORY</i></td>
                                    @elseif ($course['grade'] == 'F')
                                        <td style="background:rgb(235, 75, 75); padding:2px 10px ">grade
                                            {{ $course['grade'] }} - <i>FAIL</i></td>
                                    @else
                                        <td style="background:rgb(235, 75, 75); padding:2px 10px ">grade
                                            {{ $course['grade'] }}</td>
                                    @endif
                                @endif
                            </tr>
                        @endforeach
                    </table>
                    <hr>
                </div>

                @if ($marking_style != 3)
                    <!-- SUBJECTWISE PERFORMANCE SUMMARY FOR MARKING STYLES 1 & 2 -->
                    <div class="final-summary">
                        <table class="table table-bordered" style="font-size: 12px; text-align:center;">
                            <thead>
                                <tr style="background: rgb(187, 163, 56); color:black">
                                    <th style="align-text:center" colspan="16">SUBJECTWISE PERFORMANCE SUMMARY</th>
                                </tr>
                                <tr style="">
                                    <th rowspan="2" style="background: gray;">SUBJECTS</th>
                                    <th colspan="3" style="background:rgb(117, 244, 48);">A</th>
                                    <th colspan="3" style="background:rgb(153, 250, 237);">B</th>
                                    <th colspan="3" style="background:rgb(237, 220, 113);">C</th>
                                    <th colspan="3" style="background:rgb(182, 176, 176);">D</th>
                                    <th colspan="3" style="background:rgb(235, 75, 75);">E</th>
                                </tr>
                                <tr style="">
                                    <th style="background:rgb(117, 244, 48);">Boys</th>
                                    <th style="background:rgb(117, 244, 48);">Girls</th>
                                    <th style="background:rgb(117, 244, 48);">Total</th>
                                    <th style="background:rgb(153, 250, 237);">Boys</th>
                                    <th style="background:rgb(153, 250, 237);">Girls</th>
                                    <th style="background:rgb(153, 250, 237);">Total</th>
                                    <th style="background:rgb(237, 220, 113);">Boys</th>
                                    <th style="background:rgb(237, 220, 113);">Girls</th>
                                    <th style="background:rgb(237, 220, 113);">Total</th>
                                    <th style="background:rgb(182, 176, 176);">Boys</th>
                                    <th style="background:rgb(182, 176, 176);">Girls</th>
                                    <th style="background:rgb(182, 176, 176);">Total</th>
                                    <th style="background: rgb(235, 75, 75);">Boys</th>
                                    <th style="background: rgb(235, 75, 75);">Girls</th>
                                    <th style="background: rgb(235, 75, 75);">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjectGradesByGender as $courseId => $grades)
                                    <tr style="">
                                        <!-- Get the course name for this course_id -->
                                        <td style="text-transform: uppercase">
                                            {{ $courses->find($courseId)->course_code }}</td>

                                        <!-- Grade A Counts -->
                                        <td style="background:rgb(117, 244, 48);">{{ $grades['A']['male'] }}</td>
                                        <td style="background:rgb(117, 244, 48);">{{ $grades['A']['female'] }}</td>
                                        <td style="background:rgb(117, 244, 48);">
                                            {{ $grades['A']['male'] + $grades['A']['female'] }}</td>

                                        <!-- Grade B Counts -->
                                        <td style="background:rgb(153, 250, 237);">{{ $grades['B']['male'] }}</td>
                                        <td style="background:rgb(153, 250, 237);">{{ $grades['B']['female'] }}</td>
                                        <td style="background:rgb(153, 250, 237);">
                                            {{ $grades['B']['male'] + $grades['B']['female'] }}</td>

                                        <!-- Grade C Counts -->
                                        <td style="background:rgb(237, 220, 113);">{{ $grades['C']['male'] }}</td>
                                        <td style="background:rgb(237, 220, 113);">{{ $grades['C']['female'] }}</td>
                                        <td style="background:rgb(237, 220, 113);">
                                            {{ $grades['C']['male'] + $grades['C']['female'] }}</td>

                                        <!-- Grade D Counts -->
                                        <td style="background:rgb(182, 176, 176);">{{ $grades['D']['male'] }}</td>
                                        <td style="background:rgb(182, 176, 176);">{{ $grades['D']['female'] }}</td>
                                        <td style="background:rgb(182, 176, 176);">
                                            {{ $grades['D']['male'] + $grades['D']['female'] }}</td>

                                        <!-- Grade E Counts -->
                                        <td style="background: rgb(235, 75, 75);">{{ $grades['E']['male'] }}</td>
                                        <td style="background: rgb(235, 75, 75);">{{ $grades['E']['female'] }}</td>
                                        <td style="background: rgb(235, 75, 75);">
                                            {{ $grades['E']['male'] + $grades['E']['female'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- SUBJECTWISE PERFORMANCE SUMMARY FOR MARKING STYLE 3 -->
                    <div class="final-summary">
                        <table class="table table-bordered" style="font-size: 12px; text-align:center;">
                            <thead>
                                <tr style="background: rgb(187, 163, 56); color:black">
                                    <th style="align-text:center" colspan="19">SUBJECTWISE PERFORMANCE SUMMARY</th>
                                </tr>
                                <tr style="">
                                    <th rowspan="2" style="background: gray;">SUBJECTS</th>
                                    <th colspan="3" style="background:rgb(117, 244, 48);">A</th>
                                    <th colspan="3" style="background:rgb(153, 250, 237);">B</th>
                                    <th colspan="3" style="background:rgb(237, 220, 113);">C</th>
                                    <th colspan="3" style="background:rgb(182, 176, 176);">D</th>
                                    <th colspan="3" style="background:rgb(235, 75, 75);">F</th>
                                    <th colspan="3" style="background:rgb(235, 75, 75);">ABS</th>
                                </tr>
                                <tr style="">
                                    <th style="background:rgb(117, 244, 48);">Boys</th>
                                    <th style="background:rgb(117, 244, 48);">Girls</th>
                                    <th style="background:rgb(117, 244, 48);">Total</th>
                                    <th style="background:rgb(153, 250, 237);">Boys</th>
                                    <th style="background:rgb(153, 250, 237);">Girls</th>
                                    <th style="background:rgb(153, 250, 237);">Total</th>
                                    <th style="background:rgb(237, 220, 113);">Boys</th>
                                    <th style="background:rgb(237, 220, 113);">Girls</th>
                                    <th style="background:rgb(237, 220, 113);">Total</th>
                                    <th style="background:rgb(182, 176, 176);">Boys</th>
                                    <th style="background:rgb(182, 176, 176);">Girls</th>
                                    <th style="background:rgb(182, 176, 176);">Total</th>
                                    <th style="background: rgb(235, 75, 75);">Boys</th>
                                    <th style="background: rgb(235, 75, 75);">Girls</th>
                                    <th style="background: rgb(235, 75, 75);">Total</th>
                                    <th style="background: rgb(235, 75, 75);">Boys</th>
                                    <th style="background: rgb(235, 75, 75);">Girls</th>
                                    <th style="background: rgb(235, 75, 75);">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjectGradesByGender as $courseId => $grades)
                                    <tr style="">
                                        <!-- Get the course name for this course_id -->
                                        <td style="text-transform: uppercase">
                                            {{ $courses->find($courseId)->course_code }}</td>

                                        <!-- Grade A Counts -->
                                        <td style="background:rgb(117, 244, 48);">{{ $grades['A']['male'] }}</td>
                                        <td style="background:rgb(117, 244, 48);">{{ $grades['A']['female'] }}</td>
                                        <td style="background:rgb(117, 244, 48);">
                                            {{ $grades['A']['male'] + $grades['A']['female'] }}</td>

                                        <!-- Grade B Counts -->
                                        <td style="background:rgb(153, 250, 237);">{{ $grades['B']['male'] }}</td>
                                        <td style="background:rgb(153, 250, 237);">{{ $grades['B']['female'] }}</td>
                                        <td style="background:rgb(153, 250, 237);">
                                            {{ $grades['B']['male'] + $grades['B']['female'] }}</td>

                                        <!-- Grade C Counts -->
                                        <td style="background:rgb(237, 220, 113);">{{ $grades['C']['male'] }}</td>
                                        <td style="background:rgb(237, 220, 113);">{{ $grades['C']['female'] }}</td>
                                        <td style="background:rgb(237, 220, 113);">
                                            {{ $grades['C']['male'] + $grades['C']['female'] }}</td>

                                        <!-- Grade D Counts -->
                                        <td style="background:rgb(182, 176, 176);">{{ $grades['D']['male'] }}</td>
                                        <td style="background:rgb(182, 176, 176);">{{ $grades['D']['female'] }}</td>
                                        <td style="background:rgb(182, 176, 176);">
                                            {{ $grades['D']['male'] + $grades['D']['female'] }}</td>

                                        <!-- Grade F Counts -->
                                        <td style="background: rgb(235, 75, 75);">{{ $grades['F']['male'] }}</td>
                                        <td style="background: rgb(235, 75, 75);">{{ $grades['F']['female'] }}</td>
                                        <td style="background: rgb(235, 75, 75);">
                                            {{ $grades['F']['male'] + $grades['F']['female'] }}</td>

                                        <!-- ABS Counts -->
                                        <td style="background: rgb(235, 75, 75);">{{ $grades['ABS']['male'] }}</td>
                                        <td style="background: rgb(235, 75, 75);">{{ $grades['ABS']['female'] }}</td>
                                        <td style="background: rgb(235, 75, 75);">
                                            {{ $grades['ABS']['male'] + $grades['ABS']['female'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
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
