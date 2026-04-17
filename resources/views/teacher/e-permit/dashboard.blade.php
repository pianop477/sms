{{-- resources/views/teacher/e-permit/dashboard.blade.php --}}
@extends('SRTDashboard.frame')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border-left: 4px solid;
        margin-bottom: 20px;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .stat-card.pending { border-left-color: #f59e0b; }
    .stat-card.approved { border-left-color: #22c55e; }
    .stat-card.rejected { border-left-color: #ef4444; }
    .stat-card.completed { border-left-color: #3b82f6; }

    .stat-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-pending-class-teacher { background: #fef3c7; color: #92400e; }
    .status-pending-duty-teacher { background: #ffedd5; color: #9a3412; }
    .status-pending-academic { background: #dbeafe; color: #1e40af; }
    .status-pending-head { background: #e0e7ff; color: #3730a3; }
    .status-approved { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .status-completed { background: #d1fae5; color: #065f46; }

    .filter-section {
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
    }

    .permit-table {
        width: 100%;
        border-collapse: collapse;
    }

    .permit-table th {
        background: #f8fafc;
        padding: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }

    .permit-table td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }

    .permit-table tr:hover {
        background: #f8fafc;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-view {
        background: #3b82f6;
        color: white;
    }

    .btn-approve {
        background: #22c55e;
        color: white;
    }

    .btn-reject {
        background: #ef4444;
        color: white;
    }

    .role-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .role-class-teacher { background: #dbeafe; color: #1e40af; }
    .role-head { background: #e0e7ff; color: #3730a3; }
    .role-academic { background: #dcfce7; color: #166534; }

    .nav-pills-custom .nav-link {
        color: #475569;
        border-radius: 10px;
        padding: 10px 20px;
        margin: 0 5px;
        transition: all 0.3s;
    }

    .nav-pills-custom .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .nav-pills-custom .nav-link:hover:not(.active) {
        background: #f1f5f9;
    }

    .info-banner {
        background: #e0f2fe;
        border-left: 4px solid #0284c7;
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .stat-number { font-size: 1.2rem; }
        .btn-action { padding: 4px 8px; font-size: 0.7rem; }
        .permit-table th, .permit-table td { padding: 8px; }
    }
</style>

<div class="py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i> e-Permit Management System
                        </h5>
                        <div class="mt-2 mt-sm-0">
                            <span class="role-badge role-{{
                                $teacher->role_id == 4 ? 'class-teacher' :
                                ($teacher->role_id == 2 ? 'head' : 'academic')
                            }}">
                                <i class="fas fa-user-shield me-1"></i>
                                {{ $teacher->role_id == 4 ? 'Mwalimu wa Darasa' :
                                   ($teacher->role_id == 2 ? 'Mwalimu Mkuu' : 'Mwalimu wa Taaluma') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Stats Cards -->
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="stat-card pending">
                                <div class="stat-title">Pending Requests</div>
                                <div class="stat-number">{{ $stats['pending'] }}</div>
                                <small class="text-muted">Awaiting action</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card approved">
                                <div class="stat-title">Approved</div>
                                <div class="stat-number">{{ $stats['approved'] }}</div>
                                <small class="text-muted">Permits issued</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card rejected">
                                <div class="stat-title">Rejected</div>
                                <div class="stat-number">{{ $stats['rejected'] }}</div>
                                <small class="text-muted">Declined requests</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card completed">
                                <div class="stat-title">Completed</div>
                                <div class="stat-number">{{ $stats['completed'] }}</div>
                                <small class="text-muted">Students returned</small>
                            </div>
                        </div>
                    </div>

                    <!-- Info Banner for Class Teacher -->
                    <!-- Info Banner for Academic Teacher -->
                    @if($teacher->role_id == 3)
                        <div class="info-banner">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Taarifa:</strong> Unaweza kuthibitisha maombi ya Mwalimu wa Zamu pale ambapo hakuna duty roster
                            au mwalimu wa zamu hayupo. Pia una access ya kuripoti.
                        </div>
                    @endif

                    <!-- Role-based Navigation Tabs -->
                    <ul class="nav nav-pills nav-pills-custom mb-4 flex-wrap" id="ePermitTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pending" type="button" role="tab">
                                <i class="fas fa-clock me-1"></i> Pending Requests
                                @if($stats['pending'] > 0)
                                    <span class="badge bg-danger ms-1">{{ $stats['pending'] }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#history" type="button" role="tab">
                                <i class="fas fa-history me-1"></i> History
                            </button>
                        </li>

                        {{-- Academic (role_id=3) and Head Teacher (role_id=2) can see Reports --}}
                        @if(in_array($teacher->role_id, [2, 3]))
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ route('teacher.e-permit.reports') }}">
                                    <i class="fas fa-chart-bar me-1"></i> Reports
                                </a>
                            </li>
                        @endif

                        {{-- Academic (role_id=3) and Head Teacher (role_id=2) can see Return Check-in --}}
                        @if(in_array($teacher->role_id, [2, 3]))
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ route('teacher.e-permit.return-form') }}">
                                    <i class="fas fa-undo-alt me-1"></i> Student Return
                                </a>
                            </li>
                        @endif
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Pending Tab -->
                        <div class="tab-pane fade show active" id="pending" role="tabpanel">
                            <div class="filter-section">
                                <div class="row align-items-end g-2">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">Search</label>
                                        <input type="text" id="searchPending" class="form-control form-control-sm"
                                               placeholder="Permit #, Student name...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Date From</label>
                                        <input type="date" id="dateFromPending" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Date To</label>
                                        <input type="date" id="dateToPending" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <button id="resetPendingFilters" class="btn btn-secondary btn-sm w-100">
                                            <i class="fas fa-undo-alt"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table permit-table" id="pendingTable">
                                    <thead>
                                        <tr>
                                            <th width="12%">Permit #</th>
                                            <th width="18%">Student</th>
                                            <th width="10%">Class</th>
                                            <th width="18%">Guardian</th>
                                            <th width="10%">Requested At</th>
                                            <th width="15%">Status</th>
                                            <th width="17%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingPermits as $permit)
                                        <tr>
                                            <td><strong>{{ $permit->permit_number }}</strong><br>
                                                <small class="text-muted">{{ $permit->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                {{ ucfirst($permit->student->first_name) }} {{ ucfirst($permit->student->last_name) }}<br>
                                                <small class="text-muted">{{ strtoupper($permit->student->admission_number) }}</small>
                                            </td>
                                            <td>{{ strtoupper($permit->student->class->class_code ?? 'N/A') }}<br>
                                                @if($permit->student->group)
                                                    <small class="text-muted">{{ strtoupper($permit->student->group) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ ucwords(strtolower($permit->guardian_name)) }}<br>
                                                <small>{{ $permit->guardian_phone }}</small>
                                            </td>
                                            <td>{{ $permit->departure_date->format('d/m/Y') }}<br>
                                                <small>{{ $permit->departure_time->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($permit->status) {
                                                        'pending_class_teacher' => 'pending-class-teacher',
                                                        'pending_duty_teacher' => 'pending-duty-teacher',
                                                        'pending_academic' => 'pending-academic',
                                                        'pending_head' => 'pending-head',
                                                        default => 'pending'
                                                    };
                                                    $statusText = match($permit->status) {
                                                        'pending_class_teacher' => 'Mwalimu wa Darasa',
                                                        'pending_duty_teacher' => 'Mwalimu wa Zamu',
                                                        'pending_academic' => 'Mwalimu wa Taaluma',
                                                        'pending_head' => 'Mwalimu Mkuu',
                                                        default => ucfirst(str_replace('_', ' ', $permit->status))
                                                    };
                                                @endphp
                                                <span class="status-badge status-{{ $statusClass }}">
                                                    <i class="fas fa-clock me-1"></i> {{ $statusText }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    <a href="{{ route('teacher.e-permit.show', ['id' => Hashids::encode($permit->id)]) }}"
                                                       class="btn-action btn-view" title="View Details">
                                                        <i class="fas fa-eye"></i> <span class="d-none d-md-inline">View</span>
                                                    </a>

                                                    {{-- Class Teacher can approve/reject their pending permits --}}
                                                    @if($permit->status === 'pending_class_teacher' && $teacher->role_id == 4)
                                                        <button onclick="quickApprove({{ $permit->id }})"
                                                                class="btn-action btn-approve" title="Approve">
                                                            <i class="fas fa-check"></i> <span class="d-none d-md-inline">Approve</span>
                                                        </button>
                                                        <button onclick="quickReject({{ $permit->id }})"
                                                                class="btn-action btn-reject" title="Reject">
                                                            <i class="fas fa-times"></i> <span class="d-none d-md-inline">Reject</span>
                                                        </button>
                                                    @endif

                                                    {{-- Academic Teacher can approve/reject duty teacher and academic permits --}}
                                                    @if($teacher->role_id == 3 && in_array($permit->status, ['pending_duty_teacher', 'pending_academic']))
                                                        <button onclick="quickApprove({{ $permit->id }})"
                                                                class="btn-action btn-approve" title="Approve">
                                                            <i class="fas fa-check"></i> <span class="d-none d-md-inline">Approve</span>
                                                        </button>
                                                        <button onclick="quickReject({{ $permit->id }})"
                                                                class="btn-action btn-reject" title="Reject">
                                                            <i class="fas fa-times"></i> <span class="d-none d-md-inline">Reject</span>
                                                        </button>
                                                    @endif

                                                    {{-- Head Teacher can approve/reject head permits --}}
                                                    @if($permit->status === 'pending_head' && $teacher->role_id == 2)
                                                        <button onclick="quickApprove({{ $permit->id }})"
                                                                class="btn-action btn-approve" title="Approve">
                                                            <i class="fas fa-check"></i> <span class="d-none d-md-inline">Approve</span>
                                                        </button>
                                                        <button onclick="quickReject({{ $permit->id }})"
                                                                class="btn-action btn-reject" title="Reject">
                                                            <i class="fas fa-times"></i> <span class="d-none d-md-inline">Reject</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                    Hakuna ombi linalosubiri kuthibitishwa
                                                    <br>
                                                    <small>Utapata arifa ukishapata ombi jipya</small>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if(method_exists($pendingPermits, 'links'))
                                <div class="mt-3 d-flex justify-content-center">
                                    {{ $pendingPermits->links() }}
                                </div>
                            @endif
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <div class="filter-section">
                                <div class="row align-items-end g-2">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">Search</label>
                                        <input type="text" id="searchHistory" class="form-control form-control-sm"
                                               placeholder="Permit #, Student name...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Date From</label>
                                        <input type="date" id="dateFromHistory" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Date To</label>
                                        <input type="date" id="dateToHistory" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <button id="resetHistoryFilters" class="btn btn-secondary btn-sm w-100">
                                            <i class="fas fa-undo-alt"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table permit-table" id="historyTable">
                                    <thead>
                                        <tr>
                                            <th width="12%">Permit #</th>
                                            <th width="18%">Student</th>
                                            <th width="15%">Guardian</th>
                                            <th width="10%">Requested At</th>
                                            <th width="10%">Return Date</th>
                                            <th width="10%">Actual Return</th>
                                            <th width="10%">Status</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($historyPermits as $permit)
                                        <tr>
                                            <td><strong>{{ $permit->permit_number }}</strong><br>
                                                <small>{{ $permit->created_at->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                {{ ucfirst($permit->student->first_name) }} {{ ucfirst($permit->student->last_name) }}<br>
                                                <small class="text-muted">{{ strtoupper($permit->student->admission_number) }}</small>
                                            </td>
                                            <td>{{ ucwords(strtolower($permit->guardian_name)) }}<br><small>{{ $permit->guardian_phone }}</small></td>
                                            <td>{{ $permit->departure_date->format('d/m/Y') }}</td>
                                            <td>{{ $permit->expected_return_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($permit->actual_return_date)
                                                    {{ \Carbon\Carbon::parse($permit->actual_return_date)->format('d/m/Y') }}
                                                    @if($permit->is_late_return)
                                                        <span class="badge bg-warning text-dark ms-1">Late</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($permit->status) {
                                                        'approved' => 'approved',
                                                        'rejected' => 'rejected',
                                                        'completed' => 'completed',
                                                        default => 'pending'
                                                    };
                                                    $statusText = match($permit->status) {
                                                        'approved' => 'Approved',
                                                        'rejected' => 'Rejected',
                                                        'completed' => 'Completed',
                                                        default => ucfirst($permit->status)
                                                    };
                                                @endphp
                                                <span class="status-badge status-{{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('teacher.e-permit.show', ['id' => Hashids::encode($permit->id)]) }}"
                                                   class="btn-action btn-view">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                @if($permit->status == 'approved' && $permit->pdf_path)
                                                    <a href="{{ asset($permit->pdf_path) }}"
                                                       class="btn-action btn-view mt-1" target="_blank">
                                                        <i class="fas fa-print"></i> Print
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <i class="fas fa-history fa-2x mb-2 d-block"></i>
                                                    Hakuna historia ya maombi
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if(method_exists($historyPermits, 'links'))
                                <div class="mt-3 d-flex justify-content-center">
                                    {{ $historyPermits->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Quick Approve Function
    function quickApprove(permitId) {
        Swal.fire({
            title: 'Thibitisha Ombi',
            text: 'Je, una uhakika unataka kukubali ombi hili la ruhusa?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ndiyo, Kubali',
            cancelButtonText: 'Hapana',
            input: 'textarea',
            inputPlaceholder: 'Andika maoni (si lazima)...',
            inputAttributes: {
                'aria-label': 'Maoni'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Inachakata...',
                    text: 'Tafadhali subiri',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`{{ url('teacher/e-permit') }}/${permitId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ comment: result.value })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Imefanikiwa!', data.message, 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Hitilafu!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Hitilafu!', 'Tafadhali jaribu tena', 'error');
                });
            }
        });
    }

    // Quick Reject Function
    function quickReject(permitId) {
        Swal.fire({
            title: 'Kataa Ombi',
            text: 'Je, una uhakika unataka kukataa ombi hili la ruhusa?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ndiyo, Kataa',
            cancelButtonText: 'Hapana',
            input: 'textarea',
            inputPlaceholder: 'Tafadhali andika sababu ya kukataa...',
            inputValidator: (value) => {
                if (!value) {
                    return 'Sababu ya kukataa inahitajika!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Inachakata...',
                    text: 'Tafadhali subiri',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`{{ url('teacher/e-permit') }}/${permitId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ reason: result.value })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Imefanikiwa!', data.message, 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Hitilafu!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Hitilafu!', 'Tafadhali jaribu tena', 'error');
                });
            }
        });
    }

    // Filter functions
    function filterTable(tableId, searchText) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');
        if (!rows.length) return;

        const search = searchText.toLowerCase();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    }

    function filterDateRange(tableId, dateFrom, dateTo) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');
        if (!rows.length) return;

        rows.forEach(row => {
            const departureCell = row.cells[4];
            if (!departureCell) return;

            const departureDateText = departureCell.textContent.trim();
            const parts = departureDateText.split('/');
            if (parts.length !== 3) return;

            const departureDate = new Date(parts[2], parts[1] - 1, parts[0]);

            let show = true;
            if (dateFrom && departureDate < new Date(dateFrom)) show = false;
            if (dateTo && departureDate > new Date(dateTo)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    // Event Listeners
    document.getElementById('searchPending')?.addEventListener('keyup', function() {
        filterTable('pendingTable', this.value);
    });

    document.getElementById('searchHistory')?.addEventListener('keyup', function() {
        filterTable('historyTable', this.value);
    });

    document.getElementById('resetPendingFilters')?.addEventListener('click', function() {
        document.getElementById('searchPending').value = '';
        document.getElementById('dateFromPending').value = '';
        document.getElementById('dateToPending').value = '';
        filterTable('pendingTable', '');
    });

    document.getElementById('resetHistoryFilters')?.addEventListener('click', function() {
        document.getElementById('searchHistory').value = '';
        document.getElementById('dateFromHistory').value = '';
        document.getElementById('dateToHistory').value = '';
        filterTable('historyTable', '');
    });
</script>
@endsection
