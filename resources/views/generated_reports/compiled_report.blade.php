<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        .header td {
            vertical-align: top;
        }
        .school-info {
            text-align: center;
            text-transform: uppercase;
            font-size: 16px;
        }
        .report-header {
            width: 100%;
            margin-bottom: 10px;
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
            text-transform: uppercase;
            border-bottom: #333 solid 1px;
        }
        .student-info {
            width: 100%;
            margin-bottom: 15px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .student-info td {
            vertical-align: top;
            padding: 2px 5px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .subject-name {
            text-align: left;
            min-width: 120px;
        }
        .teacher-name {
            text-align: left;
            min-width: 80px;
            text-transform: capitalize;
        }
        .exam-score {
            min-width: 40px;
        }
        .summary-row td {
            font-weight: bold;
            padding: 3px;
            /* background-color: #f9f9f9; */
        }
        .excellent {
            background-color: #75f430;
            padding: 2px 4px;
        }
        .good {
            background-color: #99faed;
            padding: 2px 4px;
        }
        .pass {
            background-color: #eddc71;
            padding: 2px 4px;
        }
        .poor {
            background-color: #b6b0b0;
            padding: 2px 4px;
        }
        .fail {
            background-color: #eb4b4b;
            padding: 2px 4px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        .rotate-text {
            transform: rotate(-90deg);
            transform-origin: left top 0;
            white-space: nowrap;
            display: inline-block;
            position: relative;
            width: 20px;
            height: 20px;
            margin-top: 20px;
        }
        .compact-header {
            font-size: 10px;
            line-height: 1.2;
        }
    </style>
</head>
<body>

<table class="header">
    <tr>
        <td width="15%">
            <img src="{{ public_path('assets/img/logo/' . $schoolInfo->logo) }}" alt="Logo" width="70">
        </td>
        <td width="70%" class="school-info">
            <h3 style="margin:0; padding:0;">THE UNITED REPUBLIC OF TANZANIA</h3>
            <h3 style="margin:0; padding:0;">PRESIDENT OFFICE - TAMISEMI</h3>
            <h4 style="margin:0; padding:0;">{{ $schoolInfo->school_name }}</h4>
            <h5 style="margin:0; padding:0;">{{ $schoolInfo->postal_address }}, {{ $schoolInfo->postal_name }} - {{ $schoolInfo->country }}</h5>
        </td>
        <td width="15%" align="right">
            @if($students->image == null)
                <img src="{{ public_path('assets/img/students/student.jpg') }}" width="70" class="rounded-circle">
            @else
                <img src="{{ public_path('assets/img/students/' . $students->image) }}" width="70" class="rounded-circle">
            @endif
        </td>
    </tr>
</table>
<table class="report-header">
    <tr>
        <td>
            <h5 style="margin:5px 0; padding:0;">STUDENT'S PROGRESS REPORT</h5>
            <h5 style="margin:0; padding:0;"> {{ $reports->title }} - {{ \Carbon\Carbon::parse($reports->created_at)->format('d/F/Y') }}</h5>
        </td>
    </tr>
</table>
<p style="padding: 3px; background:rgb(187, 163, 56); text-align:center; font-size: 14px;"><strong>Student's Information</strong></p>
<table class="student-info">
    <tr>
        <td width="50%">
            <strong>Admission Number:</strong> <span class="">{{ $students->admission_number }}</span><br>
            <strong>Student Name:</strong> <span class="">{{ $students->first_name }} {{ $students->middle_name }} {{ $students->last_name }}</span><br>
            <strong>Gender:</strong> <span class="">{{ ucfirst($students->gender) }}</span>
        </td>
        <td width="50%">
            <strong>Class:</strong> <span class="">{{ $students->class_name }}</span><br>
            <strong>Stream:</strong> <span class="">{{ $students->group }}</span><br>
            <strong>Term:</strong> <span class="">{{ $reports->term }}</span>
        </td>
    </tr>
</table>

@if ($reports->combine_option === 'individual')
    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" class="subject-name">Subject Name (Code)</th>
                <th rowspan="2" class="teacher-name">Teacher</th>
                <th colspan="{{ count($examHeaders) }}" class="text-center">Examination Scores</th>
                <th rowspan="2">Total</th>
                <th rowspan="2">Avg</th>
                <th rowspan="2">Grade</th>
                <th rowspan="2">Rank</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                @foreach($examHeaders as $exam)
                    <th class="compact-header text-center">
                        <span style="text-transform: uppercase;" class="text-sm">{{ $exam['abbr'] }}</span><br>
                        <small>{{ \Carbon\Carbon::parse($exam['date'])->format('d-M') }}</small>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($finalData as $subject)
                <tr>
                    <td class="subject-name" style="text-transform: capitalize">{{ ucwords(strtolower($subject['subjectName'])) }} <span class="" style="text-transform: uppercase">({{ ucwords(strtoupper($subject['subjectCode'])) }})</span></td>
                    <td class="teacher-name">{{ucwords(strtolower($subject['teacher']))}}</td>
                     @foreach($examHeaders as $exam)
                        <td class="exam-score text-center">{{ $subject['examScores'][$exam['abbr'].'_'.$exam['date']] ?? 'X' }}</td>
                    @endforeach
                    <td>{{ $subject['total'] }}</td>
                    <td>{{ number_format($subject['average'], 2) }}</td>
                    <td>
                        @if ($results->first()->marking_style === 1)
                            @if ($subject['average'] >= 40.5) A
                            @elseif ($subject['average'] >= 30.5) B
                            @elseif ($subject['average'] >= 20.5) C
                            @elseif ($subject['average'] >= 10.5) D
                            @else E @endif
                        @else
                            @if ($subject['average'] >= 80.5) A
                            @elseif ($subject['average'] >= 60.5) B
                            @elseif ($subject['average'] >= 40.5) C
                            @elseif ($subject['average'] >= 20.5) D
                            @else E @endif
                        @endif
                    </td>
                    <td>{{ $subject['position'] }}</td>
                    <td style="font-style: italic">
                        @if ($results->first()->marking_style === 1)
                            @if ($subject['average'] >= 40.5) Excellent
                            @elseif ($subject['average'] >= 30.5) Good
                            @elseif ($subject['average'] >= 20.5) Pass
                            @elseif ($subject['average'] >= 10.5) Poor
                            @else Fail @endif
                        @else
                            @if ($subject['average'] >= 80.5) Excellent
                            @elseif ($subject['average'] >= 60.5) Good
                            @elseif ($subject['average'] >= 40.5) Pass
                            @elseif ($subject['average'] >= 20.5) Poor
                            @else Fail @endif
                        @endif
                    </td>
                </tr>
            @endforeach

            <tr class="summary-row">
                <td>Exam Averages</td>
                <td></td>
                @foreach ($examHeaders as $exam)
                    <td class="text-center font-weight-bold">
                        {{ number_format($examAverages[$exam['abbr'].'_'.$exam['date']] ?? 0, 2) }}
                    </td>
                @endforeach
                <td>{{ number_format($sumOfAverages, 2) }}</td>
                <td>{{ number_format($studentGeneralAverage, 2) }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            <tr class="summary-row">
                <td>Exam Grades</td>
                <td></td>
                @foreach ($examHeaders as $exam)
                    @php
                        $examKey = $exam['abbr'].'_'.$exam['date'];
                        $averageScore = $examAverages[$examKey] ?? 0;
                    @endphp
                    <td class="text-center">
                        @if ($results->first()->marking_style === 1)
                            @if ($averageScore >= 40.5) A
                            @elseif ($averageScore >= 30.5) B
                            @elseif ($averageScore >= 20.5) C
                            @elseif ($averageScore >= 10.5) D
                            @else E @endif
                        @else
                            @if ($averageScore >= 80.5) A
                            @elseif ($averageScore >= 60.5) B
                            @elseif ($averageScore >= 40.5) C
                            @elseif ($averageScore >= 20.5) D
                            @else E @endif
                            @endif
                    </td>
                @endforeach
                <td></td>
                <td>
                    @if ($results->first()->marking_style === 1)
                        @if ($studentGeneralAverage >= 40.5) A
                        @elseif ($studentGeneralAverage >= 30.5) B
                        @elseif ($studentGeneralAverage >= 20.5) C
                        @elseif ($studentGeneralAverage >= 10.5) D
                        @else E @endif
                    @else
                        @if ($studentGeneralAverage >= 80.5) A
                        @elseif ($studentGeneralAverage >= 60.5) B
                        @elseif ($studentGeneralAverage >= 40.5) C
                        @elseif ($studentGeneralAverage >= 20.5) D
                        @else E @endif
                    @endif
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            <tr class="summary-row">
                <td colspan="{{ count($examHeaders) + 7 }}" style="background: rgb(187, 163, 56)">Overall Performance Summary</td>
            </tr>

            <tr class="summary-row">
                <td colspan="2">
                    General Average: <strong>{{ number_format($studentGeneralAverage, 3) }}</strong>
                </td>
                <td colspan="2" class="text-center">
                    Grade: <strong>
                    @if ($results->first()->marking_style === 1)
                        @if ($studentGeneralAverage >= 40.5) "A"
                        @elseif ($studentGeneralAverage >= 30.5) "B"
                        @elseif ($studentGeneralAverage >= 20.5) "C"
                        @elseif ($studentGeneralAverage >= 10.5) "D"
                        @else "E" @endif
                    @else
                        @if ($studentGeneralAverage >= 80.5) "A"
                        @elseif ($studentGeneralAverage >= 60.5) "B"
                        @elseif ($studentGeneralAverage >= 40.5) "C"
                        @elseif ($studentGeneralAverage >= 20.5) "D"
                        @else "E" @endif
                    @endif
                    </strong>
                </td>
                <td colspan="3">
                    Position: <strong style="text-decoration:underline">{{ $generalPosition }}</strong> out of <strong style="text-decoration:underline">{{ $totalStudents }}</strong>
                </td>
                <td colspan="{{count($examHeaders)}}" class="text-center">
                    General Remarks:
                    @if ($results->first()->marking_style === 1)
                        @if ($studentGeneralAverage >= 40.5)
                            <span class="excellent">EXCELLENT</span>
                        @elseif ($studentGeneralAverage >= 30.5)
                            <span class="good">GOOD</span>
                        @elseif ($studentGeneralAverage >= 20.5)
                            <span class="pass">PASS</span>
                        @elseif ($studentGeneralAverage >= 10.5)
                            <span class="poor">POOR</span>
                        @else
                            <span class="fail">FAIL</span>
                        @endif
                    @else
                        @if ($studentGeneralAverage >= 80.5)
                            <span class="excellent">EXCELLENT</span>
                        @elseif ($studentGeneralAverage >= 60.5)
                            <span class="good">GOOD</span>
                        @elseif ($studentGeneralAverage >= 40.5)
                            <span class="pass">PASS</span>
                        @elseif ($studentGeneralAverage >= 20.5)
                            <span class="poor">POOR</span>
                        @else
                            <span class="fail">FAIL</span>
                        @endif
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
        @if(count($examSpecifications) > 0)
        <table class="report-table" style="margin-top: 20px;">
            <tbody>
                <tr>
                    <td colspan="7" style="background: rgb(187, 163, 56); font-size: 12px"><strong>Exam Codes Key</strong></td>
                </tr>
                <tr>
                    <th colspan="3">Exam Code</th>
                    <th colspan="4">Exam Description</th>
                </tr>
                @foreach ($examSpecifications as $spec )
                    <tr>
                        <td colspan="3" style="text-transform: uppercase">{{ $spec['abbr'] }}</td>
                        <td colspan="4" style="text-transform: capitalize">{{ $spec['full_name'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" style="text-align: center; font-style: italic;">
                        Note: "X" indicates that the student did not take the exam.
                        <br>
                        Codes above represent different exam types (e.g., Midterm, Final, etc.).
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
@elseif ($reports->combine_option === 'sum')
    <!-- Similar optimized table structure for 'sum' option -->
    <table class="report-table" style="font-size: 13px;">
        <thead>
            <tr>
                <th rowspan="2" class="subject-name">Subject Name (Code)</th>
                <th rowspan="2" class="teacher-name">Teacher</th>
                <th rowspan="2">Total</th>
                <th rowspan="2">Average</th>
                <th rowspan="2">Grade</th>
                <th rowspan="2">Rank</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>

            </tr>
        </thead>
        <tbody>
            @foreach ($finalData as $subject)
                <tr>
                    <td class="subject-name" style="text-transform: capitalize">{{ ucwords(strtolower($subject['subjectName'])) }} <span class="" style="text-transform: uppercase">({{ ucwords(strtoupper($subject['subjectCode'])) }})</span></td>
                    <td class="teacher-name">{{ucwords(strtolower($subject['teacher']))}}</td>
                    <td>{{ $subject['total'] }}</td>
                    <td>{{ number_format($subject['average'], 2) }}</td>
                    <td>
                        @if ($results->first()->marking_style === 1)
                            @if ($subject['average'] >= 40.5) A
                            @elseif ($subject['average'] >= 30.5) B
                            @elseif ($subject['average'] >= 20.5) C
                            @elseif ($subject['average'] >= 10.5) D
                            @else E @endif
                        @else
                            @if ($subject['average'] >= 80.5) A
                            @elseif ($subject['average'] >= 60.5) B
                            @elseif ($subject['average'] >= 40.5) C
                            @elseif ($subject['average'] >= 20.5) D
                            @else E @endif
                        @endif
                    </td>
                    <td>{{ $subject['position'] }}</td>
                    <td style="font-style: italic">
                        @if ($results->first()->marking_style === 1)
                            @if ($subject['average'] >= 40.5) Excellent
                            @elseif ($subject['average'] >= 30.5) Good
                            @elseif ($subject['average'] >= 20.5) Pass
                            @elseif ($subject['average'] >= 10.5) Poor
                            @else Fail @endif
                        @else
                            @if ($subject['average'] >= 80.5) Excellent
                            @elseif ($subject['average'] >= 60.5) Good
                            @elseif ($subject['average'] >= 40.5) Pass
                            @elseif ($subject['average'] >= 20.5) Poor
                            @else Fail @endif
                        @endif
                    </td>
                </tr>
            @endforeach

            <tr class="summary-row">
                <td colspan="7" style="background: rgb(187, 163, 56)">Overall Performance Summary</td>
            </tr>

            <tr class="summary-row">
                <td colspan="">
                    Total Marks: <span class="font-weight-bold">{{ $totalScoreForStudent }}</span>
                </td>
                <td colspan="">
                    General Average: <strong>{{ number_format($studentGeneralAverage, 3) }}</strong>
                </td>
                <td colspan="" class="text-center">
                    Grade: <strong>
                    @if ($results->first()->marking_style === 1)
                        @if ($studentGeneralAverage >= 40.5) "A"
                        @elseif ($studentGeneralAverage >= 30.5) "B"
                        @elseif ($studentGeneralAverage >= 20.5) "C"
                        @elseif ($studentGeneralAverage >= 10.5) "D"
                        @else "E" @endif
                    @else
                        @if ($studentGeneralAverage >= 80.5) "A"
                        @elseif ($studentGeneralAverage >= 60.5) "B"
                        @elseif ($studentGeneralAverage >= 40.5) "C"
                        @elseif ($studentGeneralAverage >= 20.5) "D"
                        @else "E" @endif
                    @endif
                    </strong>
                </td>
                <td colspan="2">
                    Position: <strong style="text-decoration:underline">{{ $generalPosition }}</strong> out of <strong style="text-decoration:underline">{{ $totalStudents }}</strong>
                </td>
                <td colspan="2" class="text-center">
                    General Remarks:
                    @if ($results->first()->marking_style === 1)
                        @if ($studentGeneralAverage >= 40.5)
                            <span class="excellent">EXCELLENT</span>
                        @elseif ($studentGeneralAverage >= 30.5)
                            <span class="good">GOOD</span>
                        @elseif ($studentGeneralAverage >= 20.5)
                            <span class="pass">PASS</span>
                        @elseif ($studentGeneralAverage >= 10.5)
                            <span class="poor">POOR</span>
                        @else
                            <span class="fail">FAIL</span>
                        @endif
                    @else
                        @if ($studentGeneralAverage >= 80.5)
                            <span class="excellent">EXCELLENT</span>
                        @elseif ($studentGeneralAverage >= 60.5)
                            <span class="good">GOOD</span>
                        @elseif ($studentGeneralAverage >= 40.5)
                            <span class="pass">PASS</span>
                        @elseif ($studentGeneralAverage >= 20.5)
                            <span class="poor">POOR</span>
                        @else
                            <span class="fail">FAIL</span>
                        @endif
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
@else
    <!-- Similar optimized table structure for default/average option -->
    <table class="report-table" style="font-size: 13px;">
        <thead>
            <tr>
                <th rowspan="2" class="subject-name">Subject Name (Code)</th>
                <th rowspan="2" class="teacher-name">Teacher</th>
                <th rowspan="2">Average</th>
                <th rowspan="2">Grade</th>
                <th rowspan="2">Rank</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>

            </tr>
        </thead>
        <tbody>
            @foreach ($finalData as $subject)
                <tr>
                    <td class="subject-name" style="text-transform: capitalize">{{ ucwords(strtolower($subject['subjectName'])) }} <span class="" style="text-transform: uppercase">({{ ucwords(strtoupper($subject['subjectCode'])) }})</span></td>
                    <td class="teacher-name">{{ucwords(strtolower($subject['teacher']))}}</td>
                    <td>{{ number_format($subject['average'], 2) }}</td>
                    <td>
                        @if ($results->first()->marking_style === 1)
                            @if ($subject['average'] >= 40.5) A
                            @elseif ($subject['average'] >= 30.5) B
                            @elseif ($subject['average'] >= 20.5) C
                            @elseif ($subject['average'] >= 10.5) D
                            @else E @endif
                        @else
                            @if ($subject['average'] >= 80.5) A
                            @elseif ($subject['average'] >= 60.5) B
                            @elseif ($subject['average'] >= 40.5) C
                            @elseif ($subject['average'] >= 20.5) D
                            @else E @endif
                        @endif
                    </td>
                    <td>{{ $subject['position'] }}</td>
                    <td style="font-style: italic">
                        @if ($results->first()->marking_style === 1)
                            @if ($subject['average'] >= 40.5) Excellent
                            @elseif ($subject['average'] >= 30.5) Good
                            @elseif ($subject['average'] >= 20.5) Pass
                            @elseif ($subject['average'] >= 10.5) Poor
                            @else Fail @endif
                        @else
                            @if ($subject['average'] >= 80.5) Excellent
                            @elseif ($subject['average'] >= 60.5) Good
                            @elseif ($subject['average'] >= 40.5) Pass
                            @elseif ($subject['average'] >= 20.5) Poor
                            @else Fail @endif
                        @endif
                    </td>
                </tr>
            @endforeach

            <tr class="summary-row">
                <td colspan="6" style="background: rgb(187, 163, 56)">Overall Performance Summary</td>
            </tr>

            <tr class="summary-row">
                <td colspan="">
                    General Average: <strong>{{ number_format($studentGeneralAverage, 3) }}</strong>
                </td>
                <td colspan="" class="text-center">
                    Grade: <strong>
                    @if ($results->first()->marking_style === 1)
                        @if ($studentGeneralAverage >= 40.5) "A"
                        @elseif ($studentGeneralAverage >= 30.5) "B"
                        @elseif ($studentGeneralAverage >= 20.5) "C"
                        @elseif ($studentGeneralAverage >= 10.5) "D"
                        @else "E" @endif
                    @else
                        @if ($studentGeneralAverage >= 80.5) "A"
                        @elseif ($studentGeneralAverage >= 60.5) "B"
                        @elseif ($studentGeneralAverage >= 40.5) "C"
                        @elseif ($studentGeneralAverage >= 20.5) "D"
                        @else "E" @endif
                    @endif
                    </strong>
                </td>
                <td colspan="2">
                    Position: <strong style="text-decoration:underline">{{ $generalPosition }}</strong> out of <strong style="text-decoration:underline">{{ $totalStudents }}</strong>
                </td>
                <td colspan="2" class="text-center">
                    General Remarks:
                    @if ($results->first()->marking_style === 1)
                        @if ($studentGeneralAverage >= 40.5)
                            <span class="excellent">EXCELLENT</span>
                        @elseif ($studentGeneralAverage >= 30.5)
                            <span class="good">GOOD</span>
                        @elseif ($studentGeneralAverage >= 20.5)
                            <span class="pass">PASS</span>
                        @elseif ($studentGeneralAverage >= 10.5)
                            <span class="poor">POOR</span>
                        @else
                            <span class="fail">FAIL</span>
                        @endif
                    @else
                        @if ($studentGeneralAverage >= 80.5)
                            <span class="excellent">EXCELLENT</span>
                        @elseif ($studentGeneralAverage >= 60.5)
                            <span class="good">GOOD</span>
                        @elseif ($studentGeneralAverage >= 40.5)
                            <span class="pass">PASS</span>
                        @elseif ($studentGeneralAverage >= 20.5)
                            <span class="poor">POOR</span>
                        @else
                            <span class="fail">FAIL</span>
                        @endif
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
@endif

<div class="footer" style="text-transform: capitalize;">
    &copy; {{ $schoolInfo->school_name }} - {{ date('Y') }}.
</div>

</body>
</html>
