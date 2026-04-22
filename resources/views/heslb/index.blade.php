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
                                    <tr>
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
                                    </tr>
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
                                        <tr>
                                            <th>#</th>
                                            <th>Staff ID</th>
                                            <th>Employee Name</th>
                                            <th>Staff Type</th>
                                            <th>Loan Number</th>
                                            <th class="text-end">Monthly Amount</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                        </tr>
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

    {{-- Scripts zote kwa order sahihi --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            console.log('HESLB page loaded - jQuery ready');

            // Helper function to initialize DataTables
            function initializeDataTable(tableId, options = {}) {
                const table = $(tableId);
                if (!table.length) {
                    console.log(`Table ${tableId} not found`);
                    return false;
                }

                // Check if table has any rows
                const hasRows = table.find('tbody tr').length > 0;
                if (!hasRows) {
                    console.log(`Table ${tableId} has no data, skipping DataTable initialization`);
                    return false;
                }

                // Check if already initialized
                if ($.fn.DataTable.isDataTable(tableId)) {
                    console.log(`Table ${tableId} already initialized, destroying first`);
                    $(tableId).DataTable().destroy();
                }

                // Default options
                const defaultOptions = {
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
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
                };

                // Merge options
                const finalOptions = { ...defaultOptions, ...options };

                try {
                    $(tableId).DataTable(finalOptions);
                    console.log(`Table ${tableId} initialized successfully`);
                    return true;
                } catch (error) {
                    console.error(`Error initializing ${tableId}:`, error);
                    return false;
                }
            }

            // Initialize Active Deductions Table
            initializeDataTable('#activeTable', {
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [9] } // Actions column (index 9)
                ]
            });

            // Initialize Inactive Deductions Table (if it exists and has data)
            if ($('#inactiveTable').length && $('#inactiveTable tbody tr').length > 0) {
                initializeDataTable('#inactiveTable', {
                    order: [[0, 'asc']]
                });
            } else {
                console.log('Inactive table has no data or does not exist');
            }
        });

        // ==================== HESLB FUNCTIONS ====================

        function stopDeduction(id) {
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert not loaded');
                if (confirm('Are you sure you want to stop this HESLB deduction?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url('/heslb') }}/' + id + '/stop';
                    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                    document.body.appendChild(form);
                    form.submit();
                }
                return;
            }

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
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert not loaded');
                const newAmount = prompt('Enter new monthly amount (TZS):', currentAmount);
                if (newAmount && !isNaN(newAmount)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url('/heslb') }}/' + id + '/update-amount';
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="monthly_amount" value="${newAmount}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
                return;
            }

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

        // ==================== SWEETALERT NOTIFICATIONS ====================
        @if (session('success'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        @endif

        @if (session('error'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK'
                });
            }
        @endif

        @if ($errors->any())
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: '{!! implode('<br>', $errors->all()) !!}',
                    confirmButtonText: 'OK'
                });
            }
        @endif
    </script>
@endsection
