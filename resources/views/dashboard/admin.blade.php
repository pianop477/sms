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
        .bg-school {
            background: linear-gradient(135deg, #93dad6 0%, #5ec4bf 100%);
        }

        .bg-teacher {
            background: linear-gradient(135deg, #e176a6 0%, #d04a88 100%);
        }

        .bg-student {
            background: linear-gradient(135deg, #098ddf 0%, #0568a8 100%);
        }

        .bg-parent {
            background: linear-gradient(135deg, #c84fe0 0%, #9c27b0 100%);
        }

        .bg-course {
            background: linear-gradient(135deg, #9fbc71 0%, #689f38 100%);
        }

        .bg-class {
            background: linear-gradient(135deg, #bf950a 0%, #ff9800 100%);
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
            transform: translateY(-5px);
            box-shadow: 0 0.75rem 2rem rgba(58, 59, 69, 0.15);
        }

        .chart-wrapper {
            flex: 1;
            min-height: 250px;
            position: relative;
            width: 100%;
        }

        .chart-wrapper canvas {
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
            overflow: hidden;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
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
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
                min-height: 200px;
            }
        }
    </style>

    <div class="py-4">
        <div class="row">
            <!-- Quick Stats Summary -->
            <div class="col-lg-12 mb-4">
                <div class="row">
                    <!-- Schools Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="stat-card bg-school text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="card-title">Schools</div>
                                        <div class="card-value">{{ count($schools) }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-university card-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Teachers Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
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
                    </div>

                    <!-- Students Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
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
                    </div>

                    <!-- Parents Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
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
                    </div>

                    <!-- Courses Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="stat-card bg-course text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="card-title">Courses</div>
                                        <div class="card-value">
                                            @if (count($subjects) > 99)
                                                100+
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
                    </div>

                    <!-- Classes Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
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
                    </div>
                </div>
            </div>

            <!-- Analytics Charts Section -->
            <div class="col-lg-12 mb-4">
                <div class="row">
                    <!-- Schools Growth Chart -->
                    <div class="col-xl-6 mb-4">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h5 class="chart-title">
                                    <i class="fas fa-chart-line me-2"></i> Schools Growth Trend
                                </h5>
                                <p class="chart-subtitle">Monthly school registration progress</p>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="schoolsGrowthChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- User Distribution Chart -->
                    <div class="col-xl-4 mb-4">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h5 class="chart-title">
                                    <i class="fas fa-chart-pie me-2"></i> User Distribution
                                </h5>
                                <p class="chart-subtitle">Platform users overview</p>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="userDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 mb-4">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h5 class="chart-title">
                                    <i class="fas fa-sms"></i> Account Information
                                </h5>
                                <p class="chart-subtitle"> NextSms Account Balance</p>
                            </div>
                            <div class="chart-wrapper">
                                <div class="justify-content-center text-center">
                                    <h6 class="">Internet SMS: </h6>
                                    <p><span class="badge bg-success text-white">{{ $smsBalance }}</span></p>
                                    <p class="text-muted">Available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schools Table -->
            <div class="col-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="header-title mb-0">
                                <i class="fas fa-university me-2"></i> Registered Schools
                            </h4>
                            <a href="{{ route('Schools.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add New School
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover progress-table mb-0" id="schoolsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Institute Name</th>
                                        <th>Admission #</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schools as $school)
                                        <tr>
                                            <td class="fw-bold">{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('schools.show', ['school' => Hashids::encode($school->id)]) }}"
                                                    class="text-primary fw-bold">
                                                    {{ ucwords(strtolower($school->school_name)) }}
                                                </a>
                                            </td>
                                            <td class="fw-bold text-info">{{ strtoupper($school->school_reg_no) }}</td>
                                            <td class="text-muted">
                                                {{ ucwords(strtolower($school->postal_address)) }} -
                                                {{ ucwords(strtolower($school->postal_name)) }}
                                            </td>
                                            <td class="text-center">
                                                @if ($school->status == 1)
                                                    <span class="status-badge bg-success text-white">
                                                        <i class="fas fa-check-circle me-1"></i> Active
                                                    </span>
                                                @elseif($school->status == 2)
                                                    <span class="status-badge bg-warning text-white">
                                                        <i class="fas fa-clock me-1"></i> Unpaid
                                                    </span>
                                                @else
                                                    <span class="status-badge bg-danger text-white">
                                                        <i class="fas fa-times-circle me-1"></i> Closed
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    @if ($school->status == 1)
                                                        <a href="{{ route('schools.edit', ['school' => Hashids::encode($school->id)]) }}"
                                                            class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="ti-pencil"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('deactivate.status', ['school' => Hashids::encode($school->id)]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-warning"
                                                                data-bs-toggle="tooltip" title="Deactivate"
                                                                onclick="return confirm('Are you sure you want to block this school?')">
                                                                <i class="ti-na"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form
                                                            action="{{ route('activate.status', ['school' => Hashids::encode($school->id)]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                data-bs-toggle="tooltip" title="Activate"
                                                                onclick="return confirm('Are you sure you want to unblock this school?')">
                                                                <i class="ti-reload"></i>
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('schools.destroy', ['school' => Hashids::encode($school->id)]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                data-bs-toggle="tooltip" title="Delete"
                                                                onclick="return confirm('Are you sure you want to delete this school?')">
                                                                <i class="ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            if (document.getElementById('schoolsTable')) {
                $('#schoolsTable').DataTable({
                    "language": {
                        "search": "<i class='fas fa-search'></i>",
                        "searchPlaceholder": "Search schools...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "paginate": {
                            "previous": "<i class='fas fa-chevron-left'></i>",
                            "next": "<i class='fas fa-chevron-right'></i>"
                        }
                    },
                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    "pageLength": 10
                });
            }

            // Prepare chart data
            const chartData = {
                schoolsGrowth: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                    data: [12, 19, 3, 5, 2, 3, 7, 8, 10, 15, 20, 25]
                },
                userDistribution: {
                    labels: ['Students', 'Teachers', 'Parents'],
                    data: [{{ count($students) }}, {{ count($teachers) }}, {{ count($parents) }}],
                    colors: ['#098ddf', '#e176a6', '#c84fe0']
                }
            };

            // Schools Growth Chart
            const growthCtx = document.getElementById('schoolsGrowthChart').getContext('2d');
            new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: chartData.schoolsGrowth.labels,
                    datasets: [{
                        label: 'New Schools Registered',
                        data: chartData.schoolsGrowth.data,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4e73df',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // User Distribution Chart
            const distributionCtx = document.getElementById('userDistributionChart').getContext('2d');
            new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: chartData.userDistribution.labels,
                    datasets: [{
                        data: chartData.userDistribution.data,
                        backgroundColor: chartData.userDistribution.colors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
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

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Authorization check
        @if (Auth::user()->usertype != 1)
            window.location.href = '/error-page';
        @endif
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
