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

        .year-highlight {
            color: #ffd700;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
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

        .exam-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .exam-card {
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

        .exam-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
        }

        .exam-card.single::before {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .exam-card.combined::before {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
        }

        .exam-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .exam-card h5 {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 20px;
            text-transform: capitalize;
        }

        .exam-card .btn-view {
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
        }

        .exam-card.single .btn-view {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .exam-card.combined .btn-view {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
        }

        .exam-card .btn-view:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .exam-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .exam-card.single .exam-icon {
            color: var(--primary);
        }

        .exam-card.combined .exam-icon {
            color: var(--success);
        }

        .exam-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            color: white;
            border-radius: 50px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .exam-card.single .exam-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .exam-card.combined .exam-badge {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
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

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
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

        @media (max-width: 768px) {
            .exam-grid {
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
    </style>
    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h4 class="header-title">
                            <i class="fas fa-tasks me-2"></i>
                            Select Examination Type - For Year <span class="year-highlight"> {{$year}}</span>
                        </h4>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('results.index', ['student' => Hashids::encode($students->id)]) }}" class="btn btn-back btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-file-alt floating-icons"></i>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <p class="instruction-text"><i class="fas fa-mouse-pointer me-2"></i> Select Examination type</p>
                </div>

                @if ($paginated->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h5> No Result Records Found!</h5>
                        <p class="mb-0"> There are no result records available for {{$year}}.</p>
                    </div>
                @else
                    <div class="exam-grid">
                        @foreach ($paginated as $item)
                            <div class="exam-card {{ $item['type'] === 'exam' ? 'single' : 'combined' }}">
                                <span class="exam-badge">
                                    {{ $item['type'] === 'exam' ? 'Single' : 'Combined' }}
                                </span>
                                <div class="exam-icon">
                                    <i class="{{ $item['type'] === 'exam' ? 'fas fa-file-alt' : 'fas fa-file-pdf' }}"></i>
                                </div>
                                <h5>{{ $item['label'] }}</h5>
                                @if ($item['type'] === 'exam')
                                    <a href="{{ route('result.byMonth', ['student' => Hashids::encode($students->id), 'year' => $year, 'exam_type' => Hashids::encode($item['id'])]) }}" class="btn-view pulse-animation">
                                        <i class="fas fa-eye me-2"></i> View Results
                                    </a>
                                @else
                                    <a href="{{route('student.combined.report', ['school' => Hashids::encode($students->school_id), 'year' => $year, 'class' => Hashids::encode($students->class_id), 'report' => Hashids::encode($item['id']), 'student' => Hashids::encode($students->id)])}}" class="btn-view pulse-animation">
                                        <i class="fas fa-eye me-2"></i> View Report
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($paginated->hasPages())
                        <div class="pagination-container">
                            <nav>
                                {{ $paginated->links('vendor.pagination.bootstrap-5') }}
                            </nav>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <script>
        // Add animation to exam cards
        document.addEventListener("DOMContentLoaded", function() {
            const examCards = document.querySelectorAll('.exam-card');

            examCards.forEach((card, index) => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";

                setTimeout(() => {
                    card.style.transition = "all 0.5s ease";
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, index * 100);
            });
        });
    </script>
@endsection
