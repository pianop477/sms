<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Academic Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
            position: relative;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
        }
        .school-info {
            text-align: center;
            padding-top: 5px;
            text-transform: uppercase;
            font-size: 18px;
        }
        .student-image {
            position: absolute;
            right: 0;
            top: 0;
            text-align: center;
        }
        .student-image img {
            width: 70px;
            height: 70px;
            /* border-radius: 5px; */
            /* border: 1px solid #ddd; */
        }
        .student-details {
            width: 100%;
            margin: 10px 0;
            font-size: 12px;
        }
        .student-details td {
            vertical-align: top;
            padding: 2px 5px;
        }
        .section-title {
            font-weight: bold;
            text-align: center;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }
        .subject-name {
            text-align: left;
        }
        .teacher-name {
            text-align: left;
        }
        .summary-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .excellent {
            background-color: #75f430;
            padding: 2px 5px;
        }
        .good {
            background-color: #99faed;
            padding: 2px 5px;
        }
        .pass {
            background-color: #eddc71;
            padding: 2px 5px;
        }
        .poor {
            background-color: #b6b0b0;
            padding: 2px 5px;
        }
        .fail {
            background-color: #eb4b4b;
            padding: 2px 5px;
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
        .performance-summary {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .performance-item {
            margin: 5px 0;
        }
        @media print {
            .header, .footer {
                position: fixed;
                left: 0;
                right: 0;
            }
            .header {
                top: 0;
            }
            .footer {
                bottom: 0;
            }
            body {
                padding-top: 120px;
                padding-bottom: 30px;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">
        <img src="{{ public_path('assets/img/logo/'.$results->first()->logo) }}" alt="Logo" width="70">
    </div>
    <div class="school-info">
        <h3 style="margin:0; padding:0;">THE UNITED REPUBLIC OF TANZANIA</h3>
        <h4 style="margin:0; padding:0;">PRESIDENT OFFICE - TAMISEMI</h4>
        <h4 style="margin:0; padding:0;">{{ $results->first()->school_name }}</h4>
        <h5 style="margin:0; padding:0;">{{ $results->first()->postal_address }} - {{ $results->first()->postal_name }}, {{ $results->first()->country }}</h5>
        <h5 style="margin:5px 0; padding:0;">STUDENT'S ACADEMIC REPORT</h5>
    </div>
    <div class="student-image">
        @php
            $imagePath = public_path('assets/img/students/' . $studentId->image);
            $defaultImagePath = public_path('assets/img/students/student.jpg');
        @endphp

        @if(file_exists($imagePath) && !is_dir($imagePath))
            <img src="{{ $imagePath }}" alt="Student Image">
        @else
            <img src="{{ $defaultImagePath }}" alt="Student Image">
        @endif
        <p style="margin:2px 0; font-size:10px;">Adm.No: {{ strtoupper($results->first()->admission_number) }}</p>
    </div>
</div>

<div class="section-title">A. Student Information</div>

<table class="student-details">
    <tr>
        <td width="50%">
            <strong>Student Full Name:</strong> {{ strtoupper($studentId->first_name) }} {{ strtoupper($studentId->middle_name) }} {{ strtoupper($studentId->last_name) }}<br>
            <strong>Gender:</strong> {{ ucfirst($studentId->gender) }}
        </td>
        <td width="50%">
            <strong>Class:</strong> {{ strtoupper($results->first()->class_name) }}<br>
            <strong>Stream:</strong> {{ strtoupper($studentId->group) }}
        </td>
    </tr>
</table>

<div class="section-title">B. Examination Details</div>

<table class="student-details">
    <tr>
        <td width="50%">
            <strong>Examination Type:</strong> {{ strtoupper($results->first()->exam_type) }}<br>
            <strong>Term:</strong> {{ strtoupper($results->first()->Exam_term) }}
        </td>
        <td width="50%">
            <strong>Exam Date:</strong> {{ \Carbon\Carbon::parse($date)->format('d-F-Y') }}
        </td>
    </tr>
</table>

<div class="section-title">C. Student Overall Performance</div>

<table class="report-table" style="font-size: 12px;">
    <thead>
        <tr>
            <th>#</th>
            <th class="subject-name">Subject</th>
            <th class="teacher-name">Teacher</th>
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
                <td class="subject-name">{{ ucwords(strtolower($result->course_name)) }}</td>
                <td class="teacher-name">{{ ucwords(strtolower($result->teacher_first_name. '. '.$result->teacher_last_name[0])) }}</td>
                <td style="text-transform: uppercase">{{ $result->course_code }}</td>
                <td>{{ $result->score ?? 'X' }}</td>
                <td>{{ $result->score ? $result->grade : 'X' }}</td>
                <td>{{ $result->score ? $result->remarks : 'X' }}</td>
                <td>{{ $result->score ? $result->courseRank : 'X' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="performance-summary">
    <div class="performance-item"><strong>Total Marks:</strong> {{ $totalScore }}</div>
    <div class="performance-item"><strong>Overall Average:</strong> {{ number_format($averageScore, 2) }}</div>
    @php
        $grade = '';
        $gradeClass = '';
        if($results->first()->marking_style == 1) {
            if ($averageScore >= 40.5) {
                $grade = "'A' - EXCELLENT";
                $gradeClass = 'excellent';
            } elseif ($averageScore >= 30.5) {
                $grade = "'B' - GOOD";
                $gradeClass = 'good';
            } elseif ($averageScore >= 20.5) {
                $grade = "'C' - PASS";
                $gradeClass = 'pass';
            } elseif ($averageScore >= 10.5) {
                $grade = "'D' - POOR";
                $gradeClass = 'poor';
            } else {
                $grade = "'E' - FAIL";
                $gradeClass = 'fail';
            }
        }
        else {
            if ($averageScore >= 81) {
                $grade = "'A' - EXCELLENT";
                $gradeClass = 'excellent';
            } elseif ($averageScore >= 61) {
                $grade = "'B' - GOOD";
                $gradeClass = 'good';
            } elseif ($averageScore >= 41) {
                $grade = "'C' - PASS";
                $gradeClass = 'pass';
            } elseif ($averageScore >= 21) {
                $grade = "'D' - POOR";
                $gradeClass = 'poor';
            } else {
                $grade = "'E' - FAIL";
                $gradeClass = 'fail';
            }
        }
    @endphp
    <div class="performance-item"><strong>Grade Level:</strong> <span class="{{ $gradeClass }}">{{ $grade }}</span></div>
    <div class="performance-item"><strong>Position:</strong> {{ $studentRank }} out of {{ $rankings->count() }} students</div>
</div>

<div class="footer">
    &copy; Copyright {{ $results->first()->school_name }} - {{ date('Y') }}
</div>

</body>
</html>
