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

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .form-control-custom {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 16px;
        width: 100%;
        transition: all 0.3s;
        background-color: white;
    }

    .form-control-custom:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
    }

    /* Modern Card Styles */
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

    /* Chart Container Styles */
    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 0.5rem 1.5rem rgba(58, 59, 69, 0.1);
        border: none;
        transition: all 0.3s ease;
        height: 100%;
    }

    .chart-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 2rem rgba(58, 59, 69, 0.15);
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
    }

    .chart-subtitle {
        color: #6c757d;
        font-size: 0.875rem;
    }

    /* Table Styles */
    .table-responsive {
        border-radius: 15px;
        overflow: auto;
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

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 3px currentColor;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid currentColor;
    }

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
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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

    .badge-premium {
        padding: 6px 12px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

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


    /* Responsive Design */
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: row;
            align-items: center;
        }

        .stat-card .card-value {
            font-size: 1.5rem;
        }

        .stat-card-premium .card-value {
            font-size: 1.8rem;
        }

        .countdown-item .number {
            font-size: 24px;
        }

        .stat-card .card-icon {
            font-size: 2.5rem;
        }
    }

    /* =========================================================
        GLOBAL DASHBOARD GREETING
        ========================================================= */

        .dashboard-greeting {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            background:
                linear-gradient(
                    135deg,
                    rgba(78, 115, 223, 0.98) 0%,
                    rgba(111, 66, 193, 0.96) 100%
                );
            box-shadow:
                0 12px 30px rgba(78, 115, 223, 0.16);
            color: #fff;
        }

        .dashboard-greeting::before {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            top: -120px;
            right: -60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .dashboard-greeting::after {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            bottom: -100px;
            left: 35%;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .greeting-content {
            position: relative;
            z-index: 2;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 18px;
            padding: 20px 24px;
        }

        .greeting-icon {
            width: 52px;
            height: 52px;
            min-width: 52px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 15px;

            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.18);

            font-size: 22px;

            box-shadow:
                0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .greeting-text {
            flex: 1;
            min-width: 0;
        }

        .greeting-message {
            margin-bottom: 4px;

            font-size: clamp(1.15rem, 2vw, 1.55rem);
            font-weight: 800;

            line-height: 1.25;
            letter-spacing: -0.2px;
        }

        .greeting-subtitle {
            color: rgba(255, 255, 255, 0.78);

            font-size: 0.82rem;
            line-height: 1.5;
        }

        .greeting-time {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            white-space: nowrap;

            padding: 8px 12px;

            border-radius: 10px;

            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.12);

            color: rgba(255, 255, 255, 0.9);

            font-size: 0.75rem;
            font-weight: 600;
        }

        .greeting-time i {
            font-size: 0.8rem;
        }

        /* Tablet */
        @media (max-width: 768px) {
            .greeting-content {
                padding: 17px;
                gap: 13px;
            }

            .greeting-icon {
                width: 45px;
                height: 45px;
                min-width: 45px;
                border-radius: 12px;
                font-size: 18px;
            }

            .greeting-message {
                font-size: 1.1rem;
            }

            .greeting-subtitle {
                font-size: 0.75rem;
            }

            .greeting-time {
                padding: 7px 9px;
                font-size: 0.7rem;
            }
        }

        /* Mobile */
        @media (max-width: 576px) {
            .dashboard-greeting {
                border-radius: 14px;
            }

            .greeting-content {
                display: grid;
                grid-template-columns: auto 1fr;
                padding: 15px;
                gap: 11px;
            }

            .greeting-icon {
                width: 42px;
                height: 42px;
                min-width: 42px;
            }

            .greeting-message {
                font-size: 1rem;
            }

            .greeting-subtitle {
                font-size: 0.7rem;
            }

            .greeting-time {
                grid-column: 1 / -1;
                justify-content: center;
                width: 100%;
            }
        }

        /* Very small devices */
        @media (max-width: 360px) {
            .greeting-content {
                padding: 12px;
            }

            .greeting-message {
                font-size: 0.92rem;
            }

            .greeting-subtitle {
                font-size: 0.66rem;
            }
        }
</style>

<div class="row">
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
        $statusBg = 'var(--success-color)';
        $statusText = 'Active';
        $icon = 'fa-check-circle';
        $progressColor = 'bg-success';
        } elseif ($daysRemaining > 15) {
        $statusColor = 'black';
        $statusBg = 'var(--warning-color)';
        $statusText = 'Expiring Soon';
        $icon = 'fa-clock';
        $progressColor = 'bg-warning';
        } else {
        $statusColor = 'black';
        $statusBg = 'var(--danger-color)';
        $statusText = 'Critical';
        $icon = 'fa-exclamation-triangle';
        $progressColor = 'bg-danger';
        }
    @endphp
    @php
    $currentUser = Auth::user();

    $currentHour = now()->hour;

    $greeting = match (true) {
        $currentHour < 12 => 'Good Morning',
        $currentHour < 16 => 'Good Afternoon',
        default => 'Good Evening',
    };

    $firstName = ucwords(strtolower(trim($currentUser->first_name)));
@endphp
<!-- Global User Greeting -->
<div class="dashboard-greeting mb-4">
    <div class="greeting-content">
        <div class="greeting-text">
            <div class="greeting-message">
                {{ $greeting }}, {{ $firstName }}
            </div>

            <div class="greeting-subtitle">
                Welcome back. Here's what's happening in your dashboard today.
            </div>
        </div>

        <div class="greeting-time">
            <i class="far fa-clock"></i>
            <span id="dashboardCurrentTime"></span>
        </div>
    </div>
</div>
    <!-- Service Status Card - Premium Compact -->
    <div class="modern-card service-card-premium mb-4 col-12">
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
                    <small class="text-muted d-block">Current Plan.</small>
                    @if ($school->package == 'premium')
                    <span class="fw-semibold badge bg-success text-white"><i class="fas fa-crown"></i>
                        {{ ucfirst($school->package) }}</span>
                    @else
                    <span class="fw-semibold badge bg-warning text-dark"><i class="fas fa-star"></i>
                        {{ ucfirst($school->package) }}</span>
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
    <!-- Quick Stats Summary -->
    <div class="col-lg-12">
        <div class="row">
            <!-- Expense Categories Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #93dad6 0%, #5ec4bf 100%);">
                    <div class="card-body text-white">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-uppercase small font-weight-bold">Students Enrolled</div>
                                <div class="h3 mb-0 font-weight-bold">{{ count($students) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-graduate card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Transactions Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #e176a6 0%, #d04a88 100%);">
                    <div class="card-body text-white">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-uppercase small font-weight-bold">Today Expenses</div>
                                <div class="h3 mb-0 font-weight-bold">{{ number_format($daily) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-sack-dollar card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Transactions Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #c84fe0 0%, #9c27b0 100%);">
                    <div class="card-body text-white">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-uppercase small font-weight-bold">This Month</div>
                                <div class="h3 mb-0 font-weight-bold">{{ number_format($monthly) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yearly Transactions Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #098ddf 0%, #0568a8 100%);">
                    <div class="card-body text-white">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-uppercase small font-weight-bold">This Year</div>
                                <div class="h3 mb-0 font-weight-bold">{{ number_format($yearly) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <!-- Analytics Charts Section -->
    <div class="col-lg-12 mb-4">
        <div class="row">
            <!-- Daily Expense Trend -->
            <div class="col-xl-8 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-chart-bar me-2"></i> Recent Expense Transactions Trend
                        </h5>
                        <p class="chart-subtitle">Last 7 days Expenses transactions</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="recentTransactionsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Expense Distribution by Category -->
            <div class="col-xl-4 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-chart-pie me-2"></i> Expenses Status Distribution
                        </h5>
                        <p class="chart-subtitle">Current Expenses status overview</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="statusDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly & Payment Charts -->
    <div class="col-lg-12 mb-4">
        <div class="row">
            <!-- Monthly Comparison -->
            <div class="col-xl-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-chart-line me-2"></i> Expense Overview
                        </h5>
                        <p class="chart-subtitle">Daily, Monthly & Yearly comparison</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="expenseOverviewChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Payment Method Distribution -->
            <div class="col-xl-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-credit-card me-2"></i> Payment Methods
                        </h5>
                        <p class="chart-subtitle">Expense distribution by payment type</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="col-12 mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <h4 class="header-title mb-0">Recent Expense Transactions</h4>
                    </div>
                    @if (Auth::user()->school->package == 'premium')
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-action btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addTeacherModal">
                            <i class="fas fa-plus me-1"></i> New Bill
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('expenditure.all.transactions') }}"
                            class="btn btn-primary btn-sm float-right">
                            <i class="fas fa-list me-1"></i> View All
                        </a>
                    </div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-responsive-md" id="recentTransactionsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Reference #</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Payment Mode</th>
                                <th>Status</th>
                                <th>Issued date</th>
                                <th>Issued by</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($recent))
                            <tr>
                                <td colspan="9" class="text-center text-danger py-4">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block"></i>
                                    No recent Expense bills were found!
                                </td>
                            </tr>
                            @else
                            @foreach ($recent as $row)
                            <tr>
                                <td class="fw-bold">{{ $loop->iteration }}</td>
                                <td class="fw-bold text-primary">{{ strtoupper($row['reference_number']) }}
                                </td>
                                <td>
                                    <span class="text-truncate" title="{{ $row['description'] }}">
                                        {{ Str::limit($row['description'], 50) }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-success">
                                    {{ number_format($row['amount']) }}
                                </td>
                                <td>
                                    @if ($row['payment_mode'] == 'cash')
                                    <span class="badge bg-success text-white">{{ ucwords($row['payment_mode']) }}</span>
                                    @elseif ($row['payment_mode'] == 'bank')
                                    <span class="badge bg-danger text-white">{{ ucwords($row['payment_mode']) }}</span>
                                    @else
                                    <span class="badge bg-primary text-white">{{ ucwords($row['payment_mode']) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($row['status'] == 'active')
                                    <span class="badge bg-success text-white">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ ucwords($row['status']) }}
                                    </span>
                                    @elseif ($row['status'] == 'pending')
                                    <span class="badge bg-warning text-white">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ ucwords($row['status']) }}
                                    </span>
                                    @else
                                    <span class="badge bg-danger text-white">
                                        <i class="fas fa-times-circle me-1"></i>
                                        {{ ucwords($row['status']) }}
                                    </span>
                                    @endif
                                </td>
                                <td class="text-muted">
                                    <small>
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($row['expense_date'])->format('d-m-Y') }}
                                    </small>
                                </td>
                                <td>
                                    @php
                                    $user = \App\Models\User::where('id', $row['user_id'])->first();
                                    @endphp
                                    @if ($user == null)
                                    <span class="text-muted">N/A</span>
                                    @else
                                    <div class="d-flex align-items-center">
                                        {{-- <div
                                            class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-1"
                                            style="width: 35px; height: 35px;">
                                            <span class="text-white fw-bold small">
                                                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{
                                                strtoupper(substr($user->last_name, 0, 1)) }}
                                            </span> --}}
                                        </div>
                                        <span class="ms-2 fw-semibold">
                                            {{ ucwords(strtolower($user->first_name . '. ' . $user->last_name[0])) }}
                                        </span>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button"
                                            class="btn btn-xs btn-outline-primary edit-transaction-btn"
                                            title="Edit Bill" data-transaction-id="{{ $row['id'] }}"
                                            data-bs-toggle="modal" data-bs-target="#editTransactionModal">
                                            <i class="fas fa-pencil"></i>
                                        </button>
                                        @if ($row['status'] == 'active')
                                        <a href="#" title="View Bill" data-bs-toggle="modal"
                                            class="btn btn-sm btn-outline-info"
                                            data-bs-target="#viewModal{{ $row['reference_number'] }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" title="Cancel Bill" data-bs-toggle="modal"
                                            class="btn btn-sm btn-outline-warning"
                                            data-bs-target="#cancelModal{{ $row['reference_number'] }}">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                        @else
                                        <a href="#" title="View Bill" data-bs-toggle="modal"
                                            class="btn btn-sm btn-outline-info"
                                            data-bs-target="#viewModal{{ $row['reference_number'] }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form
                                            action="{{ route('expenditure.delete.bill', ['bill' => Hashids::encode($row['id'])]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit"
                                                title="Delete Bill"
                                                onclick="return confirm('Are you sure you want to delete this bill?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals Section - Placed OUTSIDE the table -->
@if (!empty($recent))
@foreach ($recent as $row)
<!-- View Modal -->
<div class="modal fade" id="viewModal{{ $row['reference_number'] }}" tabindex="-1"
    aria-labelledby="viewModalLabel{{ $row['reference_number'] }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewModalLabel{{ $row['reference_number'] }}">
                    <i class="fas fa-receipt me-2"></i> Expense Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fas fa-close text-danger"></i></button>
            </div>
            <div class="modal-body p-0">
                <!-- Header with Reference & Status -->
                <div class="bg-light p-4 border-bottom">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Reference Number</h6>
                            <h4 class="text-primary fw-bold">{{ strtoupper($row['reference_number']) }}</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6 class="text-muted mb-1">Status</h6>
                            @if ($row['status'] == 'active')
                            <span class="badge bg-success text-white fs-6">{{ ucwords($row['status']) }}</span>
                            @elseif ($row['status'] == 'pending')
                            <span class="badge bg-warning text-white fs-6">{{ ucwords($row['status']) }}</span>
                            @else
                            <span class="badge bg-danger text-white fs-6">{{ ucwords($row['status']) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs nav-justified" id="transactionTabs{{ $row['reference_number'] }}"
                    role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab{{ $row['reference_number'] }}"
                            data-bs-toggle="tab" data-bs-target="#details{{ $row['reference_number'] }}" type="button"
                            role="tab">
                            <i class="fas fa-info-circle me-2"></i> Details
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payment-tab{{ $row['reference_number'] }}" data-bs-toggle="tab"
                            data-bs-target="#payment{{ $row['reference_number'] }}" type="button" role="tab">
                            <i class="fas fa-credit-card me-2"></i> Payment
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="timeline-tab{{ $row['reference_number'] }}" data-bs-toggle="tab"
                            data-bs-target="#timeline{{ $row['reference_number'] }}" type="button" role="tab">
                            <i class="fas fa-history me-2"></i> Timeline
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="attachment-table{{ $row['reference_number'] }}"
                            data-bs-toggle="tab" data-bs-target="#attachment{{ $row['reference_number'] }}"
                            type="button" role="tab">
                            <i class="fas fa-paperclip me-2"></i> Attachments
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content p-4" id="transactionTabsContent{{ $row['reference_number'] }}">
                    <!-- Details Tab -->
                    <div class="tab-pane fade show active" id="details{{ $row['reference_number'] }}" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Account</label>
                                <p class="fw-semibold">
                                    {{ ucwords(strtolower($row['expense_type'] ?? 'N/A')) }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Amount</label>
                                <p class="fw-bold fs-5 text-primary">TZS {{ number_format($row['amount']) }}
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted small mb-1">Description</label>
                                <p class="fw-semibold">{{ $row['description'] ?? 'No description provided' }}
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                @php
                                $user = \App\Models\User::where('id', $row['user_id'])->first();
                                @endphp
                                <label class="form-label text-muted small mb-1">Issued By</label>
                                <p class="fw-semibold">
                                    @if ($user == null)
                                    N/A
                                    @else
                                    {{ ucwords(strtolower($user->first_name . ' ' . $user->last_name)) }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Created Date</label>
                                <p class="fw-semibold">
                                    @if (isset($row['created_at']))
                                    {{ \Carbon\Carbon::parse($row['created_at'])->format('d-m-Y h:i A') }}
                                    @else
                                    N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Tab -->
                    <div class="tab-pane fade" id="payment{{ $row['reference_number'] }}" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Payment Mode</label>
                                <p class="fw-semibold">
                                    @if ($row['payment_mode'] == 'cash')
                                    <span class="text-success fs-6">{{ ucwords($row['payment_mode']) }}</span>
                                    @elseif($row['payment_mode'] == 'mobile_money')
                                    <span class="text-primary fs-6">{{ ucwords($row['payment_mode']) }}</span>
                                    @else
                                    <span class="text-danger fs-6">{{ ucwords($row['payment_mode']) }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Bill Type</label>
                                <p class="fw-semibold">
                                    <span class="badge bg-info fs-6 text-white">Expense</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Tab -->
                    <div class="tab-pane fade" id="timeline{{ $row['reference_number'] }}" role="tabpanel">
                        <div class="timeline">
                            @if (isset($row['created_at']))
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold">Bill Created</h6>
                                    <p class="text-muted small mb-0">
                                        {{ \Carbon\Carbon::parse($row['created_at'])->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            </div>
                            @endif

                            @if (isset($row['updated_at']) && $row['created_at'] != $row['updated_at'])
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold">Bill Updated</h6>
                                    <p class="text-muted small mb-0">
                                        {{ \Carbon\Carbon::parse($row['updated_at'])->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            </div>
                            @endif

                            @if ($row['status'] == 'cancelled')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold">Bill Cancelled</h6>
                                    <p class="text-muted small mb-0">
                                        {{ \Carbon\Carbon::parse($row['updated_at'])->format('M d, Y h:i A') }}
                                    </p>
                                    @if (isset($row['cancel_reason']))
                                    <p>Reason: <span class="small text-danger">{{ $row['cancel_reason'] }}</span>
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- attachment Tab -->
                    <div class="tab-pane fade" id="attachment{{ $row['reference_number'] }}" role="tabpanel">
                        <div class="timeline">
                            @if (isset($row['attachment']))
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold">Bill Receipt</h6>

                                    @if (!empty($row['attachment_url']))
                                    @php
                                    $extension = strtolower(
                                    pathinfo($row['attachment'], PATHINFO_EXTENSION),
                                    );
                                    $isImage = in_array($extension, [
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'gif',
                                    ]);
                                    @endphp

                                    @if ($isImage)
                                    <!-- Display Image -->
                                    <a href="{{ $row['attachment_url'] }}" target="_blank">
                                        <img src="{{ $row['attachment_url'] }}" alt="Receipt"
                                            style="max-width: 100px; max-height: 100px; border-radius: 5px;">
                                    </a>
                                    @elseif($extension === 'pdf')
                                    <!-- Display PDF -->
                                    <a href="{{ $row['attachment_url'] }}" target="_blank"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-pdf"></i> View PDF Receipt
                                    </a>
                                    @else
                                    <!-- Other file types -->
                                    <a href="{{ $row['attachment_url'] }}" target="_blank"
                                        class="btn btn-sm btn-secondary">
                                        <i class="fas fa-file"></i> View File
                                    </a>
                                    @endif
                                    @else
                                    <span class="text-muted">No attachment</span>
                                    @endif
                                </div>
                            </div>
                            @else
                            <span class="text-muted">No attachment</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Close
                </button>
                @if ($row['status'] == 'active')
                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                    data-bs-target="#cancelModal{{ $row['reference_number'] }}">
                    <i class="fas fa-ban me-2"></i> Cancel
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal{{ $row['reference_number'] }}" tabindex="-1"
    aria-labelledby="cancelModalLabel{{ $row['reference_number'] }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cancelModalLabel{{ $row['reference_number'] }}">
                    Cancel Bill - {{ strtoupper($row['reference_number']) }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fas fa-close text-danger"></i></button>
            </div>
            <form action="{{ route('expenditure.cancel.bill', ['bill' => Hashids::encode($row['id'])]) }}"
                method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cancelReason{{ $row['reference_number'] }}" class="form-label">Cancel
                            Reason <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-custom" id="cancelReason{{ $row['reference_number'] }}"
                            name="cancel_reason" placeholder="Enter cancel reason" required>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">
                            <strong>Bill Details:</strong><br>
                            Reference: {{ strtoupper($row['reference_number']) }}<br>
                            Amount: {{ number_format($row['amount']) }}<br>
                            Account: {{ ucwords(strtolower($row['expense_type'] ?? 'N/A')) }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to cancel this Bill?')">
                        <i class="fas fa-ban me-2"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif
{{-- register new transactions modal --}}
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTeacherModalLabel"><i class="fas fa-exchange-alt"></i> Manage new
                    Expenses and Bills</h5>
                <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fas fa-close"></i></button>
            </div>
            <div class="modal-body">
                <!-- NIMEONGEZA id="addBillForm" HAPA -->
                <form class="needs-validation" novalidate action="{{ route('expenditure.store') }}" method="POST"
                    enctype="multipart/form-data" id="addBillForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Date</label>
                            <input type="date" name="date" class="form-control-custom"
                                min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" id="date"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            @error('date')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Account</label>
                            <select name="category" id="category" class="form-control-custom" required>
                                <option value="">--Select Account--</option>
                                @if (empty($categories))
                                <option value="">{{ 'No accounts were found' }}</option>
                                @else
                                @foreach ($categories as $row)
                                <option value="{{ $row['id'] }}">
                                    {{ ucwords(strtolower($row['expense_type'])) }}</option>
                                @endforeach
                                @endif
                            </select>
                            @error('category')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea type="text" required name="description" class="form-control-custom"
                                id="description" placeholder="Enter service description e.g. umeme"
                                value="{{ old('description') }}"></textarea>
                            @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Amount (in. TZS)</label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control-custom" step="0.01" min="0"
                                    placeholder="0.00" value="{{ old('amount') }}">
                            </div>
                            @error('amount')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Payment Mode</label>
                            <select name="payment" id="payment" class="form-control-custom">
                                <option value="cash" selected>Cash</option>
                                <option value="mobile_money">Mobile Payment</option>
                                <option value="bank">Bank Transfer</option>
                                {{-- <option value="other">Others</option> --}}
                            </select>
                            @error('payment')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="street" class="form-label">Receipt Attachment <span
                                    class="text-muted">(optional)</span></label>
                            <input type="file" name="attachment" class="form-control-custom" id="attachment"
                                value="{{ old('attachment') }}">
                            @error('attachment')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="saveButton" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- edit transaction modal --}}
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editTransactionModalLabel">
                    <i class="fas fa-pencil me-2"></i>
                    Edit expense Bill - <span id="modalReferenceNumber" class="text-uppercase fw-bold"></span>
                </h5>
                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fas fa-close"></i></button>
            </div>
            <div class="modal-body">
                <form id="editTransactionForm" class="needs-validation" novalidate method="POST"
                    enctype="multipart/form-data" data-no-preloader>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editTransactionId" name="transaction_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editDate" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control-custom" id="editDate"
                                min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            <div class="invalid-feedback">Please select a valid date</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editCategory" class="form-label">Account <span
                                    class="text-danger">*</span></label>
                            <select name="category" id="editCategory" class="form-control-custom" required>
                                <option value="">--Select Account--</option>
                                @if (empty($categories))
                                <option value="">No accounts found</option>
                                @else
                                @foreach ($categories as $category)
                                <option value="{{ $category['id'] }}">
                                    {{ ucwords(strtolower($category['expense_type'])) }}</option>
                                @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback">Please select an account</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editDescription" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <textarea name="description" class="form-control-custom" id="editDescription" rows="3"
                                placeholder="Enter transaction description" required></textarea>
                            <div class="invalid-feedback">Please enter a description</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editAmount" class="form-label">Amount (in TZS) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="amount" class="form-control-custom" id="editAmount"
                                    step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="invalid-feedback">Please enter a valid amount</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="editPayment" class="form-label">Payment Mode <span
                                    class="text-danger">*</span></label>
                            <select name="payment" id="editPayment" class="form-control-custom" required>
                                <option value="cash">Cash</option>
                                <option value="mobile_money">Mobile Payment</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                            <div class="invalid-feedback">Please select payment mode</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editStatus" class="form-label">Status <span class="text-danger"></span></label>
                            <select name="status" id="editStatus" class="form-control-custom" required>
                                <option value="active">Active</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <div class="invalid-feedback">Please select payment mode</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editAttachment" class="form-label">
                                Receipt Attachment
                                <span class="text-muted small">(optional - leave empty to keep current)</span>
                            </label>
                            <input type="file" name="attachment" class="form-control-custom" id="editAttachment"
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <div class="form-text">Max file size: 5MB. Allowed: JPG, PNG, PDF, DOC</div>
                        </div>
                    </div>

                    <!-- Current Attachment Preview -->
                    <div class="row" id="currentAttachmentSection" style="display: none;">
                        <div class="col-12 mb-3">
                            <label class="form-label">Current Attachment:</label>
                            <div id="currentAttachmentPreview" class="border rounded p-3 bg-light">
                                <!-- Attachment preview will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="updateTransactionBtn">
                            <i class="fas fa-save me-2"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const clockElement = document.getElementById('dashboardCurrentTime');

        if (!clockElement) {
            return;
        }

        function updateDashboardClock() {
            const now = new Date();

            const time = now.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            clockElement.textContent = time;
        }

        updateDashboardClock();

        setInterval(updateDashboardClock, 1000);
    // ============================================
    // 1. COUNTDOWN FUNCTIONALITY (EXISTING)
    // ============================================
    function initializeCountdown() {
        const endDateStr = '{{ $jsEndDate }}';
        const endDate = new Date(endDateStr.replace(' ', 'T')).getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endDate - now;

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

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const format = (num) => num.toString().padStart(2, '0');

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

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    initializeCountdown();

    // ============================================
    // 2. DATATABLE INITIALIZATION (EXISTING)
    // ============================================
    if (document.getElementById('recentTransactionsTable')) {
        $('#recentTransactionsTable').DataTable({
            "language": {
                "search": "<i class='fas fa-search'></i>",
                "searchPlaceholder": "Search...",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "paginate": {
                    "previous": "<i class='fas fa-chevron-left'></i>",
                    "next": "<i class='fas fa-chevron-right'></i>"
                }
            },
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "pageLength": 5
        });
    }

    // ============================================
    // 3. CHART DATA PREPARATION (EXISTING)
    // ============================================
    const chartData = {
        recentTransactions: prepareRecentTransactionsData(),
        statusDistribution: prepareStatusDistributionData(),
        expenseOverview: prepareExpenseOverviewData(),
        paymentMethods: preparePaymentMethodData()
    };

    // Recent Transactions Chart
    const recentCtx = document.getElementById('recentTransactionsChart').getContext('2d');
    new Chart(recentCtx, {
        type: 'line',
        data: {
            labels: chartData.recentTransactions.labels,
            datasets: [{
                label: 'Bills Amount (TZS)',
                data: chartData.recentTransactions.data,
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
                legend: { display: true, position: 'top' },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `TZS ${context.parsed.y.toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'TZS ' + (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000) return 'TZS ' + (value / 1000).toFixed(0) + 'K';
                            return 'TZS ' + value;
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: chartData.statusDistribution.labels,
            datasets: [{
                data: chartData.statusDistribution.data,
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b', '#858796'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } },
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

    // Expense Overview Chart
    const overviewCtx = document.getElementById('expenseOverviewChart').getContext('2d');
    new Chart(overviewCtx, {
        type: 'bar',
        data: {
            labels: chartData.expenseOverview.labels,
            datasets: [{
                label: 'Amount (TZS)',
                data: chartData.expenseOverview.data,
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(54, 185, 204, 0.8)'
                ],
                borderColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'TZS ' + (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000) return 'TZS ' + (value / 1000).toFixed(0) + 'K';
                            return 'TZS ' + value;
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Payment Method Chart
    const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: chartData.paymentMethods.labels,
            datasets: [{
                data: chartData.paymentMethods.data,
                backgroundColor: ['#1cc88a', '#e74a3b', '#4e73df', '#f6c23e'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // ============================================
    // 4. DATA PREPARATION FUNCTIONS
    // ============================================
    function prepareRecentTransactionsData() {
        @if (!empty($last7DaysExpenses))
            const last7Days = [];
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                const dateStr = date.toISOString().split('T')[0];
                const dayExpense = @json($last7DaysExpenses).find(expense =>
                    expense.expense_date === dateStr
                );
                last7Days.push({
                    label: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
                    amount: dayExpense ? parseFloat(dayExpense.total_amount) : 0
                });
            }
            return {
                labels: last7Days.map(day => day.label),
                data: last7Days.map(day => day.amount)
            };
        @else
            return { labels: ['No Data'], data: [0] };
        @endif
    }

    function prepareStatusDistributionData() {
        @if (!empty($recent))
            const recentData = @json($recent);
            const statusCount = { active: 0, pending: 0, cancelled: 0, completed: 0 };
            recentData.forEach(transaction => {
                const status = transaction.status?.toLowerCase() || 'pending';
                if (statusCount.hasOwnProperty(status)) {
                    statusCount[status]++;
                } else {
                    statusCount.completed++;
                }
            });
            return {
                labels: ['Active', 'Pending', 'Cancelled', 'Completed'],
                data: [statusCount.active, statusCount.pending, statusCount.cancelled, statusCount.completed]
            };
        @else
            return { labels: ['No Data'], data: [1] };
        @endif
    }

    function prepareExpenseOverviewData() {
        return {
            labels: ['Daily', 'Monthly', 'Yearly'],
            data: [{{ $daily }}, {{ $monthly }}, {{ $yearly }}]
        };
    }

    function preparePaymentMethodData() {
        @if (!empty($recent))
            const recentData = @json($recent);
            const paymentCount = { cash: 0, bank: 0, mobile_money: 0, other: 0 };
            recentData.forEach(transaction => {
                const method = transaction.payment_mode?.toLowerCase() || 'other';
                if (paymentCount.hasOwnProperty(method)) {
                    paymentCount[method]++;
                } else {
                    paymentCount.other++;
                }
            });
            const labels = [], data = [];
            if (paymentCount.cash > 0) { labels.push('Cash'); data.push(paymentCount.cash); }
            if (paymentCount.bank > 0) { labels.push('Bank'); data.push(paymentCount.bank); }
            if (paymentCount.mobile_money > 0) { labels.push('Mobile Money'); data.push(paymentCount.mobile_money); }
            if (paymentCount.other > 0) { labels.push('Other'); data.push(paymentCount.other); }
            return { labels: labels, data: data };
        @else
            return { labels: ['No Data'], data: [1] };
        @endif
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ============================================
    // 5. EDIT TRANSACTION FUNCTIONALITY (EXISTING)
    // ============================================
    const editButtons = document.querySelectorAll('.edit-transaction-btn');
    const editModal = document.getElementById('editTransactionModal');
    const editForm = document.getElementById('editTransactionForm');
    const modalTitle = document.getElementById('modalReferenceNumber');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const transactionId = this.getAttribute('data-transaction-id');
            loadTransactionData(transactionId);
        });
    });

    function loadTransactionData(transactionId) {
        const updateBtn = document.getElementById('updateTransactionBtn');
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Loading...';

        fetch(`/expenditure/get-transaction/${transactionId}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.status || data.success) {
                    populateEditForm(data.transaction);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to load bills data',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load Bills data',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            })
            .finally(() => {
                updateBtn.disabled = false;
                updateBtn.innerHTML = '<i class="fas fa-save me-2"></i> Update';
            });
    }

    function populateEditForm(transaction) {
        modalTitle.textContent = transaction.reference_number;
        document.getElementById('editTransactionId').value = transaction.id;
        document.getElementById('editDate').value = transaction.expense_date;
        document.getElementById('editCategory').value = transaction.expense_type_id || transaction.category_id;
        document.getElementById('editDescription').value = transaction.description || '';
        document.getElementById('editAmount').value = transaction.amount || '';
        document.getElementById('editPayment').value = transaction.payment_mode || 'cash';
        document.getElementById('editStatus').value = transaction.status || 'active';

        const attachmentSection = document.getElementById('currentAttachmentSection');
        const attachmentPreview = document.getElementById('currentAttachmentPreview');

        if (transaction.attachment_url && transaction.attachment) {
            const extension = transaction.attachment.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(extension);
            const isPDF = extension === 'pdf';

            let previewHtml = '';
            if (isImage) {
                previewHtml = `
                    <div class="d-flex align-items-center">
                        <img src="${transaction.attachment_url}" alt="Current receipt"
                            style="max-width: 100px; max-height: 100px; border-radius: 5px;" class="me-3">
                        <div>
                            <strong>Current Attachment:</strong><br>
                            <a href="${transaction.attachment_url}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                        </div>
                    </div>
                `;
            } else if (isPDF) {
                previewHtml = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf text-danger fa-3x me-3"></i>
                        <div>
                            <strong>Current Attachment:</strong><br>
                            <a href="${transaction.attachment_url}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fas fa-eye me-1"></i> View PDF
                            </a>
                        </div>
                    </div>
                `;
            } else {
                previewHtml = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file text-secondary fa-3x me-3"></i>
                        <div>
                            <strong>Current Attachment:</strong><br>
                            <a href="${transaction.attachment_url}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                `;
            }
            attachmentPreview.innerHTML = previewHtml;
            attachmentSection.style.display = 'block';
        } else {
            attachmentSection.style.display = 'none';
        }
    }

    // ============================================
    // 6. FORM SUBMISSION - RACE CONDITION PREVENTION
    // ============================================

    // 6.1 ADD NEW BILL FORM - FIXED
    const addBillForm = document.getElementById('addBillForm');
    if (addBillForm) {
        addBillForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('saveButton');

            // Check form validation
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('was-validated');
                const firstInvalid = this.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
                // Re-enable button if validation fails
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save';
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                return;
            }

            // DISABLE BUTTON - Prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting...';
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';

            // Store original text
            if (!submitBtn.getAttribute('data-original-text')) {
                submitBtn.setAttribute('data-original-text', 'Save');
            }

            // Note: Form will submit naturally, button stays disabled
            // If form submission fails, button will be re-enabled by modal close handler
        });
    }

    // 6.2 EDIT TRANSACTION FORM
    if (editForm) {
        // Remove existing submit listeners to avoid duplicates
        const newEditForm = editForm.cloneNode(true);
        editForm.parentNode.replaceChild(newEditForm, editForm);

        const updatedEditForm = document.getElementById('editTransactionForm');
        const updateBtn = document.getElementById('updateTransactionBtn');

        if (updatedEditForm && updateBtn) {
            updatedEditForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Check form validation
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    const firstInvalid = this.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                    return;
                }

                // DISABLE BUTTON - Prevent double submission
                updateBtn.disabled = true;
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Updating...';
                updateBtn.style.opacity = '0.7';
                updateBtn.style.cursor = 'not-allowed';

                if (!updateBtn.getAttribute('data-original-text')) {
                    updateBtn.setAttribute('data-original-text', 'Update');
                }

                const formData = new FormData(this);
                const transactionId = document.getElementById('editTransactionId').value;

                fetch(`/expenditure/update-transaction/${transactionId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    if (data.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Expense bill updated successfully',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editTransactionModal'));
                            if (modal) modal.hide();
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update bill',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                        resetEditButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong while updating',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    resetEditButton();
                });
            });
        }
    }

    function resetEditButton() {
        const updateBtn = document.getElementById('updateTransactionBtn');
        if (updateBtn) {
            updateBtn.disabled = false;
            const originalText = updateBtn.getAttribute('data-original-text') || 'Update';
            updateBtn.innerHTML = `<i class="fas fa-save me-2"></i> ${originalText}`;
            updateBtn.style.opacity = '1';
            updateBtn.style.cursor = 'pointer';
        }
    }

    // 6.3 CANCEL BILL FORMS
    document.querySelectorAll('form[action*="expenditure/cancel-bill"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');

            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Cancelling...';
                submitBtn.style.opacity = '0.7';
                submitBtn.style.cursor = 'not-allowed';
                if (!submitBtn.getAttribute('data-original-text')) {
                    submitBtn.setAttribute('data-original-text', 'Cancel');
                }
            }
        });
    });

    // 6.4 DELETE BILL FORMS
    document.querySelectorAll('form[action*="expenditure/delete-bill"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this bill?')) {
                e.preventDefault();
                return;
            }
            const deleteBtn = this.querySelector('button[type="submit"]');
            if (deleteBtn) {
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                deleteBtn.style.opacity = '0.7';
                deleteBtn.style.cursor = 'not-allowed';
            }
        });
    });

    // ============================================
    // 7. RE-ENABLE BUTTONS WHEN MODALS CLOSE
    // ============================================
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            this.querySelectorAll('button[type="submit"]').forEach(btn => {
                btn.disabled = false;
                const originalText = btn.getAttribute('data-original-text');
                if (originalText) {
                    btn.innerHTML = originalText;
                }
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            });
            this.querySelectorAll('form').forEach(form => {
                form.classList.remove('was-validated');
            });
        });
    });

    // ============================================
    // 8. STORE ORIGINAL BUTTON TEXT
    // ============================================
    document.querySelectorAll('button[type="submit"]').forEach(btn => {
        if (!btn.getAttribute('data-original-text')) {
            btn.setAttribute('data-original-text', btn.innerHTML.trim());
        }
    });

    // ============================================
    // 9. GLOBAL DOUBLE SUBMISSION PREVENTION
    // ============================================
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.tagName === 'FORM') {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.disabled) {
                e.preventDefault();
                return false;
            }
        }
    }, true);

    // ============================================
    // 10. RECOVERY ON PAGE UNLOAD
    // ============================================
    window.addEventListener('beforeunload', function() {
        document.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.disabled = false;
        });
    });

    // ============================================
    // 11. RESET EDIT FORM WHEN MODAL HIDES
    // ============================================
    if (editModal) {
        editModal.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('editTransactionForm');
            if (form) form.reset();
            const attachmentSection = document.getElementById('currentAttachmentSection');
            if (attachmentSection) attachmentSection.style.display = 'none';
            resetEditButton();
        });
    }

    // ============================================
    // 12. FORM VALIDATION ON INPUT
    // ============================================
    document.addEventListener('input', function(e) {
        const form = e.target.closest('form');
        if (form && form.classList.contains('was-validated')) {
            if (e.target.checkValidity()) {
                e.target.classList.remove('is-invalid');
            } else {
                e.target.classList.add('is-invalid');
            }
        }
    });

    console.log('✅ All functionality initialized successfully');
});

// ============================================
// AUTHORIZATION CHECK
// ============================================
@if (Auth::user()->usertype != 5)
    window.location.href = '/error-page';
@endif
</script>

<style>
    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
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

    /* Chart Container Improvements */
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

    .chart-wrapper {
        flex: 1;
        min-height: 280px;
        position: relative;
        width: 100%;
    }

    .chart-wrapper canvas {
        width: 100% !important;
        height: 100% !important;
        max-height: 280px;
    }

    /* Specific height adjustments for different chart types */
    #recentTransactionsChart {
        min-height: 250px;
        max-height: 280px;
    }

    #statusDistributionChart {
        min-height: 250px;
        max-height: 280px;
    }

    #expenseOverviewChart {
        min-height: 230px;
        max-height: 260px;
    }

    #paymentMethodChart {
        min-height: 230px;
        max-height: 260px;
    }

    /* Ensure responsive behavior */
    @media (max-width: 768px) {
        .chart-wrapper {
            min-height: 250px;
        }

        .chart-wrapper canvas {
            max-height: 250px;
        }

        #recentTransactionsChart,
        #statusDistributionChart,
        #expenseOverviewChart,
        #paymentMethodChart {
            min-height: 220px;
            max-height: 250px;
        }
    }

    @media (max-width: 576px) {
        .chart-wrapper {
            min-height: 220px;
        }

        .chart-wrapper canvas {
            max-height: 220px;
        }

        #recentTransactionsChart,
        #statusDistributionChart,
        #expenseOverviewChart,
        #paymentMethodChart {
            min-height: 200px;
            max-height: 220px;
        }
    }
</style>
@endsection
