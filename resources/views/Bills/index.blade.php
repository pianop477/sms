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
            overflow: hidden;
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

        .action-buttons a, .action-buttons button {
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

        .form-control:focus, .select2-container--focus .select2-selection {
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
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
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
                                    <form method="GET" action="{{ url()->current() }}">
                                        <!-- Hidden fields to preserve search parameter -->
                                        @if(request('search'))
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                        @endif

                                        <select name="year" id="selectYear" class="form-control-custom" onchange="this.form.submit()">
                                            <option value="">-- Filter by Year --</option>
                                            @php
                                                $current = (int) date('Y');
                                                $start   = 2024;
                                                $end     = $current + 1; // mwaka mmoja mbele
                                            @endphp
                                            @for ($y = $end; $y >= $start; $y--)
                                                <option value="{{ $y }}" {{ ($selectedYear ?? '') == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor

                                        </select>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <form id="searchForm" method="GET" data-no-preloader>
                                        <div class="input-group">
                                            <input type="text"
                                                name="search"
                                                id="searchInput"
                                                class="form-control"
                                                placeholder="Search here..."
                                                value="{{ request('search') }}"
                                                autocomplete="off">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                            @if(request('search'))
                                                <a href="{{ url()->current() }}" class="btn btn-outline-secondary" id="clearSearch">
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
                                @if(isset($bills) && $bills->hasPages())
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
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('bills.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">Student Name</label>
                                <select name="student_name" id="studentSelect" class="form-control-custom" required>
                                    <option value="">--Select student name--</option>
                                    @if ($students->isEmpty())
                                        <option value="" disabled class="text-danger">No students records were found</option>
                                    @else
                                        @foreach ($students as $student)
                                            <option value="{{$student->id}}">
                                                {{ucwords(strtoupper($student->first_name . ' ' . $student->middle_name. ' '. $student->last_name))}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('student_name')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="controlNumber" class="form-label">Control Number</label>
                                <input type="text" name="control_number" class="form-control-custom" id="controlNumber" placeholder="EB994012345" value="{{old('control_number')}}">
                                @error('control_number')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Academic Year</label>
                                <div class="input-group">
                                    <input type="text" name="academic_year" class="form-control-custom" id="email" placeholder="2020" value="{{old('academic_year', date('Y'))}}">
                                </div>
                                @error('academic_year')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="service" class="form-label">Service</label>
                                <select name="service" id="service" class="form-control-custom" required>
                                    <option value="">-- select service --</option>
                                    @if ($services->isEmpty())
                                        <option value="">{{_('No services were found')}}</option>
                                    @else
                                        @foreach ($services as $row)
                                            <option value="{{$row->id}}" data-amount="{{$row->amount}}" data-duration="{{$row->expiry_duration}}">
                                                {{strtoupper($row->service_name)}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('service')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="text" required name="amount" class="form-control-custom" id="amount" placeholder="100000" value="{{old('amount')}}">
                                @error('amount')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dueDate" class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control-custom" id="dueDate">
                                @error('due_date')
                                <div class="text-danger small">{{$message}}</div>
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
        document.addEventListener("DOMContentLoaded", function () {
            // FIX select2 parent
            $('#studentSelect').select2({
                placeholder: "Search Student...",
                allowClear: true,
                dropdownParent: $('#addTeacherModal')
            });

            // Populate amount & due date based on service
            const serviceSelect = document.getElementById("service");
            const amountInput = document.getElementById("amount");
            const dueDateInput = document.getElementById("dueDate");

            serviceSelect.addEventListener("change", function () {
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption) return;

                const amount = selectedOption.getAttribute("data-amount");
                const duration = selectedOption.getAttribute("data-duration"); // months

                // Populate amount
                amountInput.value = amount ? amount : "";

                // Populate due date
                if (duration) {
                    const today = new Date();
                    today.setMonth(today.getMonth() + parseInt(duration));

                    const formattedDate = today.toISOString().split('T')[0];
                    dueDateInput.value = formattedDate;
                } else {
                    dueDateInput.value = "";
                }
            });

            // Button loader + bootstrap validation
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Saving...`;

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = "Save Bill";
                    return;
                }

                setTimeout(() => form.submit(), 500);
            });
        });

        //search and pagination ajax
        $(document).ready(function() {
            let searchTimeout;
            const loadingSpinner = $('#loadingSpinner');
            const billsTableSection = $('#billsTableSection');
            const paginationSection = $('#paginationSection');
            const summarySection = $('#summarySection');

            // Initial load
            loadBillsData();

            // Real-time search with debouncing
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadBillsData();
                }, 500); // 500ms delay
            });

            // Form submit prevention
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                loadBillsData();
            });

            // Clear search
            $('#clearSearch').on('click', function(e) {
                e.preventDefault();
                $('#searchInput').val('');
                loadBillsData();
                window.history.pushState({}, '', '{{ url()->current() }}');
            });

            // Pagination links
            $(document).on('click', '#paginationSection .pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                loadBillsData(url);
            });

            function loadBillsData(url = null) {
                const searchValue = $('#searchInput').val();
                const targetUrl = url || '{{ route("bills.index") }}';

                // Show loading spinner
                loadingSpinner.removeClass('d-none');
                billsTableSection.addClass('opacity-50');

                $.ajax({
                    url: targetUrl,
                    type: 'GET',
                    data: {
                        search: searchValue,
                        ajax: true
                    },
                    success: function(response) {
                        if (response.success) {
                            billsTableSection.html(response.html);
                            paginationSection.html(response.pagination);
                            summarySection.html(response.summary);

                            // Update URL without page reload
                            if (!url) {
                                const newUrl = new URL(targetUrl);
                                if (searchValue) {
                                    newUrl.searchParams.set('search', searchValue);
                                } else {
                                    newUrl.searchParams.delete('search');
                                }
                                window.history.pushState({}, '', newUrl);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading bills:', xhr);
                        billsTableSection.html('<div class="alert alert-danger">Error loading bills. Please try again.</div>');
                        summarySection.html('<div class="text-muted">Error loading data</div>');
                    },
                    complete: function() {
                        loadingSpinner.addClass('d-none');
                        billsTableSection.removeClass('opacity-50');
                    }
                });
            }

            // Load data immediately when page loads
            $(document).ready(function() {
                loadBillsData();
            });
        });
    </script>

@endsection
