<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Academic Report - {{ $studentId->first_name }} {{ $studentId->last_name }}</title>
    <style>
        /* RESET & BASE */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', 'Arial', serif;
            font-size: 12px;
            line-height: 1.4;
            background: white;
            color: #000000;
            padding: 0.5cm;
        }

        /* Page Setup for Printing */
        @page {
            size: A4;
            margin: 1cm 1cm 1.2cm 1cm;
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
            margin-bottom: 12px;
            padding-bottom: 8px;
        }

        .school-header table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 15%;
            text-align: center;
            vertical-align: middle;
        }

        .logo-img {
            max-width: 80px;
            max-height: 80px;
        }

        .school-info-cell {
            width: 70%;
            text-align: center;
            vertical-align: middle;
        }

        .school-name {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .school-address {
            font-size: 10px;
            margin: 2px 0;
        }

        .school-contacts {
            font-size: 10px;
            margin: 2px 0;
        }

        .photo-cell {
            width: 15%;
            text-align: center;
            vertical-align: middle;
        }

        .student-photo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #cccccc;
            padding: 2px;
        }

        /* ============ REPORT TITLE ============ */
        .report-title {
            text-align: center;
            margin: 12px 0;
        }

        .report-title h3 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 5px 0;
        }

        .report-title p {
            font-size: 11px;
            margin-top: 3px;
        }

        /* ============ STUDENT INFO SECTION ============ */
        .student-info {
            width: 100%;
            margin: 12px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .student-info td {
            border: 1px solid #000000;
            padding: 8px 10px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            width: 40%;
            background-color: #f5f5f5;
        }

        .info-value {
            width: 60%;
        }

        /* ============ RESULTS TABLE ============ */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 11px;
        }

        .results-table th {
            border: 1px solid #000000;
            padding: 8px 5px;
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }

        .results-table td {
            border: 1px solid #000000;
            padding: 6px 5px;
            text-align: center;
        }

        .subject-name-cell {
            text-align: left;
            font-weight: normal;
        }

        /* ============ SUMMARY SECTION ============ */
        .summary-section {
            width: 100%;
            margin: 12px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .summary-section td {
            border: 1px solid #000000;
            padding: 8px 10px;
        }

        .summary-label {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        /* ============ DIVISION SECTION ============ */
        .division-section {
            width: 100%;
            margin: 12px 0;
            border: 2px solid #000000;
            border-collapse: collapse;
            background-color: #f9f9f9;
        }

        .division-section td {
            padding: 10px;
            text-align: center;
        }

        .division-score {
            font-size: 16px;
            font-weight: bold;
        }

        /* ============ HEAD TEACHER COMMENT ============ */
        .comment-section {
            width: 100%;
            margin: 12px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .comment-section td {
            padding: 10px 12px;
            vertical-align: top;
        }

        .comment-label {
            font-weight: bold;
            width: 18%;
            background-color: #f5f5f5;
            text-align: center;
        }

        .comment-content {
            width: 82%;
            line-height: 1.5;
        }

        /* ============ QR CODE SECTION ============ */
        .qr-section {
            width: 100%;
            margin: 20px 0;
            text-align: center;
            page-break-inside: avoid;
        }

        .qr-code {
            width: 120px;
            height: 120px;
            margin: 0 auto;
        }

        .qr-text {
            font-size: 9px;
            margin-top: 2px;
        }

        /* ============ FOOTER ============ */
        .footer {
            width: 100%;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #cccccc;
            font-size: 9px;
            text-align: center;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
        }

        /* ============ GRADE COLORS ============ */
        .grade-A { background-color: #e8f5e9; font-weight: bold; padding: 2px 6px; border-radius: 2px; }
        .grade-B { background-color: #e3f2fd; padding: 2px 6px; border-radius: 2px; }
        .grade-C { background-color: #fff3e0; padding: 2px 6px; border-radius: 2px; }
        .grade-D { background-color: #ffebee; padding: 2px 6px; border-radius: 2px; }
        .grade-E { background-color: #fce4ec; padding: 2px 6px; border-radius: 2px; }
        .grade-F { background-color: #ffcdd2; padding: 2px 6px; border-radius: 2px; }

        .excellent-text { font-weight: bold; color: #2e7d32; }
        .good-text { font-weight: bold; color: #1565c0; }
        .pass-text { font-weight: bold; color: #e65100; }
        .poor-text { font-weight: bold; color: #c62828; }
        .fail-text { font-weight: bold; color: #b71c1c; }

        @media print {
            body { padding: 0; margin: 0; }
            .results-table th {
                background-color: #e0e0e0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .bold { font-weight: bold; }
    </style>
</head>

<body>
    <div class="report-container">
        <table class="school-header" cellpadding="5" cellspacing="0">
            <tr>
                <td class="logo-cell">
                    @php
                        $logoPath = storage_path('app/public/logo/' . ($results->first()->logo ?? 'default.png'));
                        $logoExists = file_exists($logoPath) && !empty($results->first()->logo);
                    @endphp
                    @if($logoExists)
                        <img src="{{ $logoPath }}" class="logo-img" alt="School Logo">
                    @else
                        <div style="width: 80px; height: 80px; border: 1px solid #ccc; margin: 0 auto;"></div>
                    @endif
                </td>
                <td class="school-info-cell">
                    <div class="school-name">THE UNITED REPUBLIC OF TANZANIA</div>
                    <div class="school-name">PRESIDENT'S OFFICE - TAMISEMI</div>
                    <div class="school-name" style="font-size: 13px; margin-top: 5px;">
                        {{ strtoupper($results->first()->school_name ?? 'SCHOOL NAME') }}
                    </div>
                    <div class="school-address">
                        P.O.BOX {{ $results->first()->postal_address ?? '______' }} -
                        {{ $results->first()->postal_name ?? '______' }},
                        {{ $results->first()->country ?? 'TANZANIA' }}
                    </div>
                    <div class="school-contacts">
                        Email: {{ strtolower($results->first()->school_email ?? 'info@school.ac.tz') }} |
                        Phone: {{ $results->first()->school_phone ?? '_________' }}
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
                        <div style="width: 80px; height: 80px; border: 1px solid #ccc; text-align: center; line-height: 80px;">
                            <img src="{{storage_path('app/public/students/student.jpg')}}" class="student-photo" alt="Student Photo">
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="report-title">
            <h3>ACADEMIC PROGRESS REPORT</h3>
            <p>
                {{ strtoupper($results->first()->exam_type ?? 'TERMINAL') }} ASSESSMENT -
                TERM {{ strtoupper($results->first()->Exam_term ?? 'I') }},
                {{ \Carbon\Carbon::parse($date ?? now())->format('Y') }}
            </p>
        </div>

        <table class="student-info" cellpadding="8" cellspacing="0">
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

        <table class="results-table" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th width="25%">SUBJECT NAME</th>
                    <th width="10%">CODE</th>
                    <th width="15%">TEACHER</th>
                    <th width="10%">SCORE</th>
                    <th width="8%">GRADE</th>
                    <th width="8%">RANK</th>
                    <th width="24%">REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @php $subjectCount = 0; @endphp
                @foreach ($results as $result)
                @php $subjectCount++; @endphp
                <tr>
                    <td class="subject-name-cell">{{ ucwords(strtolower($result->course_name ?? 'N/A')) }}</td>
                    <td>{{ strtoupper($result->course_code ?? 'N/A') }}</td>
                    <td>
                        {{ ucwords(strtolower($result->teacher_first_name ?? '')) }}
                        {{ strtoupper(substr($result->teacher_last_name ?? '', 0, 1)) }}.
                    </td>
                    <td class="bold">{{ $result->score ?? 'X' }}</td>
                    <td>
                        @php
                            $grade = $result->grade ?? 'X';
                            $gradeClass = 'grade-' . $grade;
                        @endphp
                        <span class="{{ $gradeClass }}">{{ $grade }}</span>
                    </td>
                    <td>{{ $result->score ? ($result->courseRank ?? '-') : 'X' }}</td>
                    <td>{{ $result->score ? ($result->remarks ?? '-') : 'ABSENT' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-section" cellpadding="8" cellspacing="0">
            <tr>
                <td class="summary-label" width="25%">TOTAL MARKS</td>
                <td width="25%"><strong>{{ $totalScore ?? 0 }}</strong></td>
                <td class="summary-label" width="25%">CLASS POSITION</td>
                <td width="25%"><strong>{{ $studentRank ?? 1 }} out of {{ $rankings->count() ?? 1 }}</strong></td>
            </tr>
            <tr>
                <td class="summary-label">AVERAGE SCORE</td>
                <td><strong>{{ number_format($averageScore ?? 0, 2) }}%</strong></td>
                <td class="summary-label">SUBJECTS TAKEN</td>
                <td><strong>{{ $subjectCount }}</strong></td>
            </tr>
        </table>

        @if ($marking_style == 3)
        <table class="division-section" cellpadding="10" cellspacing="0">
            <tr>
                <td width="50%">
                    <strong>AGGREGATE POINTS</strong><br>
                    <span class="division-score">{{ $aggregatePoints ?? 0 }}</span>
                </td>
                <td width="50%">
                    <strong>DIVISION</strong><br>
                    <span class="division-score" style="
                        background-color:
                            @if($division == 'I') #4caf50
                            @elseif($division == 'II') #2196f3
                            @elseif($division == 'III') #ff9800
                            @elseif($division == 'IV') #9e9e9e
                            @else #f44336
                            @endif;
                        color: white; padding: 5px 25px; border-radius: 5px; display: inline-block;
                    ">
                        {{ $division === '0' ? '0' : $division }}
                    </span>
                </td>
            </tr>
        </table>
        @endif

         @php
            // Generate motivational Head Teacher's Comment based on performance
            $headComment = '';
            $motivationalMessage = '';
            $nextGoal = '';

            if ($marking_style == 3) {
                if ($division == 'I') {
                    $headComment = 'Outstanding Achievement! You have demonstrated exceptional mastery of all subjects.';
                    $motivationalMessage = '"Excellence is not a skill, it is an attitude." Keep shining and inspire others!';
                    $nextGoal = 'Target: Maintain Division I next term. You have proven you can do it!';
                } elseif ($division == 'II') {
                    $headComment = 'Very Good Performance! You have shown strong understanding across the curriculum.';
                    $motivationalMessage = '"Success is the sum of small efforts, repeated day in and day out." Push harder!';
                    $nextGoal = 'Target: Aim for Division I next term - just a little more effort will get you there!';
                } elseif ($division == 'III') {
                    $headComment = 'Satisfactory Performance. You have met the basic requirements.';
                    $motivationalMessage = '"The only limit to your impact is your imagination and commitment." You can do better!';
                    $nextGoal = 'Target: Focus on weaker subjects to reach Division II. We believe in your potential!';
                } elseif ($division == 'IV') {
                    $headComment = 'You have potential to do better. This term was a learning experience.';
                    $motivationalMessage = '"Every expert was once a beginner." Let\'s work together to improve next term.';
                    $nextGoal = 'Target: Regular study schedule and homework completion will boost your performance.';
                } else {
                    $headComment = 'This is a wake-up call. You are capable of achieving more than this.';
                    $motivationalMessage = '"Your attitude, not your aptitude, will determine your altitude." Time to rise up!';
                    $nextGoal = 'Target: Attend extra classes and seek help from teachers. We are here to support you.';
                }
            } else {
                $avg = $averageScore ?? 0;
                if ($marking_style == 1) {
                    if ($avg >= 40.5) {
                        $headComment = 'Magnificent Performance! You have excelled brilliantly this term.';
                        $motivationalMessage = '"Success is no accident. It is hard work, perseverance, learning, and most of all, love for what you are doing."';
                        $nextGoal = 'Keep setting higher targets. Aim for 100% attendance and perfect scores!';
                    } elseif ($avg >= 30.5) {
                        $headComment = 'Well Done! You have shown consistent improvement and good understanding.';
                        $motivationalMessage = '"The future depends on what you do today." A few more steps and you reach excellence!';
                        $nextGoal = 'Target: Improve your average by 5% next term. You can achieve Grade A!';
                    } elseif ($avg >= 20.5) {
                        $headComment = 'Good Effort! You are on the right track. Keep building on this foundation.';
                        $motivationalMessage = '"Education is the most powerful weapon which you can use to change the world."';
                        $nextGoal = 'Target: Focus on daily revision and complete all assignments on time.';
                    } elseif ($avg >= 10.5) {
                        $headComment = 'Room for Improvement. This is not your full potential - you can rise higher!';
                        $motivationalMessage = '"It always seems impossible until it\'s done." Start believing in yourself!';
                        $nextGoal = 'Target: Create a study timetable and stick to it. Small daily progress adds up!';
                    } else {
                        $headComment = 'A Fresh Start Ahead. Every setback is a setup for a comeback.';
                        $motivationalMessage = '"The only person you are destined to become is the person you decide to be."';
                        $nextGoal = 'Target: Meet with subject teachers weekly. We will support your journey to success.';
                    }
                } else {
                    if ($avg >= 80.5) {
                        $headComment = 'Exceptional Achievement! You have set a standard of excellence.';
                        $motivationalMessage = '"Excellence is not an act, but a habit." Continue being a role model!';
                        $nextGoal = 'Target: Maintain top position and help classmates who are struggling.';
                    } elseif ($avg >= 60.5) {
                        $headComment = 'Commendable Performance! Your hard work is paying off.';
                        $motivationalMessage = '"Shoot for the moon. Even if you miss, you\'ll land among the stars."';
                        $nextGoal = 'Target: Push your average above 70% - you have the ability!';
                    } elseif ($avg >= 40.5) {
                        $headComment = 'You are making progress. Keep the momentum going!';
                        $motivationalMessage = '"Believe you can and you\'re halfway there." You have what it takes!';
                        $nextGoal = 'Target: Improve by 10% next term through consistent effort.';
                    } elseif ($avg >= 20.5) {
                        $headComment = 'Time to Unlock Your Potential. You have hidden talents waiting to shine.';
                        $motivationalMessage = '"The secret of getting ahead is getting started." Start today!';
                        $nextGoal = 'Target: Attend all remedial classes and ask questions without fear.';
                    } else {
                        $headComment = 'Your Comeback Story Starts Now. Every master was once a beginner.';
                        $motivationalMessage = '"Failure is not the opposite of success, it\'s part of success."';
                        $nextGoal = 'Target: One-on-one mentoring sessions arranged. Let\'s turn things around together!';
                    }
                }
            }
        @endphp

        <table class="comment-section" cellpadding="10" cellspacing="0">
            <tr>
                <td class="comment-label">HEAD TEACHER'S<br>REMARKS</td>
                <td class="comment-content">
                    <i>{{ $headComment }}</i>
                </td>
            </tr>
        </table>

        <div class="qr-section">
            <div style="display: inline-block; text-align: center;">
                <img src="data:image/png;base64,{{ $qrPng ?? '' }}" class="qr-code" alt="Verification QR Code">
                <div class="qr-text">
                    <strong>Scan to Verify Authenticity</strong><br>
                </div>
            </div>
        </div>

        <div class="footer">
            <span>&copy; {{ date('Y') }} - {{ strtoupper($results->first()->school_name ?? 'SCHOOL') }}</span>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <span>Printed: {{ now()->format('d-M-Y H:i:s') }}</span>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <span>Document ID: {{ strtoupper(substr(md5(uniqid()), 0, 8)) }}</span>
        </div>
    </div>
</body>

</html>
