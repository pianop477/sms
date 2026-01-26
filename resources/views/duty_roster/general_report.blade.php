@extends('SRTDashboard.frame')
@section('content')
    <style>
        /* Classic Professional Styling */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #f8f9fa;
            --accent-color: #3498db;
            --accent-dark: #1a5d8e;
            --border-color: #dee2e6;
            --text-color: #212529;
            --header-bg: #2c3e50;
            --table-header-bg: #3a506b;
            --table-row-alt: #f8f9fa;
            --total-row-bg: #e9ecef;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --gold-accent: #d4af37;
            --cream-bg: #fcfaf7;
            --header-gradient: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
        }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            color: var(--text-color);
            padding: 0;
            margin: 0;
            font-size: 12px;
            line-height: 1;
            background-color: var(--cream-bg);
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: var(--header-gradient);
            color: white;
            padding: 15px 10px;
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 5px solid var(--gold-accent);
            position: relative;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            font-style: italic;
        }

        .header::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gold-accent);
        }

        .report-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 0 15px;
        }

        .info-box {
            border: 1px solid var(--border-color);
            padding: 6px 8px;
            border-radius: 4px;
            background-color: var(--secondary-color);
            flex: 1;
            margin: 0 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }

        .info-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 2px;
            height: 100%;
            background: var(--accent-color);
        }

        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 4px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .section-title {
            background: var(--header-gradient);
            color: white;
            padding: 5px 8px;
            margin: 0 0 18px 0;
            font-size: 14px;
            font-weight: bold;
            border-left: 5px solid var(--gold-accent);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .teachers-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 8px;
            padding: 0 8px;
        }

        .teacher-badge {
            background-color: var(--secondary-color);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 5px 8px;
            font-size: 11px;
            flex-grow: 1;
            text-align: center;
            min-width: 100px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }

        .teacher-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .teacher-name {
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 2px;
        }

        .teacher-id {
            font-size: 11px;
            color: #6c757d;
            /* font-style: italic; */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 9px;
            font-size: 12px;
            page-break-inside: avoid;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        table th {
            background-color: var(--table-header-bg);
            color: white;
            padding: 5px 3px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #495867;
            font-family: 'Helvetica', 'Arial', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 10px;
        }

        table td {
            padding: 4px 3px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }

        table tr:nth-child(even) {
            background-color: var(--table-row-alt);
        }

        .total-row {
            background-color: var(--total-row-bg) !important;
            font-weight: bold;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }

        .report-details {
            padding: 0 8x;
        }

        .detail-item {
            margin-bottom: 9px;
            padding-bottom: 9px;
            border-bottom: 0.5px solid var(--border-color);
        }

        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 4px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .detail-label::before {
            content: "•";
            color: var(--accent-color);
            font-size: 9px;
            margin-right: 4px;
        }

        .detail-content {
            padding: 6px;
            background-color: var(--secondary-color);
            border-radius: 3px;
            border-left: 4px solid var(--accent-color);
            line-height: 1;
            font-style: italic;
        }

        .footer {
            margin-top: 35px;
            padding: 9px 7px;
            text-align: center;
            font-size: 11px;
            color: #6c757d;
            border-top: 1px solid var(--border-color);
            font-family: 'Helvetica', 'Arial', sans-serif;
        }

        .date-range {
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 2px;
        }

        .page-break {
            page-break-before: always;
            padding-top: 40px;
        }

        .report-date-header {
            background: linear-gradient(to right, #e3f2fd, #bbdefb);
            padding: 9px;
            border-radius: 4px;
            margin: 0 15px 25px 15px;
            text-align: center;
            font-weight: bold;
            border-left: 5px solid var(--accent-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .attendance-rate {
            background-color: var(--primary-color);
            color: white;
            padding: 5px 9px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 14px;
            font-weight: bold;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .attendance-high {
            background-color: var(--success-color);
            color: white;
        }

        .attendance-medium {
            background-color: var(--warning-color);
            color: #212529;
        }

        .attendance-low {
            background-color: var(--danger-color);
            color: white;
        }

        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            opacity: 0.1;
            font-size: 80px;
            color: var(--primary-color);
            transform: rotate(-45deg);
            pointer-events: none;
            z-index: -1;
            font-weight: bold;
        }

        .class-name {
            text-transform: uppercase;
            text-align: left;
            padding-left: 12px;
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Button Styles */
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin: 10px 7px;
            padding: 0 5px;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        .btn-download {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-download:hover {
            background-color: var(--accent-dark);
        }

        /* Print Styles */
        @media print {

            * {
                color: black !important;
            }
            .action-buttons, .watermark {
                display: none !important;
            }

            body {
                background-color: white;
                font-size: 10px;
                color: black !important;
            }

            .header p {
                color: black !important;
            }

            .container {
                box-shadow: none;
            }

            .header {
                border-bottom: 3px solid var(--gold-accent);
                padding: 7px 10px;
            }

            .header h1 {
                font-size: 18px;
            }

            .teacher-badge:hover {
                transform: none;
                box-shadow: none;
            }

            .table th {
                border: 1px solid black;
                /* border: none; */
            }
            th td {
                border: 1px solid black;
            }

            .report-date-header, .info-box, .teacher-badge, .detail-content {
                box-shadow: none;
            }

            .page-break {
                padding-top: 15px;
            }
        }
    </style>
    <div class="container">
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('get.school.report') }}" class="btn btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Back
            </a>
            <button onclick="printReport()" class="btn btn-download">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                Download
            </button>
        </div>

        <!-- Watermark -->
        <div class="watermark">SCHOOL REPORT</div>

        <!-- Single Header for the entire document -->
        @php
            $school = Auth::user()->school_id;
            $schoolDetails = \App\Models\school::findOrFail($school);
        @endphp
        <div class="header">
            <h1>{{strtoupper($schoolDetails->school_name)}}</h1>
            <h3>SCHOOL DAILY REPORT</h3>
            <p class="text-white">School Routine Tracking System</p>
        </div>

        <!-- Report Info -->
        <div class="report-info">
            <div class="info-box">
                <h3>GENERATED ON</h3>
                <div>{{ \Carbon\Carbon::now()->format('F j, Y \\a\\t g:i A') }}</div>
            </div>
            <div class="info-box">
                <h3>TOTAL REPORTS</h3>
                <div>{{ count($reportsWithAttendance) }} Daily Reports</div>
            </div>
        </div>

        @foreach($reportsWithAttendance as $index => $reportData)
            @php
                $report = $reportData['report'];
                $attendance = $reportData['attendance'];

                // Get assigned teachers for this roster
                $assignedTeachers = \App\Models\TodRoster::query()
                                ->join('teachers', 'tod_rosters.teacher_id', '=', 'teachers.id')
                                ->leftJoin('users', 'teachers.user_id', '=', 'users.id')
                                ->select('tod_rosters.*', 'users.first_name', 'users.last_name', 'users.image', 'teachers.member_id')
                                ->where('roster_id', $report->roster_id)
                                ->get();

                // Calculate total attendance rate for this day
                $total_registered = 0;
                $total_present = 0;

                foreach($attendance as $attendanceRecord) {
                    $total_registered += $attendanceRecord->registered_boys + $attendanceRecord->registered_girls;
                    $total_present += $attendanceRecord->present_boys + $attendanceRecord->present_girls;
                }

                $attendance_rate = $total_registered > 0 ? round(($total_present / $total_registered) * 100, 2) : 0;

                // Determine attendance rate class
                if ($attendance_rate >= 90) {
                    $rate_class = "attendance-high";
                } elseif ($attendance_rate >= 75) {
                    $rate_class = "attendance-medium";
                } else {
                    $rate_class = "attendance-low";
                }
            @endphp

            @if($index > 0)
                <div class="page-break"></div>
            @endif

            <!-- Report Date Header with Attendance Rate -->
            <div class="report-date-header">
                <div>
                    <span style="font-size: 17px;">Report Date: {{ \Carbon\Carbon::parse($report->report_date)->format('l, F j, Y') }}</span>
                </div>
                <div class="attendance-rate {{ $rate_class }}">
                    Attendance Rate: {{ $attendance_rate }}%
                </div>
            </div>

            <!-- Teachers on Duty -->
            <div class="section">
                <h3 class="section-title">TEACHERS ON DUTY</h3>

                @if($assignedTeachers->count() > 0)
                    <div class="teachers-container">
                        @foreach($assignedTeachers as $detail)
                            <div class="teacher-badge">
                                <div class="teacher-name text-capitalize">{{ ucwords(strtolower($detail->first_name)) }} {{ ucwords(strtolower($detail->last_name)) }}</div>
                                <div class="teacher-id text-uppercase"><strong>{{ strtoupper($detail->member_id) ?? 'N/A' }}</strong></div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="padding: 0 15px;">
                        <div style="background-color: #fff3cd; color: #856404; padding: 12px; border-radius: 6px; border-left: 4px solid #ffeeba;">
                            No teachers assigned to this roster.
                        </div>
                    </div>
                @endif
            </div>

            <!-- Attendance Summary -->
            <div class="section">
                <h3 class="section-title">STUDENTS ATTENDANCE</h3>
                <table class="table table-responsive-md table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">Class</th>
                            <th colspan="3">Registered</th>
                            <th colspan="3">Attended</th>
                            <th colspan="3">Absentees</th>
                            <th colspan="3">Permission</th>
                            <th rowspan="2">Attendance Rate</th>
                        </tr>
                        <tr>
                            <th>Boys</th><th>Girls</th><th>Total</th>
                            <th>Boys</th><th>Girls</th><th>Total</th>
                            <th>Boys</th><th>Girls</th><th>Total</th>
                            <th>Boys</th><th>Girls</th><th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_registered_boys = 0;
                            $total_registered_girls = 0;
                            $total_present_boys = 0;
                            $total_present_girls = 0;
                            $total_absent_boys = 0;
                            $total_absent_girls = 0;
                            $total_permission_boys = 0;
                            $total_permission_girls = 0;
                        @endphp

                        @foreach($attendance as $attendanceRecord)
                            @php
                                $class_registered = $attendanceRecord->registered_boys + $attendanceRecord->registered_girls;
                                $class_present = $attendanceRecord->present_boys + $attendanceRecord->present_girls;
                                $class_attendance_rate = $class_registered > 0 ? round(($class_present / $class_registered) * 100, 2) : 0;

                                // Determine class attendance rate class
                                if ($class_attendance_rate >= 90) {
                                    $class_rate_class = "attendance-high";
                                } elseif ($class_attendance_rate >= 75) {
                                    $class_rate_class = "attendance-medium";
                                } else {
                                    $class_rate_class = "attendance-low";
                                }

                                $total_registered_boys += $attendanceRecord->registered_boys;
                                $total_registered_girls += $attendanceRecord->registered_girls;
                                $total_present_boys += $attendanceRecord->present_boys;
                                $total_present_girls += $attendanceRecord->present_girls;
                                $total_absent_boys += $attendanceRecord->absent_boys;
                                $total_absent_girls += $attendanceRecord->absent_girls;
                                $total_permission_boys += $attendanceRecord->permission_boys;
                                $total_permission_girls += $attendanceRecord->permission_girls;
                            @endphp

                            <tr>
                                <td class="class-name">{{ $attendanceRecord->class_code }} {{ $attendanceRecord->group }}</td>
                                <td>{{ $attendanceRecord->registered_boys }}</td>
                                <td>{{ $attendanceRecord->registered_girls }}</td>
                                <td>{{ $class_registered }}</td>
                                <td>{{ $attendanceRecord->present_boys }}</td>
                                <td>{{ $attendanceRecord->present_girls }}</td>
                                <td>{{ $class_present }}</td>
                                <td>{{ $attendanceRecord->absent_boys }}</td>
                                <td>{{ $attendanceRecord->absent_girls }}</td>
                                <td>{{ $attendanceRecord->absent_boys + $attendanceRecord->absent_girls }}</td>
                                <td>{{ $attendanceRecord->permission_boys }}</td>
                                <td>{{ $attendanceRecord->permission_girls }}</td>
                                <td>{{ $attendanceRecord->permission_boys + $attendanceRecord->permission_girls }}</td>
                                <td class="{{ $class_rate_class }}" style="font-weight: bold;">{{ $class_attendance_rate }}%</td>
                            </tr>
                        @endforeach

                        <!-- Total Row -->
                        @php
                            $total_registered = $total_registered_boys + $total_registered_girls;
                            $total_present = $total_present_boys + $total_present_girls;
                            $total_attendance_rate = $total_registered > 0 ? round(($total_present / $total_registered) * 100, 2) : 0;

                            // Determine total attendance rate class
                            if ($total_attendance_rate >= 90) {
                                $total_rate_class = "attendance-high";
                            } elseif ($total_attendance_rate >= 75) {
                                $total_rate_class = "attendance-medium";
                            } else {
                                $total_rate_class = "attendance-low";
                            }
                        @endphp
                        <tr class="total-row">
                            <td class="class-name">TOTAL</td>
                            <td>{{ $total_registered_boys }}</td>
                            <td>{{ $total_registered_girls }}</td>
                            <td>{{ $total_registered }}</td>
                            <td>{{ $total_present_boys }}</td>
                            <td>{{ $total_present_girls }}</td>
                            <td>{{ $total_present }}</td>
                            <td>{{ $total_absent_boys }}</td>
                            <td>{{ $total_absent_girls }}</td>
                            <td>{{ $total_absent_boys + $total_absent_girls }}</td>
                            <td>{{ $total_permission_boys }}</td>
                            <td>{{ $total_permission_girls }}</td>
                            <td>{{ $total_permission_boys + $total_permission_girls }}</td>
                            <td class="{{ $total_rate_class }}" style="font-weight: bold;">{{ $total_attendance_rate }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Report Details -->
            <div class="section">
                <h3 class="section-title">DAILY SCHEDULE</h3>
                <div class="report-details">
                    <div class="detail-item">
                        <div class="detail-label">Morning Parade</div>
                        <div class="detail-content">{{ $report->parade ?? 'No details provided' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Break Time</div>
                        <div class="detail-content">{{ $report->break_time ?? 'No details provided' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Lunch Time</div>
                        <div class="detail-content">{{ $report->lunch_time ?? 'No details provided' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Students Attendance</div>
                        <div class="detail-content">Students attended is <strong>{{$total_present}} </strong> out of <strong>{{$total_registered}}</strong> which is Equivalent to <strong> {{$attendance_rate}}%</strong> of Attendance Performance rate for this day</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Teachers Attendance</div>
                        <div class="detail-content">{{ $report->teachers_attendance ?? 'No details provided' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Special Event</div>
                        <div class="detail-content">{{ $report->daily_new_event ?? 'No special events reported' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Teacher on Duty Remarks</div>
                        <div class="detail-content">{{ $report->tod_remarks ?? 'No remarks provided' }}</div>
                    </div>

                    @if($report->headteacher_comment)
                    <div class="detail-item">
                        <div class="detail-label">Headteacher/Academic Comments</div>
                        <div class="detail-content">{{ $report->headteacher_comment }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Report Status -->
            <div class="section">
                <h3 class="section-title">REPORT STATUS</h3>
                <div style="padding: 0 15px;">
                    <span class="status-badge status-approved">
                        Approved by: <span style="text-decoration: underline">{{ ucwords(strtolower($report->approved_by)) }}</span>
                    </span>
                </div>
            </div>

            <!-- Footer for each report -->
            <div class="footer">
                Page {{ $index + 1 }} of {{ count($reportsWithAttendance) }} | &copy {{ucwords(strtolower($schoolDetails->school_name))}}
            </div>
        @endforeach
    </div>

    <script>
        function printReport() {
            window.print();
        }
    </script>
@endsection
