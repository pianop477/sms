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
            overflow: auto;
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
                                <h4 class="header-title">School fee payment transactions</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                                        <i class="fas fa-plus-circle me-1"></i> Add Payment
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

                                        @php
                                            $currentYear  = (int) date('Y');
                                            $start        = 2024;
                                            $end          = $currentYear + 1; // mwaka mmoja mbele
                                            // $selectedYear = (int) request('year', $currentYear);
                                            $selectedYear = (int) session('selected_year', $currentYear);
                                        @endphp

                                        <select name="year" id="selectYear" class="form-control-custom" onchange="this.form.submit()">
                                            <option value="">-- Filter by Year --</option>

                                            @for ($y = $end; $y >= $start; $y--)
                                                <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <form method="GET" action="{{ url()->current() }}" data-no-preloader>
                                        <!-- Hidden field to preserve year parameter -->
                                        @if(session('selected_year'))
                                            <input type="hidden" name="year" value="{{ session('selected_year') }}">
                                        @endif

                                        <div class="input-group">
                                            <input type="text"
                                                name="search"
                                                class="form-control"
                                                placeholder="Search here..."
                                                value="{{ request('search') }}"
                                                autocomplete="off">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                            @if(request('search') || request('year'))
                                                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-times"></i> Clear
                                                </a>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Table Section -->
                            <div class="table-responsive">
                                <table class="table table-hover progress-table table-responsive-md">
                                    <thead>
                                        <tr>
                                            <th scope="col">Control #</th>
                                            <th scope="col">Student Name</th>
                                            <th scope="col">Level</th>
                                            <th scope="col">Paid Amount</th>
                                            <th scope="col">Academic Year</th>
                                            <th scope="col">Payment Mode</th>
                                            <th scope="col">Issued at</th>
                                            <th scope="col">Issued by</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $row)
                                            <tr>
                                                <td class="fw-bold">{{ strtoupper($row->control_number) }}</td>
                                                <td>
                                                    {{ ucwords(strtolower($row->student_first_name ?? 'N/A')) }}
                                                    {{ ucwords(strtolower($row->student_middle_name ?? '')) }}
                                                    {{ ucwords(strtolower($row->student_last_name ?? '')) }}
                                                </td>
                                                <td class="text-capitalize">
                                                    {{ strtoupper($row->class_code ?? 'N/A') }}
                                                </td>
                                                <td>{{ number_format($row->amount) }}</td>
                                                <td>
                                                    {{ $row->academic_year }}
                                                </td>
                                                <td>
                                                    <span class="badge {{$row->payment_mode == 'bank' ? 'bg-success' : ($row->payment_mode == 'cash' ? 'bg-primary' : 'bg-info')}} text-white">
                                                        {{ ucwords(strtolower($row->payment_mode)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $row->approved_at ?: $row->created_at }}
                                                </td>
                                                <td>
                                                    {{ ucwords(strtolower($row->approver_name ?? 'N/A')) }}
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-action btn-xs dropdown-toggle mr-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-cog me-1"></i> Manage
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="{{route('bills.edit', ['bill' => Hashids::encode($row->id)])}}" title="Edit bill">
                                                                    <i class="fas fa-pencil text-primary"></i>
                                                                    Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form action="{{route('bills.delete', ['bill' => Hashids::encode($row->id)])}}" class="dropdown-item" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn p-1 m-0 text-sm" onclick="return confirm('Are you sure you want to Delete this payment?')">
                                                                        <i class="fas fa-trash text-danger"></i>
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    @if(request('search'))
                                                        No transactions found for "{{ request('search') }}"
                                                        @if(request('year'))
                                                            in {{ request('year') }}
                                                        @endif
                                                    @elseif(request('year'))
                                                        No transactions found for year {{ request('year') }}
                                                    @else
                                                        No transactions found in the current academic year
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-7 text-end">
                                    <div class="text-muted">
                                        @if($transactions->total() > 0)
                                            Showing {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
                                            @if(request('year'))
                                                for year {{ request('year') }}
                                            @endif
                                        @else
                                            No transactions found
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination Section -->
                            @if($transactions->hasPages())
                                <div class="mt-4">
                                    {{ $transactions->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .btn-action {
            background-color: #cccccc;
            border: 1px solid #ced4da;
            color: #212529;
            font-weight: bold
        }

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

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTeacherModalLabel"> Add New Payment</h5>
                <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate action="{{route('payment.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="studentSelect" class="form-label">Student Name <span class="text-danger">*</span></label>
                        <select name="student_id" id="studentSelect" class="form-control-custom" required>
                            <option value="">--Select student name--</option>
                            @if ($students->isEmpty())
                            <option value="" disabled class="text-danger">No students records were found</option>
                            @else
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">
                                {{ ucwords(strtolower($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name)) }}
                                </option>
                            @endforeach
                            @endif
                        </select>
                        @error('student_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="controlNumberSelect" class="form-label">Control Number <span class="text-danger">*</span></label>
                        <select name="control_number" id="controlNumberSelect" class="form-control-custom text-uppercase" required>
                            <option value="">--Select Control Number--</option>
                        </select>
                        @error('control_number')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="academic_year_display" class="form-label">Academic Year</label>
                        <div class="input-group">
                            <!-- hidden input will be submitted -->
                            <input type="hidden" name="academic_year" id="academic_year_hidden">
                            <!-- visible read-only display -->
                            <input type="text" disabled class="form-control-custom" id="academic_year_display">
                        </div>
                        @error('academic_year')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="payment" class="form-label">Payment Mode</label>
                        <select name="payment" id="payment" class="form-control-custom">
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
                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                    <input type="text" required name="amount" class="form-control-custom" id="amount" placeholder="Enter Amount" value="{{ old('amount') }}">
                    @error('amount')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveButton" class="btn btn-success">Save Payment</button>
                </div>
                </form>
            </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const studentSelect = $('#studentSelect');
            const controlSelect = document.getElementById("controlNumberSelect");
            const academicYearDisplay = document.getElementById("academic_year_display");
            const academicYearHidden = document.getElementById("academic_year_hidden");

            // Initialize Select2
            studentSelect.select2({
                placeholder: "Search Student...",
                allowClear: true,
                dropdownParent: $('#addTeacherModal')
            });

            // Use Select2's change event instead of vanilla JS
            studentSelect.on('change', function () {
                const studentId = this.value;

                // Clear previous data
                controlSelect.innerHTML = '<option value="">--Select Control Number--</option>';
                academicYearDisplay.value = "";
                academicYearHidden.value = "";

                if (!studentId) {
                    controlSelect.innerHTML = '<option value="">Please select a student first</option>';
                    return;
                }

                // Show loading state
                controlSelect.innerHTML = '<option value="">Loading control numbers...</option>';
                controlSelect.disabled = true;

                // Fetch control numbers via AJAX
                fetch(`/get-student-fees/${studentId}`)
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        controlSelect.innerHTML = '<option value="">--Select Control Number--</option>';

                        if (data.success && data.fees.length > 0) {
                            data.fees.forEach(fee => {
                                const option = document.createElement("option");
                                option.value = fee.control_number;
                                option.setAttribute('data-year', fee.academic_year);
                                option.textContent = `${fee.control_number}`;
                                controlSelect.appendChild(option);
                            });
                            controlSelect.disabled = false;
                        } else {
                            controlSelect.innerHTML = '<option value="">No active control numbers found</option>';
                        }
                    })
                    .catch(error => {
                        controlSelect.innerHTML = '<option value="">Error loading data</option>';
                    });
            });

            // When control number is selected
            controlSelect.addEventListener("change", function () {
                const selectedOption = this.options[this.selectedIndex];
                const academicYear = selectedOption.getAttribute('data-year') || "";

                academicYearDisplay.value = academicYear;
                academicYearHidden.value = academicYear;
            });
        });
    </script>
@endsection
