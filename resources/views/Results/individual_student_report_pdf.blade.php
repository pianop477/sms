<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Academic Report - {{ $studentId->first_name }} {{ $studentId->last_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', 'Arial', serif;
            font-size: 12px;
            line-height: 1.35;
            background: white;
            color: #000000;
            padding: 0.4cm;
        }

        @page {
            size: A4;
            margin: 1.5cm 1.5cm 2cm 1.5cm;
        }

        .report-container {
            max-width: 100%;
            margin: 0 auto;
        }

        .school-header {
            width: 100%;
            border-bottom: 2px solid #000000;
            margin-bottom: 10px;
            padding-bottom: 6px;
        }

        .school-header table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell,
        .photo-cell {
            width: 12%;
            text-align: center;
            vertical-align: middle;
        }

        .logo-img {
            max-width: 90px;
            max-height: 90px;
        }

        .school-info-cell {
            width: 76%;
            text-align: center;
            vertical-align: middle;
        }

        .school-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .school-address,
        .school-contacts {
            font-size: 9px;
            margin: 2px 0;
        }

        .student-photo {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border: 1px solid #cccccc;
            padding: 2px;
        }

        .report-title {
            text-align: center;
            margin: 8px 0;
        }

        .report-title h3 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 3px 0;
        }

        .report-title p {
            font-size: 12px;
            margin-top: 2px;
            text-transform: uppercase;
        }

        .section-header {
            background: #000000;
            color: white;
            padding: 5px;
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin: 10px 0 6px 0;
        }

        .student-info {
            width: 100%;
            margin: 8px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .student-info td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            width: 30%;
            background-color: #f5f5f5;
            font-size: 11px;
        }

        .info-value {
            width: 70%;
            font-size: 11px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 11px;
        }

        .report-table th {
            border: 1px solid #000000;
            padding: 6px 4px;
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .report-table td {
            border: 1px solid #000000;
            padding: 5px 4px;
            text-align: center;
        }

        .subject-name,
        .teacher-name {
            text-align: left;
        }

        .grade-A,
        .grade-B,
        .grade-C,
        .grade-D,
        .grade-E,
        .grade-F {
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 3px;
            display: inline-block;
        }

        .grade-A {
            background-color: #e8f5e9;
            border: 1px solid #2e7d32;
        }

        .grade-B {
            background-color: #e3f2fd;
            border: 1px solid #1565c0;
        }

        .grade-C {
            background-color: #fff3e0;
            border: 1px solid #e65100;
        }

        .grade-D {
            background-color: #ffebee;
            border: 1px solid #c62828;
        }

        .grade-E,
        .grade-F {
            background-color: #fce4ec;
            border: 1px solid #b71c1c;
        }

        .remark-excellent,
        .remark-good,
        .remark-pass,
        .remark-poor,
        .remark-fail {
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }

        .remark-excellent {
            background-color: #2e7d32;
            color: white;
        }

        .remark-good {
            background-color: #1565c0;
            color: white;
        }

        .remark-pass {
            background-color: #e65100;
            color: white;
        }

        .remark-poor {
            background-color: #c62828;
            color: white;
        }

        .remark-fail {
            background-color: #b71c1c;
            color: white;
        }

        @media print {

            .grade-A,
            .grade-B,
            .grade-C,
            .grade-D,
            .grade-E,
            .grade-F,
            .remark-excellent,
            .remark-good,
            .remark-pass,
            .remark-poor,
            .remark-fail {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-table th {
                background-color: #e0e0e0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .section-header {
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .summary-row td {
            font-weight: bold;
            padding: 5px;
            background-color: #fafafa;
        }

        .division-section {
            width: 100%;
            margin: 8px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .division-section td {
            padding: 8px;
            text-align: center;
        }

        .division-score {
            font-size: 14px;
            font-weight: bold;
        }

        .comment-section {
            width: 100%;
            margin: 8px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .comment-section td {
            padding: 8px 12px;
            vertical-align: top;
        }

        .comment-label {
            font-weight: bold;
            width: 22%;
            background-color: #f5f5f5;
            text-align: center;
        }

        .comment-content {
            width: 78%;
            line-height: 1.4;
            font-style: italic;
        }

        .qr-section {
            text-align: center;
            margin: 10px 0 6px 0;
            page-break-inside: avoid;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }

        .qr-text {
            font-size: 10px;
            margin-top: 3px;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 5px;
            left: 0;
            right: 0;
            width: 100%;
            padding-top: 5px;
            border-top: 1px solid #000000;
            text-align: center;
            font-size: 9px;
            background: white;
        }

        @media print {

            /* Prevent memory intensive rendering */
            * {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            body {
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .footer {
                position: fixed;
                bottom: 0;
                background: white;
            }
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="report-container">
        <!-- HEADER -->
        <table class="school-header" cellpadding="4" cellspacing="0">
            <tr>
                <td class="logo-cell">
                    @php
                    $logoFileName = $schoolInfo->logo ?? '';
                    $logoPath = '';
                    $logoExists = false;
                    if(!empty($logoFileName)) {
                    $paths = [
                    public_path('storage/logo/' . $logoFileName),
                    storage_path('app/public/logo/' . $logoFileName),
                    public_path('logo/' . $logoFileName)
                    ];
                    foreach($paths as $p) {
                    if(file_exists($p) && is_file($p)) {
                    $logoPath = $p;
                    $logoExists = true;
                    break;
                    }
                    }
                    }
                    @endphp
                    @if($logoExists && !empty($logoPath))
                    <img src="{{ $logoPath }}" class="logo-img" alt="School Logo">
                    @else
                    <div
                        style="width:65px;height:65px;border:1px solid #ccc;margin:0 auto;line-height:65px;font-size:9px;">
                        LOGO</div>
                    @endif
                </td>
                <td class="school-info-cell">
                    <div class="school-name">THE UNITED REPUBLIC OF TANZANIA</div>
                    <div class="school-name">THE PRIME MINISTER OFFICE</div>
                    <div class="school-name">{{ strtoupper($schoolInfo->school_name ?? 'SCHOOL NAME') }}</div>
                    <div class="school-address">{{ ucwords(strtolower($schoolInfo->postal_address ?? '_____')) }} - {{
                        ucwords(strtolower($schoolInfo->postal_name ?? '_____')) }}</div>
                    <div class="school-contacts">Email: {{ strtolower($schoolInfo->school_email ?? 'info@school.ac.tz')
                        }} | Tel: {{ $schoolInfo->school_phone ?? '______' }}</div>
                </td>
                <td class="photo-cell">
                    @php
                    $studentImageName = $studentId->image ?? '';
                    $studentImagePath = '';
                    $hasImage = false;
                    if(!empty($studentImageName)) {
                    $paths = [
                    public_path('storage/students/' . $studentImageName),
                    storage_path('app/public/students/' . $studentImageName),
                    public_path('students/' . $studentImageName)
                    ];
                    foreach($paths as $p) {
                    if(file_exists($p) && is_file($p)) {
                    $studentImagePath = $p;
                    $hasImage = true;
                    break;
                    }
                    }
                    }
                    if(!$hasImage) {
                    $defaultPaths = [
                    public_path('storage/students/student.jpg'),
                    storage_path('app/public/students/student.jpg')
                    ];
                    foreach($defaultPaths as $p) {
                    if(file_exists($p) && is_file($p)) {
                    $studentImagePath = $p;
                    $hasImage = true;
                    break;
                    }
                    }
                    }
                    @endphp
                    @if($hasImage && !empty($studentImagePath))
                    <img src="{{ $studentImagePath }}" class="student-photo" alt="Student Photo">
                    @else
                    <div
                        style="width:65px;height:65px;border:1px solid #ccc;margin:0 auto;line-height:65px;font-size:9px;">
                        PHOTO</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- REPORT TITLE -->
        <div class="report-title">
            <h3>ACADEMIC PROGRESS REPORT</h3>
            <p>{{ strtoupper($results->first()->exam_type ?? 'TERMINAL') }} - {{ \Carbon\Carbon::parse($date ??
                now())->format('Y') }}</p>
        </div>

        <!-- STUDENT INFORMATION -->
        <div class="section-header">STUDENT'S INFORMATION</div>
        <table class="student-info">
            <tr>
                <td class="info-label">ADMISSION No.</td>
                <td class="info-value">{{ strtoupper($studentId->admission_number ?? 'N/A') }}</td>
                <td class="info-label">CLASS</td>
                <td class="info-value">{{ strtoupper($studentId->class_name ?? $results->first()->class_name ?? 'N/A')
                    }}</td>
            </tr>
            <tr>
                <td class="info-label">STUDENT NAME</td>
                <td class="info-value">{{ strtoupper($studentId->first_name ?? '') }} {{
                    strtoupper($studentId->middle_name ?? '') }} {{ strtoupper($studentId->last_name ?? '') }}</td>
                <td class="info-label">STREAM</td>
                <td class="info-value">{{ strtoupper($studentId->group ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="info-label">GENDER</td>
                <td class="info-value">{{ ucfirst($studentId->gender ?? 'N/A') }}</td>
                <td class="info-label">TERM</td>
                <td class="info-value">{{ strtoupper($results->first()->Exam_term ?? 'N/A') }}</td>
            </tr>
        </table>

        <!-- RESULTS TABLE -->
        <div class="section-header">SUBJECT PERFORMANCE</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th width="45%">SUBJECT NAME (CODE)</th>
                    <th width="20%">TEACHER</th>
                    <th width="10%">SCORE</th>
                    <th width="8%">GRADE</th>
                    <th width="7%">RANK</th>
                    <th width="10%">REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @php $subjectCount = 0; @endphp
                @foreach ($results as $result)
                @php $subjectCount++; @endphp
                <tr>
                    <td class="subject-name text-capitalize">{{ ucwords(strtolower($result->course_name ?? 'N/A')) }}
                        <span class="text-uppercase">({{ strtoupper($result->course_code ?? 'N/A') }})</span>
                    </td>
                    <td class="teacher-name text-capitalize">{{ ucwords(strtolower($result->teacher_first_name ?? ''))
                        }} {{ strtoupper(substr($result->teacher_last_name ?? '', 0, 1)) }}.</td>
                    <td class="bold text-center">{{ $result->score ?? 'X' }}</td>
                    <td class="text-center"><span class="grade-{{ $result->grade ?? 'X' }}">{{ $result->grade ?? 'X'
                            }}</span></td>
                    <td class="text-center">{{ $result->score ? ($result->courseRank ?? '-') : 'X' }}</td>
                    <td class="text-center">
                        @php
                        $score = $result->score ?? null;
                        $grade = $result->grade ?? null;

                        if(empty($score) || $score == 0) {
                        $remarks = 'ABSENT';
                        } else {
                        $remarks = match($grade) {
                        'A' => 'Excellent',
                        'B' => 'Good',
                        'C' => 'Pass',
                        'D' => 'Poor',
                        'E', 'F' => 'Fail',
                        default => '-'
                        };
                        }
                        @endphp
                        {{ $remarks }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- DIVISION SECTION (Marking Style 3 only) -->
        @if ($marking_style == 3 && isset($division))
        <table class="division-section">
            <tbody>
                <tr>
                    <td width="50%"><strong>AGGREGATE POINTS</strong><br><span class="division-score">{{
                            $aggregatePoints ?? 0 }}</span></td>
                    <td width="50%"><strong>DIVISION</strong><br><span
                            style="font-size:16px;font-weight:bold;padding:2px 15px;border:1px solid #000;display:inline-block;">{{
                            $division === '0' ? '0' : $division }}</span></td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:9px;padding:4px;"><strong>Division Guide:</strong> I (Excellent) |
                        II (Good) | III (Pass) | IV (Poor) | 0 (Fail)</td>
                </tr>
            </tbody>
        </table>
        @endif

        <!-- OVERALL PERFORMANCE SUMMARY -->
        <div class="section-header">OVERALL PERFORMANCE SUMMARY</div>
        <table class="report-table">
            <tbody>
                <tr class="summary-row">
                    <td width="25%">TOTAL MARKS</td>
                    <td width="25%"><strong>{{ number_format($totalScore ?? 0, 2) }}</strong></td>
                    <td width="25%">CLASS POSITION</td>
                    <td width="25%"><strong>{{ $studentRank ?? 1 }} out of {{ $rankings->count() ?? 1 }}</strong></td>
                </tr>
                <tr class="summary-row">
                    <td class="summary-label">GENERAL AVERAGE</td>
                    <td class="summary-value"><strong>{{ number_format($averageScore ?? 0, 2) }}</strong></td>
                    <td class="summary-label">SUBJECTS TAKEN</td>
                    <td class="summary-value"><strong>{{ $subjectCount }}</strong></td>
                </tr>
                <tr class="summary-row">
                    <td class="summary-label">OVERALL GRADE</td>
                    <td colspan="3">
                        @php
                        $overallGrade = ''; $gradeComment = ''; $remarkClass = '';
                        $avgScore = $averageScore ?? 0;
                        if ($marking_style == 3 && isset($division)) {
                        if ($division == 'I') { $overallGrade = 'I'; $gradeComment = 'EXCELLENT'; $remarkClass =
                        'remark-excellent'; }
                        elseif ($division == 'II') { $overallGrade = 'II'; $gradeComment = 'GOOD'; $remarkClass =
                        'remark-good'; }
                        elseif ($division == 'III') { $overallGrade = 'III'; $gradeComment = 'PASS'; $remarkClass =
                        'remark-pass'; }
                        elseif ($division == 'IV') { $overallGrade = 'IV'; $gradeComment = 'POOR'; $remarkClass =
                        'remark-poor'; }
                        else { $overallGrade = '0'; $gradeComment = 'FAIL'; $remarkClass = 'remark-fail'; }
                        }
                        elseif ($marking_style == 1) {
                        if ($avgScore >= 40.5) { $overallGrade = 'A'; $gradeComment = 'EXCELLENT'; $remarkClass =
                        'remark-excellent'; }
                        elseif ($avgScore >= 30.5) { $overallGrade = 'B'; $gradeComment = 'GOOD'; $remarkClass =
                        'remark-good'; }
                        elseif ($avgScore >= 20.5) { $overallGrade = 'C'; $gradeComment = 'PASS'; $remarkClass =
                        'remark-pass'; }
                        elseif ($avgScore >= 10.5) { $overallGrade = 'D'; $gradeComment = 'POOR'; $remarkClass =
                        'remark-poor'; }
                        else { $overallGrade = 'E'; $gradeComment = 'FAIL'; $remarkClass = 'remark-fail'; }
                        }
                        else {
                        if ($avgScore >= 80.5) { $overallGrade = 'A'; $gradeComment = 'EXCELLENT'; $remarkClass =
                        'remark-excellent'; }
                        elseif ($avgScore >= 60.5) { $overallGrade = 'B'; $gradeComment = 'GOOD'; $remarkClass =
                        'remark-good'; }
                        elseif ($avgScore >= 40.5) { $overallGrade = 'C'; $gradeComment = 'PASS'; $remarkClass =
                        'remark-pass'; }
                        elseif ($avgScore >= 20.5) { $overallGrade = 'D'; $gradeComment = 'POOR'; $remarkClass =
                        'remark-poor'; }
                        else { $overallGrade = 'E'; $gradeComment = 'FAIL'; $remarkClass = 'remark-fail'; }
                        }
                        @endphp
                        <strong>Grade {{ $overallGrade }}</strong>
                    </td>
                </tr>
                <tr class="summary-row">
                    <td class="summary-label">GENERAL REMARKS</td>
                    <td colspan="3"><span class="{{ $remarkClass }}">{{ $gradeComment }}</span></td>
                </tr>
            </tbody>
            }
        </table>

        <!-- HEAD TEACHER'S COMMENT -->
        @php
        $headComment = '';
        $avgScore = $averageScore ?? 0;
        if ($marking_style == 3 && isset($division)) {
        if ($division == 'I') $headComment = 'Outstanding Achievement! Keep shining!';
        elseif ($division == 'II') $headComment = 'Very Good Performance! Push harder!';
        elseif ($division == 'III') $headComment = 'Good Effort! Keep building!';
        elseif ($division == 'IV') $headComment = 'Room for Improvement. Work harder!';
        else $headComment = 'Fresh Start Ahead. We will support you!';
        } else {
        if ($marking_style == 1) {
        if ($avgScore >= 40.5) $headComment = 'Outstanding Achievement! Keep shining!';
        elseif ($avgScore >= 30.5) $headComment = 'Very Good Performance! Push harder!';
        elseif ($avgScore >= 20.5) $headComment = 'Good Effort! Keep building!';
        elseif ($avgScore >= 10.5) $headComment = 'Room for Improvement. Work harder!';
        else $headComment = 'Fresh Start Ahead. We will support you!';
        } else {
        if ($avgScore >= 80.5) $headComment = 'Exceptional Achievement! Continue being a role model!';
        elseif ($avgScore >= 60.5) $headComment = 'Commendable Performance! Keep pushing!';
        elseif ($avgScore >= 40.5) $headComment = 'Making Progress! Keep the momentum!';
        elseif ($avgScore >= 20.5) $headComment = 'Time to Unlock Your Potential!';
        else $headComment = 'Your Comeback Story Starts Now!';
        }
        }
        @endphp

        <table class="comment-section">
            <tbody>
                <tr>
                    <td class="comment-label">HEAD TEACHER'S REMARKS</td>
                    <td class="comment-content"><i>{{ $headComment }}</i></td>
                </tr>
            </tbody>
        </table>

        <!-- QR CODE SECTION -->
        <div class="qr-section">
            @if(!empty($qrPng))
            <img src="data:image/png;base64,{{ $qrPng }}" class="qr-code" alt="Verification QR Code">
            @else
            <div
                style="width:100px;height:100px;border:1px solid #ccc;margin:0 auto;text-align:center;line-height:100px;">
                QR</div>
            @endif
            <div class="qr-text"><strong>Scan to Verify Authenticity</strong></div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            &copy; {{ date('Y') }} - {{ strtoupper($schoolInfo->school_name ?? 'SCHOOL') }} |
            Printed: {{ now()->format('d-M-Y H:i') }} |
            Powered by ShuleApp System
        </div>
    </div>
</body>

</html>
