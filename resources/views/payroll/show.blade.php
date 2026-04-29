@extends('SRTDashboard.frame')

@section('content')
    <style>
        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .detail-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-draft {
            background: #f8f9fc;
            color: #5a5c69;
            border: 1px solid #e3e6f0;
        }

        .status-calculated {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-finalized {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .employee-table {
            font-size: 13px;
        }

        .employee-table th {
            background: #f8f9fc;
            font-weight: 600;
            font-size: 12px;
        }

        .summary-box {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .badge-edited {
            background-color: #ffc107;
            color: #856404;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .btn-revert {
            background: #f0ad4e;
            color: white;
        }

        .btn-revert:hover {
            background: #ec971f;
            color: white;
        }

        .btn-lock {
            background: #dc3545;
            color: white;
        }

        .btn-lock:hover {
            background: #c82333;
            color: white;
        }

        .locked-badge {
            background: #6c757d;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .edit-mode-banner {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #4e73df;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 700;
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-right: 8px;
        }

        .btn-calculate {
            background: #ffc107;
            color: #856404;
        }

        .btn-finalize {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-generate-slips {
            background: #17a2b8;
            color: white;
        }

        .table-scrollable {
            max-height: 450px;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
        }

        .employee-table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fc;
            z-index: 10;
            border-bottom: 2px solid #e3e6f0;
        }

        .editable-cell {
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .editable-cell:hover {
            background-color: #eef2ff;
            box-shadow: inset 0 0 0 1px #4e73df;
        }

        .editable-cell.editing {
            padding: 0;
            background-color: white;
            cursor: default;
        }

        .inline-editor-input {
            width: 100%;
            padding: 6px 8px;
            border: 2px solid #4e73df;
            border-radius: 6px;
            font-size: 0.85rem;
            outline: none;
        }

        .inline-editor-select {
            width: 100%;
            padding: 6px 8px;
            border: 2px solid #4e73df;
            border-radius: 6px;
            font-size: 0.85rem;
            background-color: white;
            outline: none;
        }

        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease;
        }

        .toast-notification.toast-error {
            background: #dc3545;
        }

        .toast-notification.toast-warning {
            background: #ffc107;
            color: #333;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
    <div class="py-4">
        <div class="row">
            <div class="col-12">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1 fw-bold"><i class="fas fa-calculator mr-2 text-primary"></i> Payroll Details</h4>
                        <p class="text-muted mb-0">View and manage payroll batch</p>
                    </div>
                    <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                </div>

                {{-- EDIT MODE BANNER --}}
                @if (isset($batch['is_edited_after_finalize']) && $batch['is_edited_after_finalize'] == true)
                    <div class="edit-mode-banner text-dark">
                        <div>
                            <i class="fas fa-edit"></i>
                            <strong>EDIT MODE ACTIVE</strong> - This payroll was reverted from finalized state.
                            <small class="d-block mt-1">Make your changes, then recalculate and finalize again.</small>
                        </div>
                        <span class="badge bg-light text-dark">Pending Recalculation</span>
                    </div>
                @endif

                {{-- Batch Information Card --}}
                <div class="detail-card">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="detail-label">Batch Number</div>
                            <div class="detail-value">{{ $batch['batch_number'] }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Payroll Month</div>
                            <div class="detail-value">{{ date('F Y', strtotime($batch['payroll_month'] . '-01')) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Payroll Date</div>
                            <div class="detail-value">{{ \Carbon\Carbon::parse($batch['payroll_date'])->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                @php
                                    $statusClass =
                                        [
                                            'draft' => 'status-draft',
                                            'calculated' => 'status-calculated',
                                            'finalized' => 'status-finalized',
                                        ][$batch['status']] ?? 'status-draft';
                                    $statusIcon =
                                        [
                                            'draft' => 'fa-pen',
                                            'calculated' => 'fa-calculator',
                                            'finalized' => 'fa-check-circle',
                                        ][$batch['status']] ?? 'fa-pen';
                                    // ✅ FIX: Properly check if locked - handle both null and false
                                    $isLocked = ($batch['is_locked'] ?? false) === true;
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="fas {{ $statusIcon }} mr-1"></i>
                                    {{ ucfirst($batch['status']) }}
                                    @if (isset($batch['is_edited_after_finalize']) && $batch['is_edited_after_finalize'] == true)
                                        <span class="badge-edited ms-2">Edited</span>
                                    @endif
                                    @if ($isLocked)
                                        <span class="badge bg-secondary ms-2"><i class="fas fa-lock me-1"></i> Locked</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="detail-label">Issued By</div>
                            <div class="detail-value">{{ ucwords(strtolower($batch['generated_by'] ?? 'System')) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Generated At</div>
                            <div class="detail-value">
                                {{ \Carbon\Carbon::parse($batch['generated_at'])->format('d/m/Y H:i:s') }}</div>
                        </div>
                    </div>
                    @if (isset($batch['locked_at']) && $batch['locked_at'])
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="detail-label">Locked At</div>
                                <div class="detail-value text-muted">
                                    {{ \Carbon\Carbon::parse($batch['locked_at'])->format('d/m/Y H:i:s') }}
                                    @if (isset($batch['lock_reason']) && $batch['lock_reason'])
                                        <span class="badge bg-secondary ms-2">Reason: {{ $batch['lock_reason'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Summary Statistics --}}
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="summary-box">
                            <div class="summary-title">Total Employees</div>
                            <div class="summary-value">{{ number_format($summary['total_employees'] ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="summary-box">
                            <div class="summary-title">Total Gross</div>
                            <div class="summary-value">TZS {{ number_format($summary['total_gross'] ?? 0, 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="summary-box">
                            <div class="summary-title">Total NSSF</div>
                            <div class="summary-value">TZS {{ number_format($summary['total_nssf'] ?? 0, 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="summary-box">
                            <div class="summary-title">Total PAYE</div>
                            <div class="summary-value">TZS {{ number_format($summary['total_paye'] ?? 0, 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="summary-box">
                            <div class="summary-title">Total HESLB</div>
                            <div class="summary-value text-danger">TZS {{ number_format($summary['total_heslb'] ?? 0, 0) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="summary-box">
                            <div class="summary-title">Total Loans</div>
                            <div class="summary-value text-danger">TZS
                                {{ number_format($summary['total_unofficial'] ?? 0, 0) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mb-4">
                    @if ($batch['status'] == 'draft')
                        <button type="button" class="btn btn-action btn-calculate"
                            onclick="calculatePayroll('{{ $batch['hash'] }}')"><i class="fas fa-calculator mr-1"></i>
                            Calculate</button>
                        <button type="button" class="btn btn-action btn-danger"
                            onclick="deletePayroll('{{ $batch['hash'] }}')"><i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    @endif

                    @if ($batch['status'] == 'calculated')
                        <button type="button" class="btn btn-action btn-warning"
                            onclick="recalculatePayroll('{{ $batch['hash'] }}')"><i class="fas fa-sync-alt mr-1"></i>
                            Recalculate</button>
                        <button type="button" class="btn btn-action btn-finalize"
                            onclick="finalizePayroll('{{ $batch['hash'] }}')"><i class="fas fa-check-circle mr-1"></i>
                            Finalize</button>
                        <button type="button" class="btn btn-action btn-danger"
                            onclick="deletePayroll('{{ $batch['hash'] }}')"><i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    @endif

                    @if ($batch['status'] == 'finalized')
                        {{-- ✅ FIXED: Proper condition for showing edit buttons --}}
                        @php
                            $isActuallyLocked = ($batch['is_locked'] ?? false) === true;
                        @endphp

                        @if (!$isActuallyLocked)
                            <button type="button" class="btn btn-action btn-revert"
                                onclick="revertFinalizePayroll('{{ $batch['hash'] }}')">
                                <i class="fas fa-undo-alt mr-1"></i> Enable Editing
                            </button>
                            <button type="button" class="btn btn-action btn-lock"
                                onclick="lockPayroll('{{ $batch['hash'] }}')">
                                <i class="fas fa-lock mr-1"></i> Lock Batch
                            </button>
                            <button type="button" class="btn btn-action btn-generate-slips"
                                onclick="generateSlips('{{ $batch['hash'] }}')">
                                <i class="fas fa-file-pdf mr-1"></i> Generate Slips
                            </button>
                        @endif
                        <a href="{{ route('payroll.download-slips', $batch['hash']) }}"
                            class="btn btn-action btn-download btn-primary">
                            <i class="fas fa-download mr-1"></i> Download Slips
                        </a>
                        <a href="{{ route('payroll.download-summary', $batch['hash']) }}"
                            class="btn btn-action btn-export btn-secondary">
                            <i class="fas fa-file-excel mr-1"></i> Download Payroll
                        </a>
                    @endif
                </div>

                {{-- Employees Table with Dynamic Columns --}}
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-users mr-2"></i> Employee Lists</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-scrollable">
                            <table class="table table-sm employee-table table-responsive-md mb-0 table-bordered"
                                id="employeesTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Staff ID</th>
                                        <th>Employee Name</th>

                                        @if ($batch['status'] == 'draft')
                                            <th style="min-width: 120px;">Department</th>
                                            <th style="min-width: 100px;">Contract</th>
                                            <th style="min-width: 120px;">Basic Salary</th>
                                            <th style="min-width: 120px;">Allowances</th>
                                            <th style="min-width: 120px;">Gross Pay</th>
                                            <th style="min-width: 130px;">Account Number</th>
                                            <th style="min-width: 130px;">Account Name</th>
                                            <th style="min-width: 120px;">Bank Name</th>
                                        @elseif($batch['status'] == 'calculated')
                                            <th>Department</th>
                                            <th>Contract</th>
                                            <th>Basic Salary</th>
                                            <th>Allowances</th>
                                            <th>Gross Pay</th>
                                            <th>NSSF</th>
                                            <th>PAYE</th>
                                            <th>HESLB</th>
                                            <th>Staff Loan</th>
                                            <th>Net Pay</th>
                                        @else
                                            <th>Department</th>
                                            <th>Contract</th>
                                            <th>Gross Pay</th>
                                            <th>NSSF</th>
                                            <th>PAYE</th>
                                            <th>HESLB</th>
                                            <th>Staff Loan</th>
                                            <th>Net Pay</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($batch['payroll_employees'] ?? [] as $index => $employee)
                                        @php
                                            $calculation = collect($batch['payroll_calculations'] ?? [])->firstWhere(
                                                'payroll_employee_id',
                                                $employee['id'],
                                            );
                                            $heslbAmount = isset($calculation['heslb'])
                                                ? (float) $calculation['heslb']
                                                : 0;
                                            $totalUnofficial = 0;
                                            if (
                                                isset($calculation['unofficial_deductions']) &&
                                                $calculation['unofficial_deductions']
                                            ) {
                                                $unofficialDeductions = json_decode(
                                                    $calculation['unofficial_deductions'],
                                                    true,
                                                );
                                                if (
                                                    is_array($unofficialDeductions) &&
                                                    count($unofficialDeductions) > 0
                                                ) {
                                                    $totalUnofficial = array_sum($unofficialDeductions);
                                                }
                                            }
                                            $amountToPay = isset($calculation['amount_to_pay'])
                                                ? (float) $calculation['amount_to_pay']
                                                : (isset($calculation['net_salary'])
                                                    ? (float) $calculation['net_salary']
                                                    : 0);
                                            $grossSalary = isset($calculation['gross_salary'])
                                                ? (float) $calculation['gross_salary']
                                                : 0;
                                            $nssf = isset($calculation['nssf']) ? (float) $calculation['nssf'] : 0;
                                            $paye = isset($calculation['paye_tax'])
                                                ? (float) $calculation['paye_tax']
                                                : 0;
                                            $contractType =
                                                $employee['contract_type'] ??
                                                ($employee['is_provision_period'] ? 'provision' : 'new');
                                            $isProvision =
                                                $contractType === 'provision' ||
                                                ($employee['is_provision_period'] ?? false);
                                            $isActuallyLocked = ($batch['is_locked'] ?? false) === true;
                                            $canEdit =
                                                !$isActuallyLocked &&
                                                ($batch['status'] == 'draft' ||
                                                    $batch['status'] == 'calculated' ||
                                                    (isset($batch['is_edited_after_finalize']) &&
                                                        $batch['is_edited_after_finalize'] == true));
                                            $displayBasic =
                                                $employee['basic_salary_modified'] ?? $employee['basic_salary'];
                                            $displayAllowances =
                                                $employee['modified_allowances'] ?? $employee['allowances'];
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-uppercase" style="width: 8%;">
                                                <strong>{{ strtoupper($employee['staff_id']) }}</strong>
                                            </td>
                                            <td class="text-capitalize">
                                                {{ ucwords(strtolower($employee['employee_full_name'])) }}</td>

                                            @if ($batch['status'] == 'draft')
                                                <td class="editable-cell" data-field="department"
                                                    data-employee-id="{{ $employee['id'] }}"
                                                    data-current-value="{{ $employee['employee_department'] ?? '' }}"
                                                    data-staff-id="{{ $employee['staff_id'] }}">
                                                    {{ $employee['employee_department'] ?? '—' }}
                                                </td>
                                                <td class="text-center editable-cell" data-field="contract_type"
                                                    data-employee-id="{{ $employee['id'] }}"
                                                    data-current-value="{{ $contractType }}"
                                                    data-staff-id="{{ $employee['staff_id'] }}">
                                                    @if ($isProvision)
                                                        <span class="badge bg-warning text-dark"><i
                                                                class="fas fa-clock me-1"></i> Provision</span>
                                                    @else
                                                        <span class="badge bg-success"><i
                                                                class="fas fa-check-circle me-1"></i> New</span>
                                                    @endif
                                                </td>
                                                <td class="text-end basic-salary-cell editable-cell"
                                                    data-field="basic_salary" data-employee-id="{{ $employee['id'] }}"
                                                    data-current-value="{{ $displayBasic }}"
                                                    data-staff-id="{{ $employee['staff_id'] }}">
                                                    <strong
                                                        class="text-primary">{{ number_format($displayBasic, 0) }}</strong>
                                                </td>
                                                <td class="text-end allowances-cell editable-cell" data-field="allowances"
                                                    data-employee-id="{{ $employee['id'] }}"
                                                    data-current-value="{{ $displayAllowances }}"
                                                    data-staff-id="{{ $employee['staff_id'] }}">
                                                    <strong
                                                        class="text-success">{{ number_format($displayAllowances, 0) }}</strong>
                                                </td>
                                                <td class="text-end gross-pay-cell"
                                                    data-employee-id="{{ $employee['id'] }}">
                                                    <strong
                                                        class="text-primary">{{ number_format($grossSalary, 0) }}</strong>
                                                </td>
                                                <td class="editable-cell" data-field="bank_account_number"
                                                    data-employee-id="{{ $employee['id'] }}"
                                                    data-current-value="{{ $employee['bank_account_number'] ?? '' }}"
                                                    data-staff-id="{{ $employee['staff_id'] }}">
                                                    {{ $employee['bank_account_number'] ?? '—' }}
                                                </td>
                                                <td class="editable-cell" data-field="bank_account_name"
                                                    data-employee-id="{{ $employee['id'] }}"
                                                    data-current-value="{{ $employee['bank_account_name'] ?? '' }}"
                                                    data-staff-id="{{ $employee['staff_id'] }}">
                                                    {{ $employee['bank_account_name'] ?? '—' }}
                                                </td>
                                                <td class="editable-cell" data-field="bank_name"
                                                    data-employee-id="{{ $employee['id'] }}"
                                                    data-current-value="{{ $employee['bank_name'] ?? '' }}"
                                                    data-staff-id="{{ $employee['staff_id'] }}">
                                                    {{ $employee['bank_name'] ?? '—' }}
                                                </td>
                                            @elseif($batch['status'] == 'calculated')
                                                <td>{{ $employee['employee_department'] ?? '—' }}</td>
                                                <td class="text-center">
                                                    @if ($isProvision)
                                                        <span class="badge bg-warning text-dark"><i
                                                                class="fas fa-clock me-1"></i> Provision</span>
                                                    @else
                                                        <span class="badge bg-success"><i
                                                                class="fas fa-check-circle me-1"></i> New</span>
                                                    @endif
                                                </td>
                                                <td class="text-end"><strong
                                                        class="text-primary">{{ number_format($displayBasic, 0) }}</strong>
                                                </td>
                                                <td class="text-end"><strong
                                                        class="text-success">{{ number_format($displayAllowances, 0) }}</strong>
                                                </td>
                                                <td class="text-end"><strong
                                                        class="text-primary">{{ number_format($grossSalary, 0) }}</strong>
                                                </td>
                                                <td class="text-end">{{ number_format($nssf, 0) }}</td>
                                                <td class="text-end">{{ number_format($paye, 0) }}</td>
                                                <td class="text-end">
                                                    @if ($heslbAmount > 0)
                                                        <span
                                                        class="text-danger fw-bold">{{ number_format($heslbAmount, 0) }}</span>@else<span
                                                            class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if ($totalUnofficial > 0)
                                                        <span
                                                        class="text-danger fw-bold">{{ number_format($totalUnofficial, 0) }}</span>@else<span
                                                            class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-success fw-bold text-end">
                                                    {{ number_format($amountToPay, 0) }}</td>
                                            @else
                                                <td>{{ $employee['employee_department'] ?? '—' }}</td>
                                                <td class="text-center">
                                                    @if ($isProvision)
                                                        <span class="badge bg-warning text-dark"><i
                                                                class="fas fa-clock me-1"></i> Provision</span>
                                                    @else
                                                        <span class="badge bg-success"><i
                                                                class="fas fa-check-circle me-1"></i> New</span>
                                                    @endif
                                                </td>
                                                <td class="text-end"><strong
                                                        class="text-primary">{{ number_format($grossSalary, 0) }}</strong>
                                                </td>
                                                <td class="text-end">{{ number_format($nssf, 0) }}</td>
                                                <td class="text-end">{{ number_format($paye, 0) }}</td>
                                                <td class="text-end">
                                                    @if ($heslbAmount > 0)
                                                        <span
                                                        class="text-danger fw-bold">{{ number_format($heslbAmount, 0) }}</span>@else<span
                                                            class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if ($totalUnofficial > 0)
                                                        <span
                                                        class="text-danger fw-bold">{{ number_format($totalUnofficial, 0) }}</span>@else<span
                                                            class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-success fw-bold text-end">
                                                    {{ number_format($amountToPay, 0) }}</td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="20" class="text-center text-muted py-4"><i
                                                    class="fas fa-info-circle mr-1"></i> No employees found in this payroll
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // ==================== GLOBAL VARIABLES ====================
        let batchHash = '{{ $batch['hash'] }}';
        let batchStatus = '{{ $batch['status'] }}';

        // ✅ FIX: Get boolean directly from casted value
        // The cast in model ensures is_locked is already boolean
        let isBackendLocked = @json($batch['is_locked'] ?? false);

        // Double-check: if it's string '0' or '1', convert properly
        if (typeof isBackendLocked === 'string') {
            isBackendLocked = isBackendLocked === '1' || isBackendLocked === 'true' || isBackendLocked === 'TRUE';
        }

        // Also check via raw value as backup
        let rawLockedValue = '{{ $batch['is_locked'] ?? 0 }}';
        let isActuallyLocked = isBackendLocked || (rawLockedValue === '1' || rawLockedValue === 'true');

        console.log('🔑 Lock Status Debug:', {
            fromJson: isBackendLocked,
            rawValue: rawLockedValue,
            finalLocked: isActuallyLocked,
            batchStatus: batchStatus
        });

        // canEdit: ONLY if NOT locked AND (draft OR calculated OR reverted)
        let canEdit = !isActuallyLocked && (batchStatus === 'draft' || batchStatus === 'calculated' ||
            {{ isset($batch['is_edited_after_finalize']) && $batch['is_edited_after_finalize'] == true ? 'true' : 'false' }}
        );

        console.log('📝 canEdit:', canEdit);

        let currentEditingCell = null;
        let editedBasicSalaries = new Map();
        let editedAllowances = new Map();

        // ==================== HELPER FUNCTIONS ====================
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML =
                `<div style="display: flex; align-items: center; gap: 10px;"><i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle'}"></i><span>${message}</span></div>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }

        function formatNumber(num) {
            if (num === null || num === undefined || isNaN(num)) return '0';
            return new Intl.NumberFormat().format(num);
        }

        function parseNumber(value) {
            if (!value && value !== 0) return 0;
            const cleaned = String(value).replace(/[^0-9.-]/g, '');
            const parsed = parseFloat(cleaned);
            return isNaN(parsed) ? 0 : parsed;
        }

        // ==================== GET CELL VALUES SAFELY ====================
        function getBasicSalary($row) {
            const $basicCell = $row.find('.basic-salary-cell');
            if (!$basicCell.length) return 0;
            let basicText = '';
            const $strong = $basicCell.find('strong');
            if ($strong.length) basicText = $strong.text();
            if (!basicText || basicText === '') {
                const clone = $basicCell.clone();
                clone.find('span, .badge, button, .spinner-border').remove();
                basicText = clone.text();
            }
            if (!basicText || basicText === '') basicText = $basicCell.data('current-value');
            basicText = String(basicText).replace(/[^0-9]/g, '');
            return parseNumber(basicText);
        }

        function getAllowances($row) {
            const $allowancesCell = $row.find('.allowances-cell');
            if (!$allowancesCell.length) return 0;
            let allowancesText = '';
            const $strong = $allowancesCell.find('strong');
            if ($strong.length) allowancesText = $strong.text();
            if (!allowancesText || allowancesText === '') {
                const clone = $allowancesCell.clone();
                clone.find('span, .badge, button, .spinner-border').remove();
                allowancesText = clone.text();
            }
            if (!allowancesText || allowancesText === '') allowancesText = $allowancesCell.data('current-value');
            allowancesText = String(allowancesText).replace(/[^0-9]/g, '');
            return parseNumber(allowancesText);
        }

        // ==================== GROSS PAY UPDATE ====================
        function updateGrossPayForEmployee($row, employeeId) {
            let basic = editedBasicSalaries.has(employeeId) ? editedBasicSalaries.get(employeeId) : getBasicSalary($row);
            let allowances = editedAllowances.has(employeeId) ? editedAllowances.get(employeeId) : getAllowances($row);
            basic = isNaN(basic) ? 0 : basic;
            allowances = isNaN(allowances) ? 0 : allowances;
            const newGross = basic + allowances;
            const $grossCell = $row.find('.gross-pay-cell');
            if ($grossCell.length) {
                const hasEdits = editedBasicSalaries.has(employeeId) || editedAllowances.has(employeeId);
                if (hasEdits) {
                    $grossCell.html(
                        `<strong class="text-primary">${formatNumber(newGross)}</strong> <span class="badge bg-warning text-dark ms-1">Updated</span>`
                    );
                } else {
                    $grossCell.html(`<strong class="text-primary">${formatNumber(newGross)}</strong>`);
                }
            }
            return newGross;
        }

        function initializeAllGrossPay() {
            $('.employee-table tbody tr').each(function() {
                const $row = $(this);
                let employeeId = $row.find('[data-employee-id]').first().data('employee-id');
                if (employeeId) {
                    const basic = getBasicSalary($row);
                    const allowances = getAllowances($row);
                    const gross = basic + allowances;
                    $row.find('.gross-pay-cell').html(
                        `<strong class="text-primary">${formatNumber(gross)}</strong>`);
                }
            });
        }

        // ==================== INLINE EDITING ====================
        function makeEditable(cell, field, employeeId, currentValue, staffId) {
            if (!canEdit) {
                showToast('Cannot edit - this payroll is locked.', 'warning');
                return;
            }
            if (currentEditingCell && currentEditingCell !== cell) saveCurrentEdit();
            const $cell = $(cell);
            const originalHtml = $cell.html();
            $cell.addClass('editing');
            $cell.data('original', originalHtml);
            $cell.data('field', field);
            $cell.data('employee-id', employeeId);
            $cell.data('staff-id', staffId);
            $cell.data('current-value', currentValue);
            let inputHtml = '';
            if (field === 'basic_salary' || field === 'allowances') {
                inputHtml = `<div class="input-group input-group-sm" style="min-width: 140px;">
                <input type="number" class="form-control form-control-sm inline-editor-input" value="${currentValue}" step="1000" min="0" style="text-align: right;">
                <button class="btn btn-sm btn-success save-edit" type="button"><i class="fas fa-check"></i></button>
                <button class="btn btn-sm btn-secondary cancel-edit" type="button"><i class="fas fa-times"></i></button>
            </div>`;
            } else if (field === 'contract_type') {
                inputHtml = `<div class="input-group input-group-sm" style="min-width: 120px;">
                <select class="form-select form-select-sm inline-editor-select">
                    <option value="new" ${currentValue === 'new' ? 'selected' : ''}>New</option>
                    <option value="provision" ${currentValue === 'provision' ? 'selected' : ''}>Provision</option>
                </select>
                <button class="btn btn-sm btn-success save-edit" type="button"><i class="fas fa-check"></i></button>
                <button class="btn btn-sm btn-secondary cancel-edit" type="button"><i class="fas fa-times"></i></button>
            </div>`;
            } else {
                inputHtml = `<div class="input-group input-group-sm" style="min-width: 180px;">
                <input type="text" class="form-control form-control-sm inline-editor-input" value="${escapeHtml(currentValue)}">
                <button class="btn btn-sm btn-success save-edit" type="button"><i class="fas fa-check"></i></button>
                <button class="btn btn-sm btn-secondary cancel-edit" type="button"><i class="fas fa-times"></i></button>
            </div>`;
            }
            $cell.html(inputHtml);
            const $input = $cell.find('input, select');
            $input.focus();
            if ($input.is('input')) $input.select();
            $cell.find('.save-edit').off('click').on('click', function() {
                let newValue = (field === 'basic_salary' || field === 'allowances') ? parseNumber($input.val()) :
                    $input.val();
                if (field === 'basic_salary' || field === 'allowances' && newValue < 0) {
                    showToast('Amount cannot be negative', 'warning');
                    return;
                }
                saveEdit($cell, field, employeeId, newValue, currentValue, originalHtml, staffId);
            });
            $cell.find('.cancel-edit').off('click').on('click', function() {
                cancelEditing($cell);
                $cell.html(originalHtml);
                currentEditingCell = null;
            });
            $input.off('keypress').on('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $cell.find('.save-edit').click();
                }
            });
            currentEditingCell = $cell;
        }

        function cancelEditing($cell) {
            if ($cell && $cell.find) $cell.find('.input-group').remove();
            currentEditingCell = null;
        }

        function updateCellDisplay($cell, field, newValue) {
            if (field === 'basic_salary') {
                $cell.html(
                    `<strong class="text-primary">${formatNumber(newValue)}</strong> <span class="badge bg-warning text-dark ms-1">Edited</span>`
                );
            } else if (field === 'allowances') {
                $cell.html(
                    `<strong class="text-success">${formatNumber(newValue)}</strong> <span class="badge bg-warning text-dark ms-1">Edited</span>`
                );
            } else if (field === 'contract_type') {
                const isProvision = newValue === 'provision';
                $cell.html(
                    `<span class="badge ${isProvision ? 'bg-warning text-dark' : 'bg-success'}"><i class="fas ${isProvision ? 'fa-clock' : 'fa-check-circle'} me-1"></i>${isProvision ? 'Provision' : 'New'}</span> <span class="badge bg-warning text-dark ms-1">Edited</span>`
                );
            } else {
                $cell.html(`${escapeHtml(newValue)} <span class="badge bg-warning text-dark ms-1">Edited</span>`);
            }
            $cell.data('current-value', newValue);
        }

        async function saveEdit($cell, field, employeeId, newValue, oldValue, originalHtml, staffId) {
            $cell.html('<span class="spinner-border spinner-border-sm text-primary"></span>');
            try {
                const $row = $cell.closest('tr');
                if (field === 'basic_salary') editedBasicSalaries.set(employeeId, newValue);
                else if (field === 'allowances') editedAllowances.set(employeeId, newValue);
                updateGrossPayForEmployee($row, employeeId);
                const employeesData = [{
                    payroll_employee_id: parseInt(employeeId),
                    [field]: field === 'basic_salary' || field === 'allowances' ? parseFloat(newValue) :
                        newValue
                }];
                if (field === 'basic_salary') employeesData[0].basic_salary = parseFloat(newValue);
                else if (field === 'allowances') employeesData[0].allowances = parseFloat(newValue);
                else if (field === 'contract_type') employeesData[0].contract_type = newValue;
                else if (field === 'department') employeesData[0].department = newValue;
                else if (field === 'bank_name') employeesData[0].bank_name = newValue;
                else if (field === 'bank_account_name') employeesData[0].bank_account_name = newValue;
                else if (field === 'bank_account_number') employeesData[0].bank_account_number = newValue;
                const response = await fetch(`{{ url('/payroll') }}/${batchHash}/update-employees`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        employees: employeesData
                    })
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();
                if (data.success) {
                    updateCellDisplay($cell, field, newValue);
                    updateGrossPayForEmployee($row, employeeId);
                    $cell.removeClass('editing');
                    showToast(`${field.replace('_', ' ').toUpperCase()} updated!`, 'success');
                } else {
                    showToast(data.message || 'Update failed', 'error');
                    $cell.html(originalHtml);
                    updateGrossPayForEmployee($row, employeeId);
                }
            } catch (error) {
                console.error('Save error:', error);
                showToast('Error: ' + error.message, 'error');
                $cell.html(originalHtml);
                updateGrossPayForEmployee($cell.closest('tr'), employeeId);
            }
            currentEditingCell = null;
        }

        function attachAllClickHandlers() {
            $('.editable-cell').each(function() {
                const $cell = $(this);
                const field = $cell.data('field');
                const employeeId = $cell.data('employee-id');
                let currentValue = $cell.data('current-value');
                const staffId = $cell.data('staff-id');
                if (!field || !employeeId) return;
                if (currentValue === undefined || currentValue === null) {
                    const rawText = $cell.text().trim();
                    if (field === 'basic_salary' || field === 'allowances') currentValue = parseNumber(rawText);
                    else currentValue = rawText.replace('Edited', '').replace('—', '').trim();
                    $cell.data('current-value', currentValue);
                }
                $cell.off('click').on('click', function(e) {
                    e.stopPropagation();
                    if ($(this).hasClass('editing')) return;
                    makeEditable(this, field, employeeId, currentValue, staffId);
                });
            });
        }

        // ==================== LOCK FUNCTION ====================
        function lockPayroll(hash) {
            Swal.fire({
                title: '🔒 PERMANENTLY LOCK BATCH?',
                html: `<div style="text-align: left;">
                <p><i class="fas fa-exclamation-triangle text-danger me-2"></i> <strong class="text-danger">WARNING: This action is PERMANENT!</strong></p>
                <p>Once locked:</p>
                <ul><li>❌ You will NOT be able to edit any employee details</li>
                <li>❌ The "Enable Editing" button will disappear FOREVER</li>
                <li>❌ No further changes can be made to this payroll</li>
                <li>✅ Salary slips can still be generated and downloaded</li>
                <li>✅ Payroll reports can still be accessed</li></ul>
                <div class="mt-3"><label class="form-label fw-bold">Reason for locking:</label>
                <textarea id="lockReason" class="form-control" rows="2" placeholder="e.g., Payroll verified and closed..."></textarea></div>
            </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Lock Permanently',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const reason = document.getElementById('lockReason')?.value;
                    if (!reason) {
                        Swal.showValidationMessage('Please provide a reason');
                        return false;
                    }
                    return {
                        reason: reason
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoadingAlert('Locking payroll batch...');
                    fetch(`{{ url('/payroll') }}/${hash}/lock`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            reason: result.value.reason
                        })
                    }).then(response => response.json()).then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '✅ Batch Locked!',
                                html: 'Payroll batch has been permanently locked.',
                                confirmButtonText: 'OK'
                            }).then(() => location.reload());
                        } else {
                            showErrorAlert(data.message || 'Failed to lock payroll');
                        }
                    }).catch(error => {
                        Swal.close();
                        showErrorAlert('Connection error: ' + error.message);
                    });
                }
            });
        }

        // ==================== EXISTING FUNCTIONS ====================
        function showLoadingAlert(message) {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }

        function showConfirmAlert(title, text, confirmText, callback) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) callback();
            });
        }

        function calculatePayroll(hash) {
            showConfirmAlert('Calculate Payroll?', 'This will compute PAYE, NSSF, and net salaries.', 'Yes, Calculate!',
                () => {
                    showLoadingAlert('Calculating payroll...');
                    fetch(`{{ url('/payroll') }}/${hash}/calculate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json()).then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Payroll calculated!',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                showErrorAlert(data.message || 'Calculation failed');
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Connection error: ' + error.message);
                        });
                });
        }

        function recalculatePayroll(hash) {
            showConfirmAlert('Recalculate Payroll?', '⚠️ This will recalculate all employees.', 'Yes, Recalculate!', () => {
                showLoadingAlert('Recalculating...');
                fetch(`{{ url('/payroll') }}/${hash}/recalculate`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json()).then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Payroll recalculated!',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            showErrorAlert(data.message || 'Recalculation failed');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        showErrorAlert('Connection error: ' + error.message);
                    });
            });
        }

        function finalizePayroll(hash) {
            showConfirmAlert('Finalize Payroll?', '⚠️ This action cannot be undone.', 'Yes, Finalize!', () => {
                showLoadingAlert('Finalizing payroll...');
                fetch(`{{ url('/payroll') }}/${hash}/finalize`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json()).then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Finalized!',
                                text: 'Payroll finalized!',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            showErrorAlert(data.message || 'Finalization failed');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        showErrorAlert('Connection error: ' + error.message);
                    });
            });
        }

        function revertFinalizePayroll(hash) {
            if (isActuallyLocked) {
                showToast('Cannot revert - this payroll is locked.', 'error');
                return;
            }
            Swal.fire({
                title: 'Enable Editing Mode?',
                html: `<div><p><i class="fas fa-exclamation-triangle text-warning"></i> <strong>Warning:</strong> This will revert the payroll to draft mode.</p><p>Once reverted, you can edit and must recalculate.</p><div class="mt-3"><label class="fw-bold">Reason:</label><textarea id="editReason" class="form-control" rows="2"></textarea></div></div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f0ad4e',
                confirmButtonText: 'Yes, Enable Editing',
                preConfirm: () => ({
                    reason: document.getElementById('editReason')?.value || 'Edited after finalization'
                })
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoadingAlert('Reverting payroll to draft...');
                    fetch(`{{ url('/payroll') }}/${hash}/revert-finalize`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                reason: result.value.reason
                            })
                        })
                        .then(response => response.json()).then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Editing Enabled!',
                                    text: 'Payroll reverted to draft.',
                                    confirmButtonText: 'OK'
                                }).then(() => location.reload());
                            } else {
                                showErrorAlert(data.message || 'Failed to revert');
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Connection error: ' + error.message);
                        });
                }
            });
        }

        function deletePayroll(hash) {
            showConfirmAlert('Delete Payroll?', '⚠️ WARNING: This cannot be undone.', 'Yes, Delete!', () => {
                showLoadingAlert('Deleting payroll...');
                fetch(`{{ url('/payroll') }}/${hash}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json()).then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Payroll deleted.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => window.location.href = '{{ route('payroll.index') }}');
                        } else {
                            showErrorAlert(data.message || 'Delete failed');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        showErrorAlert('Connection error: ' + error.message);
                    });
            });
        }

        function generateSlips(hash) {
            showConfirmAlert('Generate Salary Slips?', 'This will generate PDF salary slips.', 'Yes, Generate!', () => {
                showLoadingAlert('Generating salary slips...');
                fetch(`{{ url('/payroll') }}/${hash}/generate-slips`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json()).then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Salary slips generated!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            showErrorAlert(data.message || 'Generation failed');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        showErrorAlert('Error: ' + error.message);
                    });
            });
        }

        // Initialize
        $(document).ready(function() {
            console.log('🔍 Final Debug Info:', {
                batchStatus: batchStatus,
                isBackendLocked: isBackendLocked,
                isActuallyLocked: isActuallyLocked,
                rawValue: rawLockedValue,
                canEdit: canEdit
            });

            setTimeout(() => initializeAllGrossPay(), 200);

            if (canEdit) {
                attachAllClickHandlers();
                console.log('✅ Edit mode enabled - inline editing active');
            } else if (isActuallyLocked) {
                console.log('🔒 Batch is locked - editing disabled');
                // Disable all editable cells visually
                $('.editable-cell').css({
                    'cursor': 'not-allowed',
                    'background-color': '#f5f5f5',
                    'pointer-events': 'none'
                });
            } else {
                console.log('📋 View mode - no editing allowed');
                // Disable all editable cells visually
                $('.editable-cell').css({
                    'cursor': 'not-allowed',
                    'background-color': '#f5f5f5',
                    'pointer-events': 'none'
                });
            }
        });
    </script>
@endsection
