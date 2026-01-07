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
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal"
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
                                <div class="col-md-4">
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

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTeacherModalLabel"> Register Bills</h5>
                    <button type="button" class="btn btn-xs btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fas fa-close"></i></button>
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
                                <input type="text" name="control_number" class="form-control-custom" id="controlNumber"
                                    placeholder="Enter Control Number" value="{{ old('control_number') }}">
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="saveButton" class="btn btn-success">Save Bill</button>
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
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize select2
            $('#addTeacherModal').on('shown.bs.modal', function() {
                // Load students via AJAX
                $.ajax({
                    url: '{{ route('students.list') }}',
                    method: 'GET',
                    success: function(response) {
                        const select = $('#studentSelect');
                        select.empty();
                        select.append('<option value="">--Select student name--</option>');

                        // FIX: Access response.students instead of response directly
                        if (response.success && response.students && response.students.length >
                            0) {
                            response.students.forEach(student => {
                                const name =
                                    `${student.first_name} ${student.middle_name} ${student.last_name}`;
                                select.append(
                                    `<option value="${student.id}">${name.toUpperCase()}</option>`
                                );
                            });
                        } else {
                            select.append(
                                '<option value="" disabled>No students found</option>');
                        }

                        // Initialize Select2
                        select.select2({
                            placeholder: "Search Student...",
                            allowClear: true,
                            dropdownParent: $('#addTeacherModal'),
                            width: '100%'
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading students:', xhr);
                        const select = $('#studentSelect');
                        select.empty();
                        select.append('<option value="">--Select student name--</option>');
                        select.append(
                            '<option value="" disabled class="text-danger">Error loading students</option>'
                        );
                    }
                });
            });

            // Service amount & due date population - USE EVENT DELEGATION
            $(document).on('change', '#service', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption) return;

                const amount = selectedOption.getAttribute("data-amount");
                const duration = selectedOption.getAttribute("data-duration");
                const amountInput = document.getElementById("amount");
                const dueDateInput = document.getElementById("dueDate");

                if (amountInput) {
                    amountInput.value = amount ? amount : "";
                }

                if (dueDateInput && duration) {
                    const today = new Date();
                    today.setMonth(today.getMonth() + parseInt(duration));
                    dueDateInput.value = today.toISOString().split('T')[0];
                } else if (dueDateInput) {
                    dueDateInput.value = "";
                }
            });

            // Form validation - FIXED VERSION
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

                // Use setTimeout to allow button state change before form submit
                setTimeout(() => {
                    form.submit();
                }, 100);
            });

            // Re-initialize form validation when modal opens
            $('#addTeacherModal').on('shown.bs.modal', function() {
                // Reset form validation
                const form = document.querySelector('#addTeacherModal .needs-validation');
                if (form) {
                    form.classList.remove("was-validated");
                }

                // Reset button state
                const submitButton = document.getElementById("saveButton");
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = "Save Bill";
                }
            });

            // Reset form when modal is closed
            $('#addTeacherModal').on('hidden.bs.modal', function() {
                const form = this.querySelector('form');
                if (form) {
                    form.reset();
                    form.classList.remove("was-validated");

                    // Reset select2
                    if ($('#studentSelect').length) {
                        $('#studentSelect').val(null).trigger('change');
                    }

                    // Clear amount and due date
                    const amountInput = document.getElementById("amount");
                    const dueDateInput = document.getElementById("dueDate");
                    if (amountInput) amountInput.value = "";
                    if (dueDateInput) dueDateInput.value = "";
                }
            });

            let searchTimeout;
            const loadingSpinner = $('#loadingSpinner');
            const billsTableSection = $('#billsTableSection');
            const paginationSection = $('#paginationSection');
            const yearFilter = $('#yearFilter');

            // Initialize from localStorage
            const savedYear = localStorage.getItem('selectedYear');
            const currentYear = "{{ date('Y') }}";

            if (savedYear && yearFilter.length > 0) {
                yearFilter.val(savedYear);
            }

            // Load initial data
            loadBillsData();

            // Year filter change
            yearFilter.on('change', function() {
                const selectedYear = $(this).val();
                console.log('Year changed to:', selectedYear);

                // Store in localStorage
                localStorage.setItem('selectedYear', selectedYear);

                // Load data
                loadBillsData();
            });

            // Real-time search
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadBillsData();
                }, 500);
            });

            // Search form submit
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                loadBillsData();
            });

            // Clear all filters
            $('#clearFilters').on('click', function(e) {
                e.preventDefault();
                $('#searchInput').val('');
                yearFilter.val('');
                localStorage.removeItem('selectedYear');
                loadBillsData();
                window.history.pushState({}, '', '{{ route('bills.index') }}');
            });

            // Pagination links
            $(document).on('click', '#paginationSection .pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                console.log('Pagination clicked:', url);
                loadBillsData(url);
            });

            // Main function to load bills data
            function loadBillsData(url = null) {
                const searchValue = $('#searchInput').val();
                const selectedYear = yearFilter.val() || localStorage.getItem('selectedYear') || currentYear;
                const targetUrl = url || '{{ route('bills.index') }}';

                console.log('Loading bills data:', {
                    search: searchValue,
                    year: selectedYear,
                    url: targetUrl
                });

                // Show loading
                loadingSpinner.removeClass('d-none');
                billsTableSection.addClass('opacity-50');

                // Prepare data for AJAX
                const requestData = {
                    search: searchValue,
                    year: selectedYear,
                    ajax: true
                };

                $.ajax({
                    url: targetUrl,
                    type: 'GET',
                    data: requestData,
                    success: function(response) {
                        console.log('AJAX Success:', response);

                        if (response.success) {
                            billsTableSection.html(response.html);
                            paginationSection.html(response.pagination);

                            // Update year filter from response
                            if (response.selectedYear) {
                                yearFilter.val(response.selectedYear);
                                localStorage.setItem('selectedYear', response.selectedYear);
                            }

                            // Update URL without reloading page
                            updateUrl(searchValue, selectedYear, url);
                        } else {
                            console.error('AJAX Response Error:', response);
                            billsTableSection.html('<div class="alert alert-danger">' + (response
                                .message || 'Error loading data') + '</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Request Error:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
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

            // Function to update URL
            function updateUrl(search, year, isPaginationUrl = false) {
                const url = new URL(window.location);

                // Update search parameter
                if (search) {
                    url.searchParams.set('search', search);
                } else {
                    url.searchParams.delete('search');
                }

                // Update year parameter
                if (year) {
                    url.searchParams.set('year', year);
                } else {
                    url.searchParams.delete('year');
                }

                // Remove page parameter if not pagination
                if (!isPaginationUrl) {
                    url.searchParams.delete('page');
                }

                // Update browser history
                window.history.pushState({}, '', url);
            }

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const yearParam = urlParams.get('year');
                const searchParam = urlParams.get('search');

                console.log('Popstate triggered:', {
                    year: yearParam,
                    search: searchParam
                });

                // Update filters from URL
                if (yearParam) {
                    yearFilter.val(yearParam);
                    localStorage.setItem('selectedYear', yearParam);
                } else {
                    yearFilter.val('');
                    localStorage.removeItem('selectedYear');
                }

                if (searchParam) {
                    $('#searchInput').val(searchParam);
                } else {
                    $('#searchInput').val('');
                }

                loadBillsData();
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Handle Pay button click - Simple version
            $(document).on('click', '.btn-pay', function() {
                // Get data from button attributes
                const billId = $(this).data('bill-id');
                const studentId = $(this).data('student-id');
                const studentName = $(this).data('student-name');
                const controlNumber = $(this).data('control-number');
                const academicYear = $(this).data('academic-year');
                const billed = parseFloat($(this).data('billed'));
                const paid = parseFloat($(this).data('paid'));
                const balance = parseFloat($(this).data('balance'));

                // Format currency
                const formatter = new Intl.NumberFormat('en-US');

                // Set values in modal
                $('#payment_bill_id').val(billId);
                $('#payment_student_id').val(studentId);
                $('#payment_control_number').val(controlNumber);
                $('#payment_academic_year_hidden').val(academicYear);

                // Display values
                $('#payment_student_display').val(studentName);
                $('#payment_control_display').val(controlNumber);
                $('#payment_academic_display').val(academicYear);
                $('#payment_balance_display').val(formatter.format(balance));

                // Reset form fields (optional)
                $('#payment_amount').val('');
                $('#payment_mode').val('bank'); // Default to bank
                $('#payment_note').val('');

                // Optional: Set focus on amount field
                setTimeout(() => {
                    $('#payment_amount').focus();
                }, 500);
            });

            // BETTER: Format as user types with proper cleanup
            $('#payment_amount').on('input', function(e) {
                let value = $(this).val();

                // Remove all non-numeric characters except decimal
                let numericValue = value.replace(/[^\d.]/g, '');

                // Remove extra decimal points
                let parts = numericValue.split('.');
                if (parts.length > 2) {
                    numericValue = parts[0] + '.' + parts.slice(1).join('');
                }

                // Format with commas for display only
                if (numericValue) {
                    let displayParts = numericValue.split('.');
                    displayParts[0] = displayParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                    // Update display with commas
                    $(this).val(displayParts.join('.'));
                } else {
                    $(this).val('');
                }
            });

            // Before form submission, remove commas
            $('#addPaymentModal form').on('submit', function(e) {
                let amountInput = $('#payment_amount');
                let amountValue = amountInput.val();

                if (!amountValue || amountValue.trim() === '') {
                    e.preventDefault();
                    amountInput.addClass('is-invalid');
                    amountInput.after('<div class="text-danger small">Please enter an amount</div>');
                    return false;
                }

                // Clean the value: remove commas and validate
                let cleanAmount = amountValue.replace(/,/g, '');

                // Check if it's a valid number
                if (isNaN(cleanAmount) || parseFloat(cleanAmount) <= 0) {
                    e.preventDefault();
                    amountInput.addClass('is-invalid');
                    amountInput.after(
                        '<div class="text-danger small">Please enter a valid amount (greater than 0)</div>'
                        );
                    return false;
                }

                // Update hidden/cleaned value for submission
                amountInput.val(cleanAmount);

                // Optional: Show processing state
                $('#payment_save_button').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
            });

            // Reset button state when modal closes
            $('#addPaymentModal').on('hidden.bs.modal', function() {
                $('#payment_save_button').prop('disabled', false)
                    .html('Save Payment');

                // Clear validation
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').remove();

                // Reset amount field
                $('#payment_amount').val('');
            });
        });
    </script>

@endsection
