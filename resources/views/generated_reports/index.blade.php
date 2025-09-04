@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --success: #28a745;
            --light: #f8f9fa;
            --dark: #343a40;
            --gold: #ffd700;
            --government-blue: #003366;
            --government-red: #cc0000;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
        }

        .report-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin: 20px auto;
            max-width: 1200px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .government-header {
            background: linear-gradient(135deg, var(--government-blue) 0%, #004080 100%);
            color: white;
            padding: 20px 30px;
            position: relative;
            text-align: center;
            border-bottom: 4px solid var(--government-red);
        }

        .government-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--government-red) 0%, #ff0000 50%, var(--government-red) 100%);
        }

        .school-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 25px 30px;
            position: relative;
        }

        .school-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            text-align: center;
            position: relative;
            z-index: 1;
            font-size: 28px;
        }

        .government-text {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .school-name {
            color: var(--gold);
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .report-title {
            color: var(--gold);
            font-weight: 600;
            margin-top: 15px;
            text-transform: uppercase;
            text-align: center;
        }

        .card-body {
            padding: 30px;
        }

        .student-info-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .student-details {
            flex: 1;
            min-width: 300px;
        }

        .student-photo {
            text-align: center;
            flex-shrink: 0;
        }

        .student-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-weight: 700;
            color: var(--dark);
            font-size: 16px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-action {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            border: none;
        }

        .btn-back {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #495057 0%, #343a40 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-download {
            background: linear-gradient(135deg, var(--info) 0%, #138496 100%);
            color: white;
        }

        .btn-download:hover {
            background: linear-gradient(135deg, #138496 0%, #0f6674 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.3);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .table-custom {
            margin-bottom: 0;
            width: 100%;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            text-align: center;
            vertical-align: middle;
            position: sticky;
            top: 0;
        }

        .table-custom tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr {
            transition: all 0.3s;
        }

        .table-custom tbody tr:hover {
            background-color: #f8f9fa;
        }

        .subject-name {
            font-weight: 600;
            color: var(--dark);
        }

        .subject-code {
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
        }

        .teacher-name {
            color: #6c757d;
            font-style: italic;
        }

        .exam-score {
            font-weight: 700;
            text-align: center;
            color: var(--dark);
        }

        .total-score {
            font-weight: 800;
            color: var(--success);
            text-align: center;
            font-size: 16px;
        }

        .average-score {
            font-weight: 700;
            color: var(--info);
            text-align: center;
        }

        .grade {
            font-weight: 800;
            text-align: center;
            font-size: 16px;
        }

        .rank {
            font-weight: 700;
            color: var(--primary);
            text-align: center;
        }

        .remarks {
            font-style: italic;
            text-align: center;
            color: #6c757d;
        }

        .summary-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 20px;
            margin-top: 25px;
            border: 1px solid #dee2e6;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .summary-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .summary-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 14px;
        }

        .summary-value {
            font-weight: 800;
            color: var(--primary);
            font-size: 20px;
        }

        .performance-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .excellent {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .good {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }

        .pass {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            color: #856404;
        }

        .poor {
            background: linear-gradient(135deg, #fd7e14 0%, #f76707 100%);
            color: white;
        }

        .fail {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .compact-header {
            font-size: 12px;
            line-height: 1.2;
        }

        .floating-icons {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 50px;
            opacity: 0.1;
            color: white;
        }

        @media (max-width: 992px) {
            .student-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .action-buttons {
                justify-content: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .table-custom {
                display: block;
                overflow-x: auto;
            }

            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 24px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }

        .print-only {
            display: none;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .report-card {
                box-shadow: none;
                border: none;
                margin: 0;
            }

            .action-buttons {
                display: none;
            }

            .print-only {
                display: block;
            }

            .table-custom thead th {
                background: #4e54c8 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
    <div class="report-card">
        <!-- Government Header -->
        <div class="government-header">
            <h5 class="government-text">THE UNITED REPUBLIC OF TANZANIA</h5>
            <h6 class="government-text">PRESIDENT'S OFFICE - REGIONAL ADMINISTRATION AND LOCAL GOVERNMENT</h6>
        </div>

        <!-- School Header -->
        <div class="school-header">
            <h4 class="header-title">
                <span class="school-name text-uppercase">{{$schoolInfo->school_name}}</span>
            </h4>
            <p class="mb-1 text-center text-uppercase">{{$schoolInfo->postal_address}}, {{$schoolInfo->postal_name}} - {{$schoolInfo->country}}</p>
            <h5 class="report-title text-uppercase">Student's Academic Report</h5>
            <p class="mb-0 text-center text-uppercase"><strong>{{ $reports->title }} Report - {{\Carbon\Carbon::parse($reports->created_at)->format('d/m/Y')}}</strong></p>
            <i class="fas fa-graduation-cap floating-icons"></i>
        </div>

        <div class="card-body">
            <!-- Student Information -->
            <div class="student-info-section">
                <div class="student-header">
                    <div class="student-details">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Admission Number</span>
                                <span class="info-value text-uppercase">{{ $student->admission_number }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Student Name</span>
                                <span class="info-value text-uppercase">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Gender</span>
                                <span class="info-value text-uppercase">{{ ucfirst($student->gender) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Class</span>
                                <span class="info-value text-uppercase">{{ $student->class_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Stream</span>
                                <span class="info-value text-uppercase">{{ $student->group }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Term</span>
                                <span class="info-value text-uppercase">{{ $reports->term }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="student-photo">
                        @php
                            $imageName = $student->image;
                            $imagePath = public_path('assets/img/students/' . $imageName);
                            if (!empty($imageName) && file_exists($imagePath)) {
                                $avatarImage = asset('assets/img/students/' . $imageName);
                            } else {
                                $avatarImage = asset('assets/img/students/student.jpg');
                            }
                        @endphp
                        <img src="{{ $avatarImage }}" alt="Student Photo" class="student-avatar">
                        <p class="text-muted mt-2 mb-0">Student Photo</p>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{route('students.combined.report', ['school' => $school, 'year' => $year, 'class' => $class, 'report' => $report])}}" class="btn-action btn-back">
                        <i class="fas fa-arrow-circle-left"></i> Back
                    </a>
                    <a href="{{route('download.combined.report', ['school'=>$school, 'year'=>$year, 'class' => $class, 'report' => $report, 'student' => Hashids::encode($studentId)])}}" class="btn-action btn-download">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-container">
                @if ($reports->combine_option === 'individual')
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th rowspan="2">Subject Name (Code)</th>
                                <th rowspan="2">Teacher</th>
                                <th colspan="{{ count($examHeaders) }}" class="text-center">Examination Scores</th>
                                <th rowspan="2" class="text-center">Total</th>
                                <th rowspan="2" class="text-center">Avg</th>
                                <th rowspan="2" class="text-center">Grade</th>
                                <th rowspan="2" class="text-center">Rank</th>
                                <th rowspan="2" class="text-center">Remarks</th>
                            </tr>
                            <tr>
                                @foreach($examHeaders as $exam)
                                    <th class="compact-header text-center">
                                        <span class="text-sm text-uppercase">{{ $exam['abbr'] }}</span><br>
                                        <small>{{ \Carbon\Carbon::parse($exam['date'])->format('d M Y') }}</small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($finalData as $subject)
                                <tr>
                                    <td>
                                        <span class="subject-name text-capitalize">{{ ucwords(strtolower($subject['subjectName'])) }}</span>
                                        <br>
                                        <span class="subject-code">({{ $subject['subjectCode'] }})</span>
                                    </td>
                                    <td class="teacher-name text-capitalize">{{ucwords(strtolower($subject['teacher']))}}</td>

                                    @foreach($examHeaders as $exam)
                                        <td class="exam-score">{{ $subject['examScores'][$exam['abbr'].'_'.$exam['date']] ?? 'X' }}</td>
                                    @endforeach

                                    <td class="total-score">{{ $subject['total'] }}</td>
                                    <td class="average-score">{{ number_format($subject['average'], 2) }}</td>
                                    <td class="grade">
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
                                    <td class="rank">{{ $subject['position'] }}</td>
                                    <td class="remarks">
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
                        </tbody>
                    </table>
                @elseif ($reports->combine_option === 'sum')
                    <!-- Sum table layout -->
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Subject Name (Code)</th>
                                <th>Teacher</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Avg</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">Rank</th>
                                <th class="text-center">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($finalData as $subject)
                                <tr>
                                    <td>
                                        <span class="subject-name text-capitalize">{{ ucwords(strtolower($subject['subjectName'])) }}</span>
                                        <br>
                                        <span class="subject-code">({{ $subject['subjectCode'] }})</span>
                                    </td>
                                    <td class="teacher-name text-capitalize">{{ucwords(strtolower($subject['teacher']))}}</td>
                                    <td class="total-score">{{ $subject['total'] }}</td>
                                    <td class="average-score">{{ number_format($subject['average'], 2) }}</td>
                                    <td class="grade">
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
                                    <td class="rank">{{ $subject['position'] }}</td>
                                    <td class="remarks">
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
                        </tbody>
                    </table>
                @else
                    <!-- Average table layout -->
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Subject Name (Code)</th>
                                <th>Teacher</th>
                                <th class="text-center">Average</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">Rank</th>
                                <th class="text-center">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($finalData as $subject)
                                <tr>
                                    <td>
                                        <span class="subject-name text-capitalize">{{ ucwords(strtolower($subject['subjectName'])) }}</span>
                                        <br>
                                        <span class="subject-code">({{ $subject['subjectCode'] }})</span>
                                    </td>
                                    <td class="teacher-name text-capitalize">{{ucwords(strtolower($subject['teacher']))}}</td>
                                    <td class="average-score">{{ number_format($subject['average'], 2) }}</td>
                                    <td class="grade">
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
                                    <td class="rank">{{ $subject['position'] }}</td>
                                    <td class="remarks">
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
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Performance Summary -->
            <div class="summary-section">
                <h5 class="text-center mb-4" style="color: var(--primary); font-weight: 700;">OVERALL PERFORMANCE SUMMARY</h5>

                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">General Average</div>
                        <div class="summary-value">{{ number_format($studentGeneralAverage, 3) }}</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">Grade</div>
                        <div class="summary-value">
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
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">Position</div>
                        <div class="summary-value">{{ $generalPosition }} out of {{ $totalStudents }}</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">General Remarks</div>
                        <div>
                            @if ($results->first()->marking_style === 1)
                                @if ($studentGeneralAverage >= 40.5)
                                    <span class="performance-badge excellent">EXCELLENT</span>
                                @elseif ($studentGeneralAverage >= 30.5)
                                    <span class="performance-badge good">GOOD</span>
                                @elseif ($studentGeneralAverage >= 20.5)
                                    <span class="performance-badge pass">PASS</span>
                                @elseif ($studentGeneralAverage >= 10.5)
                                    <span class="performance-badge poor">POOR</span>
                                @else
                                    <span class="performance-badge fail">FAIL</span>
                                @endif
                            @else
                                @if ($studentGeneralAverage >= 80.5)
                                    <span class="performance-badge excellent">EXCELLENT</span>
                                @elseif ($studentGeneralAverage >= 60.5)
                                    <span class="performance-badge good">GOOD</span>
                                @elseif ($studentGeneralAverage >= 40.5)
                                    <span class="performance-badge pass">PASS</span>
                                @elseif ($studentGeneralAverage >= 20.5)
                                    <span class="performance-badge poor">POOR</span>
                                @else
                                    <span class="performance-badge fail">FAIL</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add print functionality
        document.addEventListener("DOMContentLoaded", function() {
            // Add animation to table rows
            const tableRows = document.querySelectorAll('.table-custom tbody tr');

            tableRows.forEach((row, index) => {
                row.style.opacity = "0";
                row.style.transform = "translateY(20px)";

                setTimeout(() => {
                    row.style.transition = "all 0.5s ease";
                    row.style.opacity = "1";
                    row.style.transform = "translateY(0)";
                }, index * 50);
            });
        });
    </script>
@endsection
