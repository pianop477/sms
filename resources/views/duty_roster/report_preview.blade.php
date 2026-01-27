@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #ecf0f1;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --error-color: #e74c3c;
            --text-color: #2c3e50;
            --border-color: #bdc3c7;
            --light-bg: #f8f9fa;
        }

        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            padding: 20px 0;
        }

        .report-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .report-header {
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            color: white;
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }

        .report-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--success-color));
        }

        .school-info {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .school-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--secondary-color);
            margin-bottom: 1rem;
        }

        .section-title {
            border-left: 4px solid var(--accent-color);
            padding-left: 15px;
            margin: 2rem 0 1.5rem 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: var(--accent-color);
        }

        .teacher-card {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: var(--light-bg);
            transition: all 0.3s;
        }

        .teacher-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        table thead {
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            color: white;
        }

        table th,
        table td {
            text-align: center;
            vertical-align: middle;
            padding: 1rem 0.5rem;
        }

        table th {
            font-weight: 600;
            font-size: 0.9rem;
        }

        table tbody tr:nth-child(even) {
            background-color: var(--light-bg);
        }

        table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
            transition: background-color 0.3s;
        }

        .report-details {
            background-color: var(--light-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .detail-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .btn-approve {
            background: linear-gradient(to right, var(--success-color), #2ecc71);
            border: none;
            padding: 0.85rem 2.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-approve:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .report-footer {
            background-color: var(--secondary-color);
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            color: #6c757d;
        }

        .total-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }

        .edit-toggle {
            position: absolute;
            right: 0;
            top: 0;
            cursor: pointer;
            color: var(--accent-color);
            font-size: 1rem;
        }

        .back-button {
            position: absolute;
            top: 15px;
            right: 15px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
                padding: 0;
            }

            .report-container {
                box-shadow: none;
                border-radius: 0;
            }
        }

        @media (max-width: 768px) {
            .report-header {
                padding: 1.2rem;
            }

            .section-title {
                font-size: 1.1rem;
            }

            table th,
            table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.85rem;
            }

            .back-button {
                position: relative;
                top: 0;
                right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <h3><i class="fas fa-clipboard-list me-2"></i> SCHOOL DAILY REPORT</h3>
            <p class="mb-0 text-white">School Routine Tracking System</p>
            <a href="{{ route('get.school.report') }}" class="btn btn-light back-button no-print">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="p-3">
            <!-- Report Date -->
            <div class="mb-4">
                <h3 class="section-title">
                    <i class="fas fa-calendar-alt"></i>Report Date
                </h3>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Report Date:
                    <strong>{{ \Carbon\Carbon::parse($reportDetails->report_date)->format('l, F j, Y') }}</strong>
                </div>
            </div>

            <!-- Teachers Assigned -->
            <div class="mb-4">
                <h3 class="section-title">
                    <i class="fas fa-chalkboard-teacher"></i>Teachers on Duty
                </h3>

                @php
                    // Pata walimu walio assigned kwenye hii roster
                    $assignedTeachers = \App\Models\TodRoster::query()
                        ->join('teachers', 'tod_rosters.teacher_id', '=', 'teachers.id')
                        ->leftJoin('users', 'teachers.user_id', '=', 'users.id')
                        ->select(
                            'tod_rosters.*',
                            'users.first_name',
                            'users.last_name',
                            'users.image', 'users.gender',
                            'teachers.member_id',
                        )
                        ->where('roster_id', $roster->roster_id)
                        ->get();
                @endphp
                @if ($assignedTeachers->count() > 0)
                    <div class="row">
                        @foreach ($assignedTeachers as $detail)
                            <div class="col-md-6 col-lg-4">
                                <div class="teacher-card">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            @php
                                                $imageName = $detail->image;
                                                $imagePath = storage_path('app/public/profile/' . $imageName);

                                                if (!empty($imageName) && file_exists($imagePath)) {
                                                    $avatarImage = asset('storage/profile/' . $imageName);
                                                } else {
                                                    $avatarImage = asset(
                                                        'storage/profile/' .
                                                            ($detail->gender == 'male'
                                                                ? 'avatar.jpg'
                                                                : 'avatar-female.jpg'),
                                                    );
                                                }
                                            @endphp
                                            <img src="{{ $avatarImage }}" alt="Teacher Avatar" class="rounded-circle" width="60" height="60">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="mb-1 text-capitalize"> {{ ucwords(strtolower($detail->first_name)) }}
                                                {{ ucwords(strtolower($detail->last_name)) }}</h5>
                                            <p class="mb-0 text-muted text-uppercase"> {{ $detail->member_id ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> No teachers assigned to this roster.
                    </div>
                @endif
            </div>

            <!-- Attendance Summary -->
            <div class="mb-4">
                <h3 class="section-title">
                    <i class="fas fa-chart-bar"></i>Students Attendance Summary
                </h3>

                <div class="table-responsive">
                    <table class="table table-bordered table-responsive-md">
                        <thead>
                            <tr>
                                <th rowspan="2">Class</th>
                                <th colspan="3">Registered</th>
                                <th colspan="3">Attended</th>
                                <th colspan="3">Absentees</th>
                                <th colspan="3">Permission</th>
                            </tr>
                            <tr>
                                <th>Boys</th>
                                <th>Girls</th>
                                <th>Total</th>
                                <th>Boys</th>
                                <th>Girls</th>
                                <th>Total</th>
                                <th>Boys</th>
                                <th>Girls</th>
                                <th>Total</th>
                                <th>Boys</th>
                                <th>Girls</th>
                                <th>Total</th>
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

                            @foreach ($dailyAttendance as $attendance)
                                @php
                                    $total_registered_boys += $attendance->registered_boys;
                                    $total_registered_girls += $attendance->registered_girls;
                                    $total_present_boys += $attendance->present_boys;
                                    $total_present_girls += $attendance->present_girls;
                                    $total_absent_boys += $attendance->absent_boys;
                                    $total_absent_girls += $attendance->absent_girls;
                                    $total_permission_boys += $attendance->permission_boys;
                                    $total_permission_girls += $attendance->permission_girls;
                                @endphp

                                <tr>
                                    <td style="text-transform:uppercase">{{ $attendance->class_code }}
                                        {{ $attendance->group }}</td>
                                    <td>{{ $attendance->registered_boys }}</td>
                                    <td>{{ $attendance->registered_girls }}</td>
                                    <td>{{ $attendance->registered_boys + $attendance->registered_girls }}</td>
                                    <td>{{ $attendance->present_boys }}</td>
                                    <td>{{ $attendance->present_girls }}</td>
                                    <td>{{ $attendance->present_boys + $attendance->present_girls }}</td>
                                    <td>{{ $attendance->absent_boys }}</td>
                                    <td>{{ $attendance->absent_girls }}</td>
                                    <td>{{ $attendance->absent_boys + $attendance->absent_girls }}</td>
                                    <td>{{ $attendance->permission_boys }}</td>
                                    <td>{{ $attendance->permission_girls }}</td>
                                    <td>{{ $attendance->permission_boys + $attendance->permission_girls }}</td>
                                </tr>
                            @endforeach

                            <!-- Total Row -->
                            <tr class="total-row">
                                <td>TOTAL</td>
                                <td>{{ $total_registered_boys }}</td>
                                <td>{{ $total_registered_girls }}</td>
                                <td>{{ $total_registered_boys + $total_registered_girls }}</td>
                                <td>{{ $total_present_boys }}</td>
                                <td>{{ $total_present_girls }}</td>
                                <td>{{ $total_present_boys + $total_present_girls }}</td>
                                <td>{{ $total_absent_boys }}</td>
                                <td>{{ $total_absent_girls }}</td>
                                <td>{{ $total_absent_boys + $total_absent_girls }}</td>
                                <td>{{ $total_permission_boys }}</td>
                                <td>{{ $total_permission_girls }}</td>
                                <td>{{ $total_permission_boys + $total_permission_girls }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Report Details Form -->
            <form action="{{ route('report.update', ['id' => Hashids::encode($reportDetails->id)]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Report Details -->
                <div class="mb-4">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i> Report Details
                    </h3>

                    <div class="report-details">
                        <div class="detail-item">
                            <span class="edit-toggle" onclick="toggleEdit('parade')">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </span>
                            <div class="detail-label"><i class="fas fa-users me-2 mr-1"></i> Morning Parade</div>
                            <textarea class="form-control" id="parade" name="parade" rows="3" readonly>{{ $reportDetails->parade }}</textarea>
                        </div>

                        <div class="detail-item">
                            <span class="edit-toggle" onclick="toggleEdit('break_time')">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </span>
                            <div class="detail-label"><i class="fas fa-coffee me-2 mr-1"></i> Break Time</div>
                            <textarea class="form-control" id="break_time" name="break_time" rows="3" readonly>{{ $reportDetails->break_time }}</textarea>
                        </div>

                        <div class="detail-item">
                            <span class="edit-toggle" onclick="toggleEdit('lunch_time')">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </span>
                            <div class="detail-label"><i class="fas fa-utensils me-2 mr-1"></i> Lunch Time</div>
                            <textarea class="form-control" id="lunch_time" name="lunch_time" rows="3" readonly>{{ $reportDetails->lunch_time }}</textarea>
                        </div>

                        <div class="detail-item">
                            <span class="edit-toggle" onclick="toggleEdit('teachers_attendance')">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </span>
                            <div class="detail-label"><i class="fas fa-chalkboard-teacher me-2 mr-1"></i> Teachers
                                Attendance</div>
                            <textarea class="form-control" id="teachers_attendance" name="teachers_attendance" rows="3" readonly>{{ $reportDetails->teachers_attendance }}</textarea>
                        </div>

                        <div class="detail-item">
                            <span class="edit-toggle" onclick="toggleEdit('daily_new_event')">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </span>
                            <div class="detail-label"><i class="fas fa-calendar-plus me-2 mr-1"></i> Special Event</div>
                            <textarea class="form-control" id="daily_new_event" name="daily_new_event" rows="3" readonly>{{ $reportDetails->daily_new_event ?? 'N/A' }}</textarea>
                        </div>

                        <div class="detail-item">
                            <span class="edit-toggle" onclick="toggleEdit('tod_remarks')">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </span>
                            <div class="detail-label"><i class="fas fa-sticky-note me-2 mr-1"></i> Teacher on Duty Remarks
                            </div>
                            <textarea class="form-control" id="tod_remarks" name="tod_remarks" rows="3" readonly>{{ $reportDetails->tod_remarks }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Approval Form -->
                @if ($reportDetails->status === 'pending')
                    <div class="mb-4 no-print">
                        <h3 class="section-title">
                            <i class="fas fa-check-circle"></i>Approve Report
                        </h3>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Please review the report and approve if everything is correct.
                        </div>

                        <div class="mb-3">
                            <label for="headteacher_comment" class="form-label">Headteacher/Academic Comments</label>
                            <textarea class="form-control" id="headteacher_comment" name="headteacher_comment" rows="3"
                                placeholder="Enter comments or feedback..." required></textarea>
                        </div>

                        <input type="hidden" name="status" value="approved">

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-approve"
                                onclick="return confirm('Are you sure you want to approve this report?');">
                                <i class="fas fa-check me-2"></i> Approve Report
                            </button>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        This report has been approved on
                        {{ \Carbon\Carbon::parse($reportDetails->updated_at)->format('F j, Y \\a\\t g:i A') }}
                    </div>
                @endif
            </form>
        </div>

        <div class="report-footer">
            <small><i class="fas fa-info-circle me-1"></i> Generated on
                {{ \Carbon\Carbon::now()->format('F j, Y \\a\\t g:i A') }}</small>
        </div>
    </div>

    <script>
        function toggleEdit(fieldId) {
            const field = document.getElementById(fieldId);
            const isReadonly = field.readOnly;

            // Toggle readonly state
            field.readOnly = !isReadonly;

            // Badilisha style kuonyesha field inaweza kubadilishwa
            if (!isReadonly) {
                field.style.backgroundColor = '#fff';
                field.style.borderColor = '#3498db';
                field.focus();
            } else {
                field.style.backgroundColor = '#f8f9fa';
                field.style.borderColor = '#bdc3c7';
            }

            // Badilisha icon kati ya edit na save
            const editIcon = event.currentTarget.querySelector('i');
            if (field.readOnly) {
                editIcon.className = 'fas fa-pencil-alt';
                // Revert text to "Edit" ikiwa inatumika
                if (editIcon.nextSibling && editIcon.nextSibling.nodeType === 3) {
                    editIcon.nextSibling.textContent = ' Edit';
                }
            } else {
                editIcon.className = 'fas fa-save';
                // Change text to "Save" ikiwa inatumika
                if (editIcon.nextSibling && editIcon.nextSibling.nodeType === 3) {
                    editIcon.nextSibling.textContent = ' Save';
                }
            }
        }
    </script>
@endsection
