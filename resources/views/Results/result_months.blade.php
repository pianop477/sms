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
            padding: 20px;
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
            padding: 25px 30px;
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

        .year-highlight {
            color: #ffd700;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .card-body {
            padding: 30px;
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

        .month-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .month-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.7);
            overflow: hidden;
        }

        .month-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .month-header:hover {
            background: rgba(78, 84, 200, 0.05);
        }

        .month-header h6 {
            color: var(--primary);
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .month-icon {
            transition: transform 0.3s ease;
        }

        .month-card.active .month-icon {
            transform: rotate(90deg);
        }

        .date-list {
            padding: 0 20px 20px;
            display: none;
        }

        .month-card.active .date-list {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .date-instruction {
            color: var(--danger);
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
            padding: 10px;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 8px;
        }

        .date-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .date-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .date-link {
            text-decoration: none;
            color: var(--success);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .date-link:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }

        .pdf-icon {
            font-size: 20px;
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

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        .month-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 50px;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .month-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h4 class="header-title">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Select Month - for <span class="year-highlight">{{ $year}}</span>
                        </h4>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('result.byType', ['student' => Hashids::encode($students->id), 'year' => $year]) }}" class="btn btn-back btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-file-alt floating-icons"></i>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <p class="instruction-text"><i class="fas fa-mouse-pointer me-2"></i> Select specific month</p>
                </div>

                @if ($months->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h5> No Result Records Found!</h5>
                        <p class="mb-0"> There are no result records for year {{$year}}</p>
                    </div>
                @else
                    <div class="month-list">
                        @foreach ($months as $month => $dates)
                            <div class="month-card">
                                <div class="month-header" data-month="{{ Str::slug($month) }}">
                                    <h6>
                                        <i class="fas fa-chevron-right month-icon"></i>
                                        {{ \Carbon\Carbon::parse($month)->format('F') }} - {{$year}}
                                    </h6>
                                    <span class="month-badge">{{ count($dates) }} dates</span>
                                </div>
                                <div id="{{ Str::slug($month) }}" class="date-list">
                                    <div class="date-instruction">
                                        <i class="fas fa-info-circle me-2"></i> Choose Date to get result
                                    </div>
                                    @foreach ($dates as $date => $results)
                                        <div class="date-item">
                                            <a href="{{ route('results.student.get', ['student' => Hashids::encode($students->id), 'year' => $year, 'exam_id' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}" target="" class="date-link">
                                                <i class="fas fa-file-pdf pdf-icon"></i>
                                                {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".month-header").forEach(header => {
                header.addEventListener("click", function () {
                    const monthCard = this.parentElement;
                    const monthDiv = document.getElementById(this.dataset.month);

                    // Toggle active class
                    monthCard.classList.toggle("active");

                    // Toggle display
                    if (monthDiv.style.display === "block") {
                        monthDiv.style.display = "none";
                    } else {
                        monthDiv.style.display = "block";
                    }
                });
            });
        });
    </script>
@endsection
