@extends('SRTDashboard.frame')

@section('content')
    <style>
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .installment-card {
            transition: all 0.3s ease;
        }

        .installment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }

        .btn-xs {
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 6px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .cumulative-bar {
            background: #e0e7ff;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }

        .cumulative-progress {
            background: #4e73df;
            height: 100%;
            transition: width 0.3s ease;
        }

        .academic-year-badge {
            background: #e0e7ff;
            color: #3730a3;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
            margin-left: 8px;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="fas fa-layer-group me-2"></i>
                                    Installments: {{ $feeStructure->name }}
                                </h5>
                                <small class="mt-1 d-block">Total: {{ number_format($feeStructure->total_amount, 0) }}
                                    TZS</small>
                            </div>
                            <div>
                                <a href="{{ route('fee-structures.index') }}" class="btn btn-sm btn-light me-2">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                                    data-bs-target="#addInstallmentModal">
                                    <i class="fas fa-plus me-1"></i> Add Installment
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($installments->count() > 0)
                            <div class="row">
                                @foreach ($installments->sortBy('order') as $inst)
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card installment-card h-100">
                                            <div class="card-header bg-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">
                                                        <span class="badge bg-primary me-2">Order {{ $inst->order }}</span>
                                                        {{ $inst->name }}
                                                        <span class="academic-year-badge">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            {{ $inst->academic_year ?? 'N/A' }}
                                                        </span>
                                                    </h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-link" type="button"
                                                            data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v text-white"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <button class="dropdown-item"
                                                                    onclick="editInstallment({{ $inst->id }})">
                                                                    <i class="fas fa-edit me-2"></i> Edit
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <form
                                                                    action="{{ route('fee-structures.installments.delete', $inst->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Delete this installment? This will also delete any associated tokens.')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash me-2"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="text-muted small">Amount</label>
                                                    <h5 class="mb-0">{{ number_format($inst->amount, 0) }} TZS</h5>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="text-muted small">Cumulative Required</label>
                                                    <h6 class="mb-1">{{ number_format($inst->cumulative_required, 0) }}
                                                        TZS</h6>
                                                    <div class="cumulative-bar">
                                                        <div class="cumulative-progress"
                                                            style="width: {{ min(100, ($inst->cumulative_required / $feeStructure->total_amount) * 100) }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="text-muted small">Period</label>
                                                    <div class="small">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        {{ \Carbon\Carbon::parse($inst->start_date)->format('d M Y') }} -
                                                        {{ \Carbon\Carbon::parse($inst->end_date)->format('d M Y') }}
                                                    </div>
                                                </div>
                                                <div class="alert alert-info small mb-0">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Students qualify when total paid ≥
                                                    {{ number_format($inst->cumulative_required, 0) }} TZS
                                                    <br>
                                                    <small class="text-muted mt-1 d-block">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        Academic Year: {{ $inst->academic_year ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Summary Card --}}
                            <div class="alert alert-secondary mt-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Total Installments:</strong> {{ $installments->count() }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Sum of Installments:</strong>
                                        {{ number_format($installments->sum('amount'), 0) }} TZS
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Structure Total:</strong>
                                        {{ number_format($feeStructure->total_amount, 0) }} TZS
                                        @if ($installments->sum('amount') != $feeStructure->total_amount)
                                            <span class="text-danger ms-2">
                                                <i class="fas fa-exclamation-triangle"></i> Mismatch!
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Academic Years:</strong>
                                        @php
                                            $years = $installments->pluck('academic_year')->unique()->sort();
                                        @endphp
                                        @foreach($years as $year)
                                            <span class="badge bg-info ms-1">{{ $year }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                                <h5>No Installments Added Yet</h5>
                                <p class="text-muted">Add installments (terms) for this fee structure</p>
                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addInstallmentModal">
                                    <i class="fas fa-plus me-1"></i> Add First Installment
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Installment Modal --}}
    <div class="modal fade" id="addInstallmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add Installment to
                        {{ $feeStructure->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('fee-structures.installments.store', $feeStructure->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Kumbuka:</strong> Academic year itatumika kutofautisha installments za miaka tofauti.
                            Mfano: Unaweza kuwa na installments za 2025 na 2026 tofauti.
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Installment Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="e.g., Term 1, Term 2, Term 3">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Order Number <span class="text-danger">*</span></label>
                                <input type="number" name="order" class="form-control" required min="1"
                                    placeholder="1, 2, 3...">
                                <small class="text-muted">Order in which installments are processed</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <input type="text" name="academic_year" class="form-control" required placeholder="e.g., 2025">
                                <small class="text-muted">The academic year for which this installment applies</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Installment Amount (TZS) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control" required min="0"
                                    step="1000" placeholder="Amount for this term">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cumulative Required (TZS) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="cumulative_required" class="form-control" required
                                    min="0" step="1000" placeholder="Total required after this term">
                                <small class="text-muted">Total amount student must have paid to qualify</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Installment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Installment Modal --}}
    <div class="modal fade" id="editInstallmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Installment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editInstallmentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Installment Name *</label>
                                <input type="text" name="name" id="edit_inst_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Order Number *</label>
                                <input type="number" name="order" id="edit_inst_order" class="form-control" required
                                    min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Academic Year *</label>
                                <input type="text" name="academic_year" id="edit_inst_academic_year" class="form-control" required placeholder="e.g. 2025">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Installment Amount (TZS) *</label>
                                <input type="number" name="amount" id="edit_inst_amount" class="form-control" required
                                    min="0" step="1000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cumulative Required (TZS) *</label>
                                <input type="number" name="cumulative_required" id="edit_inst_cumulative"
                                    class="form-control" required min="0" step="1000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date *</label>
                                <input type="date" name="start_date" id="edit_inst_start_date" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date *</label>
                                <input type="date" name="end_date" id="edit_inst_end_date" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Installment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Any initialization code if needed
        });

        function editInstallment(id) {
            // Show loading state
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching installment data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch installment data
            fetch(`/fee-structures/installments/${id}/edit`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Network response was not ok');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.close();

                    // Check if there's an error
                    if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.error
                        });
                        return;
                    }

                    // Populate form fields
                    document.getElementById('edit_inst_name').value = data.name;
                    document.getElementById('edit_inst_order').value = data.order;
                    document.getElementById('edit_inst_amount').value = data.amount;
                    document.getElementById('edit_inst_cumulative').value = data.cumulative_required;
                    document.getElementById('edit_inst_start_date').value = data.start_date;
                    document.getElementById('edit_inst_end_date').value = data.end_date;

                    // Set academic year in select dropdown
                    const academicYearSelect = document.getElementById('edit_inst_academic_year');
                    if (academicYearSelect && data.academic_year) {
                        academicYearSelect.value = data.academic_year;
                    }

                    // Set form action
                    document.getElementById('editInstallmentForm').action = `/fee-structures/installments/${id}`;

                    // Show modal
                    $('#editInstallmentModal').modal('show');
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to load installment data. Please refresh and try again.',
                        footer: error.message
                    });
                    console.error('Error:', error);
                });
        }

        function editStructure(id, name, totalAmount) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_total_amount').value = totalAmount;
            document.getElementById('editStructureForm').action = '/fee-structures/' + id;
            $('#editFeeStructureModal').modal('show');
        }

        // Display success/error messages
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
