<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Academic Report - {{ $studentId->first_name }} {{ $studentId->last_name }}</title>
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
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .school-address {
            font-size: 9px;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .school-contacts {
            font-size: 10px;
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
            font-size: 12px;
            margin-top: 2px;
            text-transform: uppercase;
        }

        /* ============ SECTION HEADER ============ */
        .section-header {
            background: #000000;
            color: white;
            padding: 4px;
            text-align: center;
            font-size: 12px;
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
            width: 35%;
            background-color: #f5f5f5;
            font-size: 12px;
        }

        .info-value {
            width: 65%;
            font-size: 12px;
        }

        /* ============ RESULTS TABLE ============ */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 11px;
        }

        .results-table th {
            border: 1px solid #000000;
            padding: 5px 3px;
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }

        .results-table td {
            border: 1px solid #000000;
            padding: 5px 3px;
            text-align: center;
            font-size: 12px;
        }

        .subject-name-cell {
            text-align: left;
        }

        /* ============ GRADE BADGES - Optimized for Color & B&W Printing ============ */
        .grade-A {
            background-color: #e8f5e9;
            border: 1px solid #2e7d32;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 12px;
        }
        .grade-B {
            background-color: #e3f2fd;
            border: 1px solid #1565c0;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 12px;
        }
        .grade-C {
            background-color: #fff3e0;
            border: 1px solid #e65100;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 12px;
        }
        .grade-D {
            background-color: #ffebee;
            border: 1px solid #c62828;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 12px;
        }
        .grade-E, .grade-F {
            background-color: #fce4ec;
            border: 1px solid #b71c1c;
            padding: 2px 7px;
            border-radius: 3px;
            display: inline-block;
            font-size: 12px;
        }

        /* General Remarks Badges */
        .remark-excellent {
            background-color: #2e7d32;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }
        .remark-good {
            background-color: #1565c0;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }
        .remark-pass {
            background-color: #e65100;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }
        .remark-poor {
            background-color: #c62828;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }
        .remark-fail {
            background-color: #b71c1c;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }

        /* For B&W Printing */
        @media print {
            .grade-A, .grade-B, .grade-C, .grade-D, .grade-E, .grade-F {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .remark-excellent, .remark-good, .remark-pass, .remark-poor, .remark-fail {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .results-table th {
                background-color: #e0e0e0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* ============ SUMMARY SECTION ============ */
        .summary-section {
            width: 100%;
            margin: 8px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .summary-section td {
            border: 1px solid #000000;
            padding: 6px 8px;
        }

        .summary-label {
            font-weight: bold;
            background-color: #f5f5f5;
            font-size: 12px;
        }

        .summary-value {
            font-size: 12px;
        }

        /* ============ DIVISION SECTION ============ */
        .division-section {
            width: 100%;
            margin: 8px 0;
            border: 2px solid #000000;
            border-collapse: collapse;
            background-color: #f9f9f9;
        }

        .division-section td {
            padding: 8px;
            text-align: center;
        }

        .division-score {
            font-size: 16px;
            font-weight: bold;
        }

        /* ============ HEAD TEACHER COMMENT ============ */
        .comment-section {
            width: 100%;
            margin: 8px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .comment-section td {
            padding: 8px 10px;
            vertical-align: top;
        }

        .comment-label {
            font-weight: bold;
            width: 22%;
            background-color: #f5f5f5;
            text-align: center;
            font-size: 12px;
        }

        .comment-content {
            width: 78%;
            line-height: 1.4;
            font-size: 12px;
            font-style: italic;
        }

        /* ============ QR CODE SECTION ============ */
        .qr-section {
            text-align: center;
            margin: 10px 0 8px 0;
            page-break-inside: avoid;
        }

        .qr-code {
            width: 110px;
            height: 110px;
            margin: 0 auto;
        }

        .qr-text {
            font-size: 9px;
            margin-top: 0px;
            font-weight: bold;
        }

        /* ============ FOOTER ============ */
        .footer {
            position: fixed;
            bottom: 3px;
            width: 100%;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #000000;
            text-align: center;
            font-size: 9px;
        }

        /* Print Optimization */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
            }
        }

        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-uppercase { text-transform: uppercase; }
        .text-capitalize { text-transform: capitalize; }
        .bold { font-weight: bold; }
    </style>
</head>

<body>
    <div class="report-container">
        <!-- ==================== HEADER ==================== -->
        <table class="school-header" cellpadding="4" cellspacing="0">
            <tr>
                <td class="logo-cell">
                    @php
                        $logoPath = storage_path('app/public/logo/' . ($results->first()->logo ?? 'default.png'));
                        $logoExists = file_exists($logoPath) && !empty($results->first()->logo);
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
                        {{ strtoupper($results->first()->school_name ?? 'SCHOOL NAME') }}
                    </div>
                    <div class="school-address">
                        P.O.BOX {{ $results->first()->postal_address ?? '______' }} -
                        {{ $results->first()->postal_name ?? '______' }},
                        {{ $results->first()->country ?? 'TANZANIA' }}
                    </div>
                    <div class="school-contacts">
                        Email: {{ strtolower($results->first()->school_email ?? 'info@school.ac.tz') }} |
                        Tel: {{ $results->first()->school_phone ?? '_________' }}
                    </div>
                </td>
                <td class="photo-cell">
                    @php
                        $studentImage = storage_path('app/public/students/' . ($studentId->image ?? ''));
                        $hasImage = file_exists($studentImage) && !empty($studentId->image);
                    @endphp
                    @if($hasImage)
                        <img src="{{ $studentImage }}" class="student-photo" alt="Student Photo">
                    @else
                        <img src="{{ storage_path('app/public/students/student.jpg') }}" class="student-photo" alt="Student Photo">
                    @endif
                </td>
            </tr>
        </table>

        <!-- ==================== REPORT TITLE ==================== -->
        <div class="report-title">
            <h3>ACADEMIC PROGRESS REPORT</h3>
            <p>
                {{ strtoupper($results->first()->exam_type ?? 'TERMINAL') }} ASSESSMENT -
                TERM {{ strtoupper($results->first()->Exam_term ?? 'I') }},
                {{ \Carbon\Carbon::parse($date ?? now())->format('Y') }}
            </p>
        </div>

        <!-- ==================== STUDENT INFORMATION ==================== -->
        <div class="section-header">STUDENT'S INFORMATION</div>
        <table class="student-info" cellpadding="6" cellspacing="0">
            <tr>
                <td class="info-label">ADMISSION NUMBER</td>
                <td class="info-value">{{ strtoupper($results->first()->admission_number ?? 'N/A') }}</td>
                <td class="info-label">CLASS</td>
                <td class="info-value">{{ strtoupper($results->first()->class_name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="info-label">STUDENT NAME</td>
                <td class="info-value">
                    {{ strtoupper($studentId->first_name ?? '') }}
                    {{ strtoupper($studentId->middle_name ?? '') }}
                    {{ strtoupper($studentId->last_name ?? '') }}
                </td>
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

        <!-- ==================== RESULTS TABLE ==================== -->
        <div class="section-header">SUBJECT PERFORMANCE</div>
        <table class="results-table" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th width="28%">SUBJECT NAME</th>
                    <th width="10%">CODE</th>
                    <th width="15%">TEACHER</th>
                    <th width="10%">SCORE</th>
                    <th width="8%">GRADE</th>
                    <th width="8%">RANK</th>
                    <th width="21%">REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @php $subjectCount = 0; @endphp
                @foreach ($results as $result)
                @php $subjectCount++; @endphp
                <tr>
                    <td class="subject-name-cell text-capitalize">{{ ucwords(strtolower($result->course_name ?? 'N/A')) }}</td>
                    <td class="text-uppercase">{{ strtoupper($result->course_code ?? 'N/A') }}</td>
                    <td class="text-capitalize">
                        {{ ucwords(strtolower($result->teacher_first_name ?? '')) }}
                        {{ strtoupper(substr($result->teacher_last_name ?? '', 0, 1)) }}.
                    </td>
                    <td class="bold">{{ $result->score ?? 'X' }}</td>
                    <td>
                        @php
                            $grade = $result->grade ?? 'X';
                            if($grade == 'A') $gradeClass = 'grade-A';
                            elseif($grade == 'B') $gradeClass = 'grade-B';
                            elseif($grade == 'C') $gradeClass = 'grade-C';
                            elseif($grade == 'D') $gradeClass = 'grade-D';
                            elseif($grade == 'E') $gradeClass = 'grade-E';
                            else $gradeClass = '';
                        @endphp
                        @if($grade != 'X')
                            <span class="{{ $gradeClass }}">{{ $grade }}</span>
                        @else
                            <span>{{ $grade }}</span>
                        @endif
                    </td>
                    <td>{{ $result->score ? ($result->courseRank ?? '-') : 'X' }}</td>
                    <td class="italic">{{ $result->score ? ($result->remarks ?? '-') : 'ABSENT' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ==================== OVERALL PERFORMANCE SUMMARY ==================== -->
        <div class="section-header">OVERALL PERFORMANCE SUMMARY</div>
        <table class="summary-section" cellpadding="6" cellspacing="0">
            <tbody>
                <tr>
                    <td class="summary-label" width="25%">TOTAL MARKS</td>
                    <td class="summary-value" width="25%"><strong>{{ $totalScore ?? 0 }}</strong></td>
                    <td class="summary-label" width="25%">CLASS POSITION</td>
                    <td class="summary-value" width="25%"><strong>{{ $studentRank ?? 1 }} out of {{ $rankings->count() ?? 1 }}</strong></td>
                </tr>
                <tr>
                    <td class="summary-label">GENERAL AVERAGE</td>
                    <td class="summary-value"><strong>{{ number_format($averageScore ?? 0, 2) }}%</strong></td>
                    <td class="summary-label">SUBJECTS TAKEN</td>
                    <td class="summary-value"><strong>{{ $subjectCount }}</strong></td>
                </tr>
                <tr>
                    <td class="summary-label">OVERALL GRADE</td>
                    <td colspan="3">
                        @php
                            $overallGrade = '';
                            $gradeComment = '';
                            $remarkClass = '';

                            if ($marking_style == 1) {
                                if (($averageScore ?? 0) >= 40.5) {
                                    $overallGrade = 'A';
                                    $gradeComment = 'EXCELLENT';
                                    $remarkClass = 'remark-excellent';
                                } elseif (($averageScore ?? 0) >= 30.5) {
                                    $overallGrade = 'B';
                                    $gradeComment = 'GOOD';
                                    $remarkClass = 'remark-good';
                                } elseif (($averageScore ?? 0) >= 20.5) {
                                    $overallGrade = 'C';
                                    $gradeComment = 'PASS';
                                    $remarkClass = 'remark-pass';
                                } elseif (($averageScore ?? 0) >= 10.5) {
                                    $overallGrade = 'D';
                                    $gradeComment = 'POOR';
                                    $remarkClass = 'remark-poor';
                                } else {
                                    $overallGrade = 'E';
                                    $gradeComment = 'FAIL';
                                    $remarkClass = 'remark-fail';
                                }
                            } elseif ($marking_style == 2) {
                                if (($averageScore ?? 0) >= 80.5) {
                                    $overallGrade = 'A';
                                    $gradeComment = 'EXCELLENT';
                                    $remarkClass = 'remark-excellent';
                                } elseif (($averageScore ?? 0) >= 60.5) {
                                    $overallGrade = 'B';
                                    $gradeComment = 'GOOD';
                                    $remarkClass = 'remark-good';
                                } elseif (($averageScore ?? 0) >= 40.5) {
                                    $overallGrade = 'C';
                                    $gradeComment = 'PASS';
                                    $remarkClass = 'remark-pass';
                                } elseif (($averageScore ?? 0) >= 20.5) {
                                    $overallGrade = 'D';
                                    $gradeComment = 'POOR';
                                    $remarkClass = 'remark-poor';
                                } else {
                                    $overallGrade = 'E';
                                    $gradeComment = 'FAIL';
                                    $remarkClass = 'remark-fail';
                                }
                            } else {
                                // Marking style 3 with division
                                if ($division == 'I') {
                                    $overallGrade = 'I';
                                    $gradeComment = 'EXCELLENT';
                                    $remarkClass = 'remark-excellent';
                                } elseif ($division == 'II') {
                                    $overallGrade = 'II';
                                    $gradeComment = 'GOOD';
                                    $remarkClass = 'remark-good';
                                } elseif ($division == 'III') {
                                    $overallGrade = 'III';
                                    $gradeComment = 'PASS';
                                    $remarkClass = 'remark-pass';
                                } elseif ($division == 'IV') {
                                    $overallGrade = 'IV';
                                    $gradeComment = 'POOR';
                                    $remarkClass = 'remark-poor';
                                } else {
                                    $overallGrade = '0';
                                    $gradeComment = 'FAIL';
                                    $remarkClass = 'remark-fail';
                                }
                            }
                        @endphp
                        <strong>Grade {{ $overallGrade }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="summary-label">GENERAL REMARKS</td>
                    <td colspan="3">
                        <span class="{{ $remarkClass }}">{{ $gradeComment }}</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ==================== DIVISION SECTION (For Marking Style 3) ==================== -->
        @if ($marking_style == 3)
        <table class="division-section" cellpadding="8" cellspacing="0">
            <tr>
                <td width="50%">
                    <strong>AGGREGATE POINTS</strong><br>
                    <span class="division-score">{{ $aggregatePoints ?? 0 }}</span>
                </td>
                <td width="50%">
                    <strong>DIVISION</strong><br>
                    <span class="division-score" style="
                        background-color:
                            @if($division == 'I') #2e7d32
                            @elseif($division == 'II') #1565c0
                            @elseif($division == 'III') #e65100
                            @elseif($division == 'IV') #c62828
                            @else #b71c1c
                            @endif;
                        color: white; padding: 4px 20px; border-radius: 4px; display: inline-block;
                    ">
                        {{ $division === '0' ? '0' : $division }}
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 8px; padding: 4px;">
                    <strong>Division Guide:</strong> I (Excellent) | II (Good) | III (Pass) | IV (Poor) | 0 (Fail)
                </td>
            </tr>
        </table>
        @endif

        <!-- ==================== HEAD TEACHER'S COMMENT ==================== -->
        @php
            $headComment = '';
            $avgScore = $averageScore ?? 0;

            if ($marking_style == 3) {
                if ($division == 'I') {
                    $headComment = 'Outstanding Achievement! You have demonstrated exceptional mastery of all subjects. Keep shining!';
                } elseif ($division == 'II') {
                    $headComment = 'Very Good Performance! You have shown strong understanding across the curriculum. Push harder!';
                } elseif ($division == 'III') {
                    $headComment = 'Good Effort! You are on the right track. Keep building on this foundation!';
                } elseif ($division == 'IV') {
                    $headComment = 'Room for Improvement. You can rise higher! Believe in yourself and work harder!';
                } else {
                    $headComment = 'Fresh Start Ahead. Every setback is a setup for a comeback. We will support you!';
                }
            } else {
                if ($marking_style == 1) {
                    if ($avgScore >= 40.5) {
                        $headComment = 'Outstanding Achievement! Excellent mastery of all subjects. Keep shining!';
                    } elseif ($avgScore >= 30.5) {
                        $headComment = 'Very Good Performance! Strong understanding across the curriculum. Push harder!';
                    } elseif ($avgScore >= 20.5) {
                        $headComment = 'Good Effort! You are on the right track. Keep building!';
                    } elseif ($avgScore >= 10.5) {
                        $headComment = 'Room for Improvement. You can rise higher! Believe in yourself!';
                    } else {
                        $headComment = 'Fresh Start Ahead. Every setback is a comeback. We will support you!';
                    }
                } else {
                    if ($avgScore >= 80.5) {
                        $headComment = 'Exceptional Achievement! You have set a standard of excellence. Continue being a role model!';
                    } elseif ($avgScore >= 60.5) {
                        $headComment = 'Commendable Performance! Your hard work is paying off. Keep pushing forward!';
                    } elseif ($avgScore >= 40.5) {
                        $headComment = 'Making Progress! Keep the momentum going. You have what it takes!';
                    } elseif ($avgScore >= 20.5) {
                        $headComment = 'Time to Unlock Your Potential. Start today and attend all remedial classes!';
                    } else {
                        $headComment = 'Your Comeback Story Starts Now. One-on-one mentoring sessions arranged!';
                    }
                }
            }
        @endphp

        <table class="comment-section" cellpadding="7" cellspacing="0">
            <tbody>
                <tr>
                    <td class="comment-label">HEAD TEACHER'S REMARKS</td>
                    <td class="comment-content">
                        {{ $headComment }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ==================== QR CODE SECTION ==================== -->
        <div class="qr-section">
            <img src="data:image/png;base64,{{ $qrPng ?? '' }}" class="qr-code" alt="Verification QR Code">
            <div class="qr-text">
                <strong>Scan to Verify Authenticity</strong>
            </div>
        </div>

        <!-- ==================== FOOTER ==================== -->
        <div class="footer">
            &copy; {{ date('Y') }} - {{ strtoupper($results->first()->school_name ?? 'SCHOOL') }} |
            Printed: {{ now()->format('d-M-Y H:i:s') }} |
            Document ID: {{ strtoupper(substr(md5(uniqid()), 0, 8)) }}
        </div>
    </div>
</body>

</html>
