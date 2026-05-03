{{-- resources/views/teacher/e-permit/dashboard.blade.php --}}
@extends('SRTDashboard.frame')

@section('content')
    <style>
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 4px solid;
            margin-bottom: 20px;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-card.pending {
            border-left-color: #f59e0b;
        }

        .stat-card.approved {
            border-left-color: #22c55e;
        }

        .stat-card.rejected {
            border-left-color: #ef4444;
        }

        .stat-card.completed {
            border-left-color: #3b82f6;
        }

        .stat-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        /* Add to your existing styles */
        #yearFilter {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #yearFilter:hover {
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }

        #yearFilter:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
        }

        .filter-section {
            background: #f8fafc;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .filter-section:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-pending-class-teacher {
            background: #fef3c7;
            color: #92400e;
        }

        .status-pending-duty-teacher {
            background: #ffedd5;
            color: #9a3412;
        }

        .status-pending-academic {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-pending-head {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

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

        .role-class-teacher {
            background: #dbeafe;
            color: #1e40af;
        }

        .role-head {
            background: #e0e7ff;
            color: #3730a3;
        }

        .role-academic {
            background: #dcfce7;
            color: #166534;
        }

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

        .student-info-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
            border-left: 4px solid #22c55e;
        }

        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-label {
            width: 150px;
            font-weight: 600;
            color: #475569;
        }

        .info-value {
            flex: 1;
            color: #1e293b;
        }

        @media (max-width: 768px) {
            .stat-number {
                font-size: 1.2rem;
            }

            .btn-action {
                padding: 4px 8px;
                font-size: 0.7rem;
            }

            .permit-table th,
            .permit-table td {
                padding: 8px;
            }
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
                                <span
                                    class="role-badge role-{{ $teacher->role_id == 4 ? 'class-teacher' : ($teacher->role_id == 2 ? 'head' : 'academic') }}">
                                    <i class="fas fa-user-shield me-1"></i>
                                    {{ $teacher->role_id == 4
                                        ? 'Mwalimu wa Darasa'
                                        : ($teacher->role_id == 2
                                            ? 'Mwalimu Mkuu'
                                            : 'Mwalimu wa Taaluma') }}
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

                        <!-- Info Banner for Academic Teacher -->
                        @if ($teacher->role_id == 3)
                            <div class="info-banner">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Taarifa:</strong> Unaweza kuthibitisha maombi ya Mwalimu wa Zamu pale ambapo hakuna
                                duty roster
                                au mwalimu wa zamu hayupo. Pia una access ya kuripoti.
                            </div>
                        @endif
                        {{-- Baada ya stats cards, ongeza hii year filter section --}}
                        <div class="filter-section mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold mb-1">
                                        <i class="fas fa-calendar-alt me-1"></i> Filter by Year
                                    </label>
                                    <select id="yearFilter" class="form-select form-select-sm"
                                        onchange="filterByYear(this.value)">
                                        @foreach ($availableYears as $year)
                                            <option value="{{ $year }}"
                                                {{ $selectedYear == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-9">
                                    <div class="alert alert-info mb-0 py-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <small>Showing permit requests for year:
                                            <strong>{{ $selectedYear }}</strong></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role-based Navigation Tabs - ALL as buttons -->
                        <ul class="nav nav-pills nav-pills-custom mb-4 flex-wrap" id="ePermitTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pending"
                                    type="button" role="tab">
                                    <i class="fas fa-clock me-1"></i> Pending Requests
                                    @if ($stats['pending'] > 0)
                                        <span class="badge bg-danger ms-1">{{ $stats['pending'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#history" type="button"
                                    role="tab">
                                    <i class="fas fa-history me-1"></i> History
                                </button>
                            </li>

                            {{-- Academic (role_id=3) and Head Teacher (role_id=2) can see Reports --}}
                            @if (in_array($teacher->role_id, [2, 3]))
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#reports" type="button"
                                        role="tab">
                                        <i class="fas fa-chart-bar me-1"></i> Reports
                                    </button>
                                </li>
                            @endif

                            {{-- Academic (role_id=3) and Head Teacher (role_id=2) can see Return Check-in --}}
                            @if (in_array($teacher->role_id, [2, 3]))
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#studentReturn"
                                        type="button" role="tab">
                                        <i class="fas fa-undo-alt me-1"></i> Student Return
                                    </button>
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
                                            <input type="date" id="dateToPending"
                                                class="form-control form-control-sm">
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
                                                        <small
                                                            class="text-muted">{{ $permit->created_at->format('d/m/Y H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        {{ ucfirst($permit->student->first_name) }}
                                                        {{ ucfirst($permit->student->last_name) }}<br>
                                                        <small
                                                            class="text-muted">{{ strtoupper($permit->student->admission_number) }}</small>
                                                    </td>
                                                    <td>{{ strtoupper($permit->student->class->class_code ?? 'N/A') }}<br>
                                                        @if ($permit->student->group)
                                                            <small
                                                                class="text-muted">{{ strtoupper($permit->student->group) }}</small>
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
                                                            $statusClass = match ($permit->status) {
                                                                'pending_class_teacher' => 'pending-class-teacher',
                                                                'pending_duty_teacher' => 'pending-duty-teacher',
                                                                'pending_academic' => 'pending-academic',
                                                                'pending_head' => 'pending-head',
                                                                default => 'pending',
                                                            };
                                                            $statusText = match ($permit->status) {
                                                                'pending_class_teacher' => 'Mwalimu wa Darasa',
                                                                'pending_duty_teacher' => 'Mwalimu wa Zamu',
                                                                'pending_academic' => 'Mwalimu wa Taaluma',
                                                                'pending_head' => 'Mwalimu Mkuu',
                                                                default => ucfirst(
                                                                    str_replace('_', ' ', $permit->status),
                                                                ),
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
                                                                <i class="fas fa-eye"></i> <span
                                                                    class="d-none d-md-inline">View</span>
                                                            </a>

                                                            @if ($permit->status === 'pending_class_teacher' && $teacher->role_id == 4)
                                                                <button onclick="quickApprove({{ $permit->id }})"
                                                                    class="btn-action btn-approve" title="Approve">
                                                                    <i class="fas fa-check"></i> <span
                                                                        class="d-none d-md-inline">Approve</span>
                                                                </button>
                                                                <button onclick="quickReject({{ $permit->id }})"
                                                                    class="btn-action btn-reject" title="Reject">
                                                                    <i class="fas fa-times"></i> <span
                                                                        class="d-none d-md-inline">Reject</span>
                                                                </button>
                                                            @endif

                                                            @if ($teacher->role_id == 3 && in_array($permit->status, ['pending_duty_teacher', 'pending_academic']))
                                                                <button onclick="quickApprove({{ $permit->id }})"
                                                                    class="btn-action btn-approve" title="Approve">
                                                                    <i class="fas fa-check"></i> <span
                                                                        class="d-none d-md-inline">Approve</span>
                                                                </button>
                                                                <button onclick="quickReject({{ $permit->id }})"
                                                                    class="btn-action btn-reject" title="Reject">
                                                                    <i class="fas fa-times"></i> <span
                                                                        class="d-none d-md-inline">Reject</span>
                                                                </button>
                                                            @endif

                                                            @if ($permit->status === 'pending_head' && $teacher->role_id == 2)
                                                                <button onclick="quickApprove({{ $permit->id }})"
                                                                    class="btn-action btn-approve" title="Approve">
                                                                    <i class="fas fa-check"></i> <span
                                                                        class="d-none d-md-inline">Approve</span>
                                                                </button>
                                                                <button onclick="quickReject({{ $permit->id }})"
                                                                    class="btn-action btn-reject" title="Reject">
                                                                    <i class="fas fa-times"></i> <span
                                                                        class="d-none d-md-inline">Reject</span>
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
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if (method_exists($pendingPermits, 'links'))
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
                                            <input type="date" id="dateFromHistory"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small">Date To</label>
                                            <input type="date" id="dateToHistory"
                                                class="form-control form-control-sm">
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
                                                        {{ ucfirst($permit->student->first_name) }}
                                                        {{ ucfirst($permit->student->last_name) }}<br>
                                                        <small
                                                            class="text-muted">{{ strtoupper($permit->student->admission_number) }}</small>
                                                    </td>
                                                    <td>{{ ucwords(strtolower($permit->guardian_name)) }}<br><small>{{ $permit->guardian_phone }}</small>
                                                    </td>
                                                    <td>{{ $permit->departure_date->format('d/m/Y') }}</td>
                                                    <td>{{ $permit->expected_return_date->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if ($permit->actual_return_date)
                                                            {{ \Carbon\Carbon::parse($permit->actual_return_date)->format('d/m/Y') }}
                                                            @if ($permit->is_late_return)
                                                                <span class="badge bg-warning text-dark ms-1">Late</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusClass = match ($permit->status) {
                                                                'approved' => 'approved',
                                                                'rejected' => 'rejected',
                                                                'completed' => 'completed',
                                                                default => 'pending',
                                                            };
                                                            $statusText = match ($permit->status) {
                                                                'approved' => 'Approved',
                                                                'rejected' => 'Rejected',
                                                                'completed' => 'Completed',
                                                                default => ucfirst($permit->status),
                                                            };
                                                        @endphp
                                                        <span
                                                            class="status-badge status-{{ $statusClass }}">{{ $statusText }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('teacher.e-permit.show', ['id' => Hashids::encode($permit->id)]) }}"
                                                            class="btn-action btn-view">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        @if ($permit->status == 'approved' && $permit->pdf_path)
                                                            <a href="{{ route('teacher.e-permit.print', ['id' => Hashids::encode($permit->id)]) }}"
                                                                class="btn-action btn-view mt-1">
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

                                @if (method_exists($historyPermits, 'links'))
                                    <div class="mt-3 d-flex justify-content-center">
                                        {{ $historyPermits->links() }}
                                    </div>
                                @endif
                            </div>

                            <!-- Reports Tab Pane -->
                            <div class="tab-pane fade" id="reports" role="tabpanel">
                                <div class="filter-section">
                                    <div class="row align-items-end g-2">
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small">Date From</label>
                                            <input type="date" id="reportDateFrom"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small">Date To</label>
                                            <input type="date" id="reportDateTo" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold small">Status</label>
                                            <select id="reportStatus" class="form-select form-select-sm">
                                                <option value="all">All</option>
                                                <option value="approved">Approved</option>
                                                <option value="rejected">Rejected</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold small">&nbsp;</label>
                                            <button id="filterReportsBtn" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-filter"></i> Filter
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold small">&nbsp;</label>
                                            <button id="resetReportsBtn" class="btn btn-secondary btn-sm w-100">
                                                <i class="fas fa-undo-alt"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button id="exportPdfBtn" class="btn btn-danger btn-sm">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                    <button id="exportExcelBtn" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table permit-table" id="reportsTable">
                                        <thead>
                                            <tr>
                                                <th>Permit #</th>
                                                <th>Student</th>
                                                <th>Class</th>
                                                <th>Guardian</th>
                                                <th>Departure</th>
                                                <th>Return Date</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <i class="fas fa-chart-line fa-2x mb-2 d-block"></i>
                                                    Select filters and click Filter to load reports
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Student Return Tab Pane -->
                            <div class="tab-pane fade" id="studentReturn" role="tabpanel">
                                <div class="filter-section">
                                    <div class="row align-items-end g-2">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">Search by Permit Number or Student
                                                ID</label>
                                            <div class="input-group">
                                                <input type="text" id="returnSearchInput"
                                                    class="form-control form-control-sm"
                                                    placeholder="e.g., EPRM/2024/00001 or S12345">
                                                <button id="searchReturnBtn" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="returnResultContainer" style="display: none;"></div>

                                <div class="table-responsive" id="returnTableContainer">
                                    <table class="table permit-table" id="returnTable">
                                        <thead>
                                            <tr>
                                                <th>Permit #</th>
                                                <th>Student</th>
                                                <th>Class</th>
                                                <th>Departure Date</th>
                                                <th>Expected Return</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="fas fa-search fa-2x mb-2 d-block"></i>
                                                    Search for a permit to confirm student return
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
                inputPlaceholder: 'Andika maoni (si lazima)...'
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
                            body: JSON.stringify({
                                comment: result.value
                            })
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
                    if (!value) return 'Sababu ya kukataa inahitajika!';
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
                            body: JSON.stringify({
                                reason: result.value
                            })
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
                            Swal.fire('Hitilafu!', 'Tafadhali jaribu tena', 'error');
                        });
                }
            });
        }

        // Filter functions for pending and history
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

        // Event Listeners for Pending and History
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

        // ============ REPORTS FUNCTIONS ============
        function loadReports() {
            const dateFrom = document.getElementById('reportDateFrom')?.value || '';
            const dateTo = document.getElementById('reportDateTo')?.value || '';
            const status = document.getElementById('reportStatus')?.value || 'all';

            fetch(`{{ url('teacher/e-permit/reports/data') }}?date_from=${dateFrom}&date_to=${dateTo}&status=${status}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#reportsTable tbody');
                    if (data.data && data.data.length > 0) {
                        tbody.innerHTML = data.data.map(permit => `
                    <tr>
                        <td><strong>${permit.permit_number}</strong></td>
                        <td>${permit.student_name}<br><small class="text-muted">${permit.admission_number}</small></td>
                        <td>${permit.class_name}</td>
                        <td>${permit.guardian_name}<br><small>${permit.guardian_phone}</small></td>
                        <td>${permit.departure_date}</td>
                        <td>${permit.expected_return_date}</td>
                        <td><span class="badge bg-${permit.status == 'approved' ? 'success' : (permit.status == 'rejected' ? 'danger' : 'info')}">${permit.status.charAt(0).toUpperCase() + permit.status.slice(1)}</span></td>
                        <td>${permit.created_date}</td>
                    </tr>
                `).join('');
                    } else {
                        tbody.innerHTML =
                            '<tr><td colspan="8" class="text-center text-muted">No records found</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        document.getElementById('filterReportsBtn')?.addEventListener('click', loadReports);
        document.getElementById('resetReportsBtn')?.addEventListener('click', function() {
            document.getElementById('reportDateFrom').value = '';
            document.getElementById('reportDateTo').value = '';
            document.getElementById('reportStatus').value = 'all';
            loadReports();
        });
        document.getElementById('exportPdfBtn')?.addEventListener('click', function() {
            const dateFrom = document.getElementById('reportDateFrom')?.value || '';
            const dateTo = document.getElementById('reportDateTo')?.value || '';
            const status = document.getElementById('reportStatus')?.value || 'all';
            window.open(
                `{{ url('teacher/e-permit/reports/export-pdf') }}?date_from=${dateFrom}&date_to=${dateTo}&status=${status}`,
                '_blank');
        });
        document.getElementById('exportExcelBtn')?.addEventListener('click', function() {
            const dateFrom = document.getElementById('reportDateFrom')?.value || '';
            const dateTo = document.getElementById('reportDateTo')?.value || '';
            const status = document.getElementById('reportStatus')?.value || 'all';
            window.open(
                `{{ url('teacher/e-permit/reports/export-excel') }}?date_from=${dateFrom}&date_to=${dateTo}&status=${status}`,
                '_blank');
        });

        // ============ STUDENT RETURN FUNCTIONS ============
        document.getElementById('searchReturnBtn')?.addEventListener('click', function() {
            const search = document.getElementById('returnSearchInput').value.trim();
            if (!search) {
                Swal.fire('Taarifa', 'Tafadhali ingiza Permit number au Student ID', 'warning');
                return;
            }

            Swal.fire({
                title: 'Inatafuta...',
                text: 'Tafadhali subiri',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`{{ url('teacher/e-permit/return/search') }}?search=${encodeURIComponent(search)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        displayReturnInfo(data.permit);
                    } else {
                        Swal.fire('Taarifa', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire('Hitilafu', 'Tafadhali jaribu tena', 'error');
                });
        });

        // Function to populate relationship options dynamically
        function populateReturnRelationships() {
            const guardianType = document.getElementById('returnGuardianType');
            const relationship = document.getElementById('returnRelationship');

            const parentRelationships = ['Baba', 'Mama'];
            const guardianRelationships = ['Dada', 'Kaka', 'Shangazi', 'Mjomba', 'Babu', 'Bibi'];

            const selectedType = guardianType.value;
            let options = '<option value="">-- Chagua Uhusiano --</option>';

            if (selectedType === 'parent') {
                parentRelationships.forEach(rel => {
                    options += `<option value="${rel.toLowerCase()}">${rel}</option>`;
                });
            } else if (selectedType === 'guardian') {
                guardianRelationships.forEach(rel => {
                    options += `<option value="${rel.toLowerCase()}">${rel}</option>`;
                });
            }

            relationship.innerHTML = options;
        }

        function displayReturnInfo(permit) {
            const container = document.getElementById('returnResultContainer');
            const tableContainer = document.getElementById('returnTableContainer');

            // Format dates nicely
            const departureDate = new Date(permit.departure_date);
            const expectedReturnDate = new Date(permit.expected_return_date);
            const formattedDeparture = departureDate.toLocaleDateString('sw-TZ', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            const formattedReturn = expectedReturnDate.toLocaleDateString('sw-TZ', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            const isLateHtml = permit.is_late ?
                '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Imechelewa</span>' :
                '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Kwa Wakati</span>';

            // Student image path
            const studentImage = permit.student_image ?
                `/storage/students/${permit.student_image}` :
                '{{ asset('storage/students/student.jpg') }}';

            container.innerHTML = `
            <div class="student-info-card" style="background: white; border-radius: 16px; padding: 20px; margin-top: 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: -20px -20px 20px -20px; padding: 15px 20px; border-radius: 16px 16px 0 0;">
                    <h5 class="mb-0 text-white"><i class="fas fa-undo-alt me-2"></i> Thibitisha Kurudi kwa Mwanafunzi</h5>
                </div>

                <div class="row">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div style="position: relative; display: inline-block;">
                            <img src="${studentImage}"
                                 class="rounded-circle"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #667eea; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
                                 onerror="this.src='{{ asset('storage/students/student.jpg') }}'"
                                 alt="Student Photo">
                            <div style="position: absolute; bottom: 5px; right: 5px; background: #22c55e; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border: 2px solid white;">
                                <i class="fas fa-user-check text-white" style="font-size: 12px;"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-primary">${permit.permit_number}</span>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; margin-bottom: 12px;">
                                    <small class="text-muted text-uppercase"><i class="fas fa-user-graduate me-1"></i> Jina Kamili</small>
                                    <h6 class="mb-0 text-capitalize">${permit.student.name}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; margin-bottom: 12px;">
                                    <small class="text-muted text-uppercase"><i class="fas fa-id-card me-1"></i> Student ID</small>
                                    <h6 class="mb-0 text-uppercase">${permit.student.admission_number}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; margin-bottom: 12px;">
                                    <small class="text-muted text-uppercase"><i class="fas fa-chalkboard-user me-1"></i> Darasa</small>
                                    <h6 class="mb-0 text-uppercase">${permit.student.class}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; margin-bottom: 12px;">
                                    <small class="text-muted text-uppercase"><i class="fas fa-calendar-alt me-1"></i> Tarehe ya Kuondoka</small>
                                    <h6 class="mb-0">${formattedDeparture}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; margin-bottom: 12px;">
                                    <small class="text-muted text-uppercase"><i class="fas fa-calendar-week me-1"></i> Tarehe ya Kurudi</small>
                                    <h6 class="mb-0">${formattedReturn} ${isLateHtml}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; margin-bottom: 12px;">
                                    <small class="text-muted text-uppercase"><i class="fas fa-user-friends me-1"></i> Mzazi/Mlezi</small>
                                    <h6 class="mb-0 text-capitalize">${permit.guardian_name}</h6>
                                    <small class="text-muted">${permit.guardian_phone}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <form id="returnConfirmForm">
                    <input type="hidden" name="permit_id" value="${permit.id}">
                    <input type="hidden" name="is_late" value="${permit.is_late}">

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-user-check me-1"></i> Je, mtoto amerudi peke yake?</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input type="radio" name="returned_alone" value="1" class="form-check-input" id="return_alone_yes" checked>
                                    <label class="form-check-label" for="return_alone_yes">
                                        <i class="fas fa-user text-success me-1"></i> Ndiyo, amerudi peke yake
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="returned_alone" value="0" class="form-check-input" id="return_alone_no">
                                    <label class="form-check-label" for="return_alone_no">
                                        <i class="fas fa-users text-primary me-1"></i> Hapana, amerudi na mtu
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="returnAccompaniedFields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-user-circle me-1"></i> Jina kamili la aliyemrudisha</label>
                                <input type="text" name="accompanied_by_name" class="form-control" placeholder="Ingiza jina kamili">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-handshake me-1"></i> Undugu</label>
                                <select name="guardian_type" id="returnGuardianType" class="form-control">
                                    <option value="">-- Chagua Undugu --</option>
                                    <option value="parent">Mzazi</option>
                                    <option value="guardian">Mlezi</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-heart me-1"></i> Uhusiano</label>
                                <select name="relationship" id="returnRelationship" class="form-control">
                                    <option value="">-- Chagua Uhusiano --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="returnLateReasonField" style="display: ${permit.is_late ? 'block' : 'none'};">
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-clock me-1"></i> Sababu ya kuchelewa kurudi</label>
                            <textarea name="late_reason" class="form-control" rows="2" placeholder="Andika sababu ya kuchelewa kurudi..."></textarea>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-check-circle me-2"></i> Thibitisha Kurudi
                        </button>
                        <button type="button" id="cancelReturnBtn" class="btn btn-secondary px-4">
                            <i class="fas fa-times me-2"></i> Ghairi
                        </button>
                    </div>
                </form>
            </div>
        `;

            container.style.display = 'block';
            tableContainer.style.display = 'none';

            // Setup dynamic relationship fields
            const guardianTypeSelect = document.getElementById('returnGuardianType');
            if (guardianTypeSelect) {
                guardianTypeSelect.addEventListener('change', populateReturnRelationships);
                populateReturnRelationships();
            }

            // Cancel button
            document.getElementById('cancelReturnBtn').addEventListener('click', () => {
                container.style.display = 'none';
                tableContainer.style.display = 'block';
                document.getElementById('returnSearchInput').value = '';
            });

            // Handle accompanied fields toggle
            const aloneYes = document.getElementById('return_alone_yes');
            const aloneNo = document.getElementById('return_alone_no');
            const accompaniedFields = document.getElementById('returnAccompaniedFields');

            aloneYes.addEventListener('change', () => accompaniedFields.style.display = 'none');
            aloneNo.addEventListener('change', () => accompaniedFields.style.display = 'block');

            // Form submission
            document.getElementById('returnConfirmForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const returnedAlone = document.querySelector('input[name="returned_alone"]:checked').value;

                // Validate accompanied person fields if not returned alone
                if (returnedAlone === '0') {
                    const accompaniedByName = document.querySelector('input[name="accompanied_by_name"]').value
                        .trim();
                    const guardianType = document.querySelector('select[name="guardian_type"]').value;
                    const relationship = document.querySelector('select[name="relationship"]').value;

                    if (!accompaniedByName) {
                        Swal.fire('Taarifa', 'Tafadhali ingiza jina la aliyemrudisha', 'warning');
                        return;
                    }
                    if (!guardianType) {
                        Swal.fire('Taarifa', 'Tafadhali chagua undugu', 'warning');
                        return;
                    }
                    if (!relationship) {
                        Swal.fire('Taarifa', 'Tafadhali chagua uhusiano', 'warning');
                        return;
                    }
                }

                const formData = new FormData(this);
                const permitId = formData.get('permit_id');

                Swal.fire({
                    title: 'Thibitisha Kurudi',
                    text: 'Je, una hakika mwanafunzi amerudi shuleni?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#22c55e',
                    confirmButtonText: 'Ndiyo, Thibitisha',
                    cancelButtonText: 'Hapana'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Inachakata...',
                            text: 'Tafadhali subiri',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        fetch(`{{ url('teacher/e-permit/return') }}/${permitId}/confirm`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(Object.fromEntries(formData))
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Imefanikiwa!', data.message, 'success').then(() => {
                                        container.style.display = 'none';
                                        tableContainer.style.display = 'block';
                                        document.getElementById('returnSearchInput').value = '';
                                    });
                                } else {
                                    Swal.fire('Hitilafu!', data.message, 'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Hitilafu!', 'Tafadhali jaribu tena', 'error');
                            });
                    }
                });
            });
        }

        // Load reports when reports tab is shown
        $('#ePermitTab button[data-bs-target="#reports"]').on('shown.bs.tab', function() {
            loadReports();
        });

        // Year filter function - real-time update
        function filterByYear(year) {
            // Show loading indicator
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching data for year ' + year,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Get current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const currentUrl = window.location.pathname;

            // Update year parameter
            urlParams.set('year', year);

            // Redirect to new URL with year filter
            window.location.href = currentUrl + '?' + urlParams.toString();
        }

        // Alternative: AJAX-based real-time update without page reload (if you prefer)
        function filterByYearAjax(year) {
            // Show loading
            $('#pending').html(
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading data...</p></div>'
                );
            $('#history').html(
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading data...</p></div>'
                );

            // Update stats via AJAX
            $.ajax({
                url: '{{ url('teacher/e-permit/dashboard/stats') }}',
                type: 'GET',
                data: {
                    year: year
                },
                success: function(response) {
                    // Update stats cards
                    $('.stat-card.pending .stat-number').text(response.stats.pending);
                    $('.stat-card.approved .stat-number').text(response.stats.approved);
                    $('.stat-card.rejected .stat-number').text(response.stats.rejected);
                    $('.stat-card.completed .stat-number').text(response.stats.completed);

                    // Update pending permits table
                    $('#pending').html(response.pendingHtml);

                    // Update history table
                    $('#history').html(response.historyHtml);

                    // Update URL without reload
                    const url = new URL(window.location.href);
                    url.searchParams.set('year', year);
                    window.history.pushState({}, '', url);

                    Swal.close();
                },
                error: function() {
                    Swal.fire('Error', 'Failed to load data for year ' + year, 'error');
                }
            });
        }

        // Optional: Add event listener for when tabs are shown to refresh data with current year
        document.addEventListener('DOMContentLoaded', function() {
            // When pending tab is shown
            $('#ePermitTab button[data-bs-target="#pending"]').on('shown.bs.tab', function() {
                const currentYear = document.getElementById('yearFilter').value;
                // Optional: refresh pending data with current year
            });

            // When history tab is shown
            $('#ePermitTab button[data-bs-target="#history"]').on('shown.bs.tab', function() {
                const currentYear = document.getElementById('yearFilter').value;
                // Optional: refresh history data with current year
            });
        });
    </script>
@endsection
