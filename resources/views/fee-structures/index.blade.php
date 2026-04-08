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

        .filter-section {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
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

        .filter-active {
            background: #e0e7ff;
            border-left: 4px solid #4e73df;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            color: #6c757d;
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
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-chalkboard me-1"></i> Filter by Class
                                    </label>
                                    <select id="classFilter" class="form-select">
                                        <option value="all">All Structures</option>
                                        <option value="general">General Structures (All Classes)</option>
                                        <option value="specific">Class Specific Structures</option>
                                        <option disabled>──────────</option>
                                        @foreach($classes as $class)
                                            <option value="class_{{ $class->id }}">
                                                {{ $class->class_name ?? $class->name }}
                                                ({{ $class->class_code ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-tag me-1"></i> Filter by Type
                                    </label>
                                    <select id="typeFilter" class="form-select">
                                        <option value="all">All Types</option>
                                        <option value="transport">With Transport</option>
                                        <option value="non-transport">Without Transport</option>
                                        <option value="hostel">Hostel/Boarding Class</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-search me-1"></i> Quick Search
                                    </label>
                                    <input type="text" id="quickSearch" class="form-control"
                                           placeholder="Search by name or class...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">&nbsp;</label>
                                    <div class="d-grid gap-2">
                                        <button id="resetFilters" class="btn btn-secondary">
                                            <i class="fas fa-undo-alt me-1"></i> Reset All Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div id="filterSummary" class="small text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="filterCount">Showing all structures</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-fee table-bordered" id="feeStructureTable">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Name</th>
                                        <th width="15%">Class</th>
                                        <th width="15%">Type</th>
                                        <th width="15%">Total Amount (TZS)</th>
                                        <th width="20%">Installments</th>
                                        <th width="10%">Created Date</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($structures as $index => $structure)
                                        @php
                                            $class = $structure->class_id ? $classes->firstWhere('id', $structure->class_id) : null;
                                            $rowClass = $structure->class_id ? 'class_'.$structure->class_id : 'general';
                                            $rowType = $structure->is_hostel_class ? 'hostel' : ($structure->transport_applies ? 'transport' : 'non-transport');
                                        @endphp
                                        <tr data-class="{{ $rowClass }}"
                                            data-type="{{ $rowType }}"
                                            data-name="{{ strtolower($structure->name) }}"
                                            data-class-name="{{ strtolower($class->class_name ?? $class->name ?? 'all classes') }}">
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
                                                    <span class="class-badge">
                                                        <i class="fas fa-graduation-cap me-1"></i>
                                                        {{ strtoupper($class->class_name ?? $class->name ?? 'N/A') }}
                                                        <small>({{ $class->class_code ?? '' }})</small>
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
                                                    <span class="installment-badge" title="{{ $inst->name }}: {{ number_format($inst->amount, 0) }} TZS">
                                                        {{ $inst->name }}
                                                        @if($inst->academic_year)
                                                            <small>({{ $inst->academic_year }})</small>
                                                        @endif
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
                                                       class="btn btn-xs btn-info" title="Manage Installments">
                                                        <i class="fas fa-list"></i> Installments
                                                    </a>
                                                    <button class="btn btn-xs btn-warning" title="Edit Structure"
                                                        onclick="editStructure({{ $structure->id }}, '{{ $structure->name }}', {{ $structure->total_amount }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="no-data-row">
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
                                    <option value="{{ $class->id }}">{{ strtoupper($class->class_name ?? $class->name) }} ({{ $class->class_code ?? '' }})</option>
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
                                            <small class="text-muted d-block">All students pay same fees (e.g., Std 7)</small>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Check if jQuery is loaded
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded!');
                return;
            }

            // Use jQuery in no-conflict mode
            jQuery(document).ready(function($) {
                let table = null;

                // Check if DataTables is loaded
                if ($.fn.DataTable) {
                    // Initialize DataTable only if there are rows with data
                    const hasDataRows = $('#feeStructureTable tbody tr').length > 0 &&
                                       $('#feeStructureTable tbody tr:first').find('td').length > 1 &&
                                       !$('#feeStructureTable tbody tr:first').hasClass('no-data-row');

                    if (hasDataRows) {
                        try {
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
                        } catch(e) {
                            console.log('DataTable initialization error:', e);
                            table = null;
                        }
                    }
                } else {
                    console.log('DataTables not loaded');
                }

                // Custom filtering function for DataTables
                if (table) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        const row = table.row(dataIndex).node();
                        const $row = $(row);

                        const classFilter = $('#classFilter').val();
                        const typeFilter = $('#typeFilter').val();
                        const quickSearch = $('#quickSearch').val().toLowerCase().trim();

                        let show = true;

                        // Class filter
                        if (classFilter !== 'all') {
                            const rowClass = $row.data('class');
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
                            const rowType = $row.data('type');
                            if (rowType !== typeFilter) {
                                show = false;
                            }
                        }

                        // Quick search filter
                        if (show && quickSearch !== '') {
                            const name = ($row.data('name') || '').toLowerCase();
                            const className = ($row.data('class-name') || '').toLowerCase();
                            if (name.indexOf(quickSearch) === -1 && className.indexOf(quickSearch) === -1) {
                                show = false;
                            }
                        }

                        return show;
                    });
                }

                // Function to update filter count display
                function updateFilterCount() {
                    if (table) {
                        // Draw the table to apply filters
                        table.draw();

                        // Get filtered records count
                        const filteredCount = table.rows({filter: 'applied'}).count();
                        const totalCount = table.rows().count();

                        const classFilter = $('#classFilter').val();
                        const typeFilter = $('#typeFilter').val();
                        const quickSearch = $('#quickSearch').val();

                        let filterText = '';
                        if (classFilter !== 'all' || typeFilter !== 'all' || quickSearch !== '') {
                            filterText = `Showing ${filteredCount} of ${totalCount} structure${totalCount !== 1 ? 's' : ''}`;

                            // Add filter details
                            const filters = [];
                            if (classFilter !== 'all') {
                                const classText = $('#classFilter option:selected').text();
                                filters.push(`Class: ${classText}`);
                            }
                            if (typeFilter !== 'all') {
                                const typeText = $('#typeFilter option:selected').text();
                                filters.push(`Type: ${typeText}`);
                            }
                            if (quickSearch !== '') {
                                filters.push(`Search: "${quickSearch}"`);
                            }
                            filterText += ` (${filters.join(', ')})`;
                        } else {
                            filterText = `Showing all ${totalCount} structure${totalCount !== 1 ? 's' : ''}`;
                        }

                        $('#filterCount').text(filterText);

                        // Update UI highlight
                        if (classFilter !== 'all' || typeFilter !== 'all' || quickSearch !== '') {
                            $('.filter-section').addClass('filter-active');
                        } else {
                            $('.filter-section').removeClass('filter-active');
                        }
                    } else {
                        // Manual filtering without DataTable
                        applyManualFilters();
                    }
                }

                // Manual filtering function (fallback)
                function applyManualFilters() {
                    const classFilter = $('#classFilter').val();
                    const typeFilter = $('#typeFilter').val();
                    const quickSearch = $('#quickSearch').val().toLowerCase().trim();

                    let visibleCount = 0;
                    let totalCount = 0;

                    $('#feeStructureTable tbody tr').each(function() {
                        const $row = $(this);

                        // Skip no-data row
                        if ($row.hasClass('no-data-row') || ($row.find('td').length === 1 && $row.find('td').attr('colspan'))) {
                            return;
                        }

                        totalCount++;
                        let show = true;

                        // Class filter
                        if (classFilter !== 'all') {
                            const rowClass = $row.data('class');
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
                            const rowType = $row.data('type');
                            if (rowType !== typeFilter) {
                                show = false;
                            }
                        }

                        // Quick search filter
                        if (show && quickSearch !== '') {
                            const name = ($row.data('name') || '').toLowerCase();
                            const className = ($row.data('class-name') || '').toLowerCase();
                            if (name.indexOf(quickSearch) === -1 && className.indexOf(quickSearch) === -1) {
                                show = false;
                            }
                        }

                        if (show) {
                            $row.show();
                            visibleCount++;
                        } else {
                            $row.hide();
                        }
                    });

                    // Show no results message if needed
                    if (visibleCount === 0 && totalCount > 0) {
                        if ($('#feeStructureTable tbody tr.no-results-row').length === 0) {
                            $('#feeStructureTable tbody').append(`
                                <tr class="no-results-row">
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-search me-1"></i> No fee structures match your filters
                                    </td>
                                </tr>
                            `);
                        }
                    } else {
                        $('#feeStructureTable tbody tr.no-results-row').remove();
                    }

                    // Update filter count
                    let filterText = '';
                    if (classFilter !== 'all' || typeFilter !== 'all' || quickSearch !== '') {
                        filterText = `Showing ${visibleCount} of ${totalCount} structure${totalCount !== 1 ? 's' : ''}`;

                        const filters = [];
                        if (classFilter !== 'all') {
                            const classText = $('#classFilter option:selected').text();
                            filters.push(`Class: ${classText}`);
                        }
                        if (typeFilter !== 'all') {
                            const typeText = $('#typeFilter option:selected').text();
                            filters.push(`Type: ${typeText}`);
                        }
                        if (quickSearch !== '') {
                            filters.push(`Search: "${quickSearch}"`);
                        }
                        filterText += ` (${filters.join(', ')})`;
                    } else {
                        filterText = `Showing all ${totalCount} structure${totalCount !== 1 ? 's' : ''}`;
                    }
                    $('#filterCount').text(filterText);

                    // Update UI highlight
                    if (classFilter !== 'all' || typeFilter !== 'all' || quickSearch !== '') {
                        $('.filter-section').addClass('filter-active');
                    } else {
                        $('.filter-section').removeClass('filter-active');
                    }
                }

                // Debounce function for search input
                function debounce(func, wait) {
                    let timeout;
                    return function() {
                        const context = this;
                        const args = arguments;
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), wait);
                    };
                }

                // Filter change events
                $('#classFilter, #typeFilter').change(function() {
                    updateFilterCount();
                });

                $('#quickSearch').on('keyup', debounce(function() {
                    updateFilterCount();
                }, 300));

                // Reset all filters
                $('#resetFilters').click(function() {
                    $('#classFilter').val('all');
                    $('#typeFilter').val('all');
                    $('#quickSearch').val('');
                    updateFilterCount();

                    Swal.fire({
                        icon: 'info',
                        title: 'Filters Reset',
                        text: 'All filters have been cleared',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                });

                // Class type toggle logic for modal
                $('input[name="class_type"]').change(function() {
                    if ($(this).val() === 'hostel') {
                        $('#transportSection').hide();
                        $('#hostelInfo').show();
                        $('input[name="transport_applies"]').prop('disabled', true);
                    } else {
                        $('#transportSection').show();
                        $('#hostelInfo').hide();
                        $('input[name="transport_applies"]').prop('disabled', false);
                    }
                });

                // Initial filter application
                updateFilterCount();
            });
        });

        function editStructure(id, name, totalAmount) {
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function($) {
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_total_amount').value = totalAmount;
                    document.getElementById('editStructureForm').action = '/fee-structures/' + id;
                    $('#editFeeStructureModal').modal('show');
                });
            } else {
                // Fallback without jQuery
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_total_amount').value = totalAmount;
                document.getElementById('editStructureForm').action = '/fee-structures/' + id;
                var modal = new bootstrap.Modal(document.getElementById('editFeeStructureModal'));
                modal.show();
            }
        }

        // Display success/error messages
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
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonText: 'OK'
                });
            }
        @endif
    </script>
@endsection
