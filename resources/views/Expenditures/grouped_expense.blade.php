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
            margin-top: 30px;
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
            text-align: center;
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
            padding: 10px;
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

        .instruction-text {
            color: var(--danger);
            font-weight: 600;
            text-align: center;
            margin-bottom: 25px;
            font-size: 18px;
            position: relative;
            display: inline-block;
            background: rgba(220, 53, 69, 0.1);
            padding: 10px 20px;
            border-radius: 50px;
        }

        .instruction-text::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 3px;
        }

        .year-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .year-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.7);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 180px;
        }

        .year-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .year-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .year-card h5 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 22px;
        }

        .year-card .btn-view {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
        }

        .year-card .btn-view:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .year-icon {
            font-size: 40px;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fff9e6 0%, #ffeeb5 100%);
            border-radius: 15px;
            border-left: 5px solid var(--warning);
            grid-column: 1 / -1;
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

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        @media (max-width: 768px) {
            .year-grid {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 24px;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(78, 84, 200, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0);
            }
        }

        .year-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 50px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .student-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
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
        }

        .info-value {
            font-weight: 700;
            color: var(--primary);
            font-size: 16px;
        }
    </style>
    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h4 class="header-title">
                            <i class="fas fa-chart-line me-2"></i>
                            BILL REPORT
                        </h4>
                    </div>
                    <div class="col-md-2">
                        <a href="{{route('expenditure.all.transactions')}}" class="btn btn-back btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-graduation-cap floating-icons"></i>
            </div>
            <div class="card-body">
                <!-- Student Information -->
                <div class="text-center mb-4">
                    <p class="instruction-text"><i class="fas fa-mouse-pointer me-2"></i> Select Year of Study - To view Bills</p>
                </div>

                @if (empty($groupedData))
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h5> No Result Records Found!</h5>
                        <p class="mb-0"> There are no records available.</p>
                    </div>
                @else
                    <div class="year-grid">
                        @foreach ($groupedData as $year => $transactions)
                            <div class="year-card">
                                <span class="year-badge">YEAR</span>
                                <div class="year-icon">
                                    <i class="fas fa-calendar-star"></i>
                                </div>
                                <h5>{{ $year }} Expense Bills</h5>
                                <a href="{{route('expenditure.previous.transactions', ['year' => $year])}}" class="btn-view pulse-animation">
                                    <i class="fas fa-eye me-2"></i> View Bills
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endsection
