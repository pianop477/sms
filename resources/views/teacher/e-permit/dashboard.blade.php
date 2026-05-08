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
        cursor: pointer;
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

    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    .filter-section {
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
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
        background: #e0e7ff;
        color: #3730a3;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-completed {
        background: #d1fae5;
        color: #065f46;
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
        text-decoration: none;
    }

    .btn-view {
        background: #3b82f6;
        color: white;
    }

    .btn-print {
        background: #22c55e;
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

    #yearFilter {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        font-weight: 500;
        cursor: pointer;
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
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> e-Permit Management System</h5>
                        <div class="mt-2 mt-sm-0">
                            <span
                                class="role-badge role-{{ $teacher->role_id == 4 ? 'class-teacher' : ($teacher->role_id == 2 ? 'head' : 'academic') }}">
                                <i class="fas fa-user-shield me-1"></i>
                                {{ $teacher->role_id == 4 ? 'Mwalimu wa Darasa' : ($teacher->role_id == 2 ? 'Mwalimu
                                Mkuu' : 'Mwalimu wa Taaluma') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Stats Cards -->
                    <div class="row" id="statsContainer">
                        <div class="col-md-3 col-6">
                            <div class="stat-card pending">
                                <div class="stat-title">Pending Requests</div>
                                <div class="stat-number" id="pendingCount">{{ $stats['pending'] }}</div>
                                <small class="text-muted">Awaiting action</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card approved">
                                <div class="stat-title">Approved</div>
                                <div class="stat-number" id="approvedCount">{{ $stats['approved'] }}</div>
                                <small class="text-muted">Permits issued</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card rejected">
                                <div class="stat-title">Rejected</div>
                                <div class="stat-number" id="rejectedCount">{{ $stats['rejected'] }}</div>
                                <small class="text-muted">Declined requests</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card completed">
                                <div class="stat-title">Completed</div>
                                <div class="stat-number" id="completedCount">{{ $stats['completed'] }}</div>
                                <small class="text-muted">Students returned</small>
                            </div>
                        </div>
                    </div>

                    @if ($teacher->role_id == 3)
                    <div class="info-banner">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Taarifa:</strong> Unaweza kuthibitisha maombi ya Mwalimu wa Zamu pale ambapo hakuna duty
                        roster au mwalimu wa zamu hayupo. Pia una access ya kuripoti.
                    </div>
                    @endif

                    <!-- Year Filter -->
                    <div class="filter-section mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-bold mb-1"><i class="fas fa-calendar-alt me-1"></i> Filter
                                    by Year</label>
                                <select id="yearFilter" class="form-select form-select-sm">
                                    @foreach ($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear==$year ? 'selected' : '' }}>{{ $year }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-9">
                                <div class="alert alert-info mb-0 py-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <small>Showing permit requests for year: <strong id="selectedYearDisplay">{{
                                            $selectedYear }}</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-pills nav-pills-custom mb-4 flex-wrap" id="ePermitTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pending"
                                type="button" role="tab">
                                <i class="fas fa-clock me-1"></i> Pending Requests
                                <span class="badge bg-danger ms-1" id="pendingBadge">{{ $stats['pending'] }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#history" type="button"
                                role="tab">
                                <i class="fas fa-history me-1"></i> History
                            </button>
                        </li>
                        @if (in_array($teacher->role_id, [2, 3]))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#reports" type="button"
                                role="tab">
                                <i class="fas fa-chart-bar me-1"></i> Reports
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#studentReturn" type="button"
                                role="tab">
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
                                        <input type="text" id="searchPendingInput" class="form-control form-control-sm"
                                            placeholder="Permit #, Student name...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Date From</label>
                                        <input type="date" id="dateFromPendingInput"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Date To</label>
                                        <input type="date" id="dateToPendingInput" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <button id="resetPendingFiltersBtn" class="btn btn-secondary btn-sm w-100">
                                            <i class="fas fa-undo-alt"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="pendingTableContainer">
                                @include('teacher.e-permit.partials.pending_table', ['pendingPermits' =>
                                $pendingPermits, 'teacher' => $teacher])
                            </div>
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <div class="filter-section">
                                <div class="row align-items-end g-2">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">Search</label>
                                        <input type="text" id="searchHistoryInput" class="form-control form-control-sm"
                                            placeholder="Permit #, Student name...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Date From</label>
                                        <input type="date" id="dateFromHistoryInput"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Date To</label>
                                        <input type="date" id="dateToHistoryInput" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <button id="resetHistoryFiltersBtn" class="btn btn-secondary btn-sm w-100">
                                            <i class="fas fa-undo-alt"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="historyTableContainer">
                                @include('teacher.e-permit.partials.history_table', ['historyPermits' =>
                                $historyPermits, 'teacher' => $teacher])
                            </div>
                        </div>

                        <!-- Reports Tab -->
                        <div class="tab-pane fade" id="reports" role="tabpanel">
                            <div class="filter-section">
                                <div class="row align-items-end g-2">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Date From</label>
                                        <input type="date" id="reportDateFrom" class="form-control form-control-sm">
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
                                        <button id="filterReportsBtn" class="btn btn-primary btn-sm w-100"><i
                                                class="fas fa-filter"></i> Filter</button>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold small">&nbsp;</label>
                                        <button id="resetReportsBtn" class="btn btn-secondary btn-sm w-100"><i
                                                class="fas fa-undo-alt"></i> Reset</button>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button id="exportPdfBtn" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i>
                                    Export PDF</button>
                                <button id="exportExcelBtn" class="btn btn-success btn-sm"><i
                                        class="fas fa-file-excel"></i> Export Excel</button>
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
                                            <td colspan="8" class="text-center py-4 text-muted">Select filters and click
                                                Filter to load reports</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Student Return Tab -->
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
                                            <button id="searchReturnBtn" class="btn btn-primary btn-sm"><i
                                                    class="fas fa-search"></i> Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="returnResultContainer" style="display: none;"></div>
                            <div class="table-responsive" id="returnTableContainer">
                                <table class="table permit-table">
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
                                            <td colspan="7" class="text-center py-4 text-muted">Search for a permit to
                                                confirm student return</td>
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
    // ============================================
// GLOBAL VARIABLES
// ============================================
let currentYear = {{ $selectedYear }};
let pendingDebounceTimer, historyDebounceTimer;

// ============================================
// TAB PERSISTENCE
// ============================================
(function() {
    const savedTab = localStorage.getItem('activePermitTab');
    if (savedTab && savedTab !== '#reports') {
        const tabButton = document.querySelector(`#ePermitTab button[data-bs-target="${savedTab}"]`);
        if (tabButton) {
            document.querySelectorAll('#ePermitTab .nav-link').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
            tabButton.classList.add('active');
            const targetPane = document.querySelector(savedTab);
            if (targetPane) targetPane.classList.add('show', 'active');
        }
    }

    document.querySelectorAll('#ePermitTab button').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            const target = e.target.getAttribute('data-bs-target');
            if (target !== '#reports') localStorage.setItem('activePermitTab', target);
        });
    });
})();

// ============================================
// LOAD STATS VIA AJAX
// ============================================
function loadStats() {
    fetch(`/teacher/e-permit/stats/data?year=${currentYear}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('pendingCount').textContent = data.stats.pending;
            document.getElementById('approvedCount').textContent = data.stats.approved;
            document.getElementById('rejectedCount').textContent = data.stats.rejected;
            document.getElementById('completedCount').textContent = data.stats.completed;
            const pendingBadge = document.getElementById('pendingBadge');
            if (pendingBadge) pendingBadge.textContent = data.stats.pending;
        }
    })
    .catch(error => console.error('Error loading stats:', error));
}

// ============================================
// LOAD PENDING TABLE VIA AJAX
// ============================================
function loadPendingTable(page = 1) {
    const search = document.getElementById('searchPendingInput')?.value || '';
    const dateFrom = document.getElementById('dateFromPendingInput')?.value || '';
    const dateTo = document.getElementById('dateToPendingInput')?.value || '';

    const container = document.getElementById('pendingTableContainer');
    if (container) {
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>';
    }

    // Build URL with proper parameters
    let url = `/teacher/e-permit/pending/data?year=${currentYear}&page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (dateFrom) url += `&date_from=${dateFrom}`;
    if (dateTo) url += `&date_to=${dateTo}`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && container) {
            container.innerHTML = data.html;
            // Re-attach pagination handlers after table is updated
            attachPendingPaginationHandlers();
        }
    })
    .catch(error => {
        console.error('Error loading pending table:', error);
        if (container) {
            container.innerHTML = '<div class="alert alert-danger text-center">Error loading data. Please refresh the page and try again.</div>';
        }
    });
}

function loadPendingTableWithUrl(url) {
    const container = document.getElementById('pendingTableContainer');
    if (container) {
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>';
    }

    // Ensure URL uses AJAX-friendly parameters
    let fetchUrl = url;
    if (!fetchUrl.includes('pending/data')) {
        // Extract page number from Laravel pagination URL
        const pageMatch = url.match(/[?&]page=(\d+)/);
        if (pageMatch) {
            const page = pageMatch[1];
            const search = document.getElementById('searchPendingInput')?.value || '';
            const dateFrom = document.getElementById('dateFromPendingInput')?.value || '';
            const dateTo = document.getElementById('dateToPendingInput')?.value || '';
            fetchUrl = `/teacher/e-permit/pending/data?year=${currentYear}&page=${page}`;
            if (search) fetchUrl += `&search=${encodeURIComponent(search)}`;
            if (dateFrom) fetchUrl += `&date_from=${dateFrom}`;
            if (dateTo) fetchUrl += `&date_to=${dateTo}`;
        }
    }

    fetch(fetchUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && container) {
            container.innerHTML = data.html;
            attachPendingPaginationHandlers();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (container) {
            container.innerHTML = '<div class="alert alert-danger text-center">Error loading data. Please refresh the page.</div>';
        }
    });
}

function attachPendingPaginationHandlers() {
    const paginationLinks = document.querySelectorAll('#pendingTableContainer .pagination a');
    paginationLinks.forEach(link => {
        // Remove existing event listeners by cloning and replacing
        const newLink = link.cloneNode(true);
        link.parentNode.replaceChild(newLink, link);

        newLink.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const href = this.getAttribute('href');
            if (href && !href.includes('javascript:void')) {
                // Extract page number
                const pageMatch = href.match(/[?&]page=(\d+)/);
                if (pageMatch) {
                    const page = pageMatch[1];
                    loadPendingTable(page);
                } else {
                    loadPendingTableWithUrl(href);
                }
            }
        });
    });
}

// ============================================
// LOAD HISTORY TABLE VIA AJAX
// ============================================
function loadHistoryTable(page = 1) {
    const search = document.getElementById('searchHistoryInput')?.value || '';
    const dateFrom = document.getElementById('dateFromHistoryInput')?.value || '';
    const dateTo = document.getElementById('dateToHistoryInput')?.value || '';

    const container = document.getElementById('historyTableContainer');
    if (container) {
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>';
    }

    let url = `/teacher/e-permit/history/data?year=${currentYear}&page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (dateFrom) url += `&date_from=${dateFrom}`;
    if (dateTo) url += `&date_to=${dateTo}`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && container) {
            container.innerHTML = data.html;
            attachHistoryPaginationHandlers();
        }
    })
    .catch(error => {
        console.error('Error loading history table:', error);
        if (container) {
            container.innerHTML = '<div class="alert alert-danger text-center">Error loading data. Please refresh the page and try again.</div>';
        }
    });
}

function loadHistoryTableWithUrl(url) {
    const container = document.getElementById('historyTableContainer');
    if (container) {
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>';
    }

    let fetchUrl = url;
    if (!fetchUrl.includes('history/data')) {
        const pageMatch = url.match(/[?&]page=(\d+)/);
        if (pageMatch) {
            const page = pageMatch[1];
            const search = document.getElementById('searchHistoryInput')?.value || '';
            const dateFrom = document.getElementById('dateFromHistoryInput')?.value || '';
            const dateTo = document.getElementById('dateToHistoryInput')?.value || '';
            fetchUrl = `/teacher/e-permit/history/data?year=${currentYear}&page=${page}`;
            if (search) fetchUrl += `&search=${encodeURIComponent(search)}`;
            if (dateFrom) fetchUrl += `&date_from=${dateFrom}`;
            if (dateTo) fetchUrl += `&date_to=${dateTo}`;
        }
    }

    fetch(fetchUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && container) {
            container.innerHTML = data.html;
            attachHistoryPaginationHandlers();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (container) {
            container.innerHTML = '<div class="alert alert-danger text-center">Error loading data. Please refresh the page.</div>';
        }
    });
}
function loadHistoryTableWithUrl(url) {
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(response => response.json())
    .then(data => { if (data.success) document.getElementById('historyTableContainer').innerHTML = data.html; attachHistoryPaginationHandlers(); })
    .catch(error => console.error('Error:', error));
}

function attachHistoryPaginationHandlers() {
    const paginationLinks = document.querySelectorAll('#historyTableContainer .pagination a');
    paginationLinks.forEach(link => {
        // Remove existing event listeners by cloning and replacing
        const newLink = link.cloneNode(true);
        link.parentNode.replaceChild(newLink, link);

        newLink.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const href = this.getAttribute('href');
            if (href && !href.includes('javascript:void')) {
                const pageMatch = href.match(/[?&]page=(\d+)/);
                if (pageMatch) {
                    const page = pageMatch[1];
                    loadHistoryTable(page);
                } else {
                    loadHistoryTableWithUrl(href);
                }
            }
        });
    });
}

// ============================================
// EVENT LISTENERS FOR FILTERS
// ============================================
document.getElementById('searchPendingInput')?.addEventListener('keyup', function() {
    clearTimeout(pendingDebounceTimer);
    pendingDebounceTimer = setTimeout(() => loadPendingTable(1), 500);
});
document.getElementById('dateFromPendingInput')?.addEventListener('change', () => loadPendingTable(1));
document.getElementById('dateToPendingInput')?.addEventListener('change', () => loadPendingTable(1));
document.getElementById('resetPendingFiltersBtn')?.addEventListener('click', function() {
    document.getElementById('searchPendingInput').value = '';
    document.getElementById('dateFromPendingInput').value = '';
    document.getElementById('dateToPendingInput').value = '';
    loadPendingTable(1);
});

document.getElementById('searchHistoryInput')?.addEventListener('keyup', function() {
    clearTimeout(historyDebounceTimer);
    historyDebounceTimer = setTimeout(() => loadHistoryTable(1), 500);
});
document.getElementById('dateFromHistoryInput')?.addEventListener('change', () => loadHistoryTable(1));
document.getElementById('dateToHistoryInput')?.addEventListener('change', () => loadHistoryTable(1));
document.getElementById('resetHistoryFiltersBtn')?.addEventListener('click', function() {
    document.getElementById('searchHistoryInput').value = '';
    document.getElementById('dateFromHistoryInput').value = '';
    document.getElementById('dateToHistoryInput').value = '';
    loadHistoryTable(1);
});

// ============================================
// YEAR FILTER - REAL TIME UPDATE
// ============================================
document.getElementById('yearFilter')?.addEventListener('change', function() {
    currentYear = this.value;
    document.getElementById('selectedYearDisplay').textContent = currentYear;
    loadStats();
    loadPendingTable(1);
    loadHistoryTable(1);
});

// ============================================
// QUICK APPROVE FUNCTION
// ============================================
function quickApprove(permitId) {
    const activeTab = document.querySelector('#ePermitTab .nav-link.active');
    if (activeTab) localStorage.setItem('activePermitTab', activeTab.getAttribute('data-bs-target'));

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
            Swal.fire({ title: 'Inachakata...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch(`/teacher/e-permit/${permitId}/approve`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ comment: result.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Imefanikiwa!', data.message, 'success');
                    loadStats();
                    loadPendingTable(1);
                } else Swal.fire('Hitilafu!', data.message, 'error');
            })
            .catch(() => Swal.fire('Hitilafu!', 'Tafadhali jaribu tena', 'error'));
        }
    });
}

// ============================================
// QUICK REJECT FUNCTION
// ============================================
function quickReject(permitId) {
    const activeTab = document.querySelector('#ePermitTab .nav-link.active');
    if (activeTab) localStorage.setItem('activePermitTab', activeTab.getAttribute('data-bs-target'));

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
        inputValidator: (value) => { if (!value) return 'Sababu ya kukataa inahitajika!'; }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Inachakata...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch(`/teacher/e-permit/${permitId}/reject`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ reason: result.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Imefanikiwa!', data.message, 'success');
                    loadStats();
                    loadPendingTable(1);
                } else Swal.fire('Hitilafu!', data.message, 'error');
            })
            .catch(() => Swal.fire('Hitilafu!', 'Tafadhali jaribu tena', 'error'));
        }
    });
}

// ============================================
// REPORTS FUNCTIONS - FIXED
// ============================================
function loadReports() {
    const dateFrom = document.getElementById('reportDateFrom')?.value || '';
    const dateTo = document.getElementById('reportDateTo')?.value || '';
    const status = document.getElementById('reportStatus')?.value || 'all';

    // Show loading
    Swal.fire({
        title: 'Loading...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(`/teacher/e-permit/reports/data?date_from=${dateFrom}&date_to=${dateTo}&status=${status}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        const tbody = document.querySelector('#reportsTable tbody');
        if (!tbody) return;

        if (data.success === true && data.data && data.data.length > 0) {
            tbody.innerHTML = data.data.map(permit => `
                <tr>
                    <td><strong>${permit.permit_number.toUpperCase()}</strong></td>
                    <td>${permit.student_name}<br><small class="text-muted">${permit.admission_number.toUpperCase()}</small></td>
                    <td>${permit.class_name}</td>
                    <td>${permit.guardian_name}<br><small>${permit.guardian_phone}</small></td>
                    <td>${permit.departure_date}</td>
                    <td>${permit.expected_return_date}</td>
                    <td><span class="badge bg-${permit.status == 'approved' ? 'success' : (permit.status == 'rejected' ? 'danger' : 'info')}">${permit.status.charAt(0).toUpperCase() + permit.status.slice(1)}</span></td>
                    <td>${permit.created_date}</td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No records found</td></tr>';
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error loading reports:', error);
        Swal.fire('Error', 'Failed to load reports. Please try again.', 'error');
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
    const d = new Date();
    window.open(`/teacher/e-permit/reports/export-pdf?date_from=${document.getElementById('reportDateFrom')?.value || ''}&date_to=${document.getElementById('reportDateTo')?.value || ''}&status=${document.getElementById('reportStatus')?.value || 'all'}&_t=${d.getTime()}`, '_blank');
});
document.getElementById('exportExcelBtn')?.addEventListener('click', function() {
    const d = new Date();
    window.open(`/teacher/e-permit/reports/export-excel?date_from=${document.getElementById('reportDateFrom')?.value || ''}&date_to=${document.getElementById('reportDateTo')?.value || ''}&status=${document.getElementById('reportStatus')?.value || 'all'}&_t=${d.getTime()}`, '_blank');
});
document.querySelector('#ePermitTab button[data-bs-target="#reports"]')?.addEventListener('shown.bs.tab', loadReports);

// ============================================
// STUDENT RETURN FUNCTIONS - FIXED
// ============================================
document.getElementById('searchReturnBtn')?.addEventListener('click', function() {
    const search = document.getElementById('returnSearchInput')?.value.trim();
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

    fetch(`/teacher/e-permit/return/search?search=${encodeURIComponent(search)}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        if (data.success) {
            displayReturnInfo(data.permit);
        } else {
            Swal.fire('Taarifa', data.message || 'Student not found', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Search error:', error);
        Swal.fire('Hitilafu', 'Tafadhali jaribu tena. Hakikisha umeingiza taarifa sahihi.', 'error');
    });
});

function populateReturnRelationships() {
    const guardianType = document.getElementById('returnGuardianType');
    const relationship = document.getElementById('returnRelationship');
    if (!guardianType || !relationship) return;
    const selectedType = guardianType.value;
    let options = '<option value="">-- Chagua Uhusiano --</option>';
    if (selectedType === 'parent') { ['Baba', 'Mama'].forEach(rel => options += `<option value="${rel.toLowerCase()}">${rel}</option>`); }
    else if (selectedType === 'guardian') { ['Dada', 'Kaka', 'Shangazi', 'Mjomba', 'Babu', 'Bibi'].forEach(rel => options += `<option value="${rel.toLowerCase()}">${rel}</option>`); }
    relationship.innerHTML = options;
}

function displayReturnInfo(permit) {
    const container = document.getElementById('returnResultContainer');
    const tableContainer = document.getElementById('returnTableContainer');
    if (!container || !tableContainer) return;

    const departureDate = new Date(permit.departure_date);
    const expectedReturnDate = new Date(permit.expected_return_date);
    const formattedDeparture = departureDate.toLocaleDateString('sw-TZ', { day: 'numeric', month: 'long', year: 'numeric' });
    const formattedReturn = expectedReturnDate.toLocaleDateString('sw-TZ', { day: 'numeric', month: 'long', year: 'numeric' });
    const isLateHtml = permit.is_late ? '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Amechelewa</span>' : '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Kwa Wakati</span>';
    const studentImage = permit.student.image ? `/storage/students/${permit.student.image}` : '{{ asset("storage/students/student.jpg") }}';

    container.innerHTML = `
        <div class="student-info-card" style="background: white; border-radius: 16px; padding: 20px; margin-top: 20px; border: 1px solid #e2e8f0;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: -20px -20px 20px -20px; padding: 15px 20px; border-radius: 16px 16px 0 0;">
                <h5 class="mb-0 text-white"><i class="fas fa-undo-alt me-2"></i> Thibitisha Kurudi kwa Mwanafunzi</h5>
            </div>
            <div class="row">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="${studentImage}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #667eea;" onerror="this.src='{{ asset("storage/students/student.jpg") }}'" alt="Student">
                    <div class="mt-2"><span class="badge bg-primary">${permit.permit_number}</span></div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6 mb-2"><strong>Jina:</strong> ${permit.student.name.toUpperCase()}</div>
                        <div class="col-md-6 mb-2"><strong>Student ID:</strong> ${permit.student.admission_number.toUpperCase()}</div>
                        <div class="col-md-6 mb-2"><strong>Darasa:</strong> ${permit.student.class.toUpperCase()}</div>
                        <div class="col-md-6 mb-2"><strong>Tarehe ya Kuondoka:</strong> ${formattedDeparture}</div>
                        <div class="col-md-6 mb-2"><strong>Tarehe ya Kurudi:</strong> ${formattedReturn} ${isLateHtml}</div>
                        <div class="col-md-6 mb-2"><strong>Mzazi/Mlezi:</strong> ${permit.guardian_name.toUpperCase()} (${permit.guardian_phone})</div>
                    </div>
                </div>
            </div>
            <hr>
            <form id="returnConfirmForm">
                <input type="hidden" name="permit_id" value="${permit.id}">
                <input type="hidden" name="is_late" value="${permit.is_late}">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold">Je, mtoto amerudi peke yake?</label>
                        <div class="d-flex gap-4">
                            <div class="form-check"><input type="radio" name="returned_alone" value="1" class="form-check-input" id="return_alone_yes" checked><label class="form-check-label" for="return_alone_yes">Ndiyo, amerudi peke yake</label></div>
                            <div class="form-check"><input type="radio" name="returned_alone" value="0" class="form-check-input" id="return_alone_no"><label class="form-check-label" for="return_alone_no">Hapana, amerudi na mtu</label></div>
                        </div>
                    </div>
                </div>
                <div id="returnAccompaniedFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="fw-bold">Jina la aliyemrudisha</label><input type="text" name="accompanied_by_name" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="fw-bold">Undugu</label><select name="guardian_type" id="returnGuardianType" class="form-control"><option value="">-- Chagua --</option><option value="parent">Mzazi</option><option value="guardian">Mlezi</option></select></div>
                        <div class="col-md-3 mb-3"><label class="fw-bold">Uhusiano</label><select name="relationship" id="returnRelationship" class="form-control"><option value="">-- Chagua --</option></select></div>
                    </div>
                </div>
                <div id="returnLateReasonField" style="display: ${permit.is_late ? 'block' : 'none'};">
                    <div class="mb-3"><label class="fw-bold">Sababu ya kuchelewa kurudi</label><textarea name="late_reason" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="fas fa-check-circle me-2"></i> Thibitisha Kurudi</button>
                    <button type="button" id="cancelReturnBtn" class="btn btn-secondary"><i class="fas fa-times me-2"></i> Ghairi</button>
                </div>
            </form>
        </div>
    `;

    container.style.display = 'block';
    tableContainer.style.display = 'none';

    document.getElementById('returnGuardianType')?.addEventListener('change', populateReturnRelationships);
    document.getElementById('cancelReturnBtn')?.addEventListener('click', () => {
        container.style.display = 'none';
        tableContainer.style.display = 'block';
        document.getElementById('returnSearchInput').value = '';
    });
    document.getElementById('return_alone_yes')?.addEventListener('change', () => document.getElementById('returnAccompaniedFields').style.display = 'none');
    document.getElementById('return_alone_no')?.addEventListener('change', () => document.getElementById('returnAccompaniedFields').style.display = 'block');

    document.getElementById('returnConfirmForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const returnedAlone = document.querySelector('input[name="returned_alone"]:checked')?.value;

        if (returnedAlone === '0') {
            const accompaniedByName = document.querySelector('input[name="accompanied_by_name"]')?.value.trim();
            const guardianType = document.querySelector('select[name="guardian_type"]')?.value;
            const relationship = document.querySelector('select[name="relationship"]')?.value;
            if (!accompaniedByName) { Swal.fire('Taarifa', 'Tafadhali ingiza jina la aliyemrudisha', 'warning'); return; }
            if (!guardianType) { Swal.fire('Taarifa', 'Tafadhali chagua undugu', 'warning'); return; }
            if (!relationship) { Swal.fire('Taarifa', 'Tafadhali chagua uhusiano', 'warning'); return; }
        }

        const formData = new FormData(this);
        Swal.fire({
            title: 'Thibitisha Kurudi',
            text: 'Je, una hakika mwanafunzi amerudi shuleni?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            confirmButtonText: 'Ndiyo',
            cancelButtonText: 'Hapana'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Inachakata...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                fetch(`/teacher/e-permit/return/${permit.id}/confirm`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) Swal.fire('Imefanikiwa!', data.message, 'success').then(() => location.reload());
                    else Swal.fire('Hitilafu!', data.message, 'error');
                })
                .catch(() => Swal.fire('Hitilafu!', 'Tafadhali jaribu tena', 'error'));
            }
        });
    });
}

// Attach initial pagination handlers
setTimeout(() => {
    attachPendingPaginationHandlers();
    attachHistoryPaginationHandlers();
}, 100);
</script>
@endsection
