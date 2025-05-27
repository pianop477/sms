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
                font-size: 13px;
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
                        <img src="{{public_path('assets/img/logo/'. $schoolInfo->logo)}}" alt="" style="max-width: 80px;">
                    </div>
                    <div class="header">
                        <h3>the united republic of tanzania</h3>
                        <h4>the president's office - tamisemi</h4>
                        <h4>{{$schoolInfo->school_name}}</h4>
                    </div>
                </div>
                <div class="details">
                    <h4>{{$classInfo->class_name}} {{$reports->title}} Results - {{$year}}</h4>
                    <h5 style="font-weight:normal">TERM: {{$results->first()->Exam_term}}</h5>
                    <h5 style="font-weight:normal">NUMBER OF CANDIDATES: {{$totalCandidates}}</h5>
                    <h5 style="font-weight:normal">CLASS AVERAGE: <strong>{{number_format($subjectAveragesSum, 4)}}</strong>
                        @if($results->first()->marking_style == 1)
                            @if ($overallTotalAverage >= 40.5)
                                    <span style="background:rgb(117, 244, 48); padding:2px 10px; ">GRADE A (EXCELLENT)</span>
                                @elseif ($overallTotalAverage >= 30.5)
                                    <span style="background:rgb(153, 250, 237); padding:2px 10px;">GRADE B (GOOD)</span>
                                @elseif ($overallTotalAverage >= 20.5)
                                    <span style="background:rgb(237, 220, 113); padding:2px 10px;">GRADE C (PASS)</span>
                                @elseif ($overallTotalAverage >= 10.5)
                                    <span style="background:rgb(182, 176, 176); padding:2px 10px;"> GRADE D (POOR)</span>
                                @elseif($overallTotalAverage <= 10.4)
                                    <span style="background:rgb(235, 75, 75); padding:2px 10px;">GRADE E (FAIL)</span>
                            @endif
                        @else
                            @if ($overallTotalAverage >= 80.5)
                                <span style="background:rgb(117, 244, 48); padding:2px 10px;">GRADE A (EXCELLENT)</span>
                            @elseif ($overallTotalAverage >= 60.5)
                                <span style="background:rgb(153, 250, 237); padding:2px 10px;">GRADE B (GOOD)</span>
                            @elseif ($overallTotalAverage >= 40.5)
                                <span style="background:rgb(237, 220, 113); padding:2px 10px">GRADE C (PASS)</span>
                            @elseif ($overallTotalAverage >= 20.5)
                                <span style="background:rgb(182, 176, 176); padding:2px 10px;"> GRADE D (POOR)</span>
                            @elseif($overallTotalAverage <= 20.4)
                                <span style="background:rgb(235, 75, 75); padding:2px 10px;">GRADE E (FAIL)</span>
                            @endif
                        @endif

                    </h5>
                    <h5 style="font-weight:normal">AVERAGE OF: <strong>{{number_format($overallTotalAverage, 2)}}</strong>
                </div>
                <div class="total-summary results">
                    <table class="table" style="text-align:center; width:80%">
                        <tr style="background: rgb(187, 163, 56); color:black">
                            <th colspan="6">OVERALL GRADE SUMMARY</th>
                        </tr>
                        <tr style="text-align: center">
                            <th>Gender</th>
                            @foreach(['A', 'B', 'C', 'D', 'E'] as $grade)
                                <th>{{ $grade }}</th>
                            @endforeach
                        </tr>
                        @foreach(['male' => 'Boys', 'female' => 'GIrls', 'total' => 'Total'] as $key => $label)
                            <tr style="text-align: center">
                                <td>{{ $label }}</td>
                                @foreach(['A', 'B', 'C', 'D', 'E'] as $grade)
                                    <td>{{ $gradeSummary[$key][$grade] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div style="background: rgb(187, 163, 56);">
                    <p style="text-align:center; font-size:14px; font-weight:bold" colspan="">STUDENTS WISE PERFORMANCE</p>
                </div>
                <table class="table results">
                    <thead>
                        </tr>
                        <tr>
                            <th>Adm.No.</th>
                            <th style="" class="">sex</th>
                            <th style="">Student Name</th>
                             @foreach($subjectCodes as $code)
                                <th style="text-transform: uppercase">{{ $code }}</th>
                            @endforeach
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Avg</th>
                            <th style="text-align:center;">Grade</th>
                            <th style="text-align:center;">Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rankedStudents  as $student)
                        <tr>
                            <td style="text-align: center; text-transform:uppercase">{{ $student['admission_number'] }}</td>
                            <td style="text-align: center">{{ $student['gender'][0] }}</td>
                            <td style="text-transform:capitalize">{{ ucwords(strtolower($student['student_name'])) }}</td>
                            @foreach($subjectCodes as $code)
                                <td style="text-align:center">
                                    @if(isset($student['subject_averages'][$code]['score']))
                                        {{ number_format($student['subject_averages'][$code]['score'], 1) }}
                                    @else
                                        X
                                    @endif
                                </td>
                            @endforeach
                            <td style="text-align:center">{{ number_format($student['total'],2 ) }}</td>
                            <td style="text-align:center">{{ number_format($student['average'], 3) }}</td>
                            <td style="text-align:center; text-transform:uppercase">
                                {{ $student['grade'] === 'ABS' ? 'ABS' : $student['grade'] }}
                            </td>
                            <td style="text-align:center">
                                @if ($student['grade'] === 'ABS')
                                    <span style="">-</span>
                                @else
                                    {{ $student['rank'] }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
                        @foreach ($subjectPositions  as $subject)
                            <tr>
                                <td style="text-transform: uppercase">{{ $subject['subject_name'] }} </td>
                                <td style="text-transform: uppercase; text-align:center">{{ $subject['subject_code'] }} </td>
                                <td style="text-align:center">{{ number_format($subject['average'], 2) }}</td>
                                <td style="text-align:center">{{ $subject['position'] }}</td>
                                    @if ($subject['grade']=='A')
                                        <td style="background:rgb(117, 244, 48); padding:2px 10px ">grade {{ $subject['grade']}} - <i>EXCELLENT</i></td>
                                    @elseif ($subject['grade']=='B')
                                        <td style="background:rgb(153, 250, 237); padding:2px 10px ">grade {{ $subject['grade']}} - <i>GOOD</i></td>
                                    @elseif ($subject['grade']=='C')
                                        <td style="background:rgb(237, 220, 113); padding:2px 10px ">grade {{ $subject['grade']}} - <i>PASS</i></td>
                                    @elseif ($subject['grade']=='D')
                                        <td style="background:rgb(182, 176, 176); padding:2px 10px ">grade {{ $subject['grade']}} - <i>POOR</i></td>
                                    @else
                                        <td style="background:rgb(235, 75, 75); padding:2px 10px ">grade {{ $subject['grade']}} - <i>FAIL</i></td>
                                    @endif
                            </tr>
                        @endforeach
                    </table>
                    <hr>
                </div>
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
                            @foreach ($performanceAnalysis as $subject)
                                <tr>
                                    <td style="text-transform: uppercase">{{ $subject['subject_code'] }}</td>

                                    @foreach (['A', 'B', 'C', 'D', 'E'] as $grade)
                                        <td style="background:{{ $grade == 'A' ? 'rgb(117, 244, 48)' : ($grade == 'B' ? 'rgb(153, 250, 237)' : ($grade == 'C' ? 'rgb(237, 220, 113)' : ($grade == 'D' ? 'rgb(182, 176, 176)' : 'rgb(235, 75, 75)'))) }}">
                                            {{ $subject['male_grades'][$grade] ?? 0 }}
                                        </td>
                                        <td style="background:{{ $grade == 'A' ? 'rgb(117, 244, 48)' : ($grade == 'B' ? 'rgb(153, 250, 237)' : ($grade == 'C' ? 'rgb(237, 220, 113)' : ($grade == 'D' ? 'rgb(182, 176, 176)' : 'rgb(235, 75, 75)'))) }}">
                                            {{ $subject['female_grades'][$grade] ?? 0 }}
                                        </td>
                                        <td style="background:{{ $grade == 'A' ? 'rgb(117, 244, 48)' : ($grade == 'B' ? 'rgb(153, 250, 237)' : ($grade == 'C' ? 'rgb(237, 220, 113)' : ($grade == 'D' ? 'rgb(182, 176, 176)' : 'rgb(235, 75, 75)'))) }}">
                                            {{ ($subject['male_grades'][$grade] ?? 0) + ($subject['female_grades'][$grade] ?? 0) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
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
