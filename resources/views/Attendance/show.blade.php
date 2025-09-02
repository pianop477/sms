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
        }

        .card-body {
            padding: 30px;
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0;
        }

        .student-name {
            color: var(--secondary-color);
            font-weight: 600;
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
            font-size: 0.875rem;
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

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .table th {
            padding: 15px 10px;
            font-weight: 600;
            vertical-align: middle;
            border: none;
        }

        .table td {
            padding: 15px 10px;
            vertical-align: middle;
            border: 1px solid #e3e6f0;
        }

        .badge-status {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .week-header {
            background-color: #f8f9fc !important;
            color: var(--dark-color);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
            border-radius: 8px;
        }

        .pagination {
            margin: 20px 0 0 0;
        }

        .page-link {
            color: var(--primary-color);
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .table-responsive-lg {
                overflow-x: auto;
            }

            .btn-action {
                margin-bottom: 10px;
            }
        }
    </style>

    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-body">
                <!-- Header Section -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <span class="header-title">Student Name:</span>
                        <span class="student-name text-uppercase ms-2">{{ $firstRecord->student_firstname }} {{ $firstRecord->student_middlename }} {{ $firstRecord->student_lastname }}</span>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('attendance.byYear', ['student' => Hashids::encode($students->id)])}}" class="btn btn-info btn-action float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="single-table">
                    <div class="table-responsive-lg">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($groupedData->isNotEmpty())
                                    @foreach ($groupedData as $week => $records)
                                        <tr class="week-header">
                                            <td colspan="3" class="text-center">
                                                <i class="fas fa-calendar-week me-2"></i>Week {{ $week }}
                                            </td>
                                        </tr>
                                        @foreach ($records as $item)
                                            <tr>
                                                <td class="fw-medium">
                                                    <i class="fas fa-calendar-day me-2 text-primary"></i>
                                                    {{ \Carbon\Carbon::parse($item->attendance_date)->format('D') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($item->attendance_date)->format('M d, Y') }}</td>
                                                <td>
                                                    @if ($item->attendance_status == 'present')
                                                        <span class="badge-status bg-success text-white">
                                                            <i class="fas fa-check-circle me-1"></i>Present
                                                        </span>
                                                    @elseif ($item->attendance_status == 'absent')
                                                        <span class="badge-status bg-danger text-white">
                                                            <i class="fas fa-times-circle me-1"></i>Absent
                                                        </span>
                                                    @else
                                                        <span class="badge-status bg-warning text-dark">
                                                            <i class="fas fa-exclamation-circle me-1"></i>Permission
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">
                                            <div class="alert alert-warning text-center py-4" role="alert">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                No Attendance Records Submitted for your Children!
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Links -->
                @if ($groupedData->isNotEmpty())
                    <div class="d-flex justify-content-center mt-4">
                        <nav>
                            {{ $groupedData->links('vendor.pagination.bootstrap-5') }}
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
