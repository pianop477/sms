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
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin: 20px auto;
            max-width: 800px;
        }

        .card-body {
            padding: 30px;
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .btn-action {
            border-radius: 5px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .back-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .year-list {
            list-style: none;
            padding: 0;
        }

        .year-item {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .year-item:hover {
            border-color: var(--primary-color);
            box-shadow: 0 0.15rem 0.5rem 0 rgba(58, 59, 69, 0.1);
            transform: translateY(-2px);
        }

        .year-link {
            display: block;
            padding: 20px;
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .year-link:hover {
            background-color: #f8f9fc;
            color: var(--primary-color);
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
            border-radius: 8px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .card {
                margin: 10px;
            }

            .card-body {
                padding: 20px;
            }
        }
    </style>
    <div class="container py-4">
        <div class="card">
            <div class="card-body">
                <!-- Header Section -->
                <div class="row mb-4">
                    <div class="col-md-10">
                        <h4 class="header-title">Select Attendance Year</h4>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="{{route('students.profile', ['student' => Hashids::encode($students->id)])}}" class="btn btn-info btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-2"></i> Back
                        </a>
                    </div>
                </div>

                <!-- Year Selection List -->
                @if ($groupedAttendance->isEmpty())
                    <div class="alert alert-warning text-center py-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <h6 class="mb-0">No Attendance Records found</h6>
                    </div>
                @else
                    <ul class="year-list">
                        @foreach ($groupedAttendance as $year => $yearData)
                            <li class="year-item">
                                <a href="{{ route('students.show.attendance', ['year' => $year, 'student' => Hashids::encode($students->id)])}}" class="year-link">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-calendar-alt me-3 text-primary"></i>
                                            <span class="fw-bold">Attendance Year - {{$year}}</span>
                                        </div>
                                        <i class="fas fa-chevron-right text-primary"></i>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    @endsection
