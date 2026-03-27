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
    .type-loan { color: #3b82f6; }
    .type-advance { color: #8b5cf6; }
    .type-penalty { color: #ef4444; }
    .type-fine { color: #f59e0b; }
    .type-other { color: #6b7280; }
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-hand-holding-usd me-2"></i> Staff Loans & Advances
                        </h5>
                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addLoanModal">
                            <i class="fas fa-plus me-1"></i> Add Loan/Advance
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    {{-- Pending Deductions Table --}}
                    <h6 class="mb-3">
                        <i class="fas fa-clock me-1 text-warning"></i> Pending Deductions
                        <span class="badge bg-secondary ms-2">{{ count($deductions['pending']) }}</span>
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-deductions table-bordered" id="myTable">
                            <thead>
                                    <th>#</th>
                                    <th>Reference#</th>
                                    <th>Staff ID</th>
                                    <th>Employee Name</th>
                                    <th>Staff Type</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount (TZS)</th>
                                    <th>Duration</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </thead>
                            <tbody>
                                @forelse($deductions['pending'] as $index => $deduction)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $deduction['reference_number'] ?? 'N/A' }}</td>
                                    <td><strong>{{ strtoupper($deduction['staff_id']) }}</strong></td>
                                    <td>{{ ucwords(strtolower($deduction['employee_name'])) }}</td>
                                    <td>{{ $deduction['staff_type'] }}</td>
                                    <td>
                                        <span class="deduction-type type-{{ $deduction['deduction_type'] }}">
                                            <i class="fas
                                                @if($deduction['deduction_type'] == 'loan') fa-hand-holding-usd
                                                @elseif($deduction['deduction_type'] == 'advance') fa-money-bill-wave
                                                @elseif($deduction['deduction_type'] == 'penalty') fa-gavel
                                                @elseif($deduction['deduction_type'] == 'fine') fa-exclamation-triangle
                                                @else fa-tag
                                                @endif me-1"></i>
                                            {{ ucfirst($deduction['deduction_type']) }}
                                        </span>
                                    </td>
                                    <td>{{ $deduction['description'] }}</td>
                                    <td class="text-end fw-bold">{{ number_format($deduction['amount'], 0) }}</td>
                                    <td class="text-center">
                                        @if($deduction['is_recurring'])
                                            <span class="badge bg-info">
                                                <i class="fas fa-sync-alt me-1"></i>
                                                {{ $deduction['remaining_months'] }}/{{ $deduction['recurring_months'] }} months
                                            </span>
                                            <div class="help-text">Remaining: {{ $deduction['remaining_months'] }} month(s)</div>
                                        @else
                                            <span class="badge bg-secondary">One-time</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($deduction['created_at'])->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-xs btn-danger" style="border-radius: 12px" onclick="cancelDeduction({{ $deduction['id'] }})">
                                                 Cancel
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle me-1"></i> No pending deductions
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- History/Deducted Table --}}
                    @if(count($deductions['history']) > 0)
                    <h6 class="mb-3 mt-4">
                        <i class="fas fa-history me-1 text-muted"></i> Deduction History
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-deductions table-bordered" id="myTable">
                            <thead>
                                    <th>#</th>
                                    <th>Staff ID</th>
                                    <th>Employee Name</th>
                                    <th>Staff Type</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount (TZS)</th>
                                    <th>Deducted On</th>
                                    <th>Payroll Month</th>
                                </thead>
                            <tbody>
                                @foreach($deductions['history'] as $index => $deduction)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ strtoupper($deduction['staff_id']) }}</strong></td>
                                    <td>{{ ucwords(strtolower($deduction['employee_name'])) }}</td>
                                    <td>{{ $deduction['staff_type'] }}</td>
                                    <td>
                                        <span class="deduction-type type-{{ $deduction['deduction_type'] }}">
                                            {{ ucfirst($deduction['deduction_type']) }}
                                        </span>
                                    </td>
                                    <td>{{ $deduction['description'] }}</td>
                                    <td class="text-end">{{ number_format($deduction['amount'], 0) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($deduction['deducted_at'])->format('d/m/Y') }}</td>
                                    <td>{{ $deduction['payroll_month'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                            <input type="text"
                                   name="staff_id"
                                   class="form-control @error('staff_id') is-invalid @enderror"
                                   value="{{ old('staff_id') }}"
                                   required
                                   placeholder="TCH-001">
                            @error('staff_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Enter staff ID (Teacher, Transport, or Other Staff)</div>
                        </div>

                        {{-- Staff Type --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Staff Type <span class="text-danger">*</span></label>
                            <select name="staff_type" class="form-select @error('staff_type') is-invalid @enderror" required>
                                <option value="">Select Staff Type</option>
                                <option value="Teacher" {{ old('staff_type') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="Transport Staff" {{ old('staff_type') == 'Transport Staff' ? 'selected' : '' }}>Transport Staff</option>
                                <option value="Other Staff" {{ old('staff_type') == 'Other Staff' ? 'selected' : '' }}>Other Staff</option>
                            </select>
                            @error('staff_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deduction Type --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Deduction Type <span class="text-danger">*</span></label>
                            <select name="deduction_type" id="deductionType" class="form-select @error('deduction_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="loan" {{ old('deduction_type') == 'loan' ? 'selected' : '' }}>Loan (Can be recurring)</option>
                                <option value="advance" {{ old('deduction_type') == 'advance' ? 'selected' : '' }}>Salary Advance (One-time)</option>
                                <option value="penalty" {{ old('deduction_type') == 'penalty' ? 'selected' : '' }}>Penalty (One-time)</option>
                                <option value="fine" {{ old('deduction_type') == 'fine' ? 'selected' : '' }}>Fine (One-time)</option>
                                <option value="other" {{ old('deduction_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('deduction_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="amount"
                                   id="amountInput"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}"
                                   required
                                   min="1000"
                                   step="1000"
                                   placeholder="500000">
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text" id="amountHelpText">
                                @if(old('deduction_type') == 'loan')
                                    For recurring loans, this is the <strong>monthly installment amount</strong>
                                @else
                                    This is the <strong>total amount to deduct</strong>
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="2"
                                      required
                                      placeholder="Describe the reason for this deduction...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Recurring Option (Only for Loans) --}}
                        <div class="col-md-6 mb-3" id="recurringOptionDiv" style="display: none;">
                            <div class="form-check">
                                <input type="checkbox"
                                       name="is_recurring"
                                       class="form-check-input"
                                       id="isRecurring"
                                       value="1"
                                       {{ old('is_recurring') ? 'checked' : '' }}>
                                <label class="form-check-label" for="isRecurring">
                                    Recurring Deduction (Multiple Months)
                                </label>
                            </div>
                            <div class="help-text">Check this if the deduction will be spread over multiple months</div>
                        </div>

                        {{-- Recurring Months (Shown only when recurring is checked) --}}
                        <div class="col-md-6 mb-3" id="recurringMonthsDiv" style="display: none;">
                            <label class="form-label">Number of Months <span class="text-danger">*</span></label>
                            <select name="recurring_months" id="recurringMonthsSelect" class="form-select @error('recurring_months') is-invalid @enderror">
                                <option value="">Select months</option>
                                @for($i = 1; $i <= 36; $i++)
                                    <option value="{{ $i }}" {{ old('recurring_months') == $i ? 'selected' : '' }}>
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
                            <textarea name="authorization_notes"
                                      class="form-control @error('authorization_notes') is-invalid @enderror"
                                      rows="2"
                                      placeholder="Any additional notes or authorization details...">{{ old('authorization_notes') }}</textarea>
                            @error('authorization_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hidden fields that are auto-generated/populated --}}
                        <input type="hidden" name="authorized_by" value="{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Get elements
    const deductionType = document.getElementById('deductionType');
    const recurringOptionDiv = document.getElementById('recurringOptionDiv');
    const recurringMonthsDiv = document.getElementById('recurringMonthsDiv');
    const isRecurringCheckbox = document.getElementById('isRecurring');
    const amountInput = document.getElementById('amountInput');
    const recurringMonthsSelect = document.getElementById('recurringMonthsSelect');
    const totalAmountPreview = document.getElementById('totalAmountPreview');
    const amountHelpText = document.getElementById('amountHelpText');

    // Show/hide recurring option based on deduction type (only for loans)
    function toggleRecurringOption() {
        if (deductionType.value === 'loan') {
            recurringOptionDiv.style.display = 'block';
            if (amountHelpText) {
                amountHelpText.innerHTML = '<strong>Monthly installment amount</strong> - This amount will be deducted each month';
            }
        } else {
            recurringOptionDiv.style.display = 'none';
            recurringMonthsDiv.style.display = 'none';
            isRecurringCheckbox.checked = false;
            if (amountHelpText) {
                amountHelpText.innerHTML = '<strong>Total amount to deduct</strong> - One-time deduction';
            }
        }
    }

    // Show/hide recurring months field and calculate total
    function toggleRecurringMonths() {
        if (isRecurringCheckbox.checked) {
            recurringMonthsDiv.style.display = 'block';
            calculateTotalAmount();
        } else {
            recurringMonthsDiv.style.display = 'none';
        }
    }

    // Calculate total amount when amount or months change
    function calculateTotalAmount() {
        const amount = parseFloat(amountInput.value) || 0;
        const months = parseInt(recurringMonthsSelect.value) || 0;
        const total = amount * months;
        if (totalAmountPreview && months > 0) {
            totalAmountPreview.textContent = total.toLocaleString();
        } else if (totalAmountPreview) {
            totalAmountPreview.textContent = '0';
        }
    }

    // Event listeners
    deductionType.addEventListener('change', toggleRecurringOption);
    isRecurringCheckbox.addEventListener('change', toggleRecurringMonths);
    amountInput.addEventListener('input', calculateTotalAmount);
    recurringMonthsSelect.addEventListener('change', calculateTotalAmount);

    // On page load
    toggleRecurringOption();
    if (isRecurringCheckbox.checked) {
        toggleRecurringMonths();
    }

    // Cancel deduction function
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
                form.action = '{{ url("/deductions/staff-loans") }}/' + id + '/cancel';
                form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Display success/error messages with SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonText: 'OK'
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: '{!! implode("<br>", $errors->all()) !!}',
            confirmButtonText: 'OK'
        });
    @endif
</script>

@endsection
