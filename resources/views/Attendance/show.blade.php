@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 10px;
            position: relative;
            overflow: hidden;
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: rotate(30deg);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 28px;
        }

        .student-highlight {
            color: #ffd700;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            text-transform: uppercase;
        }

        .card-body {
            padding: 5px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
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
        }

        .table-custom tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr {
            transition: all 0.3s;
        }

        .table-custom tbody tr:hover {
            background-color: #f8f9fa;
        }

        .week-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: var(--primary);
            font-weight: 700;
            font-size: 18px;
            text-align: center;
        }

        .week-header td {
            padding: 15px;
            border-bottom: 2px solid var(--primary);
        }

        .day-cell {
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-cell {
            color: #6c757d;
            font-weight: 500;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-width: 100px;
            justify-content: center;
        }

        .status-present {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
        }

        .status-absent {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
        }

        .status-permission {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            color: #856404;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fff9e6 0%, #ffeeb5 100%);
            border-radius: 15px;
            border-left: 5px solid var(--warning);
        }

        .empty-state i {
            font-size: 60px;
            color: var(--warning);
            margin-bottom: 15px;
        }

        .empty-state h5 {
            color: #856404;
            font-weight: 600;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 25px;
        }

        .page-link {
            border-radius: 8px;
            margin: 0 5px;
            border: 1px solid #dee2e6;
            color: var(--primary);
            font-weight: 600;
            transition: all 0.3s;
        }

        .page-link:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-color: var(--primary);
            color: white;
        }

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        .attendance-stats {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .stat-value {
            font-weight: 800;
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 14px;
            text-transform: uppercase;
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

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title">
                            <i class="fas fa-calendar-check me-2"></i>
                            Attendance Records for <span class="student-highlight"> {{ $firstRecord->student_firstname }} {{ $firstRecord->student_lastname }}</span>
                        </h4>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('attendance.byYear', ['student' => Hashids::encode($students->id)])}}" class="btn btn-back btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-user-clock floating-icons"></i>
            </div>
            <div class="card-body">
                <!-- Attendance Statistics -->
                @if ($groupedData->isNotEmpty())
                    @php
                        $presentCount = 0;
                        $absentCount = 0;
                        $permissionCount = 0;

                        foreach ($groupedData as $week => $records) {
                            foreach ($records as $item) {
                                if ($item->attendance_status == 'present') $presentCount++;
                                elseif ($item->attendance_status == 'absent') $absentCount++;
                                else $permissionCount++;
                            }
                        }

                        $totalRecords = $presentCount + $absentCount + $permissionCount;
                        $attendanceRate = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 2) : 0;
                    @endphp

                    <div class="attendance-stats">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-value">{{ $presentCount }}</div>
                                <div class="stat-label">Present Days</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $absentCount }}</div>
                                <div class="stat-label">Absent Days</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $permissionCount }}</div>
                                <div class="stat-label">Permission Days</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $attendanceRate }}%</div>
                                <div class="stat-label">Attendance Rate</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Attendance Table -->
                <div class="table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th class="text-center">Day</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($groupedData->isNotEmpty())
                                @foreach ($groupedData as $week => $records)
                                    <tr class="week-header">
                                        <td colspan="3" class="text-center">
                                            <i class="fas fa-calendar-week me-2"></i> Week {{ $week }}
                                        </td>
                                    </tr>
                                    @foreach ($records as $item)
                                        <tr>
                                            <td class="text-center">
                                                <div class="day-cell justify-content-center">
                                                    <i class="fas fa-calendar-day text-primary"></i>
                                                    {{ \Carbon\Carbon::parse($item->attendance_date)->format('D') }}
                                                </div>
                                            </td>
                                            <td class="text-center date-cell">
                                                {{ \Carbon\Carbon::parse($item->attendance_date)->format('M d, Y') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($item->attendance_status == 'present')
                                                    <span class="status-badge status-present">
                                                        <i class="fas fa-check-circle"></i> Present
                                                    </span>
                                                @elseif ($item->attendance_status == 'absent')
                                                    <span class="status-badge status-absent">
                                                        <i class="fas fa-times-circle"></i> Absent
                                                    </span>
                                                @else
                                                    <span class="status-badge status-permission">
                                                        <i class="fas fa-exclamation-circle"></i> Permission
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <h5> No Attendance Records Found!</h5>
                                            <p class="mb-0"> There are no attendance records available for this student.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($groupedData->isNotEmpty() && $groupedData->hasPages())
                    <div class="pagination-container">
                        <nav>
                            {{ $groupedData->links('vendor.pagination.bootstrap-5') }}
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Add animation to table rows
        document.addEventListener("DOMContentLoaded", function() {
            const tableRows = document.querySelectorAll('.table-custom tbody tr');

            tableRows.forEach((row, index) => {
                if (!row.classList.contains('week-header')) {
                    row.style.opacity = "0";
                    row.style.transform = "translateY(20px)";

                    setTimeout(() => {
                        row.style.transition = "all 0.5s ease";
                        row.style.opacity = "1";
                        row.style.transform = "translateY(0)";
                    }, index * 100);
                }
            });
        });
    </script>
@endsection
