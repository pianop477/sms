@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1.5rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.75rem 2rem 0 rgba(58, 59, 69, 0.15);
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 800;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
            font-size: 1.75rem;
        }

        /* Statistics Cards Styling */
        .stat-card {
            border-radius: 15px;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            min-height: 140px;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }

        .stat-card .card-body {
            position: relative;
            z-index: 2;
            padding: 1.5rem;
        }

        .stat-card .card-icon {
            position: absolute;
            right: 20px;
            top: 20px;
            opacity: 0.2;
            font-size: 4rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .card-icon {
            opacity: 0.3;
            transform: scale(1.1);
        }

        .stat-card .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .stat-card .card-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0;
        }

        /* Gradient Backgrounds for Cards */
        .bg-teacher {
            background: linear-gradient(135deg, #e176a6 0%, #d04a88 100%);
        }

        .bg-parent {
            background: linear-gradient(135deg, #c84fe0 0%, #9c27b0 100%);
        }

        .bg-student {
            background: linear-gradient(135deg, #098ddf 0%, #0568a8 100%);
        }

        .bg-course {
            background: linear-gradient(135deg, #9fbc71 0%, #689f38 100%);
        }

        .bg-class {
            background: linear-gradient(135deg, #bf950a 0%, #ff9800 100%);
        }

        .bg-bus {
            background: linear-gradient(135deg, #329688 0%, #00796b 100%);
        }

        .bg-my-courses {
            background: linear-gradient(135deg, #b14fbe 0%, #8e24aa 100%);
        }

        .bg-attendance {
            background: linear-gradient(135deg, #098ddf 0%, #0568a8 100%);
        }

        /* Chart Container Styles */
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0.5rem 1.5rem rgba(58, 59, 69, 0.1);
            border: none;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .chart-container:hover {
            /* transform: translateY(-5px); */
            box-shadow: 0 0.75rem 2rem rgba(58, 59, 69, 0.15);
        }

        .chart-wrapper {
            flex: 1;
            min-height: 300px;
            position: relative;
            width: 100%;
        }

        .chart-wrapper canvas,
        .chart-wrapper .chart-canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .chart-header {
            border-bottom: 2px solid #f8f9fc;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .chart-title {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .chart-subtitle {
            color: #6c757d;
            font-size: 0.875rem;
        }

        /* Table Styles */
        .table-responsive {
            border-radius: 15px;
            overflow-x: auto !important;
            /* Horizontal scrolling only */
            overflow-y: visible !important;
            /* Allow vertical overflow for dropdowns */
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            position: relative;
            /* Important for z-index */
            z-index: 1;
        }

        .progress-table {
            background-color: white;
            border: none;
        }

        .progress-table thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2e59d9 100%);
            color: white;
        }

        .progress-table th {
            padding: 18px 12px;
            font-weight: 700;
            vertical-align: middle;
            border: none;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .progress-table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .progress-table tbody tr:hover td {
            background-color: #f8f9fc;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-buttons a,
        .action-buttons button {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .action-buttons a:hover,
        .action-buttons button:hover {
            transform: translateY(-2px);
        }

        /* Alert Styles */
        .alert-custom {
            border-radius: 10px;
            border: none;
            border-left: 5px solid;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .stat-card .card-value {
                font-size: 1.5rem;
            }

            .stat-card .card-icon {
                font-size: 2.5rem;
            }

            .chart-wrapper {
                min-height: 250px;
            }
        }

        .dropdown-menu {
            z-index: 9999 !important;
            /* Higher than table */
            position: absolute !important;
        }

        .chart-container .dropdown-menu {
            position: fixed !important;
            z-index: 1060 !important;
        }

        /* Ensure table doesn't clip dropdowns */
        .table-responsive {
            overflow-x: auto !important;
            overflow-y: visible !important;
        }
    </style>

    <div class="py-4">
        <!-- Contract Status Alert -->
        @if (Auth::user()->usertype == 3)
            <div class="row mb-4">
                <div class="col-12">
                    @if ($contract == null)
                        <div class="alert alert-danger alert-custom alert-dismissible fade show">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i> Contract Status:</strong> Not applied.
                            <a href="{{ route('contract.index') }}" class="alert-link fw-bold">Apply here</a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @else
                        @if ($contract->status == 'expired')
                            <div class="alert alert-danger alert-custom alert-dismissible fade show">
                                <strong><i class="fas fa-times-circle me-2"></i> Contract Status:</strong> Expired
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @elseif ($contract->status == 'rejected')
                            <div class="alert alert-secondary alert-custom alert-dismissible fade show">
                                <strong><i class="fas fa-times me-2"></i> Contract Status:</strong> Rejected |
                                <a href="{{ route('contract.index') }}" class="alert-link fw-bold">View details</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @elseif ($contract->status == 'approved' && $contract->end_date <= now()->addDays(30))
                            <div class="alert alert-warning alert-custom alert-dismissible fade show">
                                <strong><i class="fas fa-exclamation-circle me-2"></i> Contract Status:</strong> Expiring
                                soon ({{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }})
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @elseif ($contract->status == 'pending')
                            <div class="alert alert-info alert-custom alert-dismissible fade show">
                                <strong><i class="fas fa-clock me-2"></i> Contract Status:</strong> Pending |
                                <a href="{{ route('contract.index') }}" class="alert-link fw-bold">View details</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @else
                            <div class="alert alert-success alert-custom alert-dismissible fade show">
                                <strong><i class="fas fa-check-circle me-2"></i> Contract Status:</strong> Active (Expires:
                                {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}) |
                                <a href="{{ route('contract.index') }}" class="alert-link fw-bold">View contract</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                    @endif

                    <!-- TOD Duty Alert -->
                    @php
                        $today = \Carbon\Carbon::now()->format('Y-m-d');
                        $user = auth()->user();
                        $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
                        $myDuty = \App\Models\TodRoster::where('teacher_id', $teacher->id)
                            ->where('status', 'active')
                            ->first();
                    @endphp
                    @if ($myDuty)
                        <div class="alert alert-warning alert-custom"
                            style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left-color: #ffc107;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><i class="fas fa-bell me-2"></i> You are on duty this week!</strong>
                                    Please, collect school report to document today's activities.
                                </div>
                                <a href="{{ route('tod.report.create') }}" class="btn btn-warning btn-sm"
                                    onclick="return confirm('Are you sure you want to fill the daily report?')">
                                    <i class="fas fa-file-pen me-1"></i> Collect Report
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Head Teacher Dashboard -->
        @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2)
            <div class="row">
                <!-- Stats Cards for Head Teacher -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('Teachers.index') }}" class="text-decoration-none">
                                <div class="stat-card bg-teacher text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Teachers</div>
                                                <div class="card-value">
                                                    @if (count($teachers) > 99)
                                                        100+
                                                    @else
                                                        {{ count($teachers) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user-tie card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('Parents.index') }}" class="text-decoration-none">
                                <div class="stat-card bg-parent text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Parents</div>
                                                <div class="card-value">
                                                    @if (count($parents) > 1999)
                                                        2000+
                                                    @else
                                                        {{ count($parents) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user-friends card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('classes.list') }}" class="text-decoration-none">
                                <div class="stat-card bg-student text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Students</div>
                                                <div class="card-value">
                                                    @if (count($students) > 1999)
                                                        2000+
                                                    @else
                                                        {{ count($students) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user-graduate card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('courses.index') }}" class="text-decoration-none">
                                <div class="stat-card bg-course text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Open Courses</div>
                                                <div class="card-value">
                                                    @if (count($subjects) > 49)
                                                        50+
                                                    @else
                                                        {{ count($subjects) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="ti-book card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('Classes.index') }}" class="text-decoration-none">
                                <div class="stat-card bg-class text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Classes</div>
                                                <div class="card-value">
                                                    @if (count($classes) > 49)
                                                        50+
                                                    @else
                                                        {{ count($classes) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="ti-blackboard card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('Transportation.index') }}" class="text-decoration-none">
                                <div class="stat-card bg-bus text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">School Buses</div>
                                                <div class="card-value">
                                                    @if (count($buses) > 49)
                                                        50+
                                                    @else
                                                        {{ count($buses) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-bus card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-8 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-chart-bar me-2"></i> Student Registration by Class & Gender
                                    </h5>
                                    <p class="chart-subtitle">Distribution of students across classes</p>
                                </div>
                                <div class="chart-wrapper">
                                    <div id="studentChart" class="chart-canvas"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-chart-pie me-2"></i> Teacher Qualifications
                                    </h5>
                                    <p class="chart-subtitle">Educational background overview</p>
                                </div>
                                <div class="chart-wrapper">
                                    <div id="qualificationChart" class="chart-canvas"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Analytics -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-4 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-venus-mars me-2"></i> Student Gender Distribution
                                    </h5>
                                    <p class="chart-subtitle">Male vs Female students ratio</p>
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="genderChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-5 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-0 pb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="chart-title">
                                                <i class="fas fa-calendar-check me-2"></i> Today's Attendance Summary
                                            </h5>
                                            <p class="text-muted small mb-0">
                                                {{ \Carbon\Carbon::today()->format('l, d F Y') }}
                                            </p>
                                        </div>
                                        @if (isset($attendanceByClassData) && count($attendanceByClassData) > 0)
                                            <span class="badge bg-primary text-white">
                                                {{ count($attendanceByClassData) }} Streams
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body p-1">
                                    @if (isset($attendanceByClassData) && count($attendanceByClassData) > 0)
                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-hover mb-0 table-sm">
                                                <thead class="sticky-top" style="background: #f8f9fa; z-index: 1;">
                                                    <tr>
                                                        <th class="border-0 py-3 ps-4">Classes</th>
                                                        <th class="border-0 py-3 text-center">
                                                            <span class="text-success">Pres</span>
                                                        </th>
                                                        <th class="border-0 py-3 text-center">
                                                            <span class="text-danger">Abs</span>
                                                        </th>
                                                        <th class="border-0 py-3 text-center">
                                                            <span class="text-secondary">Perm</span>
                                                        </th>
                                                        <th class="border-0 py-3 text-center pe-4">Total</th>
                                                        <th class="border-0 py-3 text-center">Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalPresent = 0;
                                                        $totalAbsent = 0;
                                                        $totalPermission = 0;
                                                        $grandTotalStudents = 0;
                                                        $previousClass = null;

                                                        // Get total registered students for ALL classes in the school
                                                        $totalRegisteredInSchool = \App\Models\Student::where(
                                                            'status',
                                                            1,
                                                        )->count();

                                                        $classGroupColors = [
                                                            'A' => 'bg-success',
                                                            'B' => 'bg-dark',
                                                            'C' => 'bg-warning',
                                                            'D' => 'bg-danger',
                                                            'E' => 'bg-primary',
                                                            'F' => 'bg-secondary',
                                                            'G' => 'bg-info',
                                                        ];
                                                    @endphp

                                                    @foreach ($attendanceByClassData as $classData)
                                                        @php
                                                            // Debug: Check if class_id exists
                                                            $classId = $classData['class_id'] ?? null;
                                                            $stream = $classData['class_stream'] ?? null;

                                                            // Initialize registered students count
                                                            $registeredStudents = 0;

                                                            // Only query if class_id exists
                                                            if ($classId) {
                                                                try {
                                                                    $query = \App\Models\Student::where(
                                                                        'class_id',
                                                                        $classId,
                                                                    )->where('status', 1);

                                                                    if (!empty($stream)) {
                                                                        $query->where('group', $stream);
                                                                    }

                                                                    $registeredStudents = $query->count();
                                                                } catch (Exception $e) {
                                                                    // Fallback: use attendance data if query fails
                                                                    $registeredStudents =
                                                                        $classData['present'] +
                                                                        $classData['absent'] +
                                                                        $classData['permission'];
                                                                }
                                                            } else {
                                                                // If no class_id, use attendance data
                                                                $registeredStudents =
                                                                    $classData['present'] +
                                                                    $classData['absent'] +
                                                                    $classData['permission'];
                                                            }

                                                            // Calculate attendance rate for this class
                                                            // Use registeredStudents for CLASS rate
                                                            $attendanceRate =
                                                                $registeredStudents > 0
                                                                    ? round(
                                                                        ($classData['present'] / $registeredStudents) *
                                                                            100,
                                                                        2,
                                                                    )
                                                                    : 0;

                                                            // Update totals
                                                            $totalPresent += $classData['present'];
                                                            $totalAbsent += $classData['absent'];
                                                            $totalPermission += $classData['permission'];
                                                            $grandTotalStudents += $registeredStudents; // This is for classes with attendance only

                                                            // Determine if we need to show class header
                                                            $currentClass = $classData['original_class_name'] ?? '';
                                                            $showClassHeader = $previousClass !== $currentClass;
                                                            $previousClass = $currentClass;

                                                            // Determine badge color for stream
                                                            $streamBadgeClass = 'bg-secondary';
                                                            if (
                                                                $stream &&
                                                                isset($classGroupColors[strtoupper($stream)])
                                                            ) {
                                                                $streamBadgeClass =
                                                                    $classGroupColors[strtoupper($stream)];
                                                            }
                                                        @endphp

                                                        <tr class="border-bottom">
                                                            <td class="ps-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div>
                                                                        @if (!empty($stream))
                                                                            <div class="text-dark small">
                                                                                <strong>{{ strtoupper($classData['class_code'] ?? '') }}
                                                                                    -
                                                                                    <span
                                                                                        class="badge {{ $streamBadgeClass }} text-white">
                                                                                        {{ strtoupper($stream) }}
                                                                                    </span>
                                                                                </strong>
                                                                            </div>
                                                                        @else
                                                                            <strong
                                                                                class="text-dark">{{ $classData['class_name'] ?? '' }}</strong>
                                                                            <div class="text-muted small">
                                                                                {{ $classData['class_code'] ?? '' }}
                                                                                <br>
                                                                                Registered: {{ $registeredStudents }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="px-3 py-1 text-success">
                                                                    {{ $classData['present'] }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="px-3 py-1 text-danger">
                                                                    {{ $classData['absent'] }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="px-3 py-1 text-secondary">
                                                                    {{ $classData['permission'] }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center pe-4">
                                                                <strong>{{ $registeredStudents }}</strong>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="progress"
                                                                    style="height: 6px; width: 80px; margin: 0 auto;">
                                                                    <div class="progress-bar
                                                                    @if ($attendanceRate >= 90) bg-success
                                                                    @elseif($attendanceRate >= 70) bg-info
                                                                    @elseif($attendanceRate >= 50) bg-warning
                                                                    @else bg-danger @endif"
                                                                        role="progressbar"
                                                                        style="width: {{ min($attendanceRate, 100) }}%"
                                                                        aria-valuenow="{{ $attendanceRate }}"
                                                                        aria-valuemin="0" aria-valuemax="100">
                                                                    </div>
                                                                </div>
                                                                <small
                                                                    class="text-muted d-block mt-1">{{ $attendanceRate }}%</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                                @if (count($attendanceByClassData) > 1)
                                                    @php
                                                        // Overall rate for classes with attendance only
                                                        $overallRate =
                                                            $grandTotalStudents > 0
                                                                ? round(($totalPresent / $grandTotalStudents) * 100, 2)
                                                                : 0;

                                                        // School-wide rate (for display if needed)
                                                        $schoolWideRate =
                                                            $totalRegisteredInSchool > 0
                                                                ? round(
                                                                    ($totalPresent / $totalRegisteredInSchool) * 100,
                                                                    2,
                                                                )
                                                                : 0;
                                                    @endphp
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <th class="ps-4 py-3 border-top">
                                                                <strong>Total</strong>
                                                            </th>
                                                            <th class="text-center py-2 border-top">
                                                                <span
                                                                    class="badge bg-success text-white px-3">{{ $totalPresent }}</span>
                                                            </th>
                                                            <th class="text-center py-2 border-top">
                                                                <span
                                                                    class="badge bg-danger text-white px-3">{{ $totalAbsent }}</span>
                                                            </th>
                                                            <th class="text-center py-2 border-top">
                                                                <span
                                                                    class="badge bg-secondary text-white px-3">{{ $totalPermission }}</span>
                                                            </th>
                                                            <th class="text-center pe-4 py-2 border-top">
                                                                <strong
                                                                    class="text-dark">{{ $grandTotalStudents }}</strong>
                                                            </th>
                                                            <th class="text-center py-2 border-top">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center">
                                                                    <div class="progress"
                                                                        style="height: 8px; width: 100px;">
                                                                        @php
                                                                            $generalRate = round(($totalPresent / $totalRegisteredInSchool) * 100, 2);
                                                                        @endphp
                                                                        <div class="progress-bar
                                                                        @if ($generalRate >= 90) bg-success
                                                                        @elseif($generalRate >= 70) bg-info
                                                                        @elseif($generalRate >= 50) bg-warning
                                                                        @else bg-danger @endif"
                                                                            role="progressbar"
                                                                            style="width: {{ min($generalRate, 100) }}%">
                                                                        </div>
                                                                    </div>
                                                                    <strong
                                                                        class="ms-2
                                                                        @if ($generalRate >= 90) text-success
                                                                        @elseif($generalRate >= 70) text-info
                                                                        @elseif($generalRate >= 50) text-warning
                                                                        @else text-danger @endif">
                                                                        {{ $generalRate }}%
                                                                    </strong>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>

                                            {{-- Summary Stats Cards - Use $totalRegisteredInSchool for school-wide percentage --}}
                                            <div class="row g-2 mt-1 mx-2">
                                                <div class="col-4">
                                                    <div class="border rounded p-1 text-center">
                                                        <small class="text-success">Present</small>
                                                        @if ($totalRegisteredInSchool > 0)
                                                            <div class="small text-success">
                                                                {{ round(($totalPresent / $totalRegisteredInSchool) * 100, 2) }}%
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border rounded p-1 text-center">
                                                        <small class="text-danger">Absent</small>
                                                        @if ($totalRegisteredInSchool > 0)
                                                            <div class="small text-danger">
                                                                {{ round(($totalAbsent / $totalRegisteredInSchool) * 100, 2) }}%
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border rounded p-1 text-center">
                                                        <small class="text-secondary">Permission</small>
                                                        @if ($totalRegisteredInSchool > 0)
                                                            <div class="small text-secondary">
                                                                {{ round(($totalPermission / $totalRegisteredInSchool) * 100, 2) }}%
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="fas fa-calendar-times fa-3x text-muted"></i>
                                            </div>
                                            <h5 class="text-muted mb-2">No Attendance Today</h5>
                                            <p class="text-muted small">
                                                Attendance records will appear here once submitted by teachers.
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                @if (isset($attendanceByClassData) && count($attendanceByClassData) > 0)
                                    <div class="card-footer bg-white border-0 pt-0">
                                        <div class="text-end">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Updated: {{ now()->format('h:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Stats Tables -->
                        <div class="col-xl-3 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-table me-2"></i> Quick Overview
                                    </h5>
                                    <p class="chart-subtitle">Registration statistics</p>
                                </div>
                                <div class="row">
                                    <!-- Students by Class -->
                                    <div class="col-12 mb-3">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-3">
                                                <h6 class="card-title text-center mb-3 text-primary">Students by Class</h6>
                                                @if ($studentsByClass->isEmpty())
                                                    <p class="text-center text-muted mb-0">No records available</p>
                                                @else
                                                    <div class="table-responsive">
                                                        <table class="table table-sm dashboard-table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Class</th>
                                                                    <th class="text-end">Count</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($studentsByClass as $class)
                                                                    <tr>
                                                                        <td class="fw-semibold text-uppercase">
                                                                            {{ $class->class_code }}</td>
                                                                        <td class="text-end">
                                                                            {{ strtoupper($class->student_count) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Teachers by Gender -->
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-3">
                                                <h6 class="card-title text-center mb-3 text-primary">Teachers by Gender
                                                </h6>
                                                @if ($teacherByGender->isEmpty())
                                                    <p class="text-center text-muted mb-0">No records available</p>
                                                @else
                                                    <div class="table-responsive">
                                                        <table class="table table-sm dashboard-table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Gender</th>
                                                                    <th class="text-end">Count</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($teacherByGender as $teacher)
                                                                    <tr>
                                                                        <td class="fw-semibold text-capitalize">
                                                                            {{ ucwords(strtolower($teacher->gender)) }}
                                                                        </td>
                                                                        <td class="text-end">{{ $teacher->teacher_count }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Academic Teacher Dashboard -->
        @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 3)
            <div class="row">
                <!-- Stats Cards for Academic Teacher -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('Teachers.index') }}" class="text-decoration-none">
                                <div class="stat-card bg-teacher text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Teachers</div>
                                                <div class="card-value">
                                                    @if (count($teachers) > 99)
                                                        100+
                                                    @else
                                                        {{ count($teachers) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user-tie card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('classes.list') }}" class="text-decoration-none">
                                <div class="stat-card bg-student text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Students</div>
                                                <div class="card-value">
                                                    @if (count($students) > 1999)
                                                        2000+
                                                    @else
                                                        {{ count($students) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user-graduate card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('courses.index') }}" class="text-decoration-none">
                                <div class="stat-card bg-course text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Open Courses</div>
                                                <div class="card-value">
                                                    @if (count($subjects) > 49)
                                                        50+
                                                    @else
                                                        {{ count($subjects) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="ti-book card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('Classes.index') }}" class="text-decoration-none">
                                <div class="stat-card bg-class text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Classes</div>
                                                <div class="card-value">
                                                    @if (count($classes) > 49)
                                                        50+
                                                    @else
                                                        {{ count($classes) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="ti-blackboard card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="stat-card bg-my-courses text-white">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="card-title">My Courses</div>
                                            <div class="card-value">
                                                {{ $courses->where('status', 1)->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="ti-book card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teaching Subjects and Charts -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-6 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-chart-bar me-2"></i> Student Registration
                                    </h5>
                                    <p class="chart-subtitle">Distribution by class and gender</p>
                                </div>
                                <div class="chart-wrapper">
                                    <div id="studentChart" class="chart-canvas"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-book me-2"></i> My Teaching Subjects
                                    </h5>
                                    <p class="chart-subtitle">Assigned courses and classes</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover progress-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Class</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($courses as $course)
                                                <tr>
                                                    <td class="fw-semibold text-capitalize">
                                                        {{ ucwords(strtolower($course->course_name)) }}</td>
                                                    <td class="fw-bold text-info text-uppercase">
                                                        {{ strtoupper($course->class_code) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <style>
                                                            .btn-score {
                                                                background: var(--success-color);
                                                                color: white;
                                                            }

                                                            .btn-result {
                                                                background: var(--secondary-color);
                                                                color: white;
                                                            }
                                                        </style>
                                                        @if ($course->status == 1)
                                                            <ul class="d-flex justify-content-center">
                                                                <li class="mr-3">
                                                                    <a href="{{ route('score.prepare.form', ['id' => Hashids::encode($course->id)]) }}"
                                                                        class="btn btn-xs btn-score"
                                                                        style="border-radius: 10px;">
                                                                        <i class="fas fa-file-edit"></i> Score
                                                                    </a>
                                                                </li>
                                                                <li class="">
                                                                    <a href="{{ route('results_byCourse', ['id' => Hashids::encode($course->id)]) }}"
                                                                        class="btn btn-xs btn-result"
                                                                        style="border-radius: 10px">
                                                                        <i class="fas fa-file-pdf"></i> Results
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        @else
                                                            <span class="badge bg-danger">Blocked</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted">
                                                        <i class="fas fa-book fa-2x mb-3 d-block opacity-50"></i>
                                                        No subjects assigned to you
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Additional Analytics -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-4 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-venus-mars me-2"></i> Student Gender Distribution
                                    </h5>
                                    <p class="chart-subtitle">Male vs Female students ratio</p>
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="genderChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-0 pb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="chart-title">
                                                <i class="fas fa-calendar-check me-2"></i> Today's Attendance Summary
                                            </h5>
                                            <p class="text-muted small mb-0">
                                                {{ \Carbon\Carbon::today()->format('l, d F Y') }}
                                            </p>
                                        </div>
                                        @if (isset($attendanceByClassData) && count($attendanceByClassData) > 0)
                                            <span class="badge bg-primary text-white">
                                                {{ count($attendanceByClassData) }} Streams
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body p-1">
                                    @if (isset($attendanceByClassData) && count($attendanceByClassData) > 0)
                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-hover mb-0 table-sm">
                                                <thead class="sticky-top" style="background: #f8f9fa; z-index: 1;">
                                                    <tr>
                                                        <th class="border-0 py-3 ps-4">Classes</th>
                                                        <th class="border-0 py-3 text-center">
                                                            <span class="text-success">Pres</span>
                                                        </th>
                                                        <th class="border-0 py-3 text-center">
                                                            <span class="text-danger">Abs</span>
                                                        </th>
                                                        <th class="border-0 py-3 text-center">
                                                            <span class="text-secondary">Perm</span>
                                                        </th>
                                                        <th class="border-0 py-3 text-center pe-4">Total</th>
                                                        <th class="border-0 py-3 text-center">Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalPresent = 0;
                                                        $totalAbsent = 0;
                                                        $totalPermission = 0;
                                                        $grandTotalStudents = 0;
                                                        $previousClass = null;

                                                        // Get total registered students for ALL classes in the school
                                                        $totalRegisteredInSchool = \App\Models\Student::where(
                                                            'status',
                                                            1,
                                                        )->count();

                                                        $classGroupColors = [
                                                            'A' => 'bg-success',
                                                            'B' => 'bg-dark',
                                                            'C' => 'bg-warning',
                                                            'D' => 'bg-danger',
                                                            'E' => 'bg-primary',
                                                            'F' => 'bg-secondary',
                                                            'G' => 'bg-info',
                                                        ];
                                                    @endphp

                                                    @foreach ($attendanceByClassData as $classData)
                                                        @php
                                                            // Debug: Check if class_id exists
                                                            $classId = $classData['class_id'] ?? null;
                                                            $stream = $classData['class_stream'] ?? null;

                                                            // Initialize registered students count
                                                            $registeredStudents = 0;

                                                            // Only query if class_id exists
                                                            if ($classId) {
                                                                try {
                                                                    $query = \App\Models\Student::where(
                                                                        'class_id',
                                                                        $classId,
                                                                    )->where('status', 1);

                                                                    if (!empty($stream)) {
                                                                        $query->where('group', $stream);
                                                                    }

                                                                    $registeredStudents = $query->count();
                                                                } catch (Exception $e) {
                                                                    // Fallback: use attendance data if query fails
                                                                    $registeredStudents =
                                                                        $classData['present'] +
                                                                        $classData['absent'] +
                                                                        $classData['permission'];
                                                                }
                                                            } else {
                                                                // If no class_id, use attendance data
                                                                $registeredStudents =
                                                                    $classData['present'] +
                                                                    $classData['absent'] +
                                                                    $classData['permission'];
                                                            }

                                                            // Calculate attendance rate for this class
                                                            // Use registeredStudents for CLASS rate
                                                            $attendanceRate =
                                                                $registeredStudents > 0
                                                                    ? round(
                                                                        ($classData['present'] / $registeredStudents) *
                                                                            100,
                                                                        2,
                                                                    )
                                                                    : 0;

                                                            // Update totals
                                                            $totalPresent += $classData['present'];
                                                            $totalAbsent += $classData['absent'];
                                                            $totalPermission += $classData['permission'];
                                                            $grandTotalStudents += $registeredStudents; // This is for classes with attendance only

                                                            // Determine if we need to show class header
                                                            $currentClass = $classData['original_class_name'] ?? '';
                                                            $showClassHeader = $previousClass !== $currentClass;
                                                            $previousClass = $currentClass;

                                                            // Determine badge color for stream
                                                            $streamBadgeClass = 'bg-secondary';
                                                            if (
                                                                $stream &&
                                                                isset($classGroupColors[strtoupper($stream)])
                                                            ) {
                                                                $streamBadgeClass =
                                                                    $classGroupColors[strtoupper($stream)];
                                                            }
                                                        @endphp

                                                        <tr class="border-bottom">
                                                            <td class="ps-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div>
                                                                        @if (!empty($stream))
                                                                            <div class="text-dark small">
                                                                                <strong>{{ strtoupper($classData['class_code'] ?? '') }}
                                                                                    -
                                                                                    <span
                                                                                        class="badge {{ $streamBadgeClass }} text-white">
                                                                                        {{ strtoupper($stream) }}
                                                                                    </span>
                                                                                </strong>
                                                                            </div>
                                                                        @else
                                                                            <strong
                                                                                class="text-dark">{{ $classData['class_name'] ?? '' }}</strong>
                                                                            <div class="text-muted small">
                                                                                {{ $classData['class_code'] ?? '' }}
                                                                                <br>
                                                                                Registered: {{ $registeredStudents }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="px-3 py-1 text-success">
                                                                    {{ $classData['present'] }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="px-3 py-1 text-danger">
                                                                    {{ $classData['absent'] }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="px-3 py-1 text-secondary">
                                                                    {{ $classData['permission'] }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center pe-4">
                                                                <strong>{{ $registeredStudents }}</strong>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="progress"
                                                                    style="height: 6px; width: 80px; margin: 0 auto;">
                                                                    <div class="progress-bar
                                                                    @if ($attendanceRate >= 90) bg-success
                                                                    @elseif($attendanceRate >= 70) bg-info
                                                                    @elseif($attendanceRate >= 50) bg-warning
                                                                    @else bg-danger @endif"
                                                                        role="progressbar"
                                                                        style="width: {{ min($attendanceRate, 100) }}%"
                                                                        aria-valuenow="{{ $attendanceRate }}"
                                                                        aria-valuemin="0" aria-valuemax="100">
                                                                    </div>
                                                                </div>
                                                                <small
                                                                    class="text-muted d-block mt-1">{{ $attendanceRate }}%</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                                @if (count($attendanceByClassData) > 1)
                                                    @php
                                                        // Overall rate for classes with attendance only
                                                        $overallRate =
                                                            $grandTotalStudents > 0
                                                                ? round(($totalPresent / $grandTotalStudents) * 100, 2)
                                                                : 0;

                                                        // School-wide rate (for display if needed)
                                                        $schoolWideRate =
                                                            $totalRegisteredInSchool > 0
                                                                ? round(
                                                                    ($totalPresent / $totalRegisteredInSchool) * 100,
                                                                    2,
                                                                )
                                                                : 0;
                                                    @endphp
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <th class="ps-4 py-3 border-top">
                                                                <strong>Total</strong>
                                                            </th>
                                                            <th class="text-center py-2 border-top">
                                                                <span
                                                                    class="badge bg-success text-white px-3">{{ $totalPresent }}</span>
                                                            </th>
                                                            <th class="text-center py-2 border-top">
                                                                <span
                                                                    class="badge bg-danger text-white px-3">{{ $totalAbsent }}</span>
                                                            </th>
                                                            <th class="text-center py-2 border-top">
                                                                <span
                                                                    class="badge bg-secondary text-white px-3">{{ $totalPermission }}</span>
                                                            </th>
                                                            <th class="text-center pe-4 py-2 border-top">
                                                                <strong
                                                                    class="text-dark">{{ $grandTotalStudents }}</strong>
                                                            </th>
                                                            <th class="text-center py-2 border-top">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center">
                                                                    @php
                                                                        $generalRate = round(($totalPresent / $totalRegisteredInSchool) * 100, 2);
                                                                    @endphp
                                                                    <div class="progress"
                                                                        style="height: 8px; width: 100px;">
                                                                        <div class="progress-bar
                                                                        @if ($generalRate >= 90) bg-success
                                                                        @elseif($generalRate >= 70) bg-info
                                                                        @elseif($generalRate >= 50) bg-warning
                                                                        @else bg-danger @endif"
                                                                            role="progressbar"
                                                                            style="width: {{ min($generalRate, 100) }}%">
                                                                        </div>
                                                                    </div>

                                                                    <strong
                                                                        class="ms-2
                                                                        @if ($generalRate >= 90) text-success
                                                                        @elseif($generalRate >= 70) text-info
                                                                        @elseif($generalRate >= 50) text-warning
                                                                        @else text-danger @endif">
                                                                        {{ $generalRate }}%
                                                                    </strong>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>

                                            {{-- Summary Stats Cards - Use $totalRegisteredInSchool for school-wide percentage --}}
                                            <div class="row g-2 mt-1 mx-2">
                                                <div class="col-4">
                                                    <div class="border rounded p-1 text-center">
                                                        <small class="text-success">Present</small>
                                                        @if ($totalRegisteredInSchool > 0)
                                                            <div class="small text-success">
                                                                {{ round(($totalPresent / $totalRegisteredInSchool) * 100, 2) }}%
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border rounded p-1 text-center">
                                                        <small class="text-danger">Absent</small>
                                                        @if ($totalRegisteredInSchool > 0)
                                                            <div class="small text-danger">
                                                                {{ round(($totalAbsent / $totalRegisteredInSchool) * 100, 2) }}%
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border rounded p-1 text-center">
                                                        <small class="text-secondary">Permission</small>
                                                        @if ($totalRegisteredInSchool > 0)
                                                            <div class="small text-secondary">
                                                                {{ round(($totalPermission / $totalRegisteredInSchool) * 100, 2) }}%
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="fas fa-calendar-times fa-3x text-muted"></i>
                                            </div>
                                            <h5 class="text-muted mb-2">No Attendance Today</h5>
                                            <p class="text-muted small">
                                                Attendance records will appear here once submitted by teachers.
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                @if (isset($attendanceByClassData) && count($attendanceByClassData) > 0)
                                    <div class="card-footer bg-white border-0 pt-0">
                                        <div class="text-end">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Updated: {{ now()->format('h:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Stats Tables -->
                        <div class="col-xl-3 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-table me-2"></i> Quick Overview
                                    </h5>
                                    <p class="chart-subtitle">Registration statistics</p>
                                </div>
                                <div class="row">
                                    <!-- Students by Class -->
                                    <div class="col-12 mb-3">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-3">
                                                <h6 class="card-title text-center mb-3 text-primary">Students by Class</h6>
                                                @if ($studentsByClass->isEmpty())
                                                    <p class="text-center text-muted mb-0">No records available</p>
                                                @else
                                                    <div class="table-responsive">
                                                        <table class="table table-sm dashboard-table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Class</th>
                                                                    <th class="text-end">Count</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($studentsByClass as $class)
                                                                    <tr>
                                                                        <td class="fw-semibold text-uppercase">
                                                                            {{ $class->class_code }}</td>
                                                                        <td class="text-end">
                                                                            {{ strtoupper($class->student_count) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Teachers by Gender -->
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-3">
                                                <h6 class="card-title text-center mb-3 text-primary">Teachers by Gender
                                                </h6>
                                                @if ($teacherByGender->isEmpty())
                                                    <p class="text-center text-muted mb-0">No records available</p>
                                                @else
                                                    <div class="table-responsive">
                                                        <table class="table table-sm dashboard-table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Gender</th>
                                                                    <th class="text-end">Count</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($teacherByGender as $teacher)
                                                                    <tr>
                                                                        <td class="fw-semibold text-capitalize">
                                                                            {{ ucwords(strtolower($teacher->gender)) }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            {{ $teacher->teacher_count }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Class Teacher Dashboard -->
        @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 4)
            <div class="row">
                <!-- Stats Cards for Class Teacher -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <a href="{{ route('get.student.list', ['class' => Hashids::encode($myClass->first()->id)]) }}"
                                class="text-decoration-none">
                                <div class="stat-card bg-attendance text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">My Classes</div>
                                                <div class="card-value">{{ $myClass->count() }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user-check card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="stat-card bg-student text-white">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="card-title">Students</div>
                                            @foreach ($classData as $data)
                                                <div class="card-value">{{ $data['maleCount'] + $data['femaleCount'] }}
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="stat-card bg-my-courses text-white">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="card-title">My Courses</div>
                                            <div class="card-value">{{ $courses->where('status', 1)->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="ti-book card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Class and Course Information -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-6 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-users me-2"></i> My Attendance Class
                                    </h5>
                                    <p class="chart-subtitle">Classes assigned for attendance management</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover progress-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Class</th>
                                                <th>Stream</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($myClass as $class)
                                                <tr>
                                                    <td class="fw-bold text-uppercase">{{ $class->class_name }}</td>
                                                    <td class="text-center text-uppercase">{{ $class->group }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('attendance.get.form', ['class' => Hashids::encode($class->id)]) }}"
                                                            class="btn btn-info btn-sm" data-bs-toggle="tooltip"
                                                            title="Take Attendance">
                                                            <i class="ti-settings me-1"></i> Report
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-book me-2"></i> My Teaching Subjects
                                    </h5>
                                    <p class="chart-subtitle">Assigned courses and classes</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover progress-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Class</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($courses as $course)
                                                <tr>
                                                    <td class="fw-semibold text-capitalize">
                                                        {{ ucwords(strtolower($course->course_name)) }}</td>
                                                    <td class="fw-bold text-info text-uppercase">
                                                        {{ strtoupper($course->class_code) }}
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($course->status == 1)
                                                            <style>
                                                                .btn-score {
                                                                    background: var(--success-color);
                                                                    color: white;
                                                                }

                                                                .btn-result {
                                                                    background: var(--secondary-color);
                                                                    color: white;
                                                                }
                                                            </style>
                                                            <ul class="d-flex justify-content-center">
                                                                <li class="mr-3">
                                                                    <a href="{{ route('score.prepare.form', ['id' => Hashids::encode($course->id)]) }}"
                                                                        class="btn btn-xs btn-score"
                                                                        style="border-radius: 10px;">
                                                                        <i class="fas fa-file-edit"></i> Score
                                                                    </a>
                                                                </li>
                                                                <li class="">
                                                                    <a href="{{ route('results_byCourse', ['id' => Hashids::encode($course->id)]) }}"
                                                                        class="btn btn-xs btn-result"
                                                                        style="border-radius: 10px">
                                                                        <i class="fas fa-file-pdf"></i> Results
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        @else
                                                            <span class="badge bg-danger">Blocked</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted">
                                                        <i class="fas fa-book fa-2x mb-3 d-block opacity-50"></i>
                                                        No subjects assigned to you
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts for Class Teacher -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-6 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-venus-mars me-2"></i> Student Gender Distribution
                                    </h5>
                                    <p class="chart-subtitle">Male vs Female students in your class</p>
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="genderDistributionChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 mb-4">
                            <div class="chart-container">
                                <div class="chart-header">
                                    <h5 class="chart-title">
                                        <i class="fas fa-calendar-check me-2"></i> Today's Attendance
                                        @if (isset($classTeacherAttendance['has_data']) && $classTeacherAttendance['has_data'])
                                            <span class="badge bg-success text-white ms-2">Live</span>
                                        @else
                                            <span class="badge bg-secondary ms-2 text-white">No Data</span>
                                        @endif
                                    </h5>
                                    <p class="chart-subtitle">{{ \Carbon\Carbon::today()->format('d-m-Y') }}</p>
                                </div>
                                <div class="chart-wrapper" style="min-height: 250px;">
                                    @if (isset($classTeacherAttendance['has_data']) && $classTeacherAttendance['has_data'])
                                        <canvas id="attendanceChart"></canvas>
                                    @else
                                        <div
                                            class="no-data-placeholder d-flex flex-column align-items-center justify-content-center h-100 py-5">
                                            <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                                            <p class="text-muted text-center mb-0">No attendance records for today</p>
                                            <small class="text-muted">Collect attendance now</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Normal Teacher Dashboard -->
        @if (Auth::user()->usertype == 3 &&
                Auth::user()->teacher->role_id != 2 &&
                Auth::user()->teacher->role_id != 3 &&
                Auth::user()->teacher->role_id != 4)
            <div class="row">
                <!-- Stats Cards for Normal Teacher -->
                <div class="col-lg-12 mb-4">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="stat-card bg-my-courses text-white">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="card-title">My Courses</div>
                                            <div class="card-value">{{ $courses->where('status', 1)->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="ti-book card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teaching Subjects -->
                <div class="col-lg-12 mb-4">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="fas fa-book me-2"></i> My Teaching Subjects
                            </h5>
                            <p class="chart-subtitle">Assigned courses and classes</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover progress-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Class</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($courses as $course)
                                        <tr>
                                            <td class="fw-semibold text-capitalize">
                                                {{ ucwords(strtolower($course->course_name)) }}</td>
                                            <td class="fw-bold text-info text-uppercase">
                                                {{ strtoupper($course->class_code) }}</td>
                                            <td class="text-center">
                                                @if ($course->status == 1)
                                                    <ul class="d-flex justify-content-center">
                                                        <style>
                                                            .btn-score {
                                                                background: var(--success-color);
                                                                color: white;
                                                            }

                                                            .btn-result {
                                                                background: var(--secondary-color);
                                                                color: white;
                                                            }
                                                        </style>
                                                        <li class="mr-3">
                                                            <a href="{{ route('score.prepare.form', ['id' => Hashids::encode($course->id)]) }}"
                                                                class="btn btn-xs btn-score" style="border-radius: 10px;">
                                                                <i class="fas fa-file-edit"></i> Score
                                                            </a>
                                                        </li>
                                                        <li class="">
                                                            <a href="{{ route('results_byCourse', ['id' => Hashids::encode($course->id)]) }}"
                                                                class="btn btn-xs btn-result" style="border-radius: 10px">
                                                                <i class="fas fa-file-pdf"></i> Results
                                                            </a>
                                                        </li>
                                                    </ul>
                                                @else
                                                    <span class="badge bg-danger">Blocked</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                <i class="fas fa-book fa-2x mb-3 d-block opacity-50"></i>
                                                No subjects assigned to you
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });


            // Student Registration Chart (ECharts) - For Head Teacher and Academic Teacher
            const studentChartDom = document.getElementById('studentChart');
            if (studentChartDom) {
                const myChart = echarts.init(studentChartDom);
                const chartData = @json($chartData);

                const groupedData = {};
                chartData.forEach(item => {
                    const classCode = item.category.split(' (')[0];
                    const gender = item.category.includes('Male') ? 'Male' : 'Female';
                    if (!groupedData[classCode]) {
                        groupedData[classCode] = {
                            Male: 0,
                            Female: 0
                        };
                    }
                    groupedData[classCode][gender] = item.value;
                });

                const classCodes = Object.keys(groupedData);
                const maleData = classCodes.map(classCode => groupedData[classCode].Male);
                const femaleData = classCodes.map(classCode => groupedData[classCode].Female);

                const option = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
                    legend: {
                        data: ['Male', 'Female'],
                        bottom: 10
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '15%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        data: classCodes,
                        axisLabel: {
                            rotate: 45
                        }
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [{
                            name: 'Male',
                            type: 'bar',
                            stack: 'total',
                            emphasis: {
                                focus: 'series'
                            },
                            data: maleData,
                            itemStyle: {
                                color: '#4e73df'
                            }
                        },
                        {
                            name: 'Female',
                            type: 'bar',
                            stack: 'total',
                            emphasis: {
                                focus: 'series'
                            },
                            data: femaleData,
                            itemStyle: {
                                color: '#e74a3b'
                            }
                        }
                    ]
                };
                myChart.setOption(option);
            }

            // Teacher Qualifications Chart (amCharts) - For Head Teacher
            const qualificationChartDom = document.getElementById('qualificationChart');
            if (qualificationChartDom) {
                am5.ready(function() {
                    const root = am5.Root.new("qualificationChart");
                    root.setThemes([am5themes_Animated.new(root)]);

                    const chart = root.container.children.push(
                        am5percent.PieChart.new(root, {
                            layout: root.verticalLayout
                        })
                    );

                    const series = chart.series.push(
                        am5percent.PieSeries.new(root, {
                            valueField: "value",
                            categoryField: "category"
                        })
                    );

                    series.data.setAll([{
                            category: "Masters",
                            value: {{ $qualificationData['masters'] }}
                        },
                        {
                            category: "Degree",
                            value: {{ $qualificationData['bachelor'] }}
                        },
                        {
                            category: "Diploma",
                            value: {{ $qualificationData['diploma'] }}
                        },
                        {
                            category: "Certificate",
                            value: {{ $qualificationData['certificate'] }}
                        }
                    ]);

                    chart.children.push(am5.Legend.new(root, {}));
                    series.appear(1000, 100);
                });
            }

            // Gender Distribution Chart - For Head Teacher and Academic Teacher
            const genderCtx = document.getElementById('genderChart');
            if (genderCtx) {
                const totalMaleStudents = @json($totalMaleStudents);
                const totalFemaleStudents = @json($totalFemaleStudents);

                new Chart(genderCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Male Students', 'Female Students'],
                        datasets: [{
                            data: [totalMaleStudents, totalFemaleStudents],
                            backgroundColor: ['#4e73df', '#e74a3b'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }

            // Gender Distribution Chart - For Class Teacher
            const genderDistributionCtx = document.getElementById('genderDistributionChart');
            if (genderDistributionCtx) {
                const maleCount = @json($data['maleCount'] ?? 0);
                const femaleCount = @json($data['femaleCount'] ?? 0);

                new Chart(genderDistributionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Male Students', 'Female Students'],
                        datasets: [{
                            data: [maleCount, femaleCount],
                            backgroundColor: ['#4e73df', '#e74a3b'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }

            // Attendance Chart - For Class Teacher
            const classAttendanceCtx = document.getElementById('attendanceChart');
            if (classAttendanceCtx) {
                const attendanceData = @json($classTeacherAttendance ?? []);

                // ANGALIA KWA UMAKINI - tumia flag ya has_data
                if (attendanceData && attendanceData.has_data) {
                    const malePresent = attendanceData.male.present || 0;
                    const femalePresent = attendanceData.female.present || 0;
                    const maleAbsent = attendanceData.male.absent || 0;
                    const femaleAbsent = attendanceData.female.absent || 0;
                    const malePermission = attendanceData.male.permission || 0;
                    const femalePermission = attendanceData.female.permission || 0;

                    const totalPresent = malePresent + femalePresent;
                    const totalAbsent = maleAbsent + femaleAbsent;
                    const totalPermission = malePermission + femalePermission;

                    // Create chart with better visibility
                    const attendanceChart = new Chart(classAttendanceCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Present', 'Absent', 'Permission'],
                            datasets: [{
                                data: [totalPresent, totalAbsent, totalPermission],
                                backgroundColor: [
                                    'rgba(28, 200, 138, 0.9)', // Present - Green
                                    'rgba(231, 74, 59, 0.9)', // Absent - Red
                                    'rgba(246, 194, 62, 0.9)' // Permission - Yellow
                                ],
                                borderColor: [
                                    'rgba(28, 200, 138, 1)',
                                    'rgba(231, 74, 59, 1)',
                                    'rgba(246, 194, 62, 1)'
                                ],
                                borderWidth: 2,
                                // hoverOffset: 15
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            aspectRatio: 2, // <-- HII ITASAIDIA KUONEKANA
                            layout: {
                                padding: {
                                    top: 20,
                                    bottom: 20
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b,
                                                0);
                                            const percentage = total > 0 ? ((context.parsed / total) *
                                                100).toFixed(1) : 0;
                                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            cutout: '55%',
                            animation: {
                                animateScale: true,
                                animateRotate: true
                            }
                        }
                    });

                    // FIX: Force chart to be visible immediately
                    setTimeout(() => {
                        attendanceChart.update();
                    }, 100);
                } else {
                    // Hide canvas if no data
                    classAttendanceCtx.style.display = 'none';
                }
            }

            // Authorization check
            @if (Auth::user()->usertype != 3)
                window.location.href = '/error-page';
            @endif
        });
    </script>

    <style>
        @media (max-width: 768px) {
            .table-responsive {
                border: 0;
            }

            .table-responsive thead {
                display: none;
            }

            .table-responsive tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            .table-responsive td {
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                padding: 10px 15px;
                position: relative;
                border-bottom: 1px solid #f1f1f1;
                width: 100%;
            }

            .table-responsive td::before {
                display: none;
            }

            .btn-group {
                display: flex;
                gap: 5px;
                justify-content: center;
                width: 100%;
            }

            .table-responsive td.text-center {
                justify-content: center;
            }
        }
    </style>
@endsection
