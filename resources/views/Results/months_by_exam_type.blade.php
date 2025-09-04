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
            text-align: center;
            position: relative;
            z-index: 1;
            font-size: 28px;
        }

        .class-highlight {
            color: #ffd700;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            text-transform: uppercase;
        }

        .exam-highlight {
            color: #ffd700;
            font-weight: 600;
        }

        .year-highlight {
            color: #ffd700;
            font-weight: 600;
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
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            flex-wrap: wrap;
            gap: 15px;
        }

        .date-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .date-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-grow: 1;
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

        .date-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-action {
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .btn-students {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
        }

        .btn-students:hover {
            background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
            color: white;
            transform: translateY(-2px);
        }

        .btn-publish, .btn-unpublish {
            background: transparent;
            border: none;
            font-size: 24px;
            transition: all 0.3s;
        }

        .btn-publish:hover, .btn-unpublish:hover {
            transform: translateY(-2px) scale(1.1);
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
            border: none;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            color: white;
            transform: translateY(-2px);
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
            .date-item {
                flex-direction: column;
                text-align: center;
            }

            .date-info {
                flex-direction: column;
                gap: 10px;
            }

            .date-actions {
                justify-content: center;
                width: 100%;
            }

            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 24px;
            }
        }

        .month-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 50px;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: 600;
        }

        .pdf-icon {
            font-size: 24px;
        }
    </style>
    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h4 class="header-title">
                            <i class="fas fa-chart-line me-2"></i>
                            <span class="class-highlight"> {{strtoupper($classes->class_code)}}</span>
                            <span class="exam-highlight">{{ucwords(strtolower($exams->exam_type))}}</span> Results -
                            <span class="year-highlight">{{$year}}</span>
                        </h4>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('results.examTypesByClass', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($class_id)]) }}" class="btn btn-back btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-calendar-alt floating-icons"></i>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <p class="instruction-text"><i class="fas fa-mouse-pointer me-2"></i> Select Month to view results</p>
                </div>

                <div class="month-list">
                    @foreach ($groupedByMonth as $month => $dates)
                        <div class="month-card">
                            <div class="month-header" data-month="{{ Str::slug($month) }}">
                                <h6>
                                    <i class="fas fa-chevron-right month-icon"></i>
                                    {{ $month }} - {{ $year }}
                                </h6>
                                <span class="month-badge">{{ count($dates) }} dates</span>
                            </div>
                            <div id="{{ Str::slug($month) }}" class="date-list">
                                <div class="date-instruction">
                                    <i class="fas fa-info-circle me-2"></i>Select Date to get PDF Result
                                </div>
                                @foreach ($dates as $date => $data)
                                    <div class="date-item">
                                        <div class="date-info">
                                            <a href="{{ route('results.resultsByMonth', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}" target="" class="date-link">
                                                <i class="fas fa-file-pdf pdf-icon"></i>
                                                {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                                            </a>
                                        </div>
                                        <div class="date-actions">
                                            <a href="{{ route('individual.student.reports', ['school' => Hashids::encode($schools->id), 'year' => $year, 'examType' => Hashids::encode($exam_id), 'class' => Hashids::encode($class_id), 'month' => $month, 'date' => $date]) }}" class="btn-action btn-students">
                                                <i class="fas fa-users me-1"></i> Students
                                            </a>

                                            @php
                                                $examStatus = $results->where('exam_date', $date)->first();
                                            @endphp

                                            @if ($examStatus && $examStatus->status == 1)
                                                <form action="{{ route('publish.results', ['school' => Hashids::encode($schools->id), $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn-action btn-publish" onclick="return confirm('Are you sure you want to publish this results to parents?')">
                                                        <i class="fas fa-toggle-off text-secondary"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('unpublish.results', ['school' => Hashids::encode($schools->id), $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn-action btn-unpublish" onclick="return confirm('Are you sure you want to unpublish this results?')">
                                                        <i class="fas fa-toggle-on text-success"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('delete.results', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete these results?')">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
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
