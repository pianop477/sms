<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Academic Report - {{ $studentId->first_name }} {{ $studentId->last_name }}</title>
    <style>
        /* RESET & BASE - Relaxed Readable Fonts */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', 'Arial', serif;
            font-size: 12px;
            line-height: 1.45;
            background: white;
            color: #000000;
            padding: 0.35cm;
        }

        /* Page Setup for Printing - Balanced Margins */
        @page {
            size: A4;
            margin: auto;
        }

        /* Container */
        .report-container {
            max-width: 100%;
            margin: auto;
        }

        /* ============ HEADER SECTION - Relaxed ============ */
        .school-header {
            width: 100%;
            border-bottom: 2px solid #000000;
            margin-bottom: 12px;
            padding-bottom: 8px;
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
            object-fit: contain;
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
            margin: 3px 0;
        }

        .school-address {
            font-size: 11px;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .school-contacts {
            font-size: 11px;
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

        /* ============ REPORT TITLE - Relaxed ============ */
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
            margin-top: 3px;
            text-transform: uppercase;
        }

        /* ============ SECTION HEADER - Relaxed ============ */
        .section-header {
            background: #000000;
            color: white;
            padding: 5px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 10px 0 6px 0;
        }

        /* ============ STUDENT INFO SECTION - Relaxed ============ */
        .student-info {
            width: 100%;
            margin: 8px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .student-info td {
            border: 1px solid #000000;
            padding: 7px 10px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            width: 30%;
            background-color: #f5f5f5;
            font-size: 12px;
        }

        .info-value {
            width: 70%;
            font-size: 12px;
        }

        /* ============ RESULTS TABLE - Relaxed ============ */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 12px;
        }

        .results-table th {
            border: 1px solid #000000;
            padding: 6px 4px;
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }

        .results-table td {
            border: 1px solid #000000;
            padding: 5px 4px;
            text-align: center;
            font-size: 12px;
        }

        .subject-name-cell {
            text-align: left;
        }

        /* ============ GRADE STYLING ============ */
        .grade-text {
            font-weight: bold;
            font-size: 12px;
        }

        .remark-text {
            font-weight: bold;
            font-size: 12px;
            font-style:italic;
        }

        /* For B&W Printing */
        @media print {
            .results-table th {
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

        /* ============ SUMMARY SECTION - Relaxed ============ */
        .summary-section {
            width: 100%;
            margin: 8px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .summary-section td {
            border: 1px solid #000000;
            padding: 7px 10px;
        }

        .summary-label {
            font-weight: bold;
            background-color: #f5f5f5;
            font-size: 12px;
        }

        .summary-value {
            font-size: 12px;
            font-weight: bold;
        }

        /* ============ DIVISION SECTION - Relaxed ============ */
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

        /* ============ HEAD TEACHER COMMENT - Relaxed ============ */
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
            font-size: 12px;
        }

        .comment-content {
            width: 78%;
            line-height: 1.45;
            font-size: 12px;
            font-style: italic;
        }

        /* ============ QR CODE SECTION - Relaxed ============ */
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
            margin-top: 0.5px;
            font-weight: bold;
            font-style:italic;
        }

        /* ============ FOOTER - Relaxed ============ */
        .footer {
            position: fixed;
            bottom: 8px;
            width: 100%;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #000000;
            text-align: center;
            font-size: 10px;
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
                        $logoFileName = $results->first()->logo ?? '';
                        $logoPath = '';
                        $logoExists = false;

                        if(!empty($logoFileName)) {
                            $possiblePaths = [
                                public_path('storage/logo/' . $logoFileName),
                                storage_path('app/public/logo/' . $logoFileName),
                                public_path('logo/' . $logoFileName),
                                storage_path('app/logo/' . $logoFileName)
                            ];

                            foreach($possiblePaths as $path) {
                                if(file_exists($path) && is_file($path)) {
                                    $logoPath = $path;
                                    $logoExists = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if($logoExists && !empty($logoPath))
                        <img src="{{ $logoPath }}" class="logo-img" alt="School Logo">
                    @else
                        <div>
                            LOGO
                        </div>
                    @endif
                </td>
                <td class="school-info-cell">
                    <div class="school-name">THE UNITED REPUBLIC OF TANZANIA</div>
                    <div class="school-name">PRESIDENT'S OFFICE - TAMISEMI</div>
                    <div class="school-name">
                        {{ strtoupper($results->first()->school_name ?? 'SCHOOL NAME') }}
                    </div>
                    <div class="school-address">
                        {{ $results->first()->postal_address ?? '______' }} -
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
                        $studentImageName = $studentId->image ?? 'student.jpg';
                        $studentImagePath = '';
                        $hasImage = false;

                        if(!empty($studentImageName)) {
                            $possiblePaths = [
                                public_path('storage/students/' . $studentImageName),
                                storage_path('app/public/students/' . $studentImageName),
                                public_path('students/' . $studentImageName),
                                storage_path('app/students/' . $studentImageName)
                            ];

                            foreach($possiblePaths as $path) {
                                if(file_exists($path) && is_file($path)) {
                                    $studentImagePath = $path;
                                    $hasImage = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if($hasImage && !empty($studentImagePath))
                        <img src="{{ $studentImagePath }}" class="student-photo" alt="Student Photo">
                    @else
                        <div>
                            PHOTO
                        </div>
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
        <table class="student-info" cellpadding="7" cellspacing="0">
            <tr>
                <td class="info-label">ADMISSION No.</td>
                <td class="info-value">{{ strtoupper($results->first()->admission_number ?? 'N/A') }}</td>
                <td class="info-label">CLASS</td>
                <td class="info-value">{{ strtoupper($results->first()->class_name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="info-label">FULL NAME</td>
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
        <table class="results-table" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th width="30%">SUBJECT NAME</th>
                    <th width="10%">CODE</th>
                    <th width="15%">TEACHER</th>
                    <th width="10%">SCORE</th>
                    <th width="8%">GRADE</th>
                    <th width="8%">RANK</th>
                    <th width="19%">REMARKS</th>
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
                    <td class="grade-text">{{ $result->grade ?? 'X' }}</td>
                    <td>{{ $result->score ? ($result->courseRank ?? '-') : 'X' }}</td>
                    <td>{{ $result->score ? ($result->remarks ?? '-') : 'ABSENT' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ==================== OVERALL PERFORMANCE SUMMARY ==================== -->
        <div class="section-header">OVERALL PERFORMANCE SUMMARY</div>
        <table class="summary-section" cellpadding="7" cellspacing="0">
            <tbody>
                <tr>
                    <td class="summary-label" width="25%">TOTAL MARKS</td>
                    <td class="summary-value" width="25%"><strong>{{ number_format($totalScore ?? 0, 2) }}</strong></td>
                    <td class="summary-label" width="25%">CLASS POSITION</td>
                    <td class="summary-value" width="25%"><strong>{{ $studentRank ?? 1 }} / {{ $rankings->count() ?? 1 }}</strong></td>
                </tr>
                <tr>
                    <td class="summary-label">GENERAL AVERAGE</td>
                    <td class="summary-value"><strong>{{ number_format($averageScore ?? 0, 2) }}</strong></td>
                    <td class="summary-label">SUBJECTS TAKEN</td>
                    <td class="summary-value"><strong>{{ $subjectCount }}</strong></td>
                </tr>
                <tr>
                    <td class="summary-label">OVERALL GRADE</td>
                    <td colspan="3">
                        <strong class="remark-text">
                            @php
                                if ($marking_style == 3 && isset($division)) {
                                    echo 'Division ' . ($division === '0' ? '0' : $division);
                                } else {
                                    $avg = $averageScore ?? 0;
                                    if ($marking_style == 1) {
                                        if ($avg >= 40.5) echo 'A (Excellent)';
                                        elseif ($avg >= 30.5) echo 'B (Good)';
                                        elseif ($avg >= 20.5) echo 'C (Pass)';
                                        elseif ($avg >= 10.5) echo 'D (Poor)';
                                        else echo 'E (Fail)';
                                    } else {
                                        if ($avg >= 80.5) echo 'A (Excellent)';
                                        elseif ($avg >= 60.5) echo 'B (Good)';
                                        elseif ($avg >= 40.5) echo 'C (Pass)';
                                        elseif ($avg >= 20.5) echo 'D (Poor)';
                                        else echo 'E (Fail)';
                                    }
                                }
                            @endphp
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td class="summary-label">GENERAL REMARKS</td>
                    <td colspan="3">
                        <strong class="remark-text">
                            @php
                                if ($marking_style == 3 && isset($division)) {
                                    if ($division == 'I') echo 'EXCELLENT';
                                    elseif ($division == 'II') echo 'GOOD';
                                    elseif ($division == 'III') echo 'PASS';
                                    elseif ($division == 'IV') echo 'POOR';
                                    else echo 'FAIL';
                                } else {
                                    $avg = $averageScore ?? 0;
                                    if ($marking_style == 1) {
                                        if ($avg >= 40.5) echo 'EXCELLENT';
                                        elseif ($avg >= 30.5) echo 'GOOD';
                                        elseif ($avg >= 20.5) echo 'PASS';
                                        elseif ($avg >= 10.5) echo 'POOR';
                                        else echo 'FAIL';
                                    } else {
                                        if ($avg >= 80.5) echo 'EXCELLENT';
                                        elseif ($avg >= 60.5) echo 'GOOD';
                                        elseif ($avg >= 40.5) echo 'PASS';
                                        elseif ($avg >= 20.5) echo 'POOR';
                                        else echo 'FAIL';
                                    }
                                }
                            @endphp
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ==================== DIVISION SECTION (For Marking Style 3) ==================== -->
        @if ($marking_style == 3 && isset($division))
        <table class="division-section" cellpadding="8" cellspacing="0">
            <tr>
                <td width="50%">
                    <strong>AGGREGATE POINTS</strong><br>
                    <span class="division-score">{{ $aggregatePoints ?? 0 }}</span>
                </td>
                <td width="50%">
                    <strong>DIVISION</strong><br>
                    <span>
                        {{ $division === '0' ? '0' : $division }}
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Division Guide:</strong> I (Excellent) | II (Good) | III (Pass) | IV (Poor) | 0 (Fail)
                </td>
            </tr>
        </table>
        @endif

        <!-- ==================== HEAD TEACHER'S COMMENT ==================== -->
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

        <table class="comment-section" cellpadding="8" cellspacing="0">
            <tbody>
                <tr>
                    <td class="comment-label">HEAD TEACHER'S REMARKS</td>
                    <td class="comment-content">{{ $headComment }}</td>
                </tr>
            </tbody>
        </table>

        <!-- ==================== QR CODE SECTION ==================== -->
        <div class="qr-section">
            @if(!empty($qrPng))
                <img src="data:image/png;base64,{{ $qrPng }}" class="qr-code" alt="Verification QR Code">
            @else
                <div>
                    QR
                </div>
            @endif
            <div class="qr-text">
                <strong>Scan to Verify Authenticity</strong>
            </div>
        </div>

        <!-- ==================== FOOTER ==================== -->
        <div class="footer">
            &copy; {{ date('Y') }} - {{ strtoupper($results->first()->school_name ?? 'SCHOOL') }} |
            Printed: {{ now()->format('d-M-Y H:i') }} |
            Powered by ShuleApp System
        </div>
    </div>
</body>

</html>
