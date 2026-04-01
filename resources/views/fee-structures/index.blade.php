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

        .table-fee th {
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

        .installment-badge {
            background: #e0e7ff;
            color: #3730a3;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
            margin: 2px;
        }

        .class-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
        }

        .general-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
        }

        .hostel-badge {
            background: #fed7aa;
            color: #9b2c1d;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
        }

        .transport-badge {
            background: #bfdbfe;
            color: #1e40af;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
        }

        .non-transport-badge {
            background: #e2e3e5;
            color: #383d41;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
        }

        /* Switch Toggle Styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
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

        .filter-section {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .class-type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 5px;
        }

        .class-type-hostel {
            background: #fed7aa;
            color: #9b2c1d;
        }

        .class-type-regular {
            background: #d1fae5;
            color: #065f46;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-coins me-2"></i> Fee Structures Management
                            </h5>
                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                                data-bs-target="#addFeeStructureModal">
                                <i class="fas fa-plus me-1"></i> New Fee Structure
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Filter Section --}}
                        <div class="filter-section">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Filter by Class</label>
                                    <select id="classFilter" class="form-select">
                                        <option value="all">All Structures</option>
                                        <option value="general">General Structures (All Classes)</option>
                                        <option value="specific">Class Specific Structures</option>
                                        <option disabled>──────────</option>
                                        @foreach($classes as $class)
                                            <option value="class_{{ $class->id }}">{{ $class->class_name ?? $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Filter by Type</label>
                                    <select id="typeFilter" class="form-select">
                                        <option value="all">All Types</option>
                                        <option value="transport">With Transport</option>
                                        <option value="non-transport">Without Transport</option>
                                        <option value="hostel">Hostel Class</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">&nbsp;</label>
                                    <button id="resetFilters" class="btn btn-secondary form-control">
                                        <i class="fas fa-undo-alt me-1"></i> Reset Filters
                                    </button>
                                </div>
                                <div class="col-md-3 text-end">
                                    <span id="filterCount" class="badge bg-info">Showing all structures</span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-fee table-bordered" id="feeStructureTable">
                                <thead>
                                    32
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Type</th>
                                        <th>Total Amount (TZS)</th>
                                        <th>Installments</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </thead>
                                <tbody>
                                    @forelse($structures as $index => $structure)
                                        <tr data-class="{{ $structure->class_id ? 'class_'.$structure->class_id : 'general' }}"
                                            data-type="{{ $structure->is_hostel_class ? 'hostel' : ($structure->transport_applies ? 'transport' : 'non-transport') }}">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ ucfirst($structure->name) }}</strong>
                                                @if($structure->is_hostel_class)
                                                    <span class="class-type-badge class-type-hostel">🏠 Hostel Class</span>
                                                @else
                                                    @if($structure->transport_applies)
                                                        <span class="badge bg-info ms-2">🚌 With Transport</span>
                                                    @else
                                                        <span class="badge bg-secondary ms-2">🚶 Without Transport</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if($structure->class_id)
                                                    @php
                                                        $class = $classes->firstWhere('id', $structure->class_id);
                                                    @endphp
                                                    <span class="class-badge">
                                                        <i class="fas fa-graduation-cap me-1"></i>
                                                        {{ strtoupper($class->class_code ?? 'N/A') }}
                                                    </span>
                                                @else
                                                    <span class="general-badge">
                                                        <i class="fas fa-globe me-1"></i> All Classes
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($structure->is_hostel_class)
                                                    <span class="hostel-badge">
                                                        <i class="fas fa-hotel me-1"></i> Hostel/Boarding
                                                    </span>
                                                @elseif($structure->transport_applies)
                                                    <span class="transport-badge">
                                                        <i class="fas fa-bus me-1"></i> With Transport
                                                    </span>
                                                @else
                                                    <span class="non-transport-badge">
                                                        <i class="fas fa-walking me-1"></i> Without Transport
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <strong>{{ number_format($structure->total_amount, 0) }} TZS</strong>
                                                @if($structure->class_id)
                                                    <br>
                                                    <small class="text-muted">(Class Specific)</small>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach($structure->installments->sortBy('order') as $inst)
                                                    <span class="installment-badge">
                                                        {{ $inst->name }}: {{ number_format($inst->amount, 0) }}
                                                    </span>
                                                @endforeach
                                                @if($structure->installments->isEmpty())
                                                    <span class="text-muted">No installments</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($structure->created_at)->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('fee-structures.installments', $structure->id) }}"
                                                       class="btn btn-xs btn-info">
                                                        <i class="fas fa-list"></i> Installments
                                                    </a>
                                                    <button class="btn btn-xs btn-warning"
                                                        onclick="editStructure({{ $structure->id }}, '{{ $structure->name }}', {{ $structure->total_amount }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle me-1"></i> No fee structures found
                                                <br>
                                                <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal"
                                                    data-bs-target="#addFeeStructureModal">
                                                    <i class="fas fa-plus"></i> Add fee structure
                                                </button>
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

    {{-- Add Fee Structure Modal --}}
    <div class="modal fade" id="addFeeStructureModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Create New Fee Structure</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('fee-structures.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            Create a fee structure. Choose the class type and whether transport applies.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Structure Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                   placeholder="e.g. Transport Fees">
                            <small class="text-muted">A descriptive name for this fee structure</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Apply to Specific Class <span class="text-muted">(Optional)</span></label>
                            <select name="class_id" id="classIdSelect" class="form-select">
                                <option value="">All Classes (General Structure)</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ strtoupper($class->class_code ?? 'N/A') }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Select a class if this fee structure is specific to a particular class
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Class Type <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="class_type" id="regularClass" value="regular" checked>
                                        <label class="form-check-label" for="regularClass">
                                            <i class="fas fa-school"></i> Regular Class
                                            <small class="text-muted d-block">Transport status matters (With/Without Transport)</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="class_type" id="hostelClass" value="hostel">
                                        <label class="form-check-label" for="hostelClass">
                                            <i class="fas fa-hotel"></i> Hostel/Boarding Class
                                            <small class="text-muted d-block">All students pay same fees (e.g. Std 7)</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="transportSection">
                            <label class="form-label">This structure applies to:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="transport_applies" id="withTransport" value="1" checked>
                                        <label class="form-check-label" for="withTransport">
                                            <i class="fas fa-bus"></i> Students WITH Transport
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="transport_applies" id="withoutTransport" value="0">
                                        <label class="form-check-label" for="withoutTransport">
                                            <i class="fas fa-walking"></i> Students WITHOUT Transport
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Select which group of students this structure applies to</small>
                        </div>

                        <div class="mb-3" id="hostelInfo" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Hostel Class Mode:</strong> All students in this class will pay the same fees regardless of transport status.
                                The transport selection above will be ignored.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Amount (TZS) <span class="text-danger">*</span></label>
                            <input type="number" name="total_amount" class="form-control" required
                                   min="0" step="1000" placeholder="e.g., 450000">
                            <small class="text-muted">Total fees for the entire academic year</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Structure</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Fee Structure Modal --}}
    <div class="modal fade" id="editFeeStructureModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Fee Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editStructureForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Structure Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Amount (TZS) <span class="text-danger">*</span></label>
                            <input type="number" name="total_amount" id="edit_total_amount" class="form-control" required min="0" step="1000">
                        </div>
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i>
                            Note: Class assignment and type (Hostel/Regular) cannot be changed after creation.
                            Create a new structure for different settings.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Structure</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let table = null;

            // Class type toggle logic
            $('input[name="class_type"]').change(function() {
                if ($(this).val() === 'hostel') {
                    $('#transportSection').hide();
                    $('#hostelInfo').show();
                    // Disable transport selection for hostel classes
                    $('input[name="transport_applies"]').prop('disabled', true);
                } else {
                    $('#transportSection').show();
                    $('#hostelInfo').hide();
                    $('input[name="transport_applies"]').prop('disabled', false);
                }
            });

            // Initialize DataTable
            if ($('#feeStructureTable').length && $('#feeStructureTable tbody tr').length > 0) {
                table = $('#feeStructureTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    order: [[0, 'asc']],
                    language: {
                        search: "🔍 Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No entries found",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            previous: "← Previous",
                            next: "Next →"
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [7]
                    }],
                    responsive: true,
                    autoWidth: false
                });
            }

            // Function to apply filters
            function applyFilters() {
                const classFilter = $('#classFilter').val();
                const typeFilter = $('#typeFilter').val();

                let visibleCount = 0;
                let totalCount = 0;

                $('#feeStructureTable tbody tr').each(function() {
                    totalCount++;
                    let show = true;

                    // Class filter
                    if (classFilter !== 'all') {
                        const rowClass = $(this).data('class');
                        if (classFilter === 'general' && rowClass !== 'general') {
                            show = false;
                        } else if (classFilter === 'specific' && rowClass === 'general') {
                            show = false;
                        } else if (classFilter.startsWith('class_') && rowClass !== classFilter) {
                            show = false;
                        }
                    }

                    // Type filter
                    if (show && typeFilter !== 'all') {
                        const rowType = $(this).data('type');
                        if (rowType !== typeFilter) {
                            show = false;
                        }
                    }

                    if (show) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                // Update filter count display
                const filterText = classFilter !== 'all' || typeFilter !== 'all'
                    ? `Showing ${visibleCount} of ${totalCount} structures`
                    : `Showing all ${totalCount} structures`;
                $('#filterCount').text(filterText).removeClass().addClass('badge bg-info');

                if (table) {
                    table.draw();
                }
            }

            // Filter change events
            $('#classFilter, #typeFilter').change(function() {
                applyFilters();
            });

            // Reset filters button
            $('#resetFilters').click(function() {
                $('#classFilter').val('all');
                $('#typeFilter').val('all');
                applyFilters();
            });

            applyFilters();
        });

        function editStructure(id, name, totalAmount) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_total_amount').value = totalAmount;
            document.getElementById('editStructureForm').action = '/fee-structures/' + id;
            $('#editFeeStructureModal').modal('show');
        }

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
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endsection
