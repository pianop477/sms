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

        .year-filter {
            transition: all 0.3s ease;
        }

        .year-filter:hover {
            transform: translateY(-2px);
        }

        .year-btn {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .year-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border-color: transparent;
        }

        .year-btn.active:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46a0 100%);
            color: white !important;
        }

        .installment-count-badge {
            font-size: 10px;
            padding: 2px 6px;
            margin-left: 5px;
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
                        {{-- Year Filter Section --}}
                        @php
                            $years = $installments->pluck('academic_year')->unique()->sort()->values();
                            $currentYear = date('Y');
                            $selectedYear = request('year', $currentYear);

                            // If selected year doesn't exist in installments, use the first available year or current year
                            if (!$years->contains($selectedYear) && $years->isNotEmpty()) {
                                $selectedYear = $years->first();
                            } elseif (!$years->contains($selectedYear) && $years->isEmpty()) {
                                $selectedYear = $currentYear;
                            }

                            $filteredInstallments = $installments->where('academic_year', $selectedYear)->sortBy('order');
                        @endphp

                        @if($years->isNotEmpty())
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="fw-bold">
                                        <i class="fas fa-filter me-2"></i>Filter by Academic Year:
                                    </label>
                                    <div class="btn-group flex-wrap" role="group" aria-label="Year filter">
                                        @foreach($years as $year)
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary year-btn @if($selectedYear == $year) active @endif"
                                                    data-year="{{ $year }}">
                                                {{ $year }}
                                                <span class="badge bg-light text-dark rounded-pill installment-count-badge">
                                                    {{ $installments->where('academic_year', $year)->count() }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                @if($selectedYear != $currentYear && !$years->contains($currentYear))
                                    <div class="alert alert-info small mb-3">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Showing installments for year {{ $selectedYear }}.
                                        <a href="{{ route('fee-structures.installments.manage', $feeStructure->id) }}" class="alert-link">
                                            Click here to view current year ({{ $currentYear }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Installments Display --}}
                        @if ($filteredInstallments->count() > 0)
                            <div class="row">
                                @foreach ($filteredInstallments as $inst)
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
                                                            <i class="fas fa-ellipsis-v text-dark"></i>
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
                                        <strong>Total Installments:</strong> {{ $filteredInstallments->count() }}
                                        @if($years->count() > 1)
                                            <small class="text-muted">(of {{ $installments->count() }} total)</small>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Sum of Installments:</strong>
                                        {{ number_format($filteredInstallments->sum('amount'), 0) }} TZS
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Structure Total:</strong>
                                        {{ number_format($feeStructure->total_amount, 0) }} TZS
                                        @if ($filteredInstallments->sum('amount') != $feeStructure->total_amount)
                                            <span class="text-warning ms-2">
                                                <i class="fas fa-exclamation-triangle"></i> Partial
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Academic Year:</strong>
                                        <span class="badge bg-info">{{ $selectedYear }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                                <h5>No Installments for {{ $selectedYear }}</h5>
                                <p class="text-muted">No installments found for academic year {{ $selectedYear }}</p>
                                @if($years->isNotEmpty())
                                    <button class="btn btn-outline-primary year-btn" data-year="{{ $years->first() }}">
                                        <i class="fas fa-arrow-left me-1"></i> View {{ $years->first() }} Installments
                                    </button>
                                @endif
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                                    data-bs-target="#addInstallmentModal">
                                    <i class="fas fa-plus me-1"></i> Add Installment for {{ $selectedYear }}
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
                            <strong>Note:</strong> Academic year is used to differentiate installments for different years.
                            Current filter is set to <strong>{{ $selectedYear }}</strong>.
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
                                <input type="text" name="academic_year" class="form-control" required
                                    value="{{ $selectedYear }}" placeholder="e.g., 2025">
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
            // Year filter functionality without page reload
            $('.year-btn').click(function(e) {
                e.preventDefault();
                const year = $(this).data('year');
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('year', year);
                window.location.href = currentUrl.toString();
            });
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

                    // Set academic year
                    const academicYearInput = document.getElementById('edit_inst_academic_year');
                    if (academicYearInput && data.academic_year) {
                        academicYearInput.value = data.academic_year;
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
