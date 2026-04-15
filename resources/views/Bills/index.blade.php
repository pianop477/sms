@extends('SRTDashboard.frame')

@section('content')
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
            overflow-x: hidden;
        }

        .btn-success {
            background: var(--secondary-color);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn-remeinder {
            background: var(--success-color);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn-success:hover {
            background: #17a673;
        }

        .btn-success-custom {
            background: var(--danger-color);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }

        .table-responsive {
            border-radius: 10px;
            overflow-x: auto;
        }

        .progress-table {
            background-color: white;
        }

        .progress-table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .progress-table th {
            padding: 15px 10px;
            font-weight: 600;
        }

        .progress-table td {
            padding: 15px 10px;
            vertical-align: middle;
        }

        .btn-xs {
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.35rem;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-buttons a,
        .action-buttons button {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid #e3e6f0;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ccc !important;
            border-radius: 4px !important;
            padding: 6px 12px !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .student-info-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .badge-stream {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-stream-A {
            background-color: #e8f0fe;
            color: #1a73e8;
        }

        .badge-stream-B {
            background-color: #e6f4ea;
            color: #0f9d58;
        }

        .badge-stream-C {
            background-color: #fce8e6;
            color: #d93025;
        }

        .form-control:focus,
        .select2-container--focus .select2-selection {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
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
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="header-title">Recent Bills</h4>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <form id="remindAllForm" action="{{ route('bills.send-overdue-reminders') }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="year" value="{{ $selectedYear ?? date('Y') }}">
                                        <button type="button" id="remindAllBtn" class="btn-remeinder">
                                            <i class="fas fa-sms me-1"></i> Send Reminder
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <button type="button" style="background: var(--primary-color); color: white;"
                                        class="btn btn-info btn-action" data-bs-toggle="modal"
                                        data-bs-target="#addTeacherModal">
                                        <i class="fas fa-plus-circle me-1"></i> New Bill
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Teachers Table -->
                        <div class="single-table">
                            <!-- Search Form -->
                            <div class="row mb-3">
                                <div class="col-md-2 mb-3">
                                    <select id="yearFilter" name="year" class="form-control-custom">
                                        <option value="">-- Filter by Year --</option>
                                        @php
                                            $current = (int) date('Y');
                                            $start = 2024;
                                            $end = $current + 1;
                                            $selectedYear = session('selected_year', date('Y'));
                                        @endphp
                                        @for ($y = $end; $y >= $start; $y--)
                                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <form id="searchForm" method="GET" data-no-preloader>
                                        <div class="input-group">
                                            <input type="text" name="search" id="searchInput" class="form-control"
                                                placeholder="Search here..." value="{{ request('search') }}"
                                                autocomplete="off">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                            @if (request('search') || request('year'))
                                                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary"
                                                    id="clearFilters">
                                                    <i class="fas fa-times"></i> Clear
                                                </a>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <button type="button" class="btn-success" id="syncClassesBtn">
                                        <i class="fas fa-sync-alt"></i> Sync Classes
                                    </button>
                                </div>
                            </div>

                            <!-- Loading Spinner -->
                            <div id="loadingSpinner" class="text-center d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading bills...</p>
                            </div>

                            <!-- Table Section -->
                            <div id="billsTableSection">
                                <!-- Table content will be loaded here via AJAX -->
                                @include('Bills.partials.bills_table', ['bills' => $bills ?? []])
                            </div>

                            <!-- Pagination Section -->
                            <div id="paginationSection">
                                <!-- Pagination will be loaded here via AJAX -->
                                @if (isset($bills) && $bills->hasPages())
                                    <div class="mt-4">
                                        {{ $bills->links('vendor.pagination.bootstrap-5') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add this modal for confirmation sending reminder -->
    <div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Send Reminders</h5>
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal"><i
                            class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <p id="reminderSummary">Loading summary...</p>
                    <p class="text-danger text-center" style="font-style: italic"><strong>Are you sure you want to send this
                            reminder to parents?</strong></p>
                    <div id="reminderProgress" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: 0%"></div>
                        </div>
                        <p class="mt-2" id="progressText">Sending reminders...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-remeinder" id="confirmRemind">Send Reminders</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Sync Classes Confirmation Modal -->
    <div class="modal fade" id="syncClassesModal" tabindex="-1" aria-labelledby="syncClassesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="syncClassesModalLabel">
                        <i class="fas fa-sync-alt me-2"></i>
                        Sync Classes - Year <span id="syncYearDisplay"></span>
                    </h5>
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <!-- Loading State -->
                    <div id="syncLoading" class="text-center py-4">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted">Analyzing bills that need class update...</p>
                    </div>

                    <!-- Preview Content -->
                    <div id="syncPreviewContent" style="display: none;"></div>

                    <!-- Error Content -->
                    <div id="syncErrorContent" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn-remeinder" id="confirmSyncBtn" disabled>
                        <i class="fas fa-sync-alt"></i> Sync Now
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add new bill modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTeacherModalLabel"> Register Bills</h5>
                    <button type="button" class="btn btn-xs btn btn-danger" data-bs-dismiss="modal"
                        aria-label="Close"><i class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{ route('bills.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">Student Name <span
                                        class="text-danger">*</span></label>
                                <select name="student_name" id="studentSelect" class="form-control-custom" required>
                                    <option value="">--Select student name--</option>
                                    @if ($students->isEmpty())
                                        <option value="" disabled class="text-danger">No students records were found
                                        </option>
                                    @else
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}">
                                                {{ ucwords(strtoupper($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name)) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('student_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="controlNumber" class="form-label">Control Number</label>
                                <input type="text" name="control_number" class="form-control-custom"
                                    id="controlNumber" placeholder="Enter Control Number"
                                    value="{{ old('control_number') }}">
                                @error('control_number')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Academic Year <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="academic_year" class="form-control-custom"
                                        id="email" placeholder="2020" value="{{ old('academic_year', date('Y')) }}">
                                </div>
                                @error('academic_year')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="service" class="form-label">Service <span
                                        class="text-danger">*</span></label>
                                <select name="service" id="service" class="form-control-custom" required>
                                    <option value="">-- select service --</option>
                                    @if ($services->isEmpty())
                                        <option value="">{{ _('No services were found') }}</option>
                                    @else
                                        @foreach ($services as $row)
                                            <option value="{{ $row->id }}" data-amount="{{ $row->amount }}"
                                                data-duration="{{ $row->expiry_duration }}">
                                                {{ strtoupper($row->service_name) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('service')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount <span
                                        class="text-danger">*</span></label>
                                <input type="text" required name="amount" class="form-control-custom" id="amount"
                                    placeholder="Enter Amount" value="{{ old('amount') }}">
                                @error('amount')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dueDate" class="form-label">Due Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control-custom" id="dueDate">
                                @error('due_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Bill Description </label>
                                <input type="text" name="description" class="form-control-custom" id="description"
                                    placeholder="Enter Description" value="{{ old('description') }}">
                                @error('description')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="saveButton"
                                style="background: var(--primary-color); color: white;" class="btn btn-info">Save
                                Bill</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Record Payment</h5>
                    <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{ route('payment.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Hidden fields - populated automatically -->
                        <input type="hidden" name="bill_id" id="payment_bill_id">
                        <input type="hidden" name="student_id" id="payment_student_id">
                        <input type="hidden" name="control_number" id="payment_control_number">
                        <input type="hidden" name="academic_year" id="payment_academic_year_hidden">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Student Name</label>
                                <input type="text" id="payment_student_display" class="form-control-custom" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Control Number</label>
                                <input type="text" id="payment_control_display"
                                    class="form-control-custom text-uppercase" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_academic_display" class="form-label">Academic Year</label>
                                <div class="input-group">
                                    <input type="text" id="payment_academic_display" class="form-control-custom"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="payment_mode" class="form-label">Payment Mode <span
                                        class="text-danger">*</span></label>
                                <select name="payment" id="payment_mode" class="form-control-custom" required>
                                    <option value="">-- select payment mode --</option>
                                    <option value="bank" selected>Bank</option>
                                    <option value="cash">Cash</option>
                                    <option value="mobile">Mobile Money</option>
                                </select>
                                @error('payment')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_amount" class="form-label">Payment Amount <span
                                        class="text-danger">*</span></label>
                                <input type="text" required name="amount" class="form-control-custom"
                                    id="payment_amount" placeholder="Enter amount" value="{{ old('amount') }}">
                                @error('amount')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Current Balance</label>
                                <input type="text" id="payment_balance_display" class="form-control-custom" readonly
                                    style="background-color: #f8f9fa;">
                                <small class="text-muted">(For reference only - you can pay any amount)</small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="payment_save_button" class="btn btn-success">Save Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .opacity-50 {
            opacity: 0.5;
        }

        #loadingSpinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }
    </style>
    <script>
        // ============ GLOBAL FUNCTIONS (Ziko nje ya DOMContentLoaded) ============

        // View Bill function - GLOBAL
        window.viewBill = function(billId) {
            // console.log('Loading bill:', billId);

            // Show loading spinner
            $('#billDetailsContent').html(`
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0 small">Loading bill details...</p>
                </div>
            `);

            // Hide print button initially
            $('#printBillBtn').hide();

            // Show modal
            $('#billDetailsModal').modal('show');

            // Fetch bill details
            $.ajax({
                url: `/Bills/view/${billId}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log('Bill details response:', response);

                    if (response.success) {
                        // Store response globally for printing
                        window.currentBillData = response;

                        // Show and enable print button
                        $('#printBillBtn').show().prop('disabled', false);

                        $('#billDetailsContent').html(`
                        <!-- Student & Bill Info -->
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1 text-dark text-capitalize">${response.bill.student_first_name} ${response.bill.student_middle_name} ${response.bill.student_last_name}</h6>
                                        <small class="text-muted">${response.bill.class_code.toUpperCase()}</small>
                                    </div>
                                    <span class="badge bg-${response.bill.status === 'active' ? 'primary' : response.bill.status === 'cancelled' ? 'warning' : response.bill.status === 'full paid' ? 'success' : 'info'} ${response.bill.status === 'cancelled' ? 'text-primary' : 'text-white'}">
                                        ${response.bill.status.toUpperCase()}
                                    </span>
                                </div>
                                <div class="text-muted mb-2">
                                    <div><strong>Control #:</strong> ${response.bill.control_number.toUpperCase()}</div>
                                    <div><strong>Academic Year:</strong> ${response.bill.academic_year}</div>
                                    <div><strong>Due Date:</strong> ${response.bill.due_date || 'N/A'}</div>
                                    <div><strong>Description:</strong> ${response.bill.description.toUpperCase() || 'N/A'}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="border rounded p-2 text-center bg-light">
                                    <div class="small text-muted">Billed</div>
                                    <div class="fw-bold text-primary">${new Intl.NumberFormat().format(response.summary.total_billed)}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center bg-light">
                                    <div class="small text-muted">Paid</div>
                                    <div class="fw-bold text-success">${new Intl.NumberFormat().format(response.summary.total_paid)}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center bg-light">
                                    <div class="small text-muted">Balance</div>
                                    <div class="fw-bold ${response.summary.balance > 0 ? 'text-danger' : 'text-success'}">
                                        ${new Intl.NumberFormat().format(response.summary.balance)}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History -->
                        <div class="border-top pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 small text-muted" style="font-weight:bold"><i class="fas fa-history me-1"></i>PAYMENT HISTORY</h6>
                                <span class="badge bg-secondary text-white">${response.summary.payment_count} payments</span>
                            </div>

                            ${response.payment_history.length > 0 ? `
                                                    <div style="max-height: 200px; overflow-y: auto;">
                                                        <table class="table table-sm table-borderless mb-0">
                                                            <thead>
                                                                <tr class="small text-muted border-bottom">
                                                                    <th class="ps-2">#</th>
                                                                    <th>Date</th>
                                                                    <th>Mode</th>
                                                                    <th class="text-end pe-2">Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                ${response.payment_history.map((payment, index) => `
                                                <tr class="small border-bottom">
                                                    <td class="ps-2">#${payment.installment}</td>
                                                    <td>${new Date(payment.approved_at).toLocaleDateString('en-GB')}</td>
                                                    <td>
                                                        <span class="badge bg-info">${payment.payment_mode}</span>
                                                    </td>
                                                    <td class="text-end pe-2 fw-bold text-success">
                                                        ${new Intl.NumberFormat().format(payment.amount)}
                                                    </td>
                                                </tr>
                                            `).join('')}
                                                                <tr class="small border-top fw-bold bg-light">
                                                                    <td class="ps-2" colspan="3" style="font-weight:bold">Total Paid:</td>
                                                                    <td class="text-end pe-2 text-success" style="font-weight:bold">
                                                                        ${new Intl.NumberFormat().format(response.summary.total_paid)}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                ` : `
                                                    <div class="text-center py-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            No payments recorded
                                                        </small>
                                                    </div>
                                                `}
                        </div>
                    `);
                    } else {
                        $('#billDetailsContent').html(`
                        <div class="alert alert-danger py-2 mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <small>${response.message || 'Failed to load details'}</small>
                        </div>
                    `);
                        $('#printBillBtn').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading bill details:', error);
                    $('#billDetailsContent').html(`
                    <div class="alert alert-danger py-2 mb-0">
                        <i class="fas fa-times-circle me-1"></i>
                        <small>Error loading bill details. Please try again.</small>
                    </div>
                `);
                    $('#printBillBtn').hide();
                }
            });
        };

        window.printInvoice = function() {
            if (!window.currentBillData) {
                console.error('No bill data available');
                alert('No bill data available. Please refresh and try again.');
                return;
            }

            const response = window.currentBillData;
            const school = response.school_info || {};

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/[&<>]/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;'
                } [m]));
            }

            // Logo Logic
            let logoHtml = '';
            if (school.logo) {
                let logoPath = school.logo.replace(/^(public\/|storage\/|app\/public\/)/, '');
                if (!logoPath.includes('logo/')) logoPath = `logo/${logoPath}`;
                logoHtml =
                    `<img src="/storage/${logoPath}" alt="Logo" style="max-height: 60px;" onerror="this.style.display='none'">`;
            }

            const isPaid = response.summary.balance <= 0;
            const currentDate = new Date().toLocaleDateString('en-GB');

            const printContent = `
                <div id="printableInvoice" style="position: relative; color: #1a202c; line-height: 1.4; font-family: 'Inter', sans-serif; max-width: 800px; margin: 0 auto; min-height: 100vh; display: flex; flex-direction: column;">

                    <!-- Watermark -->
                    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 80px; color: rgba(0,0,0,0.03); font-weight: 800; pointer-events: none; z-index: 0; text-transform: uppercase; white-space: nowrap;">
                        ${isPaid ? 'OFFICIAL PAID' : 'PAYMENT DUE'}
                    </div>

                    <!-- Main Content -->
                    <div style="flex: 1; position: relative; z-index: 1;">
                        <!-- Header -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; border-bottom: 3px solid #1a202c; padding-bottom: 15px;">
                            <div>
                                ${logoHtml}
                                <h2 style="margin: 8px 0 2px 0; font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">${escapeHtml(school.school_name ? school.school_name.toUpperCase() : 'SCHOOL NAME')}</h2>
                                <p style="font-size: 10px; color: #4a5568; margin: 0; line-height: 1.2;">
                                    ${school.postal_addres ? escapeHtml(school.postal_addres.toUpperCase()) : ''} ${school.postal_name ? escapeHtml(school.postal_name.toUpperCase()) : ''}<br>
                                    ${school.school_phone ? 'Tel: ' + escapeHtml(school.school_phone) : ''} ${school.school_alternative_phone ? ' | Mobile: ' + escapeHtml(school.school_alternative_phone) : ''}<br>
                                    ${school.school_email ? 'Email: ' + escapeHtml(school.school_email) : ''}
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <h1 style="margin: 0; font-size: 28px; font-weight: 800; color: #1a202c; letter-spacing: -1px;">INVOICE</h1>
                                <p style="font-size: 12px; font-weight: 700; margin: 2px 0;">REF: ${escapeHtml(response.bill.control_number ? response.bill.control_number.toUpperCase() : 'N/A')}</p>
                                <p style="font-size: 10px; color: #718096; margin: 0;">Date: ${currentDate}</p>
                            </div>
                        </div>

                        <!-- Bill To & Status -->
                        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 15px; margin-bottom: 20px;">
                            <div style="background: #f8fafc; padding: 15px; border-radius: 6px; border-left: 4px solid #1a202c;">
                                <h4 style="font-size: 9px; text-transform: uppercase; color: #718096; margin: 0 0 5px 0; letter-spacing: 0.5px;">Bill To</h4>
                                <p style="font-size: 14px; font-weight: 700; margin: 0;">${escapeHtml(response.bill.student_first_name ? response.bill.student_first_name.toUpperCase() : '')} ${escapeHtml(response.bill.student_middle_name ? response.bill.student_middle_name.toUpperCase() : '')} ${escapeHtml(response.bill.student_last_name ? response.bill.student_last_name.toUpperCase() : '')}</p>
                                <p style="font-size: 11px; color: #4a5568; margin: 2px 0;">Class: ${escapeHtml(response.bill.class_code ? response.bill.class_code.toUpperCase() : 'N/A')} | Year: ${escapeHtml(response.bill.academic_year ? response.bill.academic_year : 'N/A')}</p>
                            </div>
                            <div style="text-align: right; align-self: center;">
                                <div style="display: inline-block; padding: 4px 12px; background: ${isPaid ? '#dcfce7' : '#fee2e2'}; color: ${isPaid ? '#166534' : '#991b1b'}; border-radius: 4px; font-weight: 700; font-size: 10px; text-transform: uppercase; margin-bottom: 8px;">
                                    ${escapeHtml(response.bill.status ? response.bill.status.toUpperCase() : 'ACTIVE')}
                                </div>
                                <p style="font-size: 10px; color: #718096; margin: 0;">Due Date: <b style="color: #1a202c">${response.bill.due_date || 'N/A'}</b></p>
                                <p style="font-size: 10px; color: #718096; margin: 0;">Description: <b style="color: #1a202c">${escapeHtml(response.bill.description ? response.bill.description.toUpperCase() : 'SCHOOL FEES')}</b></p>
                            </div>
                        </div>

                        <!-- Payment History Table -->
                        <p style="text-transform:capitalize; font-weight: bold; color:black;">Bills Transactions</p>
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                            <thead>
                                <tr style="background: #1a202c; color: white;">
                                    <th style="padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase;">Installment</th>
                                    <th style="padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase;">Payment Mode</th>
                                    <th style="padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase;">Approved Date</th>
                                    <th style="padding: 10px; text-align: right; font-size: 10px; text-transform: uppercase;">Paid Amount (TZS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${response.payment_history && response.payment_history.length > 0 ?
                                    response.payment_history.map(payment => `
                                            <tr style="border-bottom: 1px solid #edf2f7;">
                                                <td style="padding: 8px 10px; font-size: 12px; font-weight: 600;">${escapeHtml("Term "+payment.installment)}</td>
                                                <td style="padding: 8px 10px; font-size: 12px; font-weight: 600;">${escapeHtml(payment.payment_mode ? payment.payment_mode.toUpperCase() : 'N/A')}</td>
                                                <td style="padding: 8px 10px; font-size: 12px; color: #4a5568;">${payment.approved_at ? new Date(payment.approved_at).toLocaleDateString('en-GB') : 'N/A'}</td>
                                                <td style="padding: 8px 10px; font-size: 12px; text-align: right; font-weight: 700;">${new Intl.NumberFormat().format(payment.amount)}</td>
                                            </tr>
                                        `).join('') :
                                    `<tr><td colspan="3" style="padding: 15px; text-align: center; color: #a0aec0; font-size: 12px;">No transactions found</td></tr>`
                                }
                            </tbody>
                        </table>

                        <!-- Financial Summary -->
                        <div style="display: flex; justify-content: flex-end; margin-bottom: 30px;">
                            <div style="width: 280px; background: #f8fafc; padding: 15px; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px;">
                                    <span style="color: #718096;">Total Billed:</span>
                                    <span><strong>${new Intl.NumberFormat().format(response.summary.total_billed)} TZS</strong></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #38a169; font-size: 12px;">
                                    <span>Total Paid:</span>
                                    <span><strong>- ${new Intl.NumberFormat().format(response.summary.total_paid)} TZS</strong></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; border-top: 2px solid #e2e8f0; padding-top: 8px; margin-top: 5px;">
                                    <span style="font-weight: 800; font-size: 13px;">BALANCE DUE:</span>
                                    <span style="font-weight: 800; font-size: 16px; color: ${response.summary.balance > 0 ? '#e53e3e' : '#38a169'};">
                                        ${new Intl.NumberFormat().format(response.summary.balance)} TZS
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Signature Section -->
                        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 40px; margin-top: 20px; padding: 0 10px;">
                            <div style="width: 90px; height: 90px; border: 2px dashed #cbd5e0; border-radius: 50%; display: flex; align-items: center; text-align: center; justify-content: center; font-size: 8px; color: #cbd5e0; text-transform: uppercase; font-weight: bold; line-height: 1.2;">
                                OFFICIAL<br> STAMP
                            </div>
                            <div style="text-align: center; width: 200px;">
                                <div style="border-bottom: 2px solid #1a202c; height: 40px;"></div>
                                <p style="font-size: 10px; font-weight: 700; margin-top: 5px; color: #4a5568;">Accountant / Cashier Signature</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Fixed Bottom -->
                    <div style="margin-top: 40px; text-align: center; font-size: 9px; color: #718096; border-top: 1px solid #edf2f7; padding-top: 15px;">
                        <p style="margin: 0;">Computer generated official document. Presented by <b>${escapeHtml(school.school_name.toUpperCase())}</b>.</strong>.</p>
                        <p style="margin: 5px 0 0 0;">Generated by ShuleApp Billing Module</p>
                        <p style="margin: 5px 0 0 0; font-size: 8px;">If you have any questions, please contact the accounts office. Thank you!</p>
                    </div>
                </div>
            `;

            // Create and open print window - FIXED
            const printWindow = window.open('', '_blank');

            if (!printWindow) {
                alert('Please allow popups to print the invoice');
                return;
            }

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Invoice_${escapeHtml(response.bill.control_number)}</title>
                    <meta charset="UTF-8">
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        body {
                            font-family: 'Inter', -apple-system, sans-serif;
                            background: #fff;
                            padding: 30px;
                            margin: 0;
                        }
                        @media print {
                            body {
                                padding: 0;
                                margin: 0;
                            }
                            #printableInvoice {
                                width: 100%;
                                margin: 0;
                                padding: 20px;
                            }
                            .no-print {
                                display: none;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                                window.onafterprint = function() {
                                    window.close();
                                };
                            }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        };

        // ============ DOCUMENT READY (Kwa ajili ya initializers tu) ============
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize select2
            let allStudents = [];

            // Sync button click
            $(document).on('click', '#syncClassesBtn', function(e) {
                e.preventDefault();
                // console.log('Sync button clicked');
                const selectedYear = $('#yearFilter').val() || localStorage.getItem('selectedYear') ||
                    new Date().getFullYear();
                $('#syncYearDisplay').text(selectedYear);

                if ($('#syncClassesModal').length === 0) {
                    return;
                }

                $('#syncClassesModal').modal('show');
                $('#syncLoading').show();
                $('#syncPreviewContent').hide().html('');
                $('#syncErrorContent').hide().html('');
                $('#confirmSyncBtn').prop('disabled', true);

                $.ajax({
                    url: '{{ route('bills.sync-classes.preview') }}',
                    method: 'GET',
                    data: {
                        year: selectedYear
                    },
                    success: function(response) {
                        $('#syncLoading').hide();
                        if (response.success) {
                            if (response.total > 0) {
                                let html = `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i><strong>${response.total}</strong> bill(s) will be updated for year <strong>${response.year}</strong></div>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm table-bordered table-hover">
                                    <thead class="table-light sticky-top">
                                        <tr><th>#</th><th>Control Number</th><th>Student</th><th>Old Class</th><th>New Class</th></tr>
                                    </thead><tbody>`;
                                response.data.forEach((item, index) => {
                                    html +=
                                        `<tr><td>${index + 1}</td><td><span class="text-primary">${item.control_number.toUpperCase()}</span></td>
                                        <td>${item.student_name.toUpperCase()}</td>
                                        <td><span class="badge bg-secondary text-white">${item.old_class.toUpperCase()}</span></td>
                                        <td><span class="badge bg-success text-white">${item.new_class.toUpperCase()}</span></td></tr>`;
                                });
                                html +=
                                    `</tbody></table></div><p class="text-muted small mt-2 mb-0"><i class="fas fa-info-circle me-1"></i>All <strong>Bills</strong> statuses will be updated, It is safe 100%.</p>`;
                                $('#syncPreviewContent').html(html).show();
                                $('#confirmSyncBtn').prop('disabled', false);
                            } else {
                                $('#syncPreviewContent').html(
                                    `<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><strong>No bills need class update for year ${response.year}!</strong><p class="mb-0 mt-2">All Bills are already using the correct classes.</p></div>`
                                ).show();
                            }
                        } else {
                            $('#syncErrorContent').html(
                                `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${response.message || 'Error loading preview'}</div>`
                            ).show();
                        }
                    },
                    error: function(xhr) {
                        $('#syncLoading').hide();
                        let errorMsg = 'Error loading preview. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr
                            .responseJSON.message;
                        $('#syncErrorContent').html(
                            `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${errorMsg}</div>`
                        ).show();
                    }
                });
            });

            // Confirm Sync
            $(document).on('click', '#confirmSyncBtn', function() {
                const $btn = $(this);
                const selectedYear = $('#yearFilter').val() || localStorage.getItem('selectedYear') ||
                    new Date().getFullYear();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Syncing...');
                $('#syncPreviewContent').html(
                    `<div class="text-center py-4"><div class="spinner-border text-primary mb-3"></div><p class="text-muted">Updating classes... Please wait.</p></div>`
                );

                $.ajax({
                    url: '{{ route('bills.sync-classes') }}',
                    method: 'PUT',
                    data: {
                        year: selectedYear,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#syncPreviewContent').html(
                                `<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><strong>Success!</strong><p class="mb-0 mt-2">${response.message}</p></div>`
                            );
                            Swal.fire({
                                icon: 'success',
                                title: 'Sync Completed!',
                                html: response.message,
                                confirmButtonText: 'OK',
                                timer: 3000
                            });
                            setTimeout(() => {
                                $('#syncClassesModal').modal('hide');
                                if (typeof loadBillsData === 'function')
                                    loadBillsData();
                                else location.reload();
                            }, 2000);
                        } else {
                            $('#syncPreviewContent').html(
                                `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${response.message}</div>`
                            );
                            $btn.prop('disabled', false).html(
                                '<i class="fas fa-check"></i> Try Again');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error syncing classes. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr
                            .responseJSON.message;
                        $('#syncPreviewContent').html(
                            `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${errorMsg}</div>`
                        );
                        $btn.prop('disabled', false).html(
                            '<i class="fas fa-check"></i> Try Again');
                    }
                });
            });

            // Reset modal when closed
            $('#syncClassesModal').on('hidden.bs.modal', function() {
                $('#confirmSyncBtn').prop('disabled', true).html(
                    '<i class="fas fa-sync-alt"></i> Sync Now');
                $('#syncLoading').show();
                $('#syncPreviewContent').hide().html('');
                $('#syncErrorContent').hide().html('');
            });

            // Load students
            $.ajax({
                url: '{{ route('students.list') }}',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.students) {
                        allStudents = response.students.map(student => ({
                            id: student.id,
                            text: `${student.first_name} ${student.middle_name} ${student.last_name}`
                                .toUpperCase(),
                            firstName: student.first_name,
                            middleName: student.middle_name,
                            lastName: student.last_name,
                            admissionNo: student.admission_number
                        }));
                        // console.log(`Loaded ${allStudents.length} students`);
                    }
                }
            });

            // Add bill Modal Select2
            $('#addTeacherModal').on('shown.bs.modal', function() {
                $('#studentSelect').select2({
                    placeholder: "Search student... (Type to filter)",
                    allowClear: true,
                    dropdownParent: $('#addTeacherModal'),
                    width: '100%',
                    data: [],
                    minimumInputLength: 0,
                    ajax: {
                        transport: function(params, success, failure) {
                            const term = params.data.term || '';
                            const page = params.data.page || 1;
                            const pageSize = 50;
                            let filteredStudents = allStudents;
                            if (term.trim() !== '') {
                                const searchTerm = term.toLowerCase();
                                filteredStudents = allStudents.filter(student =>
                                    student.text.toLowerCase().includes(searchTerm) ||
                                    (student.admissionNo && student.admissionNo
                                        .toLowerCase().includes(searchTerm))
                                );
                            }
                            const startIndex = (page - 1) * pageSize;
                            const endIndex = startIndex + pageSize;
                            const paginatedStudents = filteredStudents.slice(startIndex,
                                endIndex);
                            setTimeout(function() {
                                success({
                                    results: paginatedStudents,
                                    pagination: {
                                        more: endIndex < filteredStudents.length
                                    }
                                });
                            }, 300);
                        }
                    }
                });
            }).on('hidden.bs.modal', function() {
                if ($('#studentSelect').hasClass("select2-hidden-accessible")) {
                    $('#studentSelect').select2('destroy');
                    $('#studentSelect').val('').trigger('change');
                }
            });

            // Service amount & due date
            $(document).on('change', '#service', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption) return;
                const amount = selectedOption.getAttribute("data-amount");
                const duration = selectedOption.getAttribute("data-duration");
                if (document.getElementById("amount")) document.getElementById("amount").value = amount ?
                    amount : "";
                if (document.getElementById("dueDate") && duration) {
                    const today = new Date();
                    today.setMonth(today.getMonth() + parseInt(duration));
                    document.getElementById("dueDate").value = today.toISOString().split('T')[0];
                } else if (document.getElementById("dueDate")) {
                    document.getElementById("dueDate").value = "";
                }
            });

            // Form validation
            $(document).on('submit', '.needs-validation', function(event) {
                event.preventDefault();
                const form = this;
                const submitButton = document.getElementById("saveButton");
                if (!form.checkValidity()) {
                    event.stopPropagation();
                    form.classList.add("was-validated");
                    return;
                }
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML =
                        `<span class="spinner-border spinner-border-sm me-2"></span> Saving...`;
                }
                setTimeout(() => {
                    form.submit();
                }, 100);
            });

            $('#addTeacherModal').on('shown.bs.modal', function() {
                const form = document.querySelector('#addTeacherModal .needs-validation');
                if (form) form.classList.remove("was-validated");
                const submitButton = document.getElementById("saveButton");
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = "Save Bill";
                }
            }).on('hidden.bs.modal', function() {
                const form = this.querySelector('form');
                if (form) {
                    form.reset();
                    form.classList.remove("was-validated");
                    if ($('#studentSelect').length) $('#studentSelect').val(null).trigger('change');
                    if (document.getElementById("amount")) document.getElementById("amount").value = "";
                    if (document.getElementById("dueDate")) document.getElementById("dueDate").value = "";
                }
            });

            // Table & AJAX handling
            let searchTimeout;
            const loadingSpinner = $('#loadingSpinner');
            const billsTableSection = $('#billsTableSection');
            const paginationSection = $('#paginationSection');
            const yearFilter = $('#yearFilter');

            const savedYear = localStorage.getItem('selectedYear');
            const currentYear = "{{ date('Y') }}";
            if (savedYear && yearFilter.length > 0) yearFilter.val(savedYear);

            function loadBillsData(url = null) {
                const searchValue = $('#searchInput').val();
                const selectedYear = yearFilter.val() || localStorage.getItem('selectedYear') || currentYear;
                const targetUrl = url || '{{ route('bills.index') }}';

                loadingSpinner.removeClass('d-none');
                billsTableSection.addClass('opacity-50');

                $.ajax({
                    url: targetUrl,
                    type: 'GET',
                    data: {
                        search: searchValue,
                        year: selectedYear,
                        ajax: true
                    },
                    success: function(response) {
                        if (response.success) {
                            billsTableSection.html(response.html);
                            paginationSection.html(response.pagination);
                            bindTableEvents();
                            if (response.selectedYear) {
                                yearFilter.val(response.selectedYear);
                                localStorage.setItem('selectedYear', response.selectedYear);
                            }
                        } else {
                            billsTableSection.html('<div class="alert alert-danger">' + (response
                                .message || 'Error loading data') + '</div>');
                        }
                    },
                    error: function() {
                        billsTableSection.html(
                            '<div class="alert alert-danger">Error loading bills. Please try again.</div>'
                        );
                    },
                    complete: function() {
                        loadingSpinner.addClass('d-none');
                        billsTableSection.removeClass('opacity-50');
                    }
                });
            }

            yearFilter.on('change', function() {
                localStorage.setItem('selectedYear', $(this).val());
                loadBillsData();
            });

            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => loadBillsData(), 500);
            });

            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                loadBillsData();
            });

            $('#clearFilters').on('click', function(e) {
                e.preventDefault();
                $('#searchInput').val('');
                yearFilter.val('');
                localStorage.removeItem('selectedYear');
                loadBillsData();
            });

            $(document).on('click', '#paginationSection .pagination a', function(e) {
                e.preventDefault();
                loadBillsData($(this).attr('href'));
            });

            function bindTableEvents() {
                $(document).off('click', '.cancel-btn').on('click', '.cancel-btn', function() {
                    const billId = $(this).data('id');
                    const controlNumber = $(this).data('control');
                    const serviceName = $(this).data('service');
                    const amount = $(this).data('amount');
                    $('#cancelBillForm').attr('action', `/Bills/cancel/${billId}`);
                    $('#billPreview').html(
                        `<div class="alert alert-info small"><strong>Bill:</strong> ${controlNumber}<br><strong>Service:</strong> ${serviceName}<br><strong>Amount:</strong> ${amount}</div>`
                    );
                });
            }

            bindTableEvents();
            loadBillsData();

            // Payment Modal Handling
            $(document).on('click', '.btn-pay', function() {
                const billId = $(this).data('bill-id');
                const studentId = $(this).data('student-id');
                const studentName = $(this).data('student-name');
                const controlNumber = $(this).data('control-number');
                const academicYear = $(this).data('academic-year');
                const billed = parseFloat($(this).data('billed'));
                const paid = parseFloat($(this).data('paid'));
                const balance = parseFloat($(this).data('balance'));
                const formatter = new Intl.NumberFormat('en-US');

                $('#payment_bill_id').val(billId);
                $('#payment_student_id').val(studentId);
                $('#payment_control_number').val(controlNumber);
                $('#payment_academic_year_hidden').val(academicYear);
                $('#payment_student_display').val(studentName);
                $('#payment_control_display').val(controlNumber);
                $('#payment_academic_display').val(academicYear);
                $('#payment_balance_display').val(formatter.format(balance));
                $('#payment_amount').val('');
                $('#payment_mode').val('bank');
                $('#payment_note').val('');
                setTimeout(() => $('#payment_amount').focus(), 500);
            });

            $('#payment_amount').on('input', function(e) {
                let value = $(this).val();
                let numericValue = value.replace(/[^\d.]/g, '');
                let parts = numericValue.split('.');
                if (parts.length > 2) numericValue = parts[0] + '.' + parts.slice(1).join('');
                if (numericValue) {
                    let displayParts = numericValue.split('.');
                    displayParts[0] = displayParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    $(this).val(displayParts.join('.'));
                } else $(this).val('');
            });

            $('#addPaymentModal form').on('submit', function(e) {
                let amountInput = $('#payment_amount');
                let amountValue = amountInput.val();
                if (!amountValue || amountValue.trim() === '') {
                    e.preventDefault();
                    amountInput.addClass('is-invalid');
                    amountInput.after('<div class="text-danger small">Please enter an amount</div>');
                    return false;
                }
                let cleanAmount = amountValue.replace(/,/g, '');
                if (isNaN(cleanAmount) || parseFloat(cleanAmount) <= 0) {
                    e.preventDefault();
                    amountInput.addClass('is-invalid');
                    amountInput.after(
                        '<div class="text-danger small">Please enter a valid amount (greater than 0)</div>'
                    );
                    return false;
                }
                amountInput.val(cleanAmount);
                $('#payment_save_button').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
            });

            $('#addPaymentModal').on('hidden.bs.modal', function() {
                $('#payment_save_button').prop('disabled', false).html('Save Payment');
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').remove();
                $('#payment_amount').val('');
            });

            // Edit Bill
            window.openEditBillModal = function(hashId) {
                $('#editBillModal').modal('show');
                $.get(`/Bills/edit/${hashId}`, function(response) {
                    $('#editBillForm').attr('action', `/Bills/update/${hashId}`);
                    $('#edit_control_number').val(response.bill.control_number);
                    $('#edit_amount').val(response.bill.amount);
                    $('#academic_year').val(response.bill.academic_year);
                    $('#edit_status').val(response.bill.status);
                    $('#description').val(response.bill.description);
                    if (response.bill.due_date) {
                        const dueDate = response.bill.due_date.split(' ')[0];
                        $('#edit_due_date').val(dueDate);
                    } else $('#edit_due_date').val('');
                    $('#edit_student_id').empty();
                    response.students.forEach(student => {
                        $('#edit_student_id').append(
                            `<option value="${student.id}">${student.first_name} ${student.last_name}</option>`
                        );
                    });
                    $('#edit_student_id').val(response.bill.student_id).trigger('change');
                    $('#edit_service_id').empty();
                    response.services.forEach(service => {
                        $('#edit_service_id').append(
                            `<option value="${service.id}" data-amount="${service.amount}" data-duration="${service.expiry_duration}">${service.service_name}</option>`
                        );
                    });
                    $('#edit_service_id').val(response.bill.service_id);
                }).fail(function(error) {
                    console.error('Error loading bill data:', error);
                    alert('Error loading bill details. Please try again.');
                });
            };

            $(document).on('change', '#edit_service_id', function() {
                let option = $(this).find(':selected');
                const amount = option.data('amount');
                const duration = option.data('duration');
                if (amount) $('#edit_amount').val(amount);
                if (duration) {
                    const today = new Date();
                    today.setMonth(today.getMonth() + parseInt(duration));
                    $('#edit_due_date').val(today.toISOString().split('T')[0]);
                } else $('#edit_due_date').val('');
            });

            $('.select2').select2({
                dropdownParent: $('#editBillModal')
            });

            $('#cancelBillForm').on('submit', function(e) {
                if (!confirm('Are you sure you want to cancel this bill?')) {
                    e.preventDefault();
                    return false;
                }
                $(this).find('.btn-danger').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Cancelling...');
            });

            // Reminder functions
            window.showReminderModal = function() {
                const selectedYear = $('#yearFilter').val() || localStorage.getItem('selectedYear') ||
                    "{{ date('Y') }}";
                $('#reminderModal').modal('show');
                $('#reminderSummary').html(
                    `<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 mb-0 small">Loading summary for ${selectedYear}...</p></div>`
                );
                $.ajax({
                    url: '{{ route('bills.get-overdue-summary') }}',
                    method: 'GET',
                    data: {
                        year: selectedYear
                    },
                    success: function(response) {
                        if (response.success) {
                            const summary = response.summary;
                            $('#reminderSummary').html(
                                `<div class="alert alert-info"><p><strong>Summary for ${selectedYear}</strong></p><hr><div class="row"><div class="col-4 text-center"><div class="h5 mb-0">${summary.total_bills}</div><small class="text-muted">Total Bills</small></div><div class="col-4 text-center"><div class="h5 mb-0">${summary.unique_parents}</div><small class="text-muted">Unique Parents</small></div><div class="col-4 text-center"><div class="h5 mb-0 text-danger">${summary.formatted_total_balance}</div><small class="text-muted">Total Unpaid</small></div></div></div>`
                            );
                        } else {
                            $('#reminderSummary').html(
                                `<div class="alert alert-danger">Error: ${response.message}</div>`
                            );
                        }
                    },
                    error: function() {
                        $('#reminderSummary').html(
                            `<div class="alert alert-danger">Error loading summary. Please try again.</div>`
                        );
                    }
                });
            };

            window.sendReminders = function() {
                const $btn = $('#confirmRemind');
                const $progress = $('#reminderProgress');
                const $progressBar = $('#reminderProgress .progress-bar');
                const $progressText = $('#progressText');
                const selectedYear = $('#yearFilter').val() || localStorage.getItem('selectedYear') ||
                    "{{ date('Y') }}";
                $('#remindAllForm input[name="year"]').val(selectedYear);
                $btn.prop('disabled', true);
                $progress.show();
                $progressBar.css('width', '10%');
                $progressText.text('Preparing to send...');
                $.ajax({
                    url: $('#remindAllForm').attr('action'),
                    method: 'POST',
                    data: $('#remindAllForm').serialize(),
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.min(90, (e.loaded / e.total) * 90);
                                $progressBar.css('width', percent + '%');
                                $progressText.text(`Sending... ${Math.round(percent)}%`);
                            }
                        });
                        return xhr;
                    },
                    success: function(response) {
                        $progressBar.css('width', '100%');
                        $progressText.text('Completed!');
                        setTimeout(() => {
                            $('#reminderModal').modal('hide');
                            let statsHtml = '';
                            if (response.stats) {
                                statsHtml =
                                    `<hr><div class="text-start"><small><strong>Statistics:</strong><br>- Total processed: ${response.stats.total}<br>- Successful: ${response.stats.successful}<br>- Failed: ${response.stats.failed}</small></div>`;
                                if (response.stats.failed > 0 && response.stats
                                    .failed_details) {
                                    statsHtml +=
                                        `<hr><div class="text-start"><small><strong>Failed details:</strong><br>${response.stats.failed_details.slice(0, 3).map(detail => `- ${detail}<br>`).join('')}${response.stats.failed_details.length > 3 ? `... and ${response.stats.failed_details.length - 3} more` : ''}</small></div>`;
                                }
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Reminders Sent!',
                                html: response.message + statsHtml,
                                confirmButtonText: 'OK'
                            });
                            $btn.prop('disabled', false);
                            $progress.hide();
                            $progressBar.css('width', '0%');
                            loadBillsData();
                        }, 1000);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false);
                        $progress.hide();
                        let errorMsg = 'Error sending reminders. ';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMsg += xhr
                            .responseJSON.message;
                        else if (xhr.status === 422) errorMsg +=
                            'Validation error. Please check the data.';
                        else if (xhr.status === 500) errorMsg +=
                            'Server error. Please try again later.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            };

            $('#reminderModal').on('hidden.bs.modal', function() {
                $('#confirmRemind').prop('disabled', false);
                $('#reminderProgress').hide();
                $('#reminderProgress .progress-bar').css('width', '0%');
            });

            $(document).on('click', '#remindAllBtn', window.showReminderModal);
            $(document).on('click', '#confirmRemind', window.sendReminders);
        });
    </script>

@endsection
