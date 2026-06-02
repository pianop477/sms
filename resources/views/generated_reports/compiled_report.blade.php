<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Academic Report - {{ $students->first_name ?? 'Student' }} {{ $students->last_name ?? '' }}</title>
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
            font-size: 12px;
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
            padding: 4px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
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
            width: 35%;
            background-color: #f5f5f5;
            font-size: 12px;
        }

        .info-value {
            width: 65%;
            font-size: 12px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 12px;
        }

        .report-table th {
            border: 1px solid #000000;
            padding: 5px 3px;
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }

        .report-table td {
            border: 1px solid #000000;
            padding: 5px 3px;
            text-align: center;
            font-size: 12px;
        }

        .subject-name {
            text-align: left;
        }

        .teacher-name {
            text-align: left;
        }

        .grade-A { background-color: #e8f5e9; border: 1px solid #2e7d32; font-weight: bold; padding: 2px 7px; border-radius: 3px; display: inline-block; font-size: 12px; }
        .grade-B { background-color: #e3f2fd; border: 1px solid #1565c0; padding: 2px 7px; border-radius: 3px; display: inline-block; font-size: 12px; }
        .grade-C { background-color: #fff3e0; border: 1px solid #e65100; padding: 2px 7px; border-radius: 3px; display: inline-block; font-size: 12px; }
        .grade-D { background-color: #ffebee; border: 1px solid #c62828; padding: 2px 7px; border-radius: 3px; display: inline-block; font-size: 12px; }
        .grade-E, .grade-F { background-color: #fce4ec; border: 1px solid #b71c1c; padding: 2px 7px; border-radius: 3px; display: inline-block; font-size: 12px; }

        .remark-excellent { background-color: #2e7d32; color: white; padding: 2px 10px; border-radius: 3px; font-weight: bold; font-size: 12px; display: inline-block; }
        .remark-good { background-color: #1565c0; color: white; padding: 2px 10px; border-radius: 3px; font-weight: bold; font-size: 12px; display: inline-block; }
        .remark-pass { background-color: #e65100; color: white; padding: 2px 10px; border-radius: 3px; font-weight: bold; font-size: 12px; display: inline-block; }
        .remark-poor { background-color: #c62828; color: white; padding: 2px 10px; border-radius: 3px; font-weight: bold; font-size: 12px; display: inline-block; }
        .remark-fail { background-color: #b71c1c; color: white; padding: 2px 10px; border-radius: 3px; font-weight: bold; font-size: 12px; display: inline-block; }

        @media print {
            .grade-A, .grade-B, .grade-C, .grade-D, .grade-E, .grade-F,
            .remark-excellent, .remark-good, .remark-pass, .remark-poor, .remark-fail {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .report-table th {
                background-color: #e0e0e0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
            }
        }

        .summary-row td {
            font-weight: bold;
            padding: 5px;
            background-color: #fafafa;
            font-size: 12px;
        }

        .highlight-bg {
            background-color: #1a1a2e;
            color: white;
            font-weight: bold;
        }

        .exam-key-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 11px;
        }

        .exam-key-table th, .exam-key-table td {
            border: 1px solid #000000;
            padding: 4px 6px;
            text-align: left;
        }

        .exam-key-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .division-section {
            width: 100%;
            margin: 8px 0;
            border: 1px solid #000000;
            border-collapse: collapse;
            background: #f9f9f9;
        }

        .division-section td {
            padding: 8px;
            text-align: center;
        }

        .division-score {
            font-size: 16px;
            font-weight: bold;
        }

        .qr-section {
            text-align: center;
            margin: 12px 0 8px 0;
            page-break-inside: avoid;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }

        .qr-text {
            font-size: 9px;
            margin-top: 3px;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 3px;
            width: 100%;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #000000;
            text-align: center;
            font-size: 10px;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }
        }

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
        <!-- HEADER -->
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
                        <div style="width: 75px; height: 75px; border: 1px solid #ccc; margin: 0 auto;">LOGO</div>
                    @endif
                </td>
                <td class="school-info-cell">
                    <div class="school-name">THE UNITED REPUBLIC OF TANZANIA</div>
                    <div class="school-name">PRESIDENT'S OFFICE - TAMISEMI</div>
                    <div class="school-name">
                        {{ strtoupper($schoolInfo->school_name ?? 'SCHOOL NAME') }}
                    </div>
                    <div class="school-address">
                        {{ $schoolInfo->postal_address ?? '______' }} -
                        {{ $schoolInfo->postal_name ?? '______' }},
                        {{ $schoolInfo->country ?? 'TANZANIA' }}
                    </div>
                    <div class="school-contacts">
                        Email: {{ strtolower($schoolInfo->school_email ?? 'info@school.ac.tz') }} |
                        Tel: {{ $schoolInfo->school_phone ?? '_________' }}
                    </div>
                </td>
                <td class="photo-cell">
                    @php
                        $studentImage = storage_path('app/public/students/' . ($students->image ?? 'student.jpg'));
                        $hasImage = file_exists($studentImage) && !empty($students->image);
                    @endphp
                    <img src="{{ $studentImage }}" class="student-photo" alt="Student Photo">
                </td>
            </tr>
        </table>

        <!-- REPORT TITLE -->
        <div class="report-title">
            <h3>ACADEMIC PROGRESS REPORT</h3>
            <p>
                {{ strtoupper($reports->title ?? 'ACADEMIC') }} ASSESSMENT -
                TERM {{ strtoupper($reports->term ?? 'I') }},
                {{ \Carbon\Carbon::parse($reports->created_at ?? now())->format('Y') }}
            </p>
        </div>

        <!-- STUDENT INFORMATION -->
        <div class="section-header">STUDENT'S INFORMATION</div>
        <table class="student-info" cellpadding="6" cellspacing="0">
            <tr>
                <td class="info-label">ADMISSION No.</td>
                <td class="info-value">{{ strtoupper($studentData->admission_number ?? 'N/A') }}</td>
                <td class="info-label">CLASS</td>
                <td class="info-value">{{ strtoupper($studentData->class_name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="info-label">STUDENT NAME</td>
                <td class="info-value">
                    {{ strtoupper($studentData->first_name ?? '') }}
                    {{ strtoupper($studentData->middle_name ?? '') }}
                    {{ strtoupper($studentData->last_name ?? '') }}
                </td>
                <td class="info-label">STREAM</td>
                <td class="info-value">{{ strtoupper($studentData->group ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="info-label">GENDER</td>
                <td class="info-value">{{ ucfirst($studentData->gender ?? 'N/A') }}</td>
                <td class="info-label">TERM</td>
                <td class="info-value">{{ strtoupper($reports->term ?? 'N/A') }}</td>
            </tr>
        </table>

        <!-- RESULTS TABLE -->
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
                            {{ strtoupper($exam['abbr'] ?? 'N/A') }}<br>
                            <small>{{ \Carbon\Carbon::parse($exam['date'] ?? now())->format('d-M') }}</small>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($finalData as $subject)
                <tr>
                    <td class="subject-name text-capitalize">
                        {{ ucwords(strtolower($subject['subjectName'] ?? 'N/A')) }}
                        <span class="text-uppercase">({{ strtoupper($subject['subjectCode'] ?? 'N/A') }})</span>
                    </td>
                    <td class="teacher-name text-capitalize">{{ ucwords(strtolower($subject['teacher'] ?? 'N/A')) }}</td>
                    @foreach ($examHeaders as $exam)
                        <td class="text-center">{{ $subject['examScores'][($exam['abbr'] ?? 'N/A') . '_' . ($exam['date'] ?? '')] ?? 'X' }}</td>
                    @endforeach
                    <td class="bold">{{ $subject['total'] ?? 0 }}</td>
                    <td class="text-center">{{ number_format($subject['average'] ?? 0, 2) }}</td>
                    <td class="text-center"><span class="grade-{{ $subject['grade'] ?? 'E' }}">{{ $subject['grade'] ?? 'E' }}</span></td>
                    <td class="text-center">{{ $subject['position'] ?? '-' }}</td>
                    <td class="text-center">
                        @php $g = $subject['grade'] ?? 'E'; @endphp
                        @if($g == 'A') Excellent
                        @elseif($g == 'B') Good
                        @elseif($g == 'C') Pass
                        @elseif($g == 'D') Poor
                        @else Fail
                        @endif
                    </td>
                </tr>
                @endforeach

                <tr class="summary-row">
                    <td colspan="2" class="bold">EXAM AVG</td>
                    @foreach ($examHeaders as $exam)
                        <td class="text-center bold">{{ number_format($examAverages[($exam['abbr'] ?? 'N/A') . '_' . ($exam['date'] ?? '')] ?? 0, 1) }}</td>
                    @endforeach
                    <td colspan="5"></td>
                </tr>
                <tr class="summary-row">
                    <td colspan="2" class="bold">EXAM GRADE</td>
                    @foreach ($examHeaders as $exam)
                        @php
                            $examKey = ($exam['abbr'] ?? 'N/A') . '_' . ($exam['date'] ?? '');
                            $avgScore = $examAverages[$examKey] ?? 0;
                            $examGrade = '';
                            if ($markingStyle == 1) {
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
                <tr><td colspan="7" class="highlight-bg text-center">EXAMINATION CODES KEY</td>
      </tr>
      <tr>
         <th width="30%">EXAM CODE</th>
         <th width="70%">EXAM DESCRIPTION</th>
      </tr>
      @foreach ($examSpecifications as $spec)
      <tr>
         <td class="text-uppercase">{{ $spec->abbr ?? 'N/A' }}</td>
         <td class="text-capitalize">{{ $spec->full_name ?? 'N/A' }}</td>
      </tr>
      @endforeach
      <tr>
         <td colspan="2" class="text-center">Note: "X" indicates that the student did not take the exam.</td>
      </tr>
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
            <td class="subject-name text-capitalize">{{ ucwords(strtolower($subject['subjectName'] ?? 'N/A')) }} <span class="text-uppercase">({{ strtoupper($subject['subjectCode'] ?? 'N/A') }})</span></td>
            <td class="teacher-name text-capitalize">{{ ucwords(strtolower($subject['teacher'] ?? 'N/A')) }}</td>
            <td class="bold text-center">{{ $subject['total'] ?? 0 }}</td>
            <td class="text-center">{{ number_format($subject['average'] ?? 0, 2) }}</td>
            <td class="text-center"><span class="grade-{{ $subject['grade'] ?? 'E' }}">{{ $subject['grade'] ?? 'E' }}</span></td>
            <td class="text-center">{{ $subject['position'] ?? '-' }}</td>
            <td class="text-center">@php $g = $subject['grade'] ?? 'E'; @endphp @if($g == 'A') Excellent @elseif($g == 'B') Good @elseif($g == 'C') Pass @elseif($g == 'D') Poor @else Fail @endif</td>
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
            <td class="subject-name text-capitalize">{{ ucwords(strtolower($subject['subjectName'] ?? 'N/A')) }} <span class="text-uppercase">({{ strtoupper($subject['subjectCode'] ?? 'N/A') }})</span></td>
            <td class="teacher-name text-capitalize">{{ ucwords(strtolower($subject['teacher'] ?? 'N/A')) }}</td>
            <td class="text-center">{{ number_format($subject['average'] ?? 0, 2) }}</td>
            <td class="text-center"><span class="grade-{{ $subject['grade'] ?? 'E' }}">{{ $subject['grade'] ?? 'E' }}</span></td>
            <td class="text-center">{{ $subject['position'] ?? '-' }}</td>
            <td class="text-center">@php $g = $subject['grade'] ?? 'E'; @endphp @if($g == 'A') Excellent @elseif($g == 'B') Good @elseif($g == 'C') Pass @elseif($g == 'D') Poor @else Fail @endif</td>
        </tr>
        @endforeach
    </tbody>
  </table>
  @endif

  <!-- DIVISION SECTION (Marking Style 3 only) -->
  @if (($markingStyle ?? 2) == 3 && isset($division))
  <table class="division-section" cellpadding="8" cellspacing="0">
    <tbody>
        <tr>
            <td width="50%"><strong>AGGREGATE POINTS</strong><br><span class="division-score">{{ $aggregatePoints ?? 0 }}</span></td>
            <td width="50%"><strong>DIVISION</strong><br><span>{{ $division === '0' ? '0' : $division }}</span></td>
        </tr>
        <tr><td colspan="2"><strong>Division Guide:</strong> I (Excellent) | II (Good) | III (Pass) | IV (Poor) | 0 (Fail)</td></tr>
    </tbody>
  </table>
  @endif

  <!-- OVERALL PERFORMANCE SUMMARY -->
  <div class="section-header">OVERALL PERFORMANCE SUMMARY</div>
  <table class="report-table" cellpadding="6" cellspacing="0">
    <tbody>
        <tr class="summary-row">
            <td width="25%">TOTAL MARKS/AVERAGE</td>
            <td width="25%"><strong>{{ number_format($totalScoreForStudent ?? 0, 2) }}</strong></td>
            <td width="25%">CLASS POSITION</td>
            <td width="25%"><strong>{{ $generalPosition ?? 1 }} out of {{ $totalStudentsCount ?? 1 }}</strong></td>
        </tr>
        <tr class="summary-row">
            <td class="summary-label">GENERAL AVERAGE</td>
            <td class="summary-value"><strong>{{ number_format($studentGeneralAverage ?? 0, 2) }}</strong></td>
            <td class="summary-label">SUBJECTS TAKEN</td>
            <td class="summary-value"><strong>{{ count($finalData) }}</strong></td>
        </tr>
        <tr class="summary-row">
            <td class="summary-label">OVERALL GRADE</td>
            <td colspan="3">
                @php
                    $overallGrade = '';
                    $gradeComment = '';
                    $remarkClass = '';
                    $avgScore = $studentGeneralAverage ?? 0;
                    $currentMarkingStyle = $markingStyle ?? 2;

                    if ($currentMarkingStyle == 3 && isset($division)) {
                        if ($division == 'I') { $overallGrade = 'I'; $gradeComment = 'EXCELLENT'; $remarkClass = 'remark-excellent'; }
                        elseif ($division == 'II') { $overallGrade = 'II'; $gradeComment = 'GOOD'; $remarkClass = 'remark-good'; }
                        elseif ($division == 'III') { $overallGrade = 'III'; $gradeComment = 'PASS'; $remarkClass = 'remark-pass'; }
                        elseif ($division == 'IV') { $overallGrade = 'IV'; $gradeComment = 'POOR'; $remarkClass = 'remark-poor'; }
                        else { $overallGrade = '0'; $gradeComment = 'FAIL'; $remarkClass = 'remark-fail'; }
                    }
                    elseif ($currentMarkingStyle == 1) {
                        if ($avgScore >= 40.5) { $overallGrade = 'A'; $gradeComment = 'EXCELLENT'; $remarkClass = 'remark-excellent'; }
                        elseif ($avgScore >= 30.5) { $overallGrade = 'B'; $gradeComment = 'GOOD'; $remarkClass = 'remark-good'; }
                        elseif ($avgScore >= 20.5) { $overallGrade = 'C'; $gradeComment = 'PASS'; $remarkClass = 'remark-pass'; }
                        elseif ($avgScore >= 10.5) { $overallGrade = 'D'; $gradeComment = 'POOR'; $remarkClass = 'remark-poor'; }
                        else { $overallGrade = 'E'; $gradeComment = 'FAIL'; $remarkClass = 'remark-fail'; }
                    }
                    else {
                        if ($avgScore >= 80.5) { $overallGrade = 'A'; $gradeComment = 'EXCELLENT'; $remarkClass = 'remark-excellent'; }
                        elseif ($avgScore >= 60.5) { $overallGrade = 'B'; $gradeComment = 'GOOD'; $remarkClass = 'remark-good'; }
                        elseif ($avgScore >= 40.5) { $overallGrade = 'C'; $gradeComment = 'PASS'; $remarkClass = 'remark-pass'; }
                        elseif ($avgScore >= 20.5) { $overallGrade = 'D'; $gradeComment = 'POOR'; $remarkClass = 'remark-poor'; }
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
  </table>

  <!-- HEAD TEACHER'S COMMENT -->
  @php
    $headComment = '';
    $avgScore = $studentGeneralAverage ?? 0;
    $currentMarkingStyle = $markingStyle ?? 2;

    if ($currentMarkingStyle == 3 && isset($division)) {
        if ($division == 'I') $headComment = 'Outstanding Achievement! Keep shining!';
        elseif ($division == 'II') $headComment = 'Very Good Performance! Push harder!';
        elseif ($division == 'III') $headComment = 'Good Effort! Keep building!';
        elseif ($division == 'IV') $headComment = 'Room for Improvement. Work harder!';
        else $headComment = 'Fresh Start Ahead. We will support you!';
    } else {
        if ($currentMarkingStyle == 1) {
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

  <table class="report-table" cellpadding="7" cellspacing="0">
    <tbody>
        <tr>
            <td width="25%" class="bold">HEAD TEACHER'S REMARKS</td>
            <td width="75%"><i>{{ $headComment }}</i></td>
        </tr>
    </tbody>
  </table>

  <!-- QR CODE SECTION -->
  <div class="qr-section">
    @if(!empty($qrPng))
        <img src="data:image/png;base64,{{ $qrPng }}" class="qr-code" alt="Verification QR Code">
    @else
        <div>QR</div>
    @endif
    <div class="qr-text"><strong>Scan to Verify Authenticity</strong></div>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    &copy; {{ date('Y') }} - {{ strtoupper($schoolInfo->school_name ?? 'SCHOOL') }} |
    Printed: {{ now()->format('d-M-Y H:i:s') }} |
    Powered by ShuleApp System
  </div>
 </div>
</body>
</html>
