{{-- resources/views/heslb/index.blade.php --}}

@extends('SRTDashboard.frame')

@section('content')
    <style>
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }

        .status-stopped {
            background: #fee2e2;
            color: #991b1b;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }

        .table-heslb th {
            background: #f8fafc;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .btn-xs {
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper {
            margin-bottom: 20px;
        }

        .dataTables_wrapper .dataTables_length select {
            min-width: 60px;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            margin-left: 8px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            margin: 0 2px;
            border-radius: 6px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4e73df;
            color: white !important;
            border: none;
        }

        table.dataTable thead th {
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 8px;
        }

        table.dataTable tbody td {
            padding: 10px 8px;
            vertical-align: middle;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-graduation-cap me-2"></i> HESLB Loan Deductions
                            </h5>
                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                                data-bs-target="#addHeslbModal">
                                <i class="fas fa-plus me-1"></i> Add Beneficiary
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- Active Deductions Table --}}
                        <h6 class="mb-3">Active Deductions</h6>
                        <div class="table-responsive">
                            <table class="table table-heslb table-bordered" id="activeTable">
                                <thead>
                                    <th>#</th>
                                    <th>Staff ID</th>
                                    <th>Employee Name</th>
                                    <th>Staff Type</th>
                                    <th>Loan Number</th>
                                    <th class="text-end">Monthly Amount</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    @forelse($deductions['active'] ?? [] as $index => $deduction)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ strtoupper($deduction['staff_id']) }}</strong></td>
                                            <td>{{ ucwords(strtolower($deduction['employee_name'])) }}</td>
                                            <td>{{ ucwords(strtolower($deduction['staff_type'])) }}</td>
                                            <td>{{ strtoupper($deduction['loan_number'] ?? 'N/A') }}</td>
                                            <td class="text-end">{{ number_format($deduction['monthly_amount'], 0) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($deduction['start_date'])->format('d/m/Y') }}</td>
                                            <td>{{ $deduction['end_date'] ? \Carbon\Carbon::parse($deduction['end_date'])->format('d/m/Y') : 'Ongoing' }}
                                            </td>
                                            <td class="text-center"><span class="status-active">Active</span></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-xs btn-warning"
                                                        onclick="updateAmount({{ $deduction['id'] }}, {{ $deduction['monthly_amount'] }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-xs btn-danger"
                                                        onclick="stopDeduction({{ $deduction['id'] }})">
                                                        <i class="fas fa-stop"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle me-1"></i> No active HESLB deductions
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Inactive Deductions Table --}}
                        @if (count($deductions['inactive'] ?? []) > 0)
                            <h6 class="mb-3 mt-4">Inactive/Stopped Deductions</h6>
                            <div class="table-responsive">
                                <table class="table table-heslb table-bordered" id="inactiveTable">
                                    <thead>
                                        32
                                        <th>#</th>
                                        <th>Staff ID</th>
                                        <th>Employee Name</th>
                                        <th>Staff Type</th>
                                        <th>Loan Number</th>
                                        <th class="text-end">Monthly Amount</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($deductions['inactive'] ?? [] as $index => $deduction)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td><strong>{{ strtoupper($deduction['staff_id']) }}</strong></td>
                                                <td>{{ ucwords(strtolower($deduction['employee_name'])) }}</td>
                                                <td>{{ ucwords(strtolower($deduction['staff_type'])) }}</td>
                                                <td>{{ $deduction['loan_number'] ?? 'N/A' }}</td>
                                                <td class="text-end">
                                                    {{ number_format($deduction['monthly_amount'], 0) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($deduction['start_date'])->format('d/m/Y') }}
                                                </td>
                                                <td>{{ $deduction['end_date'] ? \Carbon\Carbon::parse($deduction['end_date'])->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td class="text-center"><span class="status-stopped">Stopped</span></td>
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

    {{-- Add HESLB Modal --}}
    <div class="modal fade" id="addHeslbModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add HESLB Beneficiary Deduction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('heslb.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            Enter the Staff ID and monthly deduction amount.
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Staff ID <span class="text-danger">*</span></label>
                                <input type="text" name="staff_id" class="form-control" required placeholder="TCH-001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Staff Type <span class="text-danger">*</span></label>
                                <select name="staff_type" class="form-select" required>
                                    <option value="Teacher">Teacher</option>
                                    <option value="Transport Staff">Transport Staff</option>
                                    <option value="Other Staff">Other Staff</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loan Number</label>
                                <input type="text" name="loan_number" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Monthly Amount (TZS) <span class="text-danger">*</span></label>
                                <input type="number" name="monthly_amount" class="form-control" required min="0"
                                    step="1000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control">
                                <small class="text-muted">Leave empty for ongoing</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Beneficiary</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable for active deductions
            if ($('#activeTable').length && $('#activeTable tbody tr').length > 0) {
                $('#activeTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "All"]
                    ],
                    order: [
                        [0, 'asc']
                    ],
                    language: {
                        search: "🔍 Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No entries found",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            previous: "← Previous",
                            next: "Next →"
                        },
                        zeroRecords: "No matching records found"
                    },
                    columnDefs: [{
                            orderable: false,
                            targets: [9]
                        } // Disable sorting on Actions column (index 9)
                    ],
                    responsive: true,
                    autoWidth: false
                });
            }

            // Initialize DataTable for inactive deductions
            if ($('#inactiveTable').length && $('#inactiveTable tbody tr').length > 0) {
                $('#inactiveTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "All"]
                    ],
                    order: [
                        [0, 'asc']
                    ],
                    language: {
                        search: "🔍 Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No entries found",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            previous: "← Previous",
                            next: "Next →"
                        },
                        zeroRecords: "No matching records found"
                    },
                    responsive: true,
                    autoWidth: false
                });
            }
        });

        function stopDeduction(id) {
            Swal.fire({
                title: 'Stop Deduction?',
                text: 'This will stop the HESLB deduction from future payrolls.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, Stop',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url('/heslb') }}/' + id + '/stop';
                    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function updateAmount(id, currentAmount) {
            Swal.fire({
                title: 'Update Monthly Amount',
                input: 'number',
                inputLabel: 'New Monthly Amount (TZS)',
                inputValue: currentAmount,
                inputAttributes: {
                    min: 0,
                    step: 1000
                },
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url('/heslb') }}/' + id + '/update-amount';
                    form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="monthly_amount" value="${result.value}">
                `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Display success/error messages with SweetAlert
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
