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
            padding: 6px;
            text-align: center;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .subject-name {
            text-align: left;
            width: 20%;
        }
        .teacher-name {
            text-align: left;
            width: 15%;
        }
        .exam-score {
            width: 8%;
        }
        .summary-row td {
            font-weight: bold;
            padding: 6px;
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
            <img src="{{ public_path('assets/img/logo/'.$results->first()->logo) }}" alt="Logo" width="80">
        </td>
        <td width="70%" class="school-info">
            <h3 style="margin:0; padding:0;">THE UNITED REPUBLIC OF TANZANIA</h3>
            <h3 style="margin:0; padding:0;">PRESIDENT OFFICE - TAMISEMI</h3>
            <h4 style="margin:0; padding:0;">{{ $results->first()->school_name }}</h4>
            <h5 style="margin:0; padding:0;">{{ $results->first()->postal_address }} - {{ $results->first()->postal_name }}, {{ $results->first()->country }}</h5>
        </td>
        <td width="15%" align="right">
            @php
                $imagePath = public_path('assets/img/students/' . $studentId->image);
                $defaultImagePath = public_path('assets/img/students/student.jpg');
            @endphp
            @if(file_exists($imagePath) && !is_dir($imagePath))
                <img src="{{ $imagePath }}" width="300px" height="300px" class="rounded-circle">
            @else
                <img src="{{ $defaultImagePath }}" width="300px" height="300px" class="rounded-circle">
            @endif
        </td>
    </tr>
</table>
<table class="report-header">
    <tr>
        <td>
            <h5 style="margin:5px 0; padding:0;">STUDENT'S ACADEMIC REPORT</h5>
            <h5 style="margin:0; padding:0;"> {{ strtoupper($results->first()->exam_type) }} Report - {{ \Carbon\Carbon::parse($date)->format('d/m/Y')  }}</h5>
        </td>
    </tr>
</table>
<p style="padding: 3px; background:rgb(187, 163, 56); text-align:center; font-size: 12px;"><strong>Student's Information</strong></p>
<table class="student-info">
    <tr>
        <td width="50%">
            <strong>Admission Number:</strong> <span class="">{{ strtoupper($results->first()->admission_number) }}</span><br>
            <strong>Student Name:</strong> <span class="">{{ strtoupper($studentId->first_name) }} {{ strtoupper($studentId->middle_name) }} {{ strtoupper($studentId->last_name) }}</span><br>
            <strong>Gender:</strong> <span class="">{{ ucfirst($studentId->gender) }}</span>
        </td>
        <td width="50%">
            <strong>Class:</strong> <span class="">{{ strtoupper($results->first()->class_name) }}</span><br>
            <strong>Stream:</strong> <span class="">{{ strtoupper($studentId->group) }}</span><br>
            <strong>Term:</strong> <span class="">{{ strtoupper($results->first()->Exam_term) }}</span>
        </td>
    </tr>
</table>
<table class="report-table">
        <thead>
            <tr>
                <th class="subject-name">Subject Name</th>
                <th class="teacher-name">Subject Code</th>
                <th class="text-center">Teacher</th>
                <th>Score</th>
                <th>Grade</th>
                <th>Rank</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $index => $result)
                <tr>
                    <td class="subject-name" style="text-transform: capitalize">{{ ucwords(strtolower($result->course_name)) }}</td>
                    <td class="teacher-name">{{ ucwords(strtoupper($result->course_code)) }}</td>
                    <td>{{ ucwords(strtolower($result->teacher_first_name. '. '.$result->teacher_last_name[0])) }}</td>
                    <td>{{ $result->score ?? 'X' }}</td>
                    <td>{{ $result->score ? $result->grade : 'X' }}</td>
                    <td>{{ $result->score ? $result->courseRank : 'X' }}</td>
                    <td style="font-style: italic">{{ $result->score ? $result->remarks : 'X' }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7"></td>
            </tr>
            <tr class="summary-row">
                <td colspan="7" style="background: rgb(187, 163, 56)">Overall Performance Summary</td>
            </tr>
            <tr class="summary-row">
                <td colspan="1">
                    Total Marks: <strong>{{ $totalScore }}</strong>
                </td>
                <td colspan="1">
                    Average: <strong>{{ number_format($averageScore, 3) }}</strong>
                </td>
                    @php
                        $grade = '';
                        $gradeClass = '';
                        if($results->first()->marking_style == 1) {
                            if ($averageScore >= 40.5) {
                                $grade = 'A';
                                $gradeClass = 'excellent';
                            } elseif ($averageScore >= 30.5) {
                                $grade = 'B';
                                $gradeClass = 'good';
                            } elseif ($averageScore >= 20.5) {
                                $grade = 'C';
                                $gradeClass = 'pass';
                            } elseif ($averageScore >= 10.5) {
                                $grade = 'D';
                                $gradeClass = 'poor';
                            } else {
                                $grade = 'E';
                                $gradeClass = 'fail';
                            }
                        }
                        else {
                            if ($averageScore >= 81) {
                                $grade = 'A';
                                $gradeClass = 'excellent';
                            } elseif ($averageScore >= 61) {
                                $grade = 'B';
                                $gradeClass = 'good';
                            } elseif ($averageScore >= 41) {
                                $grade = 'C';
                                $gradeClass = 'pass';
                            } elseif ($averageScore >= 21) {
                                $grade = 'D';
                                $gradeClass = 'poor';
                            } else {
                                $grade = 'E';
                                $gradeClass = 'fail';
                            }
                        }
                    @endphp
                <td colspan="1" class="text-center">
                    Grade: <strong>
                        {{ $grade }}
                    </strong>
                </td>
                <td colspan="2">
                    Position: <strong style="text-decoration:underline">{{ $studentRank }} out of {{ $rankings->count() }}</strong>
                </td>
                <td colspan="2" class="text-center">
                    General Remarks:
                    @if ($results->first()->marking_style == 1)
                        @if ($averageScore >= 40.5)
                            <span class="excellent">EXCELLENT</span>
                        @elseif ($averageScore >= 30.5)
                            <span class="good">GOOD</span>
                        @elseif ($averageScore >= 20.5)
                            <span class="pass">PASS</span>
                        @elseif ($averageScore >= 10.5)
                            <span class="poor">POOR</span>
                        @else
                            <span class="fail">FAIL</span>
                        @endif
                    @else
                        @if ($averageScore >= 80.5)
                            <span class="excellent">EXCELLENT</span>
                        @elseif ($averageScore >= 60.5)
                            <span class="good">GOOD</span>
                        @elseif ($averageScore >= 40.5)
                            <span class="pass">PASS</span>
                        @elseif ($averageScore >= 20.5)
                            <span class="poor">POOR</span>
                        @else
                            <span class="fail">FAIL</span>
                        @endif
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <table class="report-table" style="margin-top: 20px;">
            <tbody>
                <tr>
                    <td colspan="7" style="background: rgb(187, 163, 56); font-size: 12px"><strong>Descriptions</strong></td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: center; font-style: italic;">
                        Note: "X" indicates that the student did not take the exam.
                    </td>
                </tr>
            </tbody>
        </table>

<div class="footer" style="text-transform: capitalize;">
    &copy; {{ $results->first()->school_name }} - {{ date('Y') }}.
</div>

</body>
</html>
