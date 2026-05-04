{{-- resources/views/staff-loans/index.blade.php --}}

@extends('SRTDashboard.frame')

@section('content')
    <style>
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-deducted {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .deduction-type {
            font-weight: 600;
        }

        .type-loan {
            color: #3b82f6;
        }

        .type-advance {
            color: #8b5cf6;
        }

        .type-penalty {
            color: #ef4444;
        }

        .type-fine {
            color: #f59e0b;
        }

        .type-other {
            color: #6b7280;
        }

        .table-deductions th {
            background: #f8fafc;
            font-weight: 600;
        }

        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0">
                                <i class="fas fa-hand-holding-usd me-2"></i> Staff Loans & Advances
                            </h5>
                            <div class="d-flex gap-2 mt-2 mt-sm-0">
                                {{-- Year Filter Dropdown --}}
                                <div class="d-flex align-items-center">
                                    <label class="me-2 text-white mb-0" style="font-size: 14px;">
                                        <i class="fas fa-calendar-alt me-1"></i> Year:
                                    </label>
                                    <select id="yearFilter" class="form-select form-select-sm bg-white"
                                        style="width: auto;">
                                        @foreach ($availableYears ?? [date('Y')] as $year)
                                            <option value="{{ $year }}"
                                                {{ ($selectedYear ?? date('Y')) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                                    data-bs-target="#addLoanModal">
                                    <i class="fas fa-plus me-1"></i> Add Loan/Advance
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- Pending Deductions Table --}}
                        <h6 class="mb-3">
                            <i class="fas fa-clock me-1 text-warning"></i> Pending Deductions
                            <span class="badge bg-secondary ms-2"
                                id="pendingCount">{{ count($deductions['pending'] ?? []) }}</span>
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-deductions table-bordered" id="pendingTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Staff ID</th>
                                        <th>Employee Name</th>
                                        <th>Staff Type</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th class="text-end">Amount (TZS)</th>
                                        <th>Duration</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingTableBody">
                                    @include('staff-loans.partials.pending_rows', [
                                        'deductions' => $deductions['pending'] ?? [],
                                    ])
                                </tbody>
                            </table>
                        </div>

                        {{-- History/Deducted Table --}}
                        @if (count($deductions['history']) > 0)
                            {{-- History/Deducted Table --}}
                            <div id="historySection" @if (count($deductions['history'] ?? []) == 0) style="display: none;" @endif>
                                <h6 class="mb-3 mt-4">
                                    <i class="fas fa-history me-1 text-muted"></i> Deduction History
                                    <span class="badge bg-secondary ms-2"
                                        id="historyCount">{{ count($deductions['history'] ?? []) }}</span>
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-deductions table-bordered" id="historyTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Staff ID</th>
                                                <th>Employee Name</th>
                                                <th>Staff Type</th>
                                                <th>Type</th>
                                                <th>Description</th>
                                                <th class="text-end">Amount (TZS)</th>
                                                <th>Deducted On</th>
                                                <th>Payroll Month</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historyTableBody">
                                            @include('staff-loans.partials.history_rows', [
                                                'deductions' => $deductions['history'] ?? [],
                                            ])
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Loan/Advance Modal --}}
    <div class="modal fade" id="addLoanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i> Add Staff Loan/Advances
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('deductions.unofficial.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            Fill in the details below to add a new deduction for staff member.
                        </div>

                        <div class="row">
                            {{-- Staff ID --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Staff ID <span class="text-danger">*</span></label>
                                <input type="text" name="staff_id"
                                    class="form-control @error('staff_id') is-invalid @enderror"
                                    value="{{ old('staff_id') }}" required placeholder="TCH-001">
                                @error('staff_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="help-text">Enter staff ID (Teacher, Transport, or Other Staff)</div>
                            </div>

                            {{-- Staff Type --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Staff Type <span class="text-danger">*</span></label>
                                <select name="staff_type" class="form-select @error('staff_type') is-invalid @enderror"
                                    required>
                                    <option value="">Select Staff Type</option>
                                    <option value="Teacher" {{ old('staff_type') == 'Teacher' ? 'selected' : '' }}>Teacher
                                    </option>
                                    <option value="Transport Staff"
                                        {{ old('staff_type') == 'Transport Staff' ? 'selected' : '' }}>Transport Staff
                                    </option>
                                    <option value="Other Staff" {{ old('staff_type') == 'Other Staff' ? 'selected' : '' }}>
                                        Other Staff</option>
                                </select>
                                @error('staff_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deduction Type --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Deduction Type <span class="text-danger">*</span></label>
                                <select name="deduction_type" id="deductionType"
                                    class="form-select @error('deduction_type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="loan" {{ old('deduction_type') == 'loan' ? 'selected' : '' }}>Loan
                                        (Can be recurring)</option>
                                    <option value="advance" {{ old('deduction_type') == 'advance' ? 'selected' : '' }}>
                                        Salary Advance (One-time)</option>
                                    <option value="penalty" {{ old('deduction_type') == 'penalty' ? 'selected' : '' }}>
                                        Penalty (One-time)</option>
                                    <option value="fine" {{ old('deduction_type') == 'fine' ? 'selected' : '' }}>Fine
                                        (One-time)</option>
                                    <option value="other" {{ old('deduction_type') == 'other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                @error('deduction_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amountInput"
                                    class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}"
                                    required min="1000" step="1000" placeholder="500000">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="help-text" id="amountHelpText">
                                    @if (old('deduction_type') == 'loan')
                                        For recurring loans, this is the <strong>monthly installment amount</strong>
                                    @else
                                        This is the <strong>total amount to deduct</strong>
                                    @endif
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="col-12 mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" required
                                    placeholder="Describe the reason for this deduction...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Recurring Option (Only for Loans) --}}
                            <div class="col-md-6 mb-3" id="recurringOptionDiv" style="display: none;">
                                <div class="form-check">
                                    <input type="checkbox" name="is_recurring" class="form-check-input" id="isRecurring"
                                        value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isRecurring">
                                        Recurring Deduction (Multiple Months)
                                    </label>
                                </div>
                                <div class="help-text">Check this if the deduction will be spread over multiple months
                                </div>
                            </div>

                            {{-- Recurring Months (Shown only when recurring is checked) --}}
                            <div class="col-md-6 mb-3" id="recurringMonthsDiv" style="display: none;">
                                <label class="form-label">Number of Months <span class="text-danger">*</span></label>
                                <select name="recurring_months" id="recurringMonthsSelect"
                                    class="form-select @error('recurring_months') is-invalid @enderror">
                                    <option value="">Select months</option>
                                    @for ($i = 1; $i <= 36; $i++)
                                        <option value="{{ $i }}"
                                            {{ old('recurring_months') == $i ? 'selected' : '' }}>
                                            {{ $i }} month{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('recurring_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="help-text">
                                    Total deduction will be: <strong id="totalAmountPreview">0</strong> TZS
                                    (Monthly amount × Number of months)
                                </div>
                            </div>

                            {{-- Authorization Notes (Optional) --}}
                            <div class="col-12 mb-3">
                                <label class="form-label">Authorization Notes</label>
                                <textarea name="authorization_notes" class="form-control @error('authorization_notes') is-invalid @enderror"
                                    rows="2" placeholder="Any additional notes or authorization details...">{{ old('authorization_notes') }}</textarea>
                                @error('authorization_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hidden fields that are auto-generated/populated --}}
                            <input type="hidden" name="authorized_by"
                                value="{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Add Deduction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Loan/Advance Modal --}}
    <div class="modal fade" id="editLoanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i> Edit Staff Loan/Advances
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editDeductionForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            Edit the deduction details below. Only pending deductions can be edited.
                        </div>

                        <input type="hidden" name="deduction_id" id="edit_deduction_id">

                        <div class="row">
                            {{-- Staff ID (Read-only) --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Staff ID</label>
                                <input type="text" id="edit_staff_id" class="form-control" readonly>
                            </div>

                            {{-- Staff Type (Read-only) --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Staff Type</label>
                                <input type="text" id="edit_staff_type" class="form-control" readonly>
                            </div>

                            {{-- Employee Name (Read-only) --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Employee Name</label>
                                <input type="text" id="edit_employee_name" class="form-control" readonly>
                            </div>

                            {{-- Deduction Type --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Deduction Type <span class="text-danger">*</span></label>
                                <select name="deduction_type" id="edit_deductionType" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="loan">Loan (Can be recurring)</option>
                                    <option value="advance">Salary Advance (One-time)</option>
                                    <option value="penalty">Penalty (One-time)</option>
                                    <option value="fine">Fine (One-time)</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="invalid-feedback" id="edit_deductionType_error"></div>
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="edit_amount" class="form-control" required
                                    min="1000" step="1000">
                                <div class="invalid-feedback" id="edit_amount_error"></div>
                                <div class="help-text" id="edit_amountHelpText">Monthly installment amount</div>
                            </div>

                            {{-- Description --}}
                            <div class="col-12 mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="edit_description" class="form-control" rows="2" required></textarea>
                                <div class="invalid-feedback" id="edit_description_error"></div>
                            </div>

                            {{-- Recurring Option --}}
                            <div class="col-md-6 mb-3" id="edit_recurringOptionDiv">
                                <div class="form-check">
                                    <input type="checkbox" name="is_recurring" class="form-check-input"
                                        id="edit_isRecurring" value="1">
                                    <label class="form-check-label" for="edit_isRecurring">
                                        Recurring Deduction (Multiple Months)
                                    </label>
                                </div>
                            </div>

                            {{-- Recurring Months --}}
                            <div class="col-md-6 mb-3" id="edit_recurringMonthsDiv" style="display: none;">
                                <label class="form-label">Number of Months <span class="text-danger">*</span></label>
                                <select name="recurring_months" id="edit_recurringMonthsSelect" class="form-select">
                                    <option value="">Select months</option>
                                    @for ($i = 1; $i <= 36; $i++)
                                        <option value="{{ $i }}">{{ $i }}
                                            month{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                <div class="invalid-feedback" id="edit_recurring_months_error"></div>
                                <div class="help-text">
                                    Total will be: <strong id="edit_totalAmountPreview">0</strong> TZS
                                    (Monthly × Months)
                                </div>
                            </div>

                            {{-- Authorization Notes --}}
                            <div class="col-12 mb-3">
                                <label class="form-label">Authorization Notes</label>
                                <textarea name="authorization_notes" id="edit_authorization_notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="editSubmitBtn">
                            <i class="fas fa-save me-1"></i> Update Deduction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REMOVE hizi scripts za zamani na uweke hizi kwa order sahihi --}}

    {{-- 1. FIRST: jQuery (from official CDN) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- 2. SECOND: Bootstrap JS (depends on jQuery) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}

    {{-- 3. THIRD: DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    {{-- 4. FOURTH: DataTables JS (depends on jQuery) --}}
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    {{-- 5. FIFTH: SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- 6. SIXTH: Your custom script --}}
    <script>
        // ==================== GLOBAL FUNCTIONS ====================

        function cancelDeduction(id) {
            Swal.fire({
                title: 'Cancel Deduction?',
                text: 'This will cancel the pending deduction and it will not be processed in payroll.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, Cancel',
                cancelButtonText: 'No, Keep'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/deductions/staff-loan/' + id + '/cancel';
                    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function editDeduction(id) {
            console.log('Edit button clicked for ID:', id);

            const url = '/deductions/staff-loan/' + id;

            Swal.fire({
                title: 'Loading...',
                text: 'Fetching deduction details...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    Swal.close();

                    if (data.success && data.data) {
                        const deduction = data.data;

                        document.getElementById('edit_deduction_id').value = deduction.id;
                        document.getElementById('edit_staff_id').value = deduction.staff_id || '';
                        document.getElementById('edit_staff_type').value = deduction.staff_type || '';
                        document.getElementById('edit_employee_name').value = deduction.employee_name || '';
                        document.getElementById('edit_deductionType').value = deduction.deduction_type;
                        document.getElementById('edit_amount').value = deduction.amount;
                        document.getElementById('edit_description').value = deduction.description;
                        document.getElementById('edit_authorization_notes').value = deduction.authorization_notes || '';

                        const isRecurring = deduction.is_recurring == 1;
                        document.getElementById('edit_isRecurring').checked = isRecurring;

                        if (isRecurring && deduction.recurring_months) {
                            document.getElementById('edit_recurringMonthsSelect').value = deduction.recurring_months;
                            document.getElementById('edit_recurringMonthsDiv').style.display = 'block';
                            const total = (parseFloat(deduction.amount) || 0) * (parseInt(deduction.recurring_months) ||
                                0);
                            document.getElementById('edit_totalAmountPreview').textContent = total.toLocaleString();
                        } else {
                            document.getElementById('edit_recurringMonthsDiv').style.display = 'none';
                        }

                        document.getElementById('editDeductionForm').action = '/deductions/staff-loan/' + deduction.id;

                        const event = new Event('change');
                        document.getElementById('edit_deductionType').dispatchEvent(event);

                        const editModal = new bootstrap.Modal(document.getElementById('editLoanModal'));
                        editModal.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to load deduction details',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: error.message,
                        confirmButtonText: 'OK'
                    });
                });
        }

        // ==================== DATATABLES INITIALIZATION ====================
        let pendingDataTable = null;
        let historyDataTable = null;

        function destroyDataTables() {
            if ($.fn.DataTable.isDataTable('#pendingTable')) {
                $('#pendingTable').DataTable().destroy();
            }
            if ($.fn.DataTable.isDataTable('#historyTable')) {
                $('#historyTable').DataTable().destroy();
            }
        }

        function initializeDataTables() {
            destroyDataTables();

            // Initialize Pending Table
            if ($('#pendingTable tbody tr').length > 0) {
                pendingDataTable = $('#pendingTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "All"]
                    ],
                    language: {
                        search: "🔍 Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            previous: "← Previous",
                            next: "Next →"
                        }
                    },
                    responsive: true,
                    autoWidth: false,
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [9]
                    }]
                });
            }

            // Initialize History Table
            if ($('#historyTable tbody tr').length > 0) {
                historyDataTable = $('#historyTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "All"]
                    ],
                    language: {
                        search: "🔍 Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            previous: "← Previous",
                            next: "Next →"
                        }
                    },
                    responsive: true,
                    autoWidth: false,
                    order: [
                        [0, 'desc']
                    ]
                });
            }
        }

        // Function to update tables based on selected year
        function filterByYear(year) {
            if (!year) return;

            // Show loading state
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching data for year ' + year,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('deductions.unofficial.filter') }}?year=' + year,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        // Update pending table body
                        if (data.pending && data.pending.length > 0) {
                            let pendingHtml = '';
                            data.pending.forEach((deduction, index) => {
                                pendingHtml += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td><strong>${deduction.staff_id.toUpperCase()}</strong></td>
                                <td>${deduction.employee_name.toLowerCase().replace(/\b\w/g, l => l.toUpperCase())}</td>
                                <td>${deduction.staff_type}</td>
                                <td>
                                    <span class="deduction-type type-${deduction.deduction_type}">
                                        <i class="fas ${deduction.deduction_type == 'loan' ? 'fa-hand-holding-usd' : (deduction.deduction_type == 'advance' ? 'fa-money-bill-wave' : (deduction.deduction_type == 'penalty' ? 'fa-gavel' : (deduction.deduction_type == 'fine' ? 'fa-exclamation-triangle' : 'fa-tag')))} me-1"></i>
                                        ${deduction.deduction_type.charAt(0).toUpperCase() + deduction.deduction_type.slice(1)}
                                    </span>
                                </td>
                                <td>${deduction.description}</td>
                                <td class="text-end fw-bold">${parseInt(deduction.amount).toLocaleString()}</td>
                                <td class="text-center">
                                    ${deduction.is_recurring ?
                                        `<span class="badge bg-info"><i class="fas fa-sync-alt me-1"></i> ${deduction.remaining_months}/${deduction.recurring_months} months</span>` :
                                        `<span class="badge bg-secondary">One-time</span>`}
                                </td>
                                <td>${new Date(deduction.created_at).toLocaleDateString('en-GB')}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-xs btn-warning" style="border-radius: 12px" onclick="editDeduction(${deduction.id})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-xs btn-danger" style="border-radius: 12px" onclick="cancelDeduction(${deduction.id})">
                                            <i class="fas fa-trash"></i> Cancel
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                            });
                            $('#pendingTableBody').html(pendingHtml);
                            $('#pendingCount').text(data.pending.length);
                        } else {
                            $('#pendingTableBody').html(`
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle me-1"></i> No pending deductions for this year
                                </td>
                            </tr>
                        `);
                            $('#pendingCount').text('0');
                        }

                        // Update history table body
                        if (data.history && data.history.length > 0) {
                            let historyHtml = '';
                            data.history.forEach((deduction, index) => {
                                historyHtml += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td><strong>${deduction.staff_id.toUpperCase()}</strong></td>
                                <td>${deduction.employee_name.toLowerCase().replace(/\b\w/g, l => l.toUpperCase())}</td>
                                <td>${deduction.staff_type}</td>
                                <td>
                                    <span class="deduction-type type-${deduction.deduction_type}">
                                        ${deduction.deduction_type.charAt(0).toUpperCase() + deduction.deduction_type.slice(1)}
                                    </span>
                                </td>
                                <td>${deduction.description}</td>
                                <td class="text-end">${parseInt(deduction.amount).toLocaleString()}</td>
                                <td>${new Date(deduction.deducted_at).toLocaleDateString('en-GB')}</td>
                                <td>${deduction.payroll_month}</td>
                            </tr>`;
                            });
                            $('#historyTableBody').html(historyHtml);
                            $('#historyCount').text(data.history.length);
                            $('#historySection').show();
                        } else {
                            $('#historyTableBody').html(`
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-history me-1"></i> No deduction history for this year
                                </td>
                            </tr>
                        `);
                            $('#historyCount').text('0');
                            $('#historySection').show();
                        }

                        // Reinitialize DataTables after updating content
                        initializeDataTables();

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: `Showing data for year ${year}`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Failed to load data', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    Swal.close();
                    Swal.fire('Error', 'Failed to fetch data. Please try again.', 'error');
                }
            });
        }

        // ==================== DOCUMENT READY - SINGLE ENTRY POINT ====================
        $(document).ready(function() {
            console.log('Document ready - Initializing...');

            // Initialize DataTables
            initializeDataTables();

            // Year filter change event
            $('#yearFilter').on('change', function() {
                const year = $(this).val();
                filterByYear(year);
            });

            // ==================== EDIT FORM SUBMISSION ====================
            $('#editDeductionForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const url = form.attr('action');
                const formData = form.serialize();
                const submitBtn = $('#editSubmitBtn');

                $('.invalid-feedback').html('');
                $('.is-invalid').removeClass('is-invalid');

                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Updating...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        const isSuccess = response.success === true || response.success ===
                            'true';

                        if (isSuccess) {
                            const editModal = bootstrap.Modal.getInstance(document
                                .getElementById('editLoanModal'));
                            if (editModal) editModal.hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message ||
                                    'Deduction updated successfully',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            submitBtn.prop('disabled', false);
                            submitBtn.html('<i class="fas fa-save me-1"></i> Update Deduction');

                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: response.message || 'Failed to update deduction',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false);
                        submitBtn.html('<i class="fas fa-save me-1"></i> Update Deduction');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul style="text-align: left;">';
                            for (const field in errors) {
                                $('#edit_' + field).addClass('is-invalid');
                                $('#edit_' + field + '_error').html(errors[field][0]);
                                errorHtml += '<li><strong>' + field + ':</strong> ' + errors[
                                    field][0] + '</li>';
                            }
                            errorHtml += '</ul>';

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: errorHtml,
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: xhr.responseJSON?.message ||
                                    'Something went wrong',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });

            // ==================== EDIT MODAL RECURRING LOGIC ====================
            function updateEditTotalAmount() {
                const amount = parseFloat($('#edit_amount').val()) || 0;
                const months = parseInt($('#edit_recurringMonthsSelect').val()) || 0;
                const total = amount * months;
                $('#edit_totalAmountPreview').text(total.toLocaleString());
            }

            $(document).on('change', '#edit_deductionType', function() {
                const type = $(this).val();
                if (type === 'loan') {
                    $('#edit_recurringOptionDiv').show();
                    $('#edit_amountHelpText').html(
                        '<strong>Monthly installment amount</strong> - This amount will be deducted each month'
                        );
                } else {
                    $('#edit_recurringOptionDiv').hide();
                    $('#edit_recurringMonthsDiv').hide();
                    $('#edit_isRecurring').prop('checked', false);
                    $('#edit_amountHelpText').html(
                        '<strong>Total amount to deduct</strong> - One-time deduction');
                }
            });

            $(document).on('change', '#edit_isRecurring', function() {
                if ($(this).is(':checked')) {
                    $('#edit_recurringMonthsDiv').show();
                    updateEditTotalAmount();
                } else {
                    $('#edit_recurringMonthsDiv').hide();
                }
            });

            $(document).on('input', '#edit_amount', updateEditTotalAmount);
            $(document).on('change', '#edit_recurringMonthsSelect', updateEditTotalAmount);
        });

        // ==================== ADD LOAN FORM HANDLERS ====================
        document.addEventListener('DOMContentLoaded', function() {
            const deductionType = document.getElementById('deductionType');
            const recurringOptionDiv = document.getElementById('recurringOptionDiv');
            const recurringMonthsDiv = document.getElementById('recurringMonthsDiv');
            const isRecurringCheckbox = document.getElementById('isRecurring');
            const amountInput = document.getElementById('amountInput');
            const recurringMonthsSelect = document.getElementById('recurringMonthsSelect');
            const totalAmountPreview = document.getElementById('totalAmountPreview');
            const amountHelpText = document.getElementById('amountHelpText');

            if (deductionType) {
                function toggleRecurringOption() {
                    if (deductionType.value === 'loan') {
                        if (recurringOptionDiv) recurringOptionDiv.style.display = 'block';
                        if (amountHelpText) {
                            amountHelpText.innerHTML =
                                '<strong>Monthly installment amount</strong> - This amount will be deducted each month';
                        }
                    } else {
                        if (recurringOptionDiv) recurringOptionDiv.style.display = 'none';
                        if (recurringMonthsDiv) recurringMonthsDiv.style.display = 'none';
                        if (isRecurringCheckbox) isRecurringCheckbox.checked = false;
                        if (amountHelpText) {
                            amountHelpText.innerHTML =
                                '<strong>Total amount to deduct</strong> - One-time deduction';
                        }
                    }
                }

                function toggleRecurringMonths() {
                    if (isRecurringCheckbox && isRecurringCheckbox.checked) {
                        if (recurringMonthsDiv) recurringMonthsDiv.style.display = 'block';
                        calculateTotalAmount();
                    } else {
                        if (recurringMonthsDiv) recurringMonthsDiv.style.display = 'none';
                    }
                }

                function calculateTotalAmount() {
                    const amount = parseFloat(amountInput?.value) || 0;
                    const months = parseInt(recurringMonthsSelect?.value) || 0;
                    const total = amount * months;
                    if (totalAmountPreview) {
                        totalAmountPreview.textContent = months > 0 ? total.toLocaleString() : '0';
                    }
                }

                deductionType.addEventListener('change', toggleRecurringOption);
                if (isRecurringCheckbox) isRecurringCheckbox.addEventListener('change', toggleRecurringMonths);
                if (amountInput) amountInput.addEventListener('input', calculateTotalAmount);
                if (recurringMonthsSelect) recurringMonthsSelect.addEventListener('change', calculateTotalAmount);

                toggleRecurringOption();
                if (isRecurringCheckbox && isRecurringCheckbox.checked) toggleRecurringMonths();
            }
        });

        // ==================== SWEETALERT NOTIFICATIONS ====================
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endsection
