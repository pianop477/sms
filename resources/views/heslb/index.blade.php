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

        /* Make sure tables are properly displayed before DataTable initialization */
        .dataTable {
            width: 100% !important;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0">
                                <i class="fas fa-graduation-cap me-2"></i> HESLB Loan Deductions
                            </h5>
                            <div class="d-flex gap-2 mt-2 mt-sm-0">
                                {{-- Year Filter Dropdown --}}
                                <div class="d-flex align-items-center">
                                    <label class="me-2 text-white mb-0" style="font-size: 14px;">
                                        <i class="fas fa-calendar-alt me-1"></i>Year:
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
                                    data-bs-target="#addHeslbModal">
                                    <i class="fas fa-plus me-1"></i> Add Beneficiary
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- Active Deductions Table --}}
                        <h6 class="mb-3">Active Deductions</h6>
                        <div class="table-responsive">
                            <table class="table table-heslb table-bordered" id="activeTable" width="100%">
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
                                            <td class="text-center">
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
                                <table class="table table-heslb table-bordered" id="inactiveTable" width="100%">
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
                                                <td class="text-end">{{ number_format($deduction['monthly_amount'], 0) }}
                                                </td>
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

    {{-- Scripts kwa order sahihi --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ==================== GLOBAL VARIABLES ====================
        let activeDataTable = null;
        let inactiveDataTable = null;

        // ==================== HELPER FUNCTIONS ====================

        function destroyDataTables() {
            if ($.fn.DataTable.isDataTable('#activeTable')) {
                $('#activeTable').DataTable().destroy();
            }
            if ($.fn.DataTable.isDataTable('#inactiveTable')) {
                $('#inactiveTable').DataTable().destroy();
            }
        }

        function initializeDataTables() {
            destroyDataTables();

            // Initialize Active Table
            if ($('#activeTable tbody tr').length > 0) {
                activeDataTable = $('#activeTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "All"]
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
                    autoWidth: false,
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: [9]
                        } // Actions column
                    ]
                });
            }

            // Initialize Inactive Table
            if ($('#inactiveTable').length && $('#inactiveTable tbody tr').length > 0) {
                inactiveDataTable = $('#inactiveTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "All"]
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
                    autoWidth: false,
                    order: [
                        [0, 'asc']
                    ]
                });
            }
        }

        // ==================== YEAR FILTER FUNCTION ====================
        function filterByYear(year) {
            if (!year) return;

            Swal.fire({
                title: 'Loading...',
                text: 'Fetching data for year ' + year,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('heslb.filter') }}?year=' + year,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        // Update Active Table
                        if (data.active && data.active.length > 0) {
                            let activeHtml = '';
                            data.active.forEach((deduction, index) => {
                                const startDate = deduction.start_date ? new Date(deduction.start_date)
                                    .toLocaleDateString('en-GB') : 'N/A';
                                const endDate = deduction.end_date ? new Date(deduction.end_date)
                                    .toLocaleDateString('en-GB') : 'Ongoing';

                                activeHtml += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td><strong>${deduction.staff_id.toUpperCase()}</strong></td>
                                <td>${deduction.employee_name.toLowerCase().replace(/\b\w/g, l => l.toUpperCase())}</td>
                                <td>${deduction.staff_type}</td>
                                <td>${deduction.loan_number || 'N/A'}</td>
                                <td class="text-end">${parseInt(deduction.monthly_amount).toLocaleString()}</td>
                                <td>${startDate}</td>
                                <td>${endDate}</td>
                                <td class="text-center"><span class="status-active">Active</span></td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn btn-xs btn-warning" onclick="updateAmount(${deduction.id}, ${deduction.monthly_amount})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger" onclick="stopDeduction(${deduction.id})">
                                            <i class="fas fa-stop"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                            });
                            $('#activeTable tbody').html(activeHtml);
                        } else {
                            $('#activeTable tbody').html(`
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-1"></i> No active HESLB deductions for this year
                                </td>
                            </tr>
                        `);
                        }

                        // Update Inactive Table
                        if (data.inactive && data.inactive.length > 0) {
                            let inactiveHtml = '';
                            data.inactive.forEach((deduction, index) => {
                                const startDate = deduction.start_date ? new Date(deduction.start_date)
                                    .toLocaleDateString('en-GB') : 'N/A';
                                const endDate = deduction.end_date ? new Date(deduction.end_date)
                                    .toLocaleDateString('en-GB') : 'N/A';

                                inactiveHtml += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td><strong>${deduction.staff_id.toUpperCase()}</strong></td>
                                <td>${deduction.employee_name.toLowerCase().replace(/\b\w/g, l => l.toUpperCase())}</td>
                                <td>${deduction.staff_type}</td>
                                <td>${deduction.loan_number || 'N/A'}</td>
                                <td class="text-end">${parseInt(deduction.monthly_amount).toLocaleString()}</td>
                                <td>${startDate}</td>
                                <td>${endDate}</td>
                                <td class="text-center"><span class="status-stopped">Stopped</span></td>
                            </tr>`;
                            });
                            $('#inactiveTable tbody').html(inactiveHtml);
                            $('#inactiveTable').parent().parent().show(); // Show the inactive section
                        } else {
                            $('#inactiveTable tbody').html(`
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-1"></i> No inactive HESLB deductions for this year
                                </td>
                            </tr>
                        `);
                            $('#inactiveTable').parent().parent().hide(); // Hide section if no data
                        }

                        // Reinitialize DataTables
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

        // ==================== HESLB FUNCTIONS ====================

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
                    submitForm('/heslb/' + id + '/stop');
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
                    min: 1000,
                    step: 1000
                },
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value || value < 1000) {
                        return 'Amount must be at least 1,000 TZS';
                    }
                    return null;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    submitForm('/heslb/' + id + '/update-amount', {
                        monthly_amount: result.value
                    });
                }
            });
        }

        function submitForm(url, data = {}) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url('') }}' + url;
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';

            for (const [key, value] of Object.entries(data)) {
                form.innerHTML += `<input type="hidden" name="${key}" value="${value}">`;
            }

            document.body.appendChild(form);
            form.submit();
        }

        // ==================== DOCUMENT READY ====================
        $(document).ready(function() {
            console.log('HESLB page loaded - Initializing...');

            // Initial delay to ensure DOM is ready
            setTimeout(function() {
                initializeDataTables();
            }, 100);

            // Year filter change event
            $('#yearFilter').on('change', function() {
                const year = $(this).val();
                filterByYear(year);
            });
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

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '{!! implode('<br>', $errors->all()) !!}',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endsection
