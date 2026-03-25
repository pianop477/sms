@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #2e59d9;
            --secondary: #6f42c1;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }

        body {
            background: #f8f9fc;
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        }

        /* Modern Card Design */
        .modern-card {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(78, 115, 223, 0.15);
        }

        /* Service Card Premium */
        .service-card-premium {
            background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
            border: 1px solid rgba(78, 115, 223, 0.1);
            position: relative;
            isolation: isolate;
        }

        .service-card-premium::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(78, 115, 223, 0.08), transparent 70%);
            z-index: -1;
        }

        .service-icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 10px 20px rgba(78, 115, 223, 0.3);
        }

        /* Countdown Timer Premium */
        .countdown-premium {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .countdown-item {
            flex: 1;
            min-width: 85px;
            background: white;
            border-radius: 16px;
            padding: 16px 8px;
            text-align: center;
            position: relative;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        .countdown-item:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 15px 30px rgba(78, 115, 223, 0.1);
        }

        .countdown-item .number {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #2c3e50, #1e293b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
            font-family: 'Poppins', monospace;
        }

        .countdown-item .label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            opacity: 0.8;
        }

        .countdown-item::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20%;
            right: 20%;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            border-radius: 3px;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .countdown-item:hover::after {
            transform: scaleX(1);
        }

        /* Stats Cards Premium */
        .stat-card-premium {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            min-height: 140px;
        }

        .stat-card-premium::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card-premium:hover::before {
            opacity: 1;
        }

        .stat-card-premium .card-body {
            position: relative;
            z-index: 2;
            padding: 1.8rem 1.5rem;
        }

        .stat-card-premium .card-icon {
            position: absolute;
            right: 20px;
            bottom: 20px;
            font-size: 4.5rem;
            opacity: 0.15;
            transition: all 0.5s ease;
            color: white;
        }

        .stat-card-premium:hover .card-icon {
            transform: scale(1.2) rotate(5deg);
            opacity: 0.25;
        }

        .stat-card-premium .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.9);
        }

        .stat-card-premium .card-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            line-height: 1.2;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Gradient variations */
        .bg-gradient-teacher {
            background: linear-gradient(145deg, #e176a6, #b2457a);
        }

        .bg-gradient-parent {
            background: linear-gradient(145deg, #c84fe0, #8e24aa);
        }

        .bg-gradient-student {
            background: linear-gradient(145deg, #098ddf, #0568a8);
        }

        .bg-gradient-course {
            background: linear-gradient(145deg, #9fbc71, #558b2f);
        }

        .bg-gradient-class {
            background: linear-gradient(145deg, #bf950a, #f57c00);
        }

        .bg-gradient-bus {
            background: linear-gradient(145deg, #329688, #00695c);
        }

        /* Chart Container */
        .chart-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            height: 100%;
        }

        .chart-card:hover {
            box-shadow: 0 20px 40px rgba(78, 115, 223, 0.08);
        }

        .chart-header {
            border-bottom: 2px solid #f1f4f9;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
            font-size: 1rem;
        }

        /* Table Premium */
        .table-premium {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.02);
        }

        .table-premium thead th {
            background: linear-gradient(135deg, #f8faff, #f1f5ff);
            color: var(--primary);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
            border: none;
        }

        .table-premium tbody tr {
            transition: all 0.2s ease;
        }

        .table-premium tbody tr:hover {
            background: #f8faff;
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.08);
        }

        /* Status Badges */
        .badge-premium {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(78, 115, 223, 0.3);
            }

            50% {
                box-shadow: 0 0 20px 5px rgba(78, 115, 223, 0.2);
            }
        }

        .countdown-item {
            animation: pulse-glow 3s ease-in-out infinite;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-card-premium .card-value {
                font-size: 1.8rem;
            }

            .countdown-item .number {
                font-size: 24px;
            }
        }
    </style>

    <div class="py-4">
        @php
            $school = App\Models\school::find(Auth::user()->school_id);
            $serviceStartDate = \Carbon\Carbon::parse($school->service_start_date);
            $serviceEndDate = \Carbon\Carbon::parse($school->service_end_date);
            $now = \Carbon\Carbon::now();
            $daysRemaining = $now->diffInDays($serviceEndDate, false);

            // FIXED: Format dates properly for JavaScript
            $jsEndDate = $serviceEndDate->format('Y-m-d H:i:s');

            // Calculate progress
            $totalDays = $serviceStartDate->diffInDays($serviceEndDate);
            $daysPassed = $serviceStartDate->diffInDays($now);
            $progressPercentage = min(100, max(0, ($daysPassed / $totalDays) * 100));

            // Status colors and messages
            if ($daysRemaining > 30) {
                $statusColor = 'white';
                $statusBg = 'var(--success)';
                $statusText = 'Active';
                $icon = 'fa-check-circle';
                $progressColor = 'bg-success';
            } elseif ($daysRemaining > 15) {
                $statusColor = 'black';
                $statusBg = 'var(--warning)';
                $statusText = 'Expiring Soon';
                $icon = 'fa-clock';
                $progressColor = 'bg-warning';
            } else {
                $statusColor = 'black';
                $statusBg = 'var(--danger)';
                $statusText = 'Critical';
                $icon = 'fa-exclamation-triangle';
                $progressColor = 'bg-danger';
            }
        @endphp

        <!-- Service Status Card - Premium Compact -->
        <div class="modern-card service-card-premium mb-4">
            <div class="p-4">
                <!-- Header with Status Bar -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="service-icon-circle float-animation mr-1">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <div>
                            <span class="badge-premium" style="background: {{ $statusBg }}; color: {{ $statusColor }};">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                {{ $statusText }}
                            </span>
                            <h5 class="mt-2 mb-0 fw-bold">{{ strtoupper($school->school_name) }}</h5>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Subscription Package.</small>
                        @if ($school->package == 'premium')
                            <span class="fw-semibold badge bg-success text-white"><i class="fas fa-crown"></i> {{ ucfirst($school->package) }}</span>
                        @else
                            <span class="fw-semibold badge bg-warning text-dark"><i class="fas fa-star"></i> {{ ucfirst($school->package) }}</span>
                        @endif
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-muted">Service Period Status Bar</span>
                        <span class="" style="color: var(--dark); font-weight:bold">{{ round($progressPercentage) }}%
                            Used</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar {{ $progressColor }}" role="progressbar"
                            style="width: {{ $progressPercentage }}%; background: {{ $statusColor }};"
                            aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">{{ $serviceStartDate->format('d M Y') }}</small>
                        <small class="text-muted">{{ $serviceEndDate->format('d M Y') }}</small>
                    </div>
                </div>

                <!-- Countdown Section - FIXED -->
                <div class="countdown-premium">
                    <div class="countdown-item">
                        <span class="number" id="days">00</span>
                        <span class="label">Days</span>
                    </div>
                    <div class="countdown-item">
                        <span class="number" id="hours">00</span>
                        <span class="label">Hours</span>
                    </div>
                    <div class="countdown-item">
                        <span class="number" id="minutes">00</span>
                        <span class="label">Minutes</span>
                    </div>
                    <div class="countdown-item">
                        <span class="number" id="seconds">00</span>
                        <span class="label">Seconds</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <!-- Teachers Card -->
            <div class="col-xl-4 col-md-4 col-6">
                <a href="{{ route('Teachers.index') }}" class="text-decoration-none">
                    <div class="stat-card-premium bg-gradient-teacher">
                        <div class="card-body">
                            <div class="card-title">Teachers</div>
                            <div class="card-value">
                                @if (count($teachers) > 99)
                                    100+
                                @else
                                    {{ count($teachers) }}
                                @endif
                            </div>
                            <i class="fas fa-user-tie card-icon"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Parents Card -->
            <div class="col-xl-4 col-md-4 col-6">
                <a href="{{ route('Parents.index') }}" class="text-decoration-none">
                    <div class="stat-card-premium bg-gradient-parent">
                        <div class="card-body">
                            <div class="card-title">Parents</div>
                            <div class="card-value">
                                @if (count($parents) > 1999)
                                    2000+
                                @else
                                    {{ count($parents) }}
                                @endif
                            </div>
                            <i class="fas fa-user-friends card-icon"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Students Card -->
            <div class="col-xl-4 col-md-4 col-6">
                <a href="{{ route('classes.list') }}" class="text-decoration-none">
                    <div class="stat-card-premium bg-gradient-student">
                        <div class="card-body">
                            <div class="card-title">Students</div>
                            <div class="card-value">
                                @if (count($students) > 1999)
                                    2000+
                                @else
                                    {{ count($students) }}
                                @endif
                            </div>
                            <i class="fas fa-user-graduate card-icon"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <!-- Courses Card -->
        <div class="row g-4 mb-4">
            <div class="col-xl-4 col-md-4 col-6">
                <a href="{{ route('courses.index') }}" class="text-decoration-none">
                    <div class="stat-card-premium bg-gradient-course">
                        <div class="card-body">
                            <div class="card-title">Courses</div>
                            <div class="card-value">
                                @if (count($subjects) > 49)
                                    50+
                                @else
                                    {{ count($subjects) }}
                                @endif
                            </div>
                            <i class="ti-book card-icon"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Classes Card -->
            <div class="col-xl-4 col-md-4 col-6">
                <a href="{{ route('Classes.index') }}" class="text-decoration-none">
                    <div class="stat-card-premium bg-gradient-class">
                        <div class="card-body">
                            <div class="card-title">Classes</div>
                            <div class="card-value">
                                @if (count($classes) > 49)
                                    50+
                                @else
                                    {{ count($classes) }}
                                @endif
                            </div>
                            <i class="ti-blackboard card-icon"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Buses Card -->
            <div class="col-xl-4 col-md-4 col-6">
                <a href="{{ route('Transportation.index') }}" class="text-decoration-none">
                    <div class="stat-card-premium bg-gradient-bus">
                        <div class="card-body">
                            <div class="card-title">Buses</div>
                            <div class="card-value">
                                @if (count($buses) > 49)
                                    50+
                                @else
                                    {{ count($buses) }}
                                @endif
                            </div>
                            <i class="fas fa-bus card-icon"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <!-- Student Registration Chart -->
            <div class="col-xl-8">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-chart-bar me-2"></i> Student Registration by Class & Gender
                        </h5>
                        <p class="text-muted small mb-0">Distribution of students across classes</p>
                    </div>
                    <div style="height: 350px;" id="studentChart"></div>
                </div>
            </div>

            <!-- Teacher Qualifications Chart -->
            <div class="col-xl-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-chart-pie me-2"></i> Teacher Qualifications
                        </h5>
                        <p class="text-muted small mb-0">Educational background</p>
                    </div>
                    <div style="height: 350px;" id="qualificationChart"></div>
                </div>
            </div>
        </div>

        <!-- Analytics Row -->
        <div class="row g-4">
            <!-- Gender Distribution -->
            <div class="col-xl-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-venus-mars me-2"></i> Student Gender Distribution
                        </h5>
                        <p class="text-muted small mb-0">Male vs Female students ratio</p>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="col-xl-5">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="chart-title">
                                    <i class="fas fa-calendar-check me-2"></i> Today's Attendance Summary
                                </h5>
                                <p class="text-muted small mb-0">{{ now()->format('l, d F Y') }}</p>
                            </div>
                            @if (isset($attendanceByClassData) && count($attendanceByClassData) > 0)
                                <span class="badge-premium" style="background: var(--primary); color: white;">
                                    {{ count($attendanceByClassData) }} Streams
                                </span>
                            @endif
                        </div>
                    </div>

                    @if (isset($attendanceByClassData) && count($attendanceByClassData) > 0)
                        <div class="table-responsive" style="overflow-y: auto;">
                            <table class="table table-premium">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th class="text-center">Pres</th>
                                        <th class="text-center">Abs</th>
                                        <th class="text-center">Perm</th>
                                        <th class="text-center">Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalPresent = 0;
                                        $totalStudents = 0;
                                    @endphp
                                    @foreach ($attendanceByClassData as $classData)
                                        @php
                                            $classId = $classData['class_id'] ?? null;
                                            $stream = $classData['class_stream'] ?? null;

                                            $registeredStudents = $classId
                                                ? \App\Models\Student::where('class_id', $classId)
                                                    ->when($stream, fn($q) => $q->where('group', $stream))
                                                    ->where('status', 1)
                                                    ->count()
                                                : $classData['present'] +
                                                    $classData['absent'] +
                                                    $classData['permission'];

                                            $attendanceRate =
                                                $registeredStudents > 0
                                                    ? round(($classData['present'] / $registeredStudents) * 100, 1)
                                                    : 0;

                                            $totalPresent += $classData['present'];
                                            $totalStudents += $registeredStudents;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">{{ $classData['class_code'] ?? '' }}</span>
                                                @if (!empty($stream))
                                                    <span class="badge bg-primary bg-opacity-10 text-primary ms-1">
                                                        {{ strtoupper($stream) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center fw-bold text-success">{{ $classData['present'] }}</td>
                                            <td class="text-center text-danger">{{ $classData['absent'] }}</td>
                                            <td class="text-center text-secondary">{{ $classData['permission'] }}</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 6px;">
                                                        <div class="progress-bar {{ $attendanceRate >= 70 ? 'bg-success' : ($attendanceRate >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                            style="width: {{ $attendanceRate }}%"></div>
                                                    </div>
                                                    <small class="fw-semibold">{{ $attendanceRate }}%</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th>Overall</th>
                                        <th class="text-center text-success">{{ $totalPresent }}</th>
                                        <th class="text-center text-danger">
                                            {{ array_sum(array_column($attendanceByClassData, 'absent')) }}</th>
                                        <th class="text-center text-secondary">
                                            {{ array_sum(array_column($attendanceByClassData, 'permission')) }}</th>
                                        <th class="text-center">
                                            @php $overallRate = $totalStudents > 0 ? round(($totalPresent / $totalStudents) * 100, 1) : 0; @endphp
                                            <span
                                                class="badge-premium {{ $overallRate >= 70 ? 'bg-success' : ($overallRate >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                style="color: white;">
                                                {{ $overallRate }}%
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h6 class="text-muted">No Attendance Today</h6>
                            <p class="text-muted small">Records will appear once submitted</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-xl-3">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-table me-2"></i> Quick Overview
                        </h5>
                    </div>

                    <!-- Students by Class -->
                    <div class="mb-4">
                        <h6 class="text-muted small fw-bold mb-3">Students by Class</h6>
                        @if ($studentsByClass->isEmpty())
                            <p class="text-center text-muted small">No data</p>
                        @else
                            @foreach ($studentsByClass as $class)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-semibold">{{ strtoupper($class->class_code) }}</span>
                                    <span class="">
                                        {{ $class->student_count }}
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Teachers by Gender -->
                    <div>
                        <h6 class="text-muted small fw-bold mb-3">Teachers by Gender</h6>
                        @if ($teacherByGender->isEmpty())
                            <p class="text-center text-muted small">No data</p>
                        @else
                            @foreach ($teacherByGender as $teacher)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span
                                        class="small fw-semibold text-capitalize">{{ ucfirst(strtolower($teacher->gender)) }}</span>
                                    <span class="">
                                        {{ $teacher->teacher_count }}
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========== FIXED COUNTDOWN TIMER ==========
            function initializeCountdown() {
                const endDateStr = '{{ $jsEndDate }}';
                // Parse the date properly
                const endDate = new Date(endDateStr.replace(' ', 'T')).getTime();

                function updateCountdown() {
                    const now = new Date().getTime();
                    const distance = endDate - now;

                    // Get elements
                    const daysEl = document.getElementById('days');
                    const hoursEl = document.getElementById('hours');
                    const minutesEl = document.getElementById('minutes');
                    const secondsEl = document.getElementById('seconds');

                    if (!daysEl || !hoursEl || !minutesEl || !secondsEl) return;

                    if (distance <= 0) {
                        daysEl.innerText = "00";
                        hoursEl.innerText = "00";
                        minutesEl.innerText = "00";
                        secondsEl.innerText = "00";
                        return;
                    }

                    // Calculate time units
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Format with leading zeros
                    const format = (num) => num.toString().padStart(2, '0');

                    // Update with animation
                    updateNumberWithAnimation(daysEl, format(days));
                    updateNumberWithAnimation(hoursEl, format(hours));
                    updateNumberWithAnimation(minutesEl, format(minutes));
                    updateNumberWithAnimation(secondsEl, format(seconds));
                }

                function updateNumberWithAnimation(element, newValue) {
                    if (element.innerText !== newValue) {
                        element.style.transform = 'scale(1.2)';
                        element.style.transition = 'transform 0.2s ease';

                        setTimeout(() => {
                            element.innerText = newValue;
                            element.style.transform = 'scale(1)';
                        }, 100);
                    }
                }

                // Initial call and set interval
                updateCountdown();
                setInterval(updateCountdown, 1000);
            }

            // Initialize countdown
            initializeCountdown();

            // ========== STUDENT CHART (ECharts) ==========
            const chartDom = document.getElementById('studentChart');
            if (chartDom) {
                const myChart = echarts.init(chartDom);
                const chartData = @json($chartData);

                // Process data
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
                const maleData = classCodes.map(code => groupedData[code].Male);
                const femaleData = classCodes.map(code => groupedData[code].Female);

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
                            data: maleData,
                            itemStyle: {
                                color: '#4e73df'
                            }
                        },
                        {
                            name: 'Female',
                            type: 'bar',
                            stack: 'total',
                            data: femaleData,
                            itemStyle: {
                                color: '#e74a3b'
                            }
                        }
                    ]
                };
                myChart.setOption(option);
                window.addEventListener('resize', () => myChart.resize());
            }

            // ========== QUALIFICATION CHART (amCharts) ==========
            if (document.getElementById('qualificationChart')) {
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
                            value: {{ $qualificationData['masters'] ?? 0 }}
                        },
                        {
                            category: "Degree",
                            value: {{ $qualificationData['bachelor'] ?? 0 }}
                        },
                        {
                            category: "Diploma",
                            value: {{ $qualificationData['diploma'] ?? 0 }}
                        },
                        {
                            category: "Certificate",
                            value: {{ $qualificationData['certificate'] ?? 0 }}
                        }
                    ]);

                    chart.children.push(am5.Legend.new(root, {}));
                    series.appear(1000, 100);
                });
            }

            // ========== GENDER CHART (Chart.js) ==========
            const genderCtx = document.getElementById('genderChart');
            if (genderCtx) {
                new Chart(genderCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Male Students', 'Female Students'],
                        datasets: [{
                            data: [{{ $totalMaleStudents ?? 0 }},
                                {{ $totalFemaleStudents ?? 0 }}
                            ],
                            backgroundColor: ['#4e73df', '#e74a3b'],
                            borderWidth: 0
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
                                    label: (ctx) => {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((ctx.parsed / total) * 100).toFixed(1);
                                        return `${ctx.label}: ${ctx.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

            // ========== AUTHORIZATION CHECK ==========
            @if (Auth::user()->usertype != 2)
                window.location.href = '/error-page';
            @endif
        });
    </script>
@endsection
