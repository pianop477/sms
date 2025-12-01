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
            box-shadow: 0 0.5rem 1.5rem 0 rgba(58, 59, 69, 0.2);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.75rem 2rem 0 rgba(58, 59, 69, 0.25);
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
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
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
            font-size: 1.68rem;
            font-weight: 800;
            margin-bottom: 0;
        }

        .bg-success-custom {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
        }

        .bg-primary-custom {
            background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
        }

        .bg-danger-custom {
            background: linear-gradient(135deg, #e74a3b 0%, #d52a1e 100%);
        }

        .bg-info-custom {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }

        .bg-warning-custom {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }

        .bg-secondary-custom {
            background: linear-gradient(135deg, #858796 0%, #60616f 100%);
        }

        .btn-action {
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-export {
            background: linear-gradient(135deg, #323633 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            color: white;
        }

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

        .badge-role {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-buttons a, .action-buttons button {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .action-buttons a:hover, .action-buttons button:hover {
            transform: translateY(-2px);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2e59d9 100%);
            color: white;
            border-radius: 0;
        }

        .form-control:focus, .form-select:focus {
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

        /* Status badges enhancement */
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Back button styling */
        .btn-back {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
            color: white;
        }

        /* Report Form Styles */
        .report-form .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .report-form .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }

        .report-form .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .date-input-group {
            position: relative;
        }

        .date-input-group .form-control {
            padding-left: 40px;
        }

        .date-input-group::before {
            content: '\f073';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .btn-action {
                margin-bottom: 10px;
            }

            .stat-card .card-value {
                font-size: 1.5rem;
            }

            .stat-card .card-icon {
                font-size: 2.5rem;
            }
        }

        /* Loading animation */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Custom scrollbar for table */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #2e59d9;
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
        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
        }
        .error-message {
            font-size: 0.875rem;
            margin-top: 5px;
            display: block;
        }

        /* Loading animation improvements */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Success message styling */
        .alert-success {
            border-radius: 10px;
            border: none;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-8">
                                <h4 class="header-title">
                                    <i class="fas fa-coins me-3"></i> Transaction Report
                                </h4>
                                <p class="text-muted mb-0">Overview Transaction Report </p>
                            </div>

                        </div>

                        {{-- Bill Statistics --}}
                        <div class="row mb-5">
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="stat-card bg-success-custom text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Total Billed Amount</div>
                                                <div class="card-value">{{ number_format($totalActiveBills) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-check-circle card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="stat-card bg-primary-custom text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Total Paid Amount</div>
                                                <div class="card-value">{{ number_format($totalPaid) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-hand-holding-dollar card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="stat-card bg-danger-custom text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Total Cancelled Amount</div>
                                                <div class="card-value">{{ number_format($totalCancelledBills) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-ban card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="stat-card bg-warning-custom text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Cancelled Count</div>
                                                <div class="card-value">{{ number_format($totalCancelledCount) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-times-circle card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{-- End of Bill Statistics --}}
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <form method="GET" action="{{ url()->current() }}">
                                    @php
                                        $currentYear = (int) date('Y');
                                        $start = 2024;
                                        $end = $currentYear + 1; // mwaka mmoja mbele
                                    @endphp

                                    <select name="year" id="selectYear" class="form-control-custom" onchange="this.form.submit()">
                                        <option value="">-- Filter by Year --</option>
                                        @for ($y = $end; $y >= $start; $y--)
                                            <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </form>
                                </div>
                            <div class="col-md-4 mb-3">
                                <form method="GET" action="{{ url()->current() }}" data-no-preloader>
                                    <div class="input-group mb-3">
                                        <input type="text"
                                            name="search"
                                            class="form-control"
                                            placeholder="Search here..."
                                            value="{{ request('search') }}"
                                            autocomplete="off">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-5 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button class="btn btn-export" data-bs-toggle="modal" data-bs-target="#exportReportModal">
                                        <i class="fas fa-file-export me-2"></i> Export Report
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Transactions Table -->
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table table-hover progress-table table-responsive-md" id="">
                                    <thead>
                                        <tr>
                                            <th scope="col">Control #</th>
                                            <th scope="col">Student Name</th>
                                            <th scope="col">Level</th>
                                            <th scope="col">Paid Amount</th>
                                            <th scope="col">Academic Year</th>
                                            <th scope="col">Issued Date</th>
                                            <th scope="col">Issued by</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($transactions->count() == 0)
                                            <tr>
                                                <td class="text-center text-danger py-4" colspan="10">
                                                    <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block"></i>
                                                    No payment records were found!
                                                </td>
                                            </tr>
                                        @else
                                            @foreach ($transactions as $row)
                                                <tr>
                                                    <td class="fw-bold text-primary">{{ strtoupper($row->control_number) }}</td>
                                                    <td>
                                                        {{ ucwords(strtolower($row->first_name . ' ' . $row->middle_name . ' ' . $row->last_name)) }}
                                                    </td>
                                                    <td>
                                                        {{ strtoupper($row->class_code) }}
                                                    </td>
                                                    <td class="fw-bold">
                                                        {{ number_format($row->total_paid) }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ strtoupper($row->academic_year) }}
                                                    </td>
                                                    <td>
                                                        @if($row->last_payment_date)
                                                            {{ $row->last_payment_date }}
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $user = \App\Models\User::where('id', $row->approved_by)->first();
                                                        @endphp
                                                        @if ($user == null)
                                                            <span class="text-muted">N/A</span>
                                                        @else
                                                            <div class="d-flex align-items-center">
                                                                <span class="ms-2 fw-semibold">
                                                                    {{ ucwords(strtolower($user->first_name . '. ' . $user->last_name[0])) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $transactions->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Report Modal -->
    <div class="modal fade" id="exportReportModal" tabindex="-1" aria-labelledby="exportReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="exportReportModalLabel">
                    <i class="fas fa-file-export me-2"></i> Generate Transaction Report
                </h5>
                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close text-white"></i></button>
            </div>
            <form id="exportReportForm" class="report-form">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Generate comprehensive Transaction report with filters below.
                    </div>

                    <div class="row">
                        <!-- Start Date -->
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">From Date <span class="text-danger">*</span></label>
                            <div class="date-input-group">
                                <input type="date" name="start_date" required id="start_date" class="form-control form-control-custom" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                            <span class="text-danger error-message" id="start_date_error"></span>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">To Date <span class="text-danger">*</span></label>
                            <div class="date-input-group">
                                <input type="date" name="end_date" required id="end_date" class="form-control form-control-custom" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                            <span class="text-danger error-message" id="end_date_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Service Filter -->
                        <div class="col-md-6 mb-3">
                            <label for="service" class="form-label">Service</label>
                            <select name="service" id="service" class="form-select form-control-custom">
                                <option value="">-- Select Service --</option>
                                @if ($services->count() > 0)
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ ucwords(strtolower($service->service_name)) }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No Services available</option>
                                @endif
                            </select>
                            <span class="text-danger error-message" id="service_error"></span>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select form-control-custom">
                                <option value="">-- Select Status --</option>
                                <option value="active">Active</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="expired">Expired</option>
                                <option value="full paid">Full Paid</option>
                                <option value="overpaid">Overpaid</option>
                            </select>
                            <span class="text-danger error-message" id="status_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Payment Mode (Optional for bills) -->
                        <div class="col-md-6 mb-3">
                            <label for="payment_mode" class="form-label">Class</label>
                            <select name="class" id="payment_mode" class="form-select form-control-custom">
                                <option value="">-- Select class --</option>
                                @if ($classes->isNotEmpty())
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ strtoupper($class->class_code) }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No classes available</option>
                                @endif
                            </select>
                            <span class="text-danger error-message" id="payment_mode_error"></span>
                        </div>

                        <!-- Export Format -->
                        <div class="col-md-6 mb-3">
                            <label for="export_format" class="form-label">Export Format <span class="text-danger">*</span></label>
                            <select name="export_format" required id="export_format" class="form-select form-control-custom">
                                <option value="">-- Select Format --</option>
                                <option value="pdf"><i class="fas fa-file-pdf"></i> PDF</option>
                                <option value="excel"><i class="fas fa-file-excel"></i> Excel</option>
                                <option value="csv"><i class="fas fa-file-csv"></i> CSV</option>
                                {{-- <option value="word"><i class="fas fa-file-word"></i> Word</option> --}}
                            </select>
                            <span class="text-danger error-message" id="export_format_error"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="generateReportBtn">
                        <i class="fas fa-download me-2"></i> Export Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Export Report Form Handling
        const exportForm = document.getElementById('exportReportForm');
        const generateBtn = document.getElementById('generateReportBtn');

        if (exportForm && generateBtn) {
            exportForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                // Clear previous errors
                clearErrors();

                // Validate required fields
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const exportFormat = document.getElementById('export_format').value;

                let hasErrors = false;

                if (!startDate) {
                    showError('start_date', 'Start date is required');
                    hasErrors = true;
                }

                if (!endDate) {
                    showError('end_date', 'End date is required');
                    hasErrors = true;
                }

                if (!exportFormat) {
                    showError('export_format', 'Export format is required');
                    hasErrors = true;
                }

                if (startDate && endDate && startDate > endDate) {
                    showError('end_date', 'End date cannot be before start date');
                    hasErrors = true;
                }

                if (hasErrors) {
                    return;
                }

                // Show loading state
                const originalText = generateBtn.innerHTML;
                generateBtn.disabled = true;
                generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Generating...';

                // Prepare form data
                const formData = new FormData(this);

                // Send AJAX request
                fetch('{{ route("bills.export") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        // Handle HTTP errors
                        if (response.status === 422) {
                            return response.json().then(data => {
                                // Handle validation errors
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(field => {
                                        showError(field, data.errors[field][0]);
                                    });
                                } else {
                                    throw new Error(data.message || 'Validation failed');
                                }
                            });
                        } else if (response.status === 404) {
                            return response.json().then(data => {
                                throw new Error(data.error || 'No data found');
                            });
                        } else if (response.status === 500) {
                            return response.json().then(data => {
                                throw new Error(data.error || 'Server error occurred');
                            });
                        }
                        throw new Error('Network response was not ok');
                    }

                    // Check content type to determine response type
                    const contentType = response.headers.get('content-type');

                    if (contentType && contentType.includes('application/json')) {
                        // Handle JSON response (errors)
                        return response.json().then(data => {
                            if (data.error) {
                                throw new Error(data.error);
                            }
                            // If no error but JSON response, try to get blob
                            return response.blob();
                        });
                    } else {
                        // Handle file response (success)
                        return response.blob();
                    }
                })
                .then(blob => {
                    if (blob instanceof Blob) {
                        // Get filename from response or generate one
                        let format = exportFormat;

                        if(format === 'excel') {
                            format = 'xlsx';
                        } else if(format === 'word') {
                            format = 'docx';
                        } else if(format === 'csv') {
                            format = 'csv';
                        } else {
                            format = 'pdf';
                        }

                        const filename = `bills_report_${startDate}_to_${endDate}.${format}`;

                        // Create blob URL
                        const url = window.URL.createObjectURL(blob);

                        // For PDF, open in new tab
                        if (format === 'pdf') {
                            const newTab = window.open(url, '_blank');
                            if (!newTab) {
                                // Fallback: download if popup blocked
                                downloadFile(url, filename);
                            }
                        } else {
                            // For other formats, download directly
                            downloadFile(url, filename);
                        }

                        // Clean up
                        setTimeout(() => window.URL.revokeObjectURL(url), 100);

                        //reset form input
                        exportForm.reset();

                        showSuccess('Report generated successfully!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorInModal(error.message || 'An error occurred while generating the report. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    resetButtonState(generateBtn, originalText);
                });
            });

            // Reset form when modal is closed
            const exportModal = document.getElementById('exportReportModal');
            if (exportModal) {
                exportModal.addEventListener('hidden.bs.modal', function () {
                    exportForm.reset();
                    clearErrors();
                    resetButtonState(generateBtn, '<i class="fas fa-download me-2"></i> Export Report');

                    // Clear any error alerts
                    const existingAlert = document.getElementById('exportErrorAlert');
                    if (existingAlert) {
                        existingAlert.remove();
                    }
                });
            }

            // Set end date min to start date when start date changes
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', function() {
                    endDateInput.min = this.value;
                    if (endDateInput.value && endDateInput.value < this.value) {
                        endDateInput.value = this.value;
                    }
                });
            }
        }

        // Helper functions
        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => {
                el.textContent = '';
            });
        }

        function showError(field, message) {
            const errorElement = document.getElementById(`${field}_error`);
            if (errorElement) {
                errorElement.textContent = message;
            }
        }

        function showErrorInModal(message) {
            // Remove any existing error alerts
            const existingAlert = document.getElementById('exportErrorAlert');
            if (existingAlert) {
                existingAlert.remove();
            }

            // Create error alert
            const errorAlert = document.createElement('div');
            errorAlert.id = 'exportErrorAlert';
            errorAlert.className = 'alert alert-danger alert-dismissible fade show mt-3';
            errorAlert.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Insert after the info alert
            const modalBody = document.querySelector('#exportReportModal .modal-body');
            const infoAlert = modalBody.querySelector('.alert-info');
            if (infoAlert) {
                infoAlert.after(errorAlert);
            } else {
                modalBody.prepend(errorAlert);
            }
        }

        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message || 'Report generated successfully!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        }

        function downloadFile(url, filename) {
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function resetButtonState(button, originalText) {
            button.disabled = false;
            button.innerHTML = originalText;
        }

        // Initialize date constraints
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').max = today;
        document.getElementById('end_date').max = today;
    });

    // Separate function for other form validation (if needed)
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton");

        if (!form || !submitButton) return;

        form.addEventListener("submit", function (event) {
            event.preventDefault();

            // Disable button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...`;

            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false;
                submitButton.innerHTML = "Save";
                return;
            }

            setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
</script>
@endsection
