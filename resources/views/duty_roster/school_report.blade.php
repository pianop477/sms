@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary: #6a11cb;
            --secondary: #2575fc;
            --success: #00b894;
            --danger: #ff6b6b;
            --warning: #feca57;
            --info: #48dbfb;
            --dark: #222f3e;
            --light: #f9f9f9;
            --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            --gradient-success: linear-gradient(135deg, var(--success) 0%, #00cec9 100%);
            --gradient-danger: linear-gradient(135deg, var(--danger) 0%, #ee5a52 100%);
            --gradient-warning: linear-gradient(135deg, var(--warning) 0%, #ff9ff3 100%);
            --card-shadow: 0 20px 30px rgba(0, 0, 0, 0.1);
            --hover-effect: translateY(-8px) scale(1.02);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            /* padding: 20px; */
        }

        .dashboard-container {
            /* max-width: 1400px; */
            margin: 0 auto;
        }

        /* Header Styles */
        .main-header {
            background: var(--gradient-primary);
            border-radius: 20px;
            padding: 25px 30px;
            margin-bottom: 30px;
            color: white;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .main-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(-15deg);
        }

        .header-title {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-weight: 300;
            opacity: 0.9;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 18px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: var(--hover-effect);
        }

        .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2.5rem;
            opacity: 0.2;
            color: var(--primary);
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-title {
            color: #666;
            font-weight: 500;
            font-size: 0.9rem;
        }

        /* Main Cards */
        .main-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .main-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: var(--gradient-primary);
            color: white;
            padding: 20px 30px;
            border-bottom: none;
            position: relative;
        }

        .card-header h3 {
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body {
            padding: 30px;
        }

        /* Form Styles */
        .date-range-form {
            background: linear-gradient(145deg, #f5f7fa, #e6e9ef);
            border-radius: 18px;
            padding: 25px;
        }

        .form-label {
            font-weight: 500;
            color: #444;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #eef2f7;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.2);
        }

        .btn {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-view {
            background: var(--gradient-success);
            color: white;
        }

        .btn-download {
            background: var(--gradient-danger);
            color: white;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(0, 0, 0, 0.2);
        }

        /* Table Styles */
        .reports-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .reports-table thead th {
            background: var(--gradient-primary);
            color: white;
            padding: 18px 15px;
            text-align: left;
            font-weight: 500;
            border: none;
        }

        .reports-table tbody td {
            padding: 16px 15px;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: middle;
        }

        .reports-table tbody tr:last-child td {
            border-bottom: none;
        }

        .reports-table tbody tr {
            transition: all 0.3s ease;
        }

        .reports-table tbody tr:hover {
            background-color: rgba(106, 17, 203, 0.05);
            transform: scale(1.01);
        }

        /* Progress Bar */
        .attendance-progress {
            height: 10px;
            border-radius: 10px;
            background-color: #f1f1f1;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 0.8s ease;
        }

        /* Action Buttons */
        .action-btns {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 8px 15px;
            border-radius: 10px;
            font-size: 0.9rem;
        }

        .btn-approve {
            background: var(--gradient-success);
            color: white;
        }

        .btn-reject {
            background: var(--gradient-danger);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #888;
        }

        .empty-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .action-btns {
                flex-direction: column;
            }

            .reports-table {
                display: block;
                overflow-x: auto;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease forwards;
        }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        /* Badge Styles */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-class {
            background: rgba(72, 219, 251, 0.2);
            color: #0abde3;
        }

        /* Chart Container */
        .chart-container {
            height: 300px;
            margin-top: 20px;
        }
    </style>
    <div class="dashboard-container">
        <!-- Header Section -->
        <header class="main-header animate-fadeIn">
            <h5 class="header-title text-white"> Daily School Reports</h5>
            <p class="header-subtitle text-white"> Management and analytics for daily school reports</p>
        </header>

        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card animate-fadeIn delay-1">
                <i class="fas fa-clipboard-list stat-icon"></i>
                <div class="stat-value">{{ $pendingReports }}</div>
                <div class="stat-title">Pending Reports</div>
            </div>
            <div class="stat-card animate-fadeIn delay-2">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-value">{{ $totalRegistered }}</div>
                <div class="stat-title">Registered</div>
            </div>
            <div class="stat-card animate-fadeIn delay-3">
                <i class="fas fa-user-check stat-icon"></i>
                @php
                    $allAttended = $reportSummary->sum('present_boys') + $reportSummary->sum('present_girls')
                @endphp
                <div class="stat-value">{{  $allAttended}}</div>
                <div class="stat-title">Attended</div>
            </div>

            <div class="stat-card animate-fadeIn delay-4">
                <i class="fas fa-user-xmark stat-icon"></i>
                <div class="stat-value">{{ $reportSummary->sum('absent_boys') + $reportSummary->sum('absent_girls') }}</div>
                <div class="stat-title">Absentees</div>
            </div>

            <div class="stat-card animate-fadeIn delay-5">
                <i class="fas fa-user-shield stat-icon"></i>
                <div class="stat-value">{{ $reportSummary->sum('permission_boys') + $reportSummary->sum('permission_girls') }}</div>
                <div class="stat-title">Permitted</div>
            </div>

            <div class="stat-card animate-fadeIn delay-6">
                <i class="fas fa-chart-line stat-icon"></i>
                @php
                    $attendanceRate = $totalRegistered > 0 ? round(($allAttended / $totalRegistered) * 100, 2) : 0;
                @endphp
                <div class="stat-value">{{ $attendanceRate }}%</div>
                <div class="stat-title">Attendance Rate</div>
            </div>
        </div>

        <!-- Report Generator Card -->
        <div class="main-card animate-fadeIn">
            <div class="card-header">
                <h3><i class="fas fa-file-export"></i> Custom Approved Reports</h3>
            </div>
            <p class="p-4 alert alert-success">Filter by date here below to get approved reports</p>
            <div class="card-body">
                <div class="date-range-form">
                    <form action="{{route('report.fetch.preview')}}" method="GET" target="">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="start_date" class="form-control" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="end_date" class="form-control" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" name="action" value="view" class="btn btn-view">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reports Table Card -->
        <div class="main-card animate-fadeIn">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-tasks"></i> Pending Reports</h3>
                <span class="badge bg-light text-dark py-2 px-3">{{ $pendingReports }} Pending</span>
            </div>
            <div class="card-body">
                @if($reports->count() > 0)
                    <div class="table-responsive">
                        <table class="reports-table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Report Date</th>
                                    <th>Roster ID#</th>
                                    <th>Attended</th>
                                    <th>Attendance Rate</th>
                                    <th>Issued by</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $key => $report)
                                    @php
                                        $totalReg = $report->registered_boys + $report->registered_girls;
                                        $totalAtt = $report->present_boys + $report->present_girls;
                                        $rate = $totalRegistered > 0 ? round(($totalAtt / $totalRegistered) * 100, 2) : 0;

                                        if ($rate >= 80) {
                                            $progressClass = 'bg-success';
                                        } elseif ($rate >= 50) {
                                            $progressClass = 'bg-warning';
                                        } else {
                                            $progressClass = 'bg-danger';
                                        }
                                    @endphp

                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}</td>
                                        <td class="text-uppercase">{{ $report->roster_id }}</td>
                                        <td>
                                            <div><small>Boys: {{ $report->present_boys }}</small>, <small>Girls: {{ $report->present_girls }}</small></div>
                                            <div><small><b>Total: {{ $totalAtt }}</b></small></div>
                                        </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar {{ $progressClass }}" style="width: {{ $rate }}%"></div>
                                            </div>
                                            <span>{{ $rate }}%</span>
                                        </td>
                                        <td class="text-capitalize">{{ $report->first_name }} {{ $report->last_name }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-danger text-white text-capitalize">{{$report->status}}</span>
                                        </td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="{{route('report.by.date', ['date' => $report->report_date])}}" class="btn btn-action btn-approve" onclick="return confirm('Are you sure you want to view and approve this school daily report for {{\Carbon\Carbon::parse($report->report_date)->format('d-m-Y')}}?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </a>
                                                <form action="{{route('report.reject', ['date' => $report->report_date])}}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-action btn-reject" onclick="return confirm('Are you sure you want to delete this school daily report for {{\Carbon\Carbon::parse($report->report_date)->format('d-m-Y')}}?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-clipboard-check empty-icon"></i>
                        <h5>No Pending Reports were Found</h5>
                        <p class="text-success">All reports has been processed successfully</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

@endsection
