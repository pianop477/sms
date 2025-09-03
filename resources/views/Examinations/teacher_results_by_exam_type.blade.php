@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --warning: #ffc107;
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
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
            font-weight: 600;
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
            color: #dc3545;
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
            min-height: 200px;
        }

        .exam-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .exam-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .exam-card h5 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 20px;
            text-transform: capitalize;
        }

        .exam-card .btn-view {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
            width: 100%;
        }

        .exam-card .btn-view:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .exam-icon {
            font-size: 40px;
            margin-bottom: 15px;
            color: var(--primary);
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

        .exam-badge {
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

        .exam-description {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 15px;
            flex-grow: 1;
        }
    </style>
    <div class="container">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h4 class="header-title text-white">
                            <i class="fas fa-tasks me-2"></i> Select Examination Type for
                            <span class="year-highlight">{{ $year }}</span>
                        </h4>
                    </div>
                    <div class="col-md-2">
                        <a href="{{route('results_byCourse', ['id' => Hashids::encode($class_course->id)])}}" class="btn btn-back btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-file-alt floating-icons"></i>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <p class="instruction-text"><i class="fas fa-mouse-pointer me-2"></i> Click to select Examination type</p>
                </div>

                <div class="exam-grid">
                    @foreach ($examTypes as $examType)
                        <div class="exam-card">
                            <span class="exam-badge">EXAM</span>
                            <div class="exam-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h5>{{ $examType->first()->exam_type }}</h5>
                            <p class="exam-description">View results for {{ $examType->first()->exam_type }} examination</p>
                            <a href="{{ route('results.byExamType', ['course' => Hashids::encode($class_course->id), 'year' => $year, 'examType' => Hashids::encode($examType->first()->exam_type_id)]) }}" class="btn-view pulse-animation">
                                <i class="fas fa-chart-line me-2"></i> View Results
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
