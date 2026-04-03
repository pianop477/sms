{{-- resources/views/payroll/show.blade.php --}}

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

        /* Scrollable Table Styles */
        .table-scrollable {
            max-height: 400px;
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

        .employee-table tbody tr:nth-child(even) {
            background-color: #f8f9fc;
        }

        .employee-table tbody tr:hover {
            background-color: #eef2ff;
        }

        @media (min-width: 1200px) {
            .table-scrollable {
                max-height: 400px;
            }
        }

        @media (min-width: 768px) and (max-width: 1199px) {
            .table-scrollable {
                max-height: 400px;
            }
        }

        @media (max-width: 767px) {
            .table-scrollable {
                max-height: 300px;
            }
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="fas fa-calculator mr-2 text-primary"></i> Payroll Details
                        </h4>
                        <p class="text-muted mb-0">View and manage payroll batch</p>
                    </div>
                    <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                </div>

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
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i
                                        class="fas {{ $batch['status'] == 'finalized' ? 'fa-check-circle' : ($batch['status'] == 'calculated' ? 'fa-calculator' : 'fa-pen') }} mr-1"></i>
                                    {{ ucfirst($batch['status']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="detail-label">Generated By</div>
                            <div class="detail-value">{{ ucwords(strtolower($batch['generated_by'] ?? 'System')) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Generated At</div>
                            <div class="detail-value">
                                {{ \Carbon\Carbon::parse($batch['generated_at'])->format('d/m/Y H:i:s') }}</div>
                        </div>
                    </div>
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
                            onclick="calculatePayroll('{{ $batch['hash'] }}')">
                            <i class="fas fa-calculator mr-1"></i> Calculate Payroll
                        </button>
                        <button type="button" class="btn btn-action btn-danger"
                            onclick="deletePayroll('{{ $batch['hash'] }}')">
                            <i class="fas fa-trash mr-1"></i> Delete Payroll
                        </button>
                    @endif

                    @if ($batch['status'] == 'calculated')
                        <button type="button" class="btn btn-action btn-warning"
                            onclick="recalculatePayroll('{{ $batch['hash'] }}')">
                            <i class="fas fa-sync-alt mr-1"></i> Recalculate Payroll
                        </button>
                        <button type="button" class="btn btn-action btn-finalize"
                            onclick="finalizePayroll('{{ $batch['hash'] }}')">
                            <i class="fas fa-check-circle mr-1"></i> Finalize Payroll
                        </button>
                        <button type="button" class="btn btn-action btn-danger"
                            onclick="deletePayroll('{{ $batch['hash'] }}')">
                            <i class="fas fa-trash mr-1"></i> Delete Payroll
                        </button>
                    @endif

                    @if ($batch['status'] == 'finalized')
                        <button type="button" class="btn btn-action btn-generate-slips"
                            onclick="generateSlips('{{ $batch['hash'] }}')">
                            <i class="fas fa-file-pdf mr-1"></i> Generate Slips
                        </button>
                        <a href="{{ route('payroll.download-slips', $batch['hash']) }}"
                            class="btn btn-action btn-download">
                            <i class="fas fa-download mr-1"></i> Download Slips
                        </a>
                        <a href="{{ route('payroll.download-summary', $batch['hash']) }}"
                            class="btn btn-action btn-export">
                            <i class="fas fa-file-excel mr-1"></i> Download Payroll
                        </a>
                    @endif
                </div>

                {{-- Employees Table --}}
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-users mr-2"></i> Employee Lists</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-scrollable">
                            <table class="table employee-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Staff ID</th>
                                        <th>Employee Name</th>
                                        <th>Staff Type</th>
                                        <th>Contract</th>
                                        <th class="text-end">Basic Salary</th>
                                        <th class="text-end">Allowances</th>
                                        <th class="text-end">Gross Pay</th>
                                        <th class="text-end">NSSF</th>
                                        <th class="text-end">PAYE</th>
                                        <th class="text-end">HESLB</th>
                                        <th class="text-end">Loan</th>
                                        <th class="text-end">Net Pay</th>
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
                                            $hasUnofficial = false;

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
                                                    $hasUnofficial = $totalUnofficial > 0;
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

                                            // Get contract type
                                            $contractType =
                                                $employee['contract_type'] ??
                                                ($employee['is_provision_period'] ? 'provision' : 'new');
                                            $isProvision =
                                                $contractType === 'provision' ||
                                                ($employee['is_provision_period'] ?? false);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ strtoupper($employee['staff_id']) }}</strong></td>
                                            <td>{{ ucwords(strtolower($employee['employee_full_name'])) }}</td>
                                            <td>{{ ucfirst($employee['staff_type']) }}</td>
                                            <td class="text-center">
                                                @if ($isProvision)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock me-1"></i> Provision
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i> New
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($employee['basic_salary'], 0) }}</td>
                                            <td class="text-end">{{ number_format($employee['allowances'], 0) }}</td>
                                            <td class="fw-bold text-end">{{ number_format($grossSalary, 0) }}</td>
                                            <td class="text-end">{{ number_format($nssf, 0) }}</td>
                                            <td class="text-end">{{ number_format($paye, 0) }}</td>
                                            <td class="text-end">
                                                @if ($heslbAmount > 0)
                                                    <span
                                                        class="text-danger fw-bold">{{ number_format($heslbAmount, 0) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($hasUnofficial)
                                                    <span
                                                        class="text-danger fw-bold">{{ number_format($totalUnofficial, 0) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-success fw-bold text-end">
                                                {{ number_format($amountToPay, 0) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle mr-1"></i> No employees found in this payroll
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

    <script>
        function showLoadingAlert(message) {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function showSuccessAlert(message, reload = true) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            }).then(() => {
                if (reload) location.reload();
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
                if (result.isConfirmed) {
                    callback();
                }
            });
        }

        function calculatePayroll(hash) {
            showConfirmAlert(
                'Calculate Payroll?',
                'This will compute PAYE, NSSF, and net salaries for all employees.',
                'Yes, Calculate!',
                () => {
                    const btn = event.currentTarget;
                    const originalHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>';

                    showLoadingAlert('Calculating payroll...');

                    fetch('{{ url('/payroll') }}/' + hash + '/calculate', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                showSuccessAlert('Payroll calculated successfully!');
                            } else {
                                showErrorAlert(data.message || 'Calculation failed');
                                btn.innerHTML = originalHtml;
                                btn.disabled = false;
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Connection error: ' + error.message);
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        });
                }
            );
        }

        function recalculatePayroll(hash) {
            showConfirmAlert(
                'Recalculate Payroll?',
                '⚠️ This will recalculate all employees. This action can be done multiple times before finalization.',
                'Yes, Recalculate!',
                () => {
                    const btn = event.currentTarget;
                    const originalHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>';

                    showLoadingAlert('Recalculating...');

                    fetch('{{ url('/payroll') }}/' + hash + '/recalculate', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                const result = data.data;
                                let message = `Payroll recalculated successfully!\n\n`;
                                message += `✅ Total Employees: ${result.total_employees}\n`;
                                message += `✅ Calculated: ${result.calculated}\n`;

                                if (result.failed > 0) {
                                    message += `\n⚠️ Failed: ${result.failed}\n`;
                                    message +=
                                        `Errors: ${result.errors.map(e => `${e.staff_id}: ${e.error}`).join(', ')}`;
                                }

                                Swal.fire({
                                    icon: result.failed > 0 ? 'warning' : 'success',
                                    title: result.failed > 0 ? 'Partial Success!' : 'Success!',
                                    html: message.replace(/\n/g, '<br>'),
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                showErrorAlert(data.message || 'Recalculation failed');
                                btn.innerHTML = originalHtml;
                                btn.disabled = false;
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Connection error: ' + error.message);
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        });
                }
            );
        }

        function finalizePayroll(hash) {
            showConfirmAlert(
                'Finalize Payroll?',
                '⚠️ This action cannot be undone. Once finalized, no further changes can be made.',
                'Yes, Finalize!',
                () => {
                    const btn = event.currentTarget;
                    const originalHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>';

                    showLoadingAlert('Finalizing payroll...');

                    fetch('{{ url('/payroll') }}/' + hash + '/finalize', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                showSuccessAlert('Payroll finalized successfully!');
                            } else {
                                showErrorAlert(data.message || 'Finalization failed');
                                btn.innerHTML = originalHtml;
                                btn.disabled = false;
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Connection error: ' + error.message);
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        });
                }
            );
        }

        function deletePayroll(hash) {
            showConfirmAlert(
                'Delete Payroll?',
                '⚠️ WARNING: This action cannot be undone. All payroll data will be permanently deleted.',
                'Yes, Delete!',
                () => {
                    const btn = event.currentTarget;
                    const originalHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>';

                    showLoadingAlert('Deleting payroll...');

                    fetch('{{ url('/payroll') }}/' + hash, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Payroll has been deleted successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '{{ route('payroll.index') }}';
                                });
                            } else {
                                showErrorAlert(data.message || 'Delete failed');
                                btn.innerHTML = originalHtml;
                                btn.disabled = false;
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Connection error: ' + error.message);
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        });
                }
            );
        }

        function generateSlips(hash) {
            showConfirmAlert(
                'Generate Salary Slips?',
                'This will generate PDF salary slips for all employees in this payroll.',
                'Yes, Generate!',
                () => {
                    const btn = event.currentTarget;
                    const originalHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>';

                    showLoadingAlert('Generating salary slips...');

                    fetch('{{ url('/payroll') }}/' + hash + '/generate-slips', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
                                });
                            }
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                return response.text().then(text => {
                                    throw new Error('Server returned HTML instead of JSON');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    html: 'Salary slips generated successfully!<br><small>You can now download the combined PDF.</small>',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                showErrorAlert(data.message || 'Generation failed');
                                btn.innerHTML = originalHtml;
                                btn.disabled = false;
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Error: ' + error.message);
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        });
                }
            );
        }

        function downloadSlips(hash) {
            Swal.fire({
                title: 'Downloading...',
                text: 'Preparing your salary slips PDF.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = '{{ url('/payroll') }}/' + hash + '/download-slips';
                    setTimeout(() => {
                        Swal.close();
                    }, 1500);
                }
            });
        }

        function downloadSummary(hash) {
            Swal.fire({
                title: 'Downloading...',
                text: 'Preparing payroll Excel file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = '{{ url('/payroll') }}/' + hash + '/download-summary';
                    setTimeout(() => {
                        Swal.close();
                    }, 1500);
                }
            });
        }
    </script>
@endsection
