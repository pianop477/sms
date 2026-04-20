<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Academic Report - {{ $students->first_name }} {{ $students->last_name }}</title>
    <style>
        /* RESET & BASE - Normal Readable Fonts */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', 'Arial', serif;
            font-size: 11px;
            line-height: 1.35;
            background: white;
            color: #000000;
            padding: 0.4cm;
        }

        /* Page Setup for Printing - Optimized for One Page */
        @page {
            size: A4;
            margin: 0.8cm 0.8cm 1cm 0.8cm;
        }

        /* Container */
        .report-container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* ============ HEADER SECTION ============ */
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

        .logo-cell {
            width: 12%;
            text-align: center;
            vertical-align: middle;
        }

        .logo-img {
            max-width: 75px;
            max-height: 75px;
        }

        .school-info-cell {
            width: 76%;
            text-align: center;
            vertical-align: middle;
        }

        .school-name {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .school-address {
            font-size: 9px;
            margin: 2px 0;
        }

        .school-contacts {
            font-size: 9px;
            margin: 2px 0;
        }

        .photo-cell {
            width: 12%;
            text-align: center;
            vertical-align: middle;
        }

        .student-photo {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border: 1px solid #cccccc;
            padding: 2px;
        }

        /* ============ REPORT TITLE ============ */
        .report-title {
            text-align: center;
            margin: 8px 0;
        }

        .report-title h3 {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 3px 0;
        }

        .report-title p {
            font-size: 10px;
            margin-top: 2px;
        }

        /* ============ SECTION HEADER ============ */
        .section-header {
            background: #000000;
            color: white;
            padding: 4px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 10px 0 6px 0;
        }

        /* ============ STUDENT INFO SECTION ============ */
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
            width: 28%;
            background-color: #f5f5f5;
            font-size: 10px;
        }

        .info-value {
            width: 72%;
            font-size: 10px;
        }

        /* ============ RESULTS TABLE ============ */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 10px;
        }

        .report-table th {
            border: 1px solid #000000;
            padding: 5px 3px;
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 9.5px;
        }

        .report-table td {
            border: 1px solid #000000;
            padding: 5px 3px;
            text-align: center;
            font-size: 10px;
        }

        .subject-name {
            text-align: left;
        }

        .teacher-name {
            text-align: left;
        }

        /* ============ GRADE COLORS ============ */
        .grade-A {
            background-color: #e8f5e9;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 10px;
        }
        .grade-B {
            background-color: #e3f2fd;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 10px;
        }
        .grade-C {
            background-color: #fff3e0;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 10px;
        }
        .grade-D {
            background-color: #ffebee;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 10px;
        }
        .grade-E {
            background-color: #fce4ec;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 10px;
        }

        .excellent {
            background-color: #4caf50;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
        }
        .good {
            background-color: #2196f3;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
        }
        .pass {
            background-color: #ff9800;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
        }
        .poor {
            background-color: #9e9e9e;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
        }
        .fail {
            background-color: #f44336;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
        }

        /* ============ SUMMARY ROW ============ */
        .summary-row td {
            font-weight: bold;
            padding: 5px;
            background-color: #fafafa;
            font-size: 10px;
        }

        .highlight-bg {
            background-color: #1a1a2e;
            color: white;
            font-weight: bold;
        }

        /* ============ EXAM KEY SECTION ============ */
        .exam-key-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 9px;
        }

        .exam-key-table th,
        .exam-key-table td {
            border: 1px solid #000000;
            padding: 4px 6px;
            text-align: left;
        }

        .exam-key-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        /* ============ QR CODE SECTION ============ */
        .qr-section {
            text-align: center;
            margin: 12px 0 8px 0;
            page-break-inside: avoid;
        }

        .qr-code {
            width: 130px;
            height: 130px;
            margin: 0 auto;
        }

        .qr-text {
            font-size: 9px;
            margin-top: 0px;
            font-weight: bold;
        }

        /* ============ FOOTER - Last part after QR ============ */
        .footer {
            width: 100%;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #000000;
            text-align: center;
            font-size: 8px;
        }

        /* Print Optimization */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .report-table th {
                background-color: #e0e0e0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .grade-A, .grade-B, .grade-C, .grade-D, .grade-E {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-uppercase { text-transform: uppercase; }
        .text-capitalize { text-transform: capitalize; }
        .bold { font-weight: bold; }
        .compact-header { font-size: 8px; }
    </style>
</head>

<body>
    <div class="report-container">
        <!-- ==================== HEADER ==================== -->
        <table class="school-header" cellpadding="4" cellspacing="0">
            <tr>
                <td class="logo-cell">
                    @php
                        $logoPath = storage_path('app/public/logo/' . ($schoolInfo->logo ?? 'default.png'));
                        $logoExists = file_exists($logoPath) && !empty($schoolInfo->logo);
                    @endphp
                    @if($logoExists)
                        <img src="{{ $logoPath }}" class="logo-img" alt="School Logo">
                    @else
                        <div style="width: 75px; height: 75px; border: 1px solid #ccc; margin: 0 auto;"></div>
                    @endif
                </td>
                <td class="school-info-cell">
                    <div class="school-name">THE UNITED REPUBLIC OF TANZANIA</div>
                    <div class="school-name">PRESIDENT'S OFFICE - TAMISEMI</div>
                    <div class="school-name" style="font-size: 11px; margin-top: 3px;">
                        {{ strtoupper($schoolInfo->school_name ?? 'SCHOOL NAME') }}
                    </div>
                    <div class="school-address">
                        P.O.BOX {{ $schoolInfo->postal_address ?? '______' }} -
                        {{ $schoolInfo->postal_name ?? '______' }},
                        {{ $schoolInfo->country ?? 'TANZANIA' }}
                    </div>
                    <div class="school-contacts">
                        Email: {{ strtolower($schoolInfo->school_email ?? 'info@school.ac.tz') }} |
                        Phone: {{ $schoolInfo->school_phone ?? '_________' }}
                    </div>
                </td>
                <td class="photo-cell">
                    @php
                        $studentImage = storage_path('app/public/students/' . ($students->image ?? ''));
                        $hasImage = file_exists($studentImage) && !empty($students->image);
                    @endphp
                    @if($hasImage)
                        <img src="{{ $studentImage }}" class="student-photo" alt="Student Photo">
                    @else
                        <div style="width: 75px; height: 75px; border: 1px solid #ccc; text-align: center; line-height: 75px; font-size: 9px;">
                            <img src="{{ public_path('storage/students/student.jpg') }}" class="student-photo" alt="Student Photo">
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- ==================== REPORT TITLE ==================== -->
        <div class="report-title">
            <h3>ACADEMIC PROGRESS REPORT</h3>
            <p>
                {{ strtoupper($reports->title) }} ASSESSMENT -
                TERM {{ $reports->term ?? 'I' }},
                {{ \Carbon\Carbon::parse($reports->created_at ?? now())->format('Y') }}
            </p>
        </div>

        <!-- ==================== STUDENT INFORMATION ==================== -->
        <div class="section-header">STUDENT'S INFORMATION</div>
        <table class="student-info" cellpadding="6" cellspacing="0">
            <tr>
                <td class="info-label">ADMISSION NUMBER</td>
                <td class="info-value">{{ strtoupper($students->admission_number ?? 'N/A') }}</td>
                <td class="info-label">CLASS</td>
                <td class="info-value">{{ strtoupper($students->class_name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="info-label">STUDENT NAME</td>
                <td class="info-value">
                    {{ strtoupper($students->first_name ?? '') }}
                    {{ strtoupper($students->middle_name ?? '') }}
                    {{ strtoupper($students->last_name ?? '') }}
                </td>
                <td class="info-label">STREAM</td>
                <td class="info-value">{{ strtoupper($students->group ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="info-label">GENDER</td>
                <td class="info-value">{{ ucfirst($students->gender ?? 'N/A') }}</td>
                <td class="info-label">TERM</td>
                <td class="info-value">{{ strtoupper($reports->term ?? 'N/A') }}</td>
            </tr>
        </table>

        <!-- ==================== RESULTS TABLE ==================== -->
        @if ($reports->combine_option === 'individual')
        <table class="report-table" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th rowspan="2" width="18%">SUBJECT (CODE)</th>
                    <th rowspan="2" width="12%">TEACHER</th>
                    <th colspan="{{ count($examHeaders) }}" class="text-center">EXAMINATION SCORES</th>
                    <th rowspan="2" width="7%">TOT</th>
                    <th rowspan="2" width="7%">AVG</th>
                    <th rowspan="2" width="6%">GRD</th>
                    <th rowspan="2" width="6%">POS</th>
                    <th rowspan="2" width="12%">REMARKS</th>
                </tr>
                <tr>
                    @foreach ($examHeaders as $exam)
                        <th class="compact-header text-center" width="5%">
                            {{ strtoupper($exam['abbr']) }}<br>
                            <small>{{ \Carbon\Carbon::parse($exam['date'])->format('d-M') }}</small>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($finalData as $subject)
                <tr>
                    <td class="subject-name text-capitalize">
                        {{ ucwords(strtolower($subject['subjectName'])) }}
                        <span class="text-uppercase">({{ strtoupper($subject['subjectCode']) }})</span>
                    </td>
                    <td class="teacher-name text-capitalize">{{ ucwords(strtolower($subject['teacher'])) }}</td>
                    @foreach ($examHeaders as $exam)
                        <td class="text-center">{{ $subject['examScores'][$exam['abbr'] . '_' . $exam['date']] ?? 'X' }}</td>
                    @endforeach
                    <td class="bold">{{ $subject['total'] }}</td>
                    <td>{{ number_format($subject['average'], 2) }}</td>
                    <td>
                        @php
                            $subjGrade = '';
                            if ($reports->marking_style === 1) {
                                if ($subject['average'] >= 40.5) {
                                    $subjGrade = 'A';
                                    $subject['remarks'] = 'Excellent';
                                }
                                elseif ($subject['average'] >= 30.5) {
                                    $subjGrade = 'B';
                                    $subject['remarks'] = 'Good';
                                }
                                elseif ($subject['average'] >= 20.5) {
                                    $subjGrade = 'C';
                                    $subject['remarks'] = 'Average';
                                }
                                elseif ($subject['average'] >= 10.5) {
                                    $subjGrade = 'D';
                                    $subject['remarks'] = 'Poor';
                                }
                                else {
                                    $subjGrade = 'E';
                                    $subject['remarks'] = 'Fail';
                                }
                            } else {
                                if ($subject['average'] >= 80.5) {
                                    $subjGrade = 'A';
                                    $subject['remarks'] = 'Excellent';
                                }
                                elseif ($subject['average'] >= 60.5) {
                                    $subjGrade = 'B';
                                    $subject['remarks'] = 'Good';
                                }
                                elseif ($subject['average'] >= 40.5) {
                                    $subjGrade = 'C';
                                    $subject['remarks'] = 'Pass';
                                }
                                elseif ($subject['average'] >= 20.5) {
                                    $subjGrade = 'D';
                                    $subject['remarks'] = 'Poor';
                                }
                                else {
                                    $subjGrade = 'E';
                                    $subject['remarks'] = 'Fail';
                                }
                            }
                        @endphp
                        <span class="grade-{{ $subjGrade }}">{{ $subjGrade }}</span>
                    </td>
                    <td>{{ $subject['position'] }}</td>
                    <td style="font-style: italic;">{{ $subject['remarks'] }}</td>
                </tr>
                @endforeach

                <!-- Exam Averages Row -->
                <tr class="summary-row">
                    <td colspan="2" class="bold">EXAM AVG</td>
                    @foreach ($examHeaders as $exam)
                        <td class="text-center bold">{{ number_format($examAverages[$exam['abbr'] . '_' . $exam['date']] ?? 0, 1) }}</td>
                    @endforeach
                    <td colspan="5"></td>
                </tr>

                <!-- Exam Grades Row -->
                <tr class="summary-row">
                    <td colspan="2" class="bold">EXAM GRADE</td>
                    @foreach ($examHeaders as $exam)
                        @php
                            $examKey = $exam['abbr'] . '_' . $exam['date'];
                            $avgScore = $examAverages[$examKey] ?? 0;
                            $examGrade = '';
                            if ($reports->marking_style === 1) {
                                if ($avgScore >= 40.5) $examGrade = 'A';
                                elseif ($avgScore >= 30.5) $examGrade = 'B';
                                elseif ($avgScore >= 20.5) $examGrade = 'C';
                                elseif ($avgScore >= 10.5) $examGrade = 'D';
                                else $examGrade = 'E';
                            } else {
                                if ($avgScore >= 80.5) $examGrade = 'A';
                                elseif ($avgScore >= 60.5) $examGrade = 'B';
                                elseif ($avgScore >= 40.5) $examGrade = 'C';
                                elseif ($avgScore >= 20.5) $examGrade = 'D';
                                else $examGrade = 'E';
                            }
                        @endphp
                        <td class="text-center bold"><span class="grade-{{ $examGrade }}">{{ $examGrade }}</span></td>
                    @endforeach
                    <td colspan="5"></td>
                </tr>
            </tbody>
        </table>

        @if (count($examSpecifications) > 0)
        <table class="exam-key-table" cellpadding="4" cellspacing="0">
            <tbody>
                <tr><td colspan="7" class="highlight-bg text-center">EXAMINATION CODES KEY</td></tr>
                <tr><th width="30%">EXAM CODE</th><th width="70%">EXAM DESCRIPTION</th></tr>
                @foreach ($examSpecifications as $spec)
                <tr><td class="text-uppercase">{{ $spec['abbr'] }}</td><td class="text-capitalize">{{ $spec['full_name'] }}</td></tr>
                @endforeach
                <tr><td colspan="2" class="text-center" style="font-style: italic;">Note: "X" indicates that the student did not take the exam.</td></tr>
            </tbody>
        </table>
        @endif

        @elseif ($reports->combine_option === 'sum')
        <table class="report-table" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th width="35%">SUBJECT NAME (CODE)</th>
                    <th width="18%">TEACHER</th>
                    <th width="10%">TOTAL</th>
                    <th width="10%">AVERAGE</th>
                    <th width="8%">GRADE</th>
                    <th width="8%">RANK</th>
                    <th width="11%">REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($finalData as $subject)
                <tr>
                    <td class="subject-name text-capitalize">
                        {{ ucwords(strtolower($subject['subjectName'])) }}
                        <span class="text-uppercase">({{ strtoupper($subject['subjectCode']) }})</span>
                    </td>
                    <td class="teacher-name text-capitalize">{{ ucwords(strtolower($subject['teacher'])) }}</td>
                    <td class="bold">{{ $subject['total'] }}</td>
                    <td>{{ number_format($subject['average'], 2) }}</td>
                    <td>
                        @php
                            $subjGrade = '';
                            if ($reports->marking_style === 1) {
                                if ($subject['average'] >= 40.5) {
                                    $subjGrade = 'A';
                                    $subject['remarks'] = 'Excellent';
                                }
                                elseif ($subject['average'] >= 30.5) {
                                    $subjGrade = 'B';
                                    $subject['remarks'] = 'Good';
                                }
                                elseif ($subject['average'] >= 20.5) {
                                    $subjGrade = 'C';
                                    $subject['remarks'] = 'Pass';
                                }
                                elseif ($subject['average'] >= 10.5) {
                                    $subjGrade = 'D';
                                    $subject['remarks'] = 'Poor';
                                }
                                else {
                                    $subjGrade = 'E';
                                    $subject['remarks'] = 'Fail';
                                }
                            } else {
                                if ($subject['average'] >= 80.5) {
                                    $subjGrade = 'A';
                                    $subject['remarks'] = 'Excellent';
                                }
                                elseif ($subject['average'] >= 60.5) {
                                    $subjGrade = 'B';
                                    $subject['remarks'] = 'Good';
                                }
                                elseif ($subject['average'] >= 40.5) {
                                    $subjGrade = 'C';
                                    $subject['remarks'] = 'Pass';
                                }
                                elseif ($subject['average'] >= 20.5) {
                                    $subjGrade = 'D';
                                    $subject['remarks'] = 'Poor';
                                }
                                else {
                                    $subjGrade = 'E';
                                    $subject['remarks'] = 'Fail';
                                }
                            }
                        @endphp
                        <span class="grade-{{ $subjGrade }}">{{ $subjGrade }}</span>
                    </td>
                    <td>{{ $subject['position'] }}</td>
                    <td style="font-style: italic;">{{ $subject['remarks']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <table class="report-table" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th width="40%">SUBJECT NAME (CODE)</th>
                    <th width="20%">TEACHER</th>
                    <th width="12%">AVERAGE</th>
                    <th width="8%">GRADE</th>
                    <th width="8%">RANK</th>
                    <th width="12%">REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($finalData as $subject)
                <tr>
                    <td class="subject-name text-capitalize">
                        {{ ucwords(strtolower($subject['subjectName'])) }}
                        <span class="text-uppercase">({{ strtoupper($subject['subjectCode']) }})</span>
                    </td>
                    <td class="teacher-name text-capitalize">{{ ucwords(strtolower($subject['teacher'])) }}</td>
                    <td>{{ number_format($subject['average'], 2) }}</td>
                    <td>
                        @php
                            $subjGrade = '';
                            if ($reports->marking_style === 1) {
                                if ($subject['average'] >= 40.5) {
                                    $subjGrade = 'A';
                                    $subject['remarks'] = 'Excellent';
                                }
                                elseif ($subject['average'] >= 30.5) {
                                    $subjGrade = 'B';
                                    $subject['remarks'] = 'Good';
                                }
                                elseif ($subject['average'] >= 20.5) {
                                    $subjGrade = 'C';
                                    $subject['remarks'] = 'Pass';
                                }
                                elseif ($subject['average'] >= 10.5) {
                                    $subjGrade = 'D';
                                    $subject['remarks'] = 'Poor';
                                }
                                else {
                                    $subjGrade = 'E';
                                    $subject['remarks'] = 'Fail';
                                }
                            } else {
                                if ($subject['average'] >= 80.5) {
                                    $subjGrade = 'A';
                                    $subject['remarks'] = 'Excellent';
                                }
                                elseif ($subject['average'] >= 60.5) {
                                    $subjGrade = 'B';
                                    $subject['remarks'] = 'Good';
                                }
                                elseif ($subject['average'] >= 40.5) {
                                    $subjGrade = 'C';
                                    $subject['remarks'] = 'Pass';
                                }
                                elseif ($subject['average'] >= 20.5) {
                                    $subjGrade = 'D';
                                    $subject['remarks'] = 'Poor';
                                }
                                else {
                                    $subjGrade = 'E';
                                    $subject['remarks'] = 'Fail';
                                }
                            }
                        @endphp
                        <span class="grade-{{ $subjGrade }}">{{ $subjGrade }}</span>
                    </td>
                    <td>{{ $subject['position'] }}</td>
                    <td style="font-style: italic;">{{ $subject['remarks'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- ==================== OVERALL PERFORMANCE SUMMARY ==================== -->
        <div class="section-header">OVERALL PERFORMANCE SUMMARY</div>
        <table class="report-table" cellpadding="6" cellspacing="0">
            <tbody>
                <tr class="summary-row">
                    <td width="25%">TOTAL MARKS</td>
                    <td width="25%"><strong>{{ number_format($totalScoreForStudent ?? 0, 2) }}</strong></td>
                    <td width="25%">CLASS POSITION</td>
                    <td width="25%"><strong>{{ $generalPosition ?? 1 }} out of {{ $totalStudents ?? 1 }}</strong></td>
                </tr>
                <tr class="summary-row">
                    <td>GENERAL AVERAGE</td>
                    <td><strong>{{ number_format($studentGeneralAverage ?? 0, 2) }}%</strong></td>
                    <td>SUBJECTS TAKEN</td>
                    <td><strong>{{ count($finalData) }}</strong></td>
                </tr>
                <tr class="summary-row">
                    <td>OVERALL GRADE</td>
                    <td colspan="3">
                        @php
                            $overallGrade = '';
                            $gradeComment = '';
                            if ($reports->marking_style === 1) {
                                if (($studentGeneralAverage ?? 0) >= 40.5) { $overallGrade = 'A'; $gradeComment = 'EXCELLENT'; }
                                elseif (($studentGeneralAverage ?? 0) >= 30.5) { $overallGrade = 'B'; $gradeComment = 'GOOD'; }
                                elseif (($studentGeneralAverage ?? 0) >= 20.5) { $overallGrade = 'C'; $gradeComment = 'PASS'; }
                                elseif (($studentGeneralAverage ?? 0) >= 10.5) { $overallGrade = 'D'; $gradeComment = 'POOR'; }
                                else { $overallGrade = 'E'; $gradeComment = 'FAIL'; }
                            } else {
                                if (($studentGeneralAverage ?? 0) >= 80.5) { $overallGrade = 'A'; $gradeComment = 'EXCELLENT'; }
                                elseif (($studentGeneralAverage ?? 0) >= 60.5) { $overallGrade = 'B'; $gradeComment = 'GOOD'; }
                                elseif (($studentGeneralAverage ?? 0) >= 40.5) { $overallGrade = 'C'; $gradeComment = 'PASS'; }
                                elseif (($studentGeneralAverage ?? 0) >= 20.5) { $overallGrade = 'D'; $gradeComment = 'POOR'; }
                                else { $overallGrade = 'E'; $gradeComment = 'FAIL'; }
                            }
                        @endphp
                        <strong>Grade {{ $overallGrade }}</strong> -
                        <span class="{{ strtolower($gradeComment) }}">{{ $gradeComment }}</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ==================== HEAD TEACHER'S COMMENT ==================== -->
        @php
            $headComment = '';
            $motivationalMessage = '';
            $nextGoal = '';
            $avgScore = $studentGeneralAverage ?? 0;

            if ($reports->marking_style == 1) {
                if ($avgScore >= 40.5) {
                    $headComment = 'Outstanding Achievement! Excellent mastery of all subjects.';
                    $motivationalMessage = '"Excellence is not a skill, it is an attitude." Keep shining!';
                    $nextGoal = 'Maintain this excellent performance next term.';
                } elseif ($avgScore >= 30.5) {
                    $headComment = 'Very Good Performance! Strong understanding across the curriculum.';
                    $motivationalMessage = '"Success is the sum of small efforts." Push harder!';
                    $nextGoal = 'Aim for Grade A next term - just a little more effort!';
                } elseif ($avgScore >= 20.5) {
                    $headComment = 'Good Effort! You are on the right track.';
                    $motivationalMessage = '"Education is the most powerful weapon." Keep building!';
                    $nextGoal = 'Focus on daily revision and complete all assignments.';
                } elseif ($avgScore >= 10.5) {
                    $headComment = 'Room for Improvement. You can rise higher!';
                    $motivationalMessage = '"It always seems impossible until it\'s done." Believe in yourself!';
                    $nextGoal = 'Create a study timetable. Small daily progress adds up!';
                } else {
                    $headComment = 'Fresh Start Ahead. Every setback is a comeback.';
                    $motivationalMessage = '"The only person you are destined to become is the person you decide to be."';
                    $nextGoal = 'Meet with subject teachers weekly. We will support you!';
                }
            } else {
                if ($avgScore >= 80.5) {
                    $headComment = 'Exceptional Achievement! You have set a standard of excellence.';
                    $motivationalMessage = '"Excellence is not an act, but a habit." Continue being a role model!';
                    $nextGoal = 'Maintain top position and help classmates.';
                } elseif ($avgScore >= 60.5) {
                    $headComment = 'Commendable Performance! Your hard work is paying off.';
                    $motivationalMessage = '"Shoot for the moon. Even if you miss, you\'ll land among the stars."';
                    $nextGoal = 'Push your average above 70% - you have the ability!';
                } elseif ($avgScore >= 40.5) {
                    $headComment = 'Making Progress! Keep the momentum going.';
                    $motivationalMessage = '"Believe you can and you\'re halfway there." You have what it takes!';
                    $nextGoal = 'Improve by 10% next term through consistent effort.';
                } elseif ($avgScore >= 20.5) {
                    $headComment = 'Time to Unlock Your Potential.';
                    $motivationalMessage = '"The secret of getting ahead is getting started." Start today!';
                    $nextGoal = 'Attend all remedial classes and ask questions.';
                } else {
                    $headComment = 'Your Comeback Story Starts Now.';
                    $motivationalMessage = '"Failure is not the opposite of success, it\'s part of success."';
                    $nextGoal = 'One-on-one mentoring sessions arranged. Let\'s turn things around!';
                }
            }
        @endphp

        <table class="report-table" cellpadding="7" cellspacing="0">
            <tbody>
                <tr>
                    <td width="25%" class="bold" style="background-color: #f5f5f5; text-align: center;">HEAD TEACHER'S REMARKS</td>
                    <td width="75%">
                        <i>{{ $headComment }}</i>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ==================== QR CODE SECTION ==================== -->
        <div class="qr-section">
            <img src="data:image/png;base64,{{ $qrPng ?? '' }}" class="qr-code" alt="Verification QR Code">
            <div class="qr-text">
                <strong>Scan to Verify Authenticity</strong><br>
            </div>
        </div>

        <!-- ==================== FOOTER - LAST PART AFTER QR CODE ==================== -->
        <div class="footer" style="position: fixed; bottom: 2;">
            &copy; {{ date('Y') }} - {{ strtoupper($schoolInfo->school_name ?? 'SCHOOL') }} |
            Printed: {{ now()->format('d-M-Y H:i:s') }} |
            Document ID: {{ strtoupper(substr(md5(uniqid()), 0, 8)) }}
        </div>
    </div>
</body>

</html>
