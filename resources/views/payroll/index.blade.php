{{-- resources/views/payroll/index.blade.php --}}

@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
            --dark-color: #5a5c69;
        }

        .stat-card {
            border-radius: 15px;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            min-height: 130px;
            cursor: pointer;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
        }

        .stat-card.active {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
            filter: brightness(1.05);
        }

        .stat-card .card-body {
            position: relative;
            z-index: 2;
            padding: 1.2rem;
        }

        .stat-card .card-icon {
            position: absolute;
            right: 15px;
            bottom: 15px;
            opacity: 0.2;
            font-size: 3.5rem;
        }

        .stat-card .card-title {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            opacity: 0.9;
        }

        .stat-card .card-value {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 0;
        }

        .bg-primary-custom {
            background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
        }

        .bg-success-custom {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }

        .bg-warning-custom {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }

        .bg-info-custom {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }

        .filter-bar {
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-draft {
            background: #f8f9fc;
            color: #5a5c69;
            border: 1px solid #e3e6f0;
        }

        .status-calculated {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-finalized {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .bg-locked {
            background: #ffebee;
            color:#c62828;
        }

        .status-cancelled {
            background: #ffebee;
            color: #dcb21bfa;
        }

        .table-payroll th {
            background: #f8f9fc;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #5a5c69;
            border-bottom: 2px solid #e3e6f0;
        }

        .table-payroll td {
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .action-btns {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .btn-view {
            background: #e3f2fd;
            color: #1976d2;
        }

        .btn-view:hover {
            background: #1976d2;
            color: white;
        }

        .btn-download {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .btn-download:hover {
            background: #2e7d32;
            color: white;
        }

        .btn-export {
            background: #fff3e0;
            color: #ed6c02;
        }

        .btn-export:hover {
            background: #ed6c02;
            color: white;
        }

        .btn-calculate {
            background: #ffc107;
            color: #856404;
        }

        .btn-calculate:hover {
            background: #e0a800;
            color: white;
        }

        .btn-finalize {
            background: #28a745;
            color: white;
        }

        .btn-finalize:hover {
            background: #218838;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            color: white;
            transform: translateY(-2px);
        }

        .pagination-custom {
            margin-top: 20px;
        }

        .pagination-custom .page-link {
            border-radius: 8px;
            margin: 0 3px;
            color: var(--primary-color);
        }

        .pagination-custom .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #d1d3e2;
            margin-bottom: 20px;
        }

        .loading-overlay {
            position: relative;
            min-height: 200px;
        }

        .loading-spinner-table {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
        }

        .table-container {
            transition: opacity 0.3s ease;
        }

        .table-container.loading {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="fas fa-calculator mr-2 text-primary"></i> Payroll Management
                        </h4>
                        <p class="text-muted mb-0">Manage and track all payroll batches</p>
                    </div>
                    <a href="{{ route('payroll.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i> Generate Payroll
                    </a>
                </div>

                {{-- Statistics Cards --}}
                <div class="row mb-4" id="statistics-container">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card bg-primary-custom text-white" data-filter-status="all">
                            <div class="card-body">
                                <div class="card-title">Total Payrolls</div>
                                <div class="card-value" id="stat-total">0</div>
                            </div>
                            <i class="fas fa-calculator card-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card bg-success-custom text-white" data-filter-status="finalized">
                            <div class="card-body">
                                <div class="card-title">Finalized</div>
                                <div class="card-value" id="stat-finalized">0</div>
                            </div>
                            <i class="fas fa-check-circle card-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card bg-warning-custom text-white" data-filter-status="draft">
                            <div class="card-body">
                                <div class="card-title">Draft</div>
                                <div class="card-value" id="stat-draft">0</div>
                            </div>
                            <i class="fas fa-pen-fancy card-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card bg-info-custom text-white" data-filter-status="calculated">
                            <div class="card-body">
                                <div class="card-title">Calculated</div>
                                <div class="card-value" id="stat-calculated">0</div>
                            </div>
                            <i class="fas fa-dollar-sign card-icon"></i>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="filter-bar">
                    <div class="row align-items-center">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <select id="filter-status" class="form-control form-control-sm">
                                <option value="">All Status</option>
                                <option value="draft">Draft</option>
                                <option value="calculated">Calculated</option>
                                <option value="finalized">Finalized</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <input type="text" id="filter-year" class="form-control form-control-sm"
                                placeholder="Year (e.g., 2024)">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <input type="text" id="filter-search" class="form-control form-control-sm"
                                placeholder="Search by batch number or name">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm w-100"
                                    id="reset-filters">
                                    <i class="fas fa-times mr-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payroll Table Container --}}
                <div class="card" id="table-container">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-payroll mb-0">
                                <thead>
                                    <th class="ps-3">#</th>
                                    <th>Batch Number</th>
                                    <th>Name</th>
                                    <th>Month</th>
                                    <th class="text-center">Employees</th>
                                    <th class="text-end">Gross Salary</th>
                                    <th class="text-end">Net Salary</th>
                                    <th class="text-center">Status</th>
                                    <th class="">Issued By</th>
                                    <th class="text-center">Actions</th>
                                </thead>
                                <tbody id="payroll-table-body">
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 text-muted">Loading payroll data...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pagination Container --}}
            <div id="pagination-container" class="pagination-custom d-flex justify-content-end mt-3"></div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ==================== GLOBAL VARIABLES ====================
        let currentPage = 1;
        let isLoading = false;

        // ==================== HELPER FUNCTIONS ====================
        function showLoadingAlert(message) {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function showSuccessAlert(message, reload = true) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            }).then(() => {
                if (reload) loadPayrollData();
            });
        }

        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }

        function showConfirmAlert(title, text, confirmText, callback) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }

        // ==================== FETCH PAYROLL DATA ====================
        async function loadPayrollData() {
            if (isLoading) return;
            isLoading = true;

            const status = document.getElementById('filter-status').value;
            const year = document.getElementById('filter-year').value;
            const search = document.getElementById('filter-search').value;

            // Show loading on table
            const tableBody = document.getElementById('payroll-table-body');
            if (tableBody) {
                tableBody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading payroll data...</p>
                    </td>
                </tr>
            `;
            }

            try {
                // Build URL with query parameters
                let url = `{{ route('payroll.data') }}?page=${currentPage}`;
                if (status) url += `&status=${encodeURIComponent(status)}`;
                if (year) url += `&year=${encodeURIComponent(year)}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Update statistics
                    updateStatistics(data.statistics);

                    // Update table
                    updatePayrollTable(data.batches.data || []);

                    // Update pagination
                    updatePagination(data.batches);

                    // Update URL without reload
                    updateUrlParams(status, year, search);
                } else {
                    showErrorAlert(data.message || 'Failed to load payroll data');
                }
            } catch (error) {
                console.error('Error loading payroll data:', error);
                showErrorAlert('Connection error: ' + error.message);
            } finally {
                isLoading = false;
            }
        }

        // ==================== UPDATE STATISTICS ====================
        function updateStatistics(statistics) {
            const statTotal = document.getElementById('stat-total');
            const statFinalized = document.getElementById('stat-finalized');
            const statDraft = document.getElementById('stat-draft');
            const statCalculated = document.getElementById('stat-calculated');

            if (statTotal) statTotal.textContent = formatNumber(statistics.total_batches || 0);
            if (statFinalized) statFinalized.textContent = formatNumber(statistics.finalized_count || 0);
            if (statDraft) statDraft.textContent = formatNumber(statistics.draft_count || 0);
            if (statCalculated) statCalculated.textContent = formatNumber(statistics.calculated || 0);
        }

        // ==================== UPDATE PAYROLL TABLE ====================
        function updatePayrollTable(batches) {
            const tableBody = document.getElementById('payroll-table-body');

            if (!tableBody) return;

            if (!batches || batches.length === 0) {
                tableBody.innerHTML = `
                <tr>
                    <td colspan="10" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5 class="mt-2">No Payroll Batches Found</h5>
                    </td>
                </tr>
            `;
                return;
            }

            let html = '';
            batches.forEach((batch, index) => {
                const statusClass = {
                    'draft': 'status-draft',
                    'calculated': 'status-calculated',
                    'finalized': 'status-finalized',
                    'cancelled': 'status-cancelled'
                } [batch.status] || 'status-draft';

                const isLocked = batch.is_locked == 0 ? 'Open' : 'Closed';
                const LockedClass = batch.is_locked ==  0 ? 'bg-success-custom' : 'bg-locked';
                const lockedIcon = batch.is_locked == 0 ? 'fa-lock-open' : 'fa-lock';

                const statusIcon = batch.status == 'finalized' ? 'fa-check-circle' :
                    (batch.status == 'calculated' ? 'fa-calculator' :
                        (batch.status == 'draft' ? 'fa-pen' : 'fa-times-circle'));

                // Build URLs using Laravel's url helper
                const viewUrl = `{{ url('/payroll') }}/${batch.hash}`;
                const downloadSlipsUrl = `{{ url('/payroll') }}/${batch.hash}/download-slips`;
                const downloadSummaryUrl = `{{ url('/payroll') }}/${batch.hash}/download-summary`;

                html += `
                <tr>
                    <td class="ps-3 fw-semibold">${(currentPage - 1) * 15 + index + 1}</td>
                    <td><span class="fw-semibold">${escapeHtml(batch.batch_number)}</span></td>
                    <td>${escapeHtml(batch.name)}</td>
                    <td><i class="far fa-calendar-alt mr-1 text-muted"></i> ${formatMonth(batch.payroll_month)}</td>
                    <td class="text-center"><span class="badge bg-light text-dark">${batch.payroll_employees_count || 0}</span></td>
                    <td class="text-end"><span class="fw-medium">${formatNumber(batch.summary?.total_gross_salary || 0)}</span></td>
                    <td class="text-end"><span class="fw-bold text-success">${formatNumber(batch.summary?.total_net_salary || 0)}</span></td>
                    <td class="text-center">
                        <span class="status-badge ${statusClass}">
                            <i class="fas ${statusIcon} mr-1"></i> ${ucfirst(batch.status)}
                        </span>
                        <span class="badge ${LockedClass}">
                            <i class="fas ${lockedIcon}"></i>
                                ${isLocked}
                        </span>
                    </td>
                    <td>${ucwords(batch.generated_by || 'System')}</td>
                    <td class="text-center">
                        <div class="action-btns">
                            <a href="${viewUrl}" class="action-btn btn-view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                `;

                // Draft status buttons
                if (batch.status == 'draft') {
                    html += `
                            <button type="button" class="action-btn btn-calculate" onclick="calculatePayroll('${batch.hash}')" title="Calculate Payroll">
                                <i class="fas fa-calculator"></i>
                            </button>
                            <button type="button" class="action-btn btn-danger" onclick="deletePayroll('${batch.hash}')" title="Delete Payroll">
                                <i class="fas fa-trash"></i>
                            </button>
                `;
                }

                // Calculated status buttons
                if (batch.status == 'calculated') {
                    html += `
                            <button type="button" class="action-btn btn-finalize" onclick="finalizePayroll('${batch.hash}')" title="Finalize Payroll">
                                <i class="fas fa-check-circle"></i>
                            </button>
                            <button type="button" class="action-btn btn-danger" onclick="deletePayroll('${batch.hash}')" title="Delete Payroll">
                                <i class="fas fa-trash"></i>
                            </button>
                `;
                }

                // Finalized status buttons
                if (batch.status == 'finalized') {
                    html += `
                            <a href="${downloadSlipsUrl}" class="action-btn btn-download" title="Download Slips">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="${downloadSummaryUrl}" class="action-btn btn-export" title="Download Payroll">
                                <i class="fas fa-file-excel"></i>
                            </a>
                `;
                }

                html += `
                        </div>
                    </td>
                </tr>
            `;
            });

            tableBody.innerHTML = html;
        }

        // ==================== UPDATE PAGINATION ====================
        function updatePagination(pagination) {
            const container = document.getElementById('pagination-container');

            if (!container) return;

            if (!pagination || pagination.last_page <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<nav><ul class="pagination">';

            // Previous button
            html += `<li class="page-item ${pagination.current_page == 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                 </li>`;

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                html += `<li class="page-item ${pagination.current_page == i ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                     </li>`;
            }

            // Next button
            html += `<li class="page-item ${pagination.current_page == pagination.last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                 </li>`;

            html += '</ul></nav>';
            container.innerHTML = html;

            // Attach pagination click handlers
            container.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(link.dataset.page);
                    if (page && !isNaN(page) && page !== currentPage) {
                        currentPage = page;
                        loadPayrollData();
                    }
                });
            });
        }

        // ==================== UPDATE URL PARAMS ====================
        function updateUrlParams(status, year, search) {
            const url = new URL(window.location.href);
            if (status) url.searchParams.set('status', status);
            else url.searchParams.delete('status');
            if (year) url.searchParams.set('year', year);
            else url.searchParams.delete('year');
            if (search) url.searchParams.set('search', search);
            else url.searchParams.delete('search');
            url.searchParams.set('page', currentPage);

            window.history.pushState({}, '', url.toString());
        }

        // ==================== HELPER FORMAT FUNCTIONS ====================
        function formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        }

        function formatMonth(monthStr) {
            if (!monthStr) return 'N/A';
            try {
                const [year, month] = monthStr.split('-');
                const date = new Date(year, month - 1, 1);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long'
                });
            } catch (e) {
                return monthStr;
            }
        }

        function ucfirst(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function ucwords(str) {
            if (!str) return '';
            return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // ==================== EVENT LISTENERS ====================
        function setupEventListeners() {
            // Real-time filter changes
            const filterStatus = document.getElementById('filter-status');
            const filterYear = document.getElementById('filter-year');
            const filterSearch = document.getElementById('filter-search');

            const debouncedLoad = debounce(() => {
                currentPage = 1;
                loadPayrollData();
            }, 1000);

            if (filterStatus) {
                filterStatus.addEventListener('change', () => {
                    currentPage = 1;
                    loadPayrollData();
                });
            }

            if (filterYear) {
                filterYear.addEventListener('input', debouncedLoad);
            }

            if (filterSearch) {
                filterSearch.addEventListener('input', debouncedLoad);
            }

            // Reset filters button
            const resetBtn = document.getElementById('reset-filters');
            if (resetBtn) {
                resetBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (filterStatus) filterStatus.value = '';
                    if (filterYear) filterYear.value = '';
                    if (filterSearch) filterSearch.value = '';
                    currentPage = 1;
                    loadPayrollData();
                });
            }

            // Stat cards click - filter by status
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('click', () => {
                    const filterStatusValue = card.dataset.filterStatus;
                    if (filterStatusValue && filterStatusValue !== 'all' && filterStatus) {
                        filterStatus.value = filterStatusValue;
                        currentPage = 1;
                        loadPayrollData();
                    } else if (filterStatusValue === 'all' && filterStatus) {
                        filterStatus.value = '';
                        currentPage = 1;
                        loadPayrollData();
                    }
                });
            });
        }

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // ==================== API ACTIONS ====================
        window.calculatePayroll = function(hash) {
            showConfirmAlert(
                'Calculate Payroll?',
                'This will compute PAYE, NSSF, and net salaries for all employees.',
                'Yes, Calculate!',
                () => {
                    showLoadingAlert('Calculating payroll...');

                    fetch(`{{ url('/payroll') }}/${hash}/calculate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                showSuccessAlert('Payroll calculated successfully!');
                            } else {
                                showErrorAlert(data.message || 'Calculation failed');
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Connection error: ' + error.message);
                        });
                }
            );
        };

        window.finalizePayroll = function(hash) {
            showConfirmAlert(
                'Finalize Payroll?',
                '⚠️ WARNING: This action cannot be undone.',
                'Yes, Finalize!',
                () => {
                    showLoadingAlert('Finalizing payroll...');

                    fetch(`{{ url('/payroll') }}/${hash}/finalize`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                showSuccessAlert('Payroll finalized successfully!');
                            } else {
                                showErrorAlert(data.message || 'Finalization failed');
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Connection error: ' + error.message);
                        });
                }
            );
        };

        window.deletePayroll = function(hash) {
            showConfirmAlert(
                'Delete Payroll?',
                '⚠️ WARNING: This action cannot be undone.',
                'Yes, Delete!',
                () => {
                    showLoadingAlert('Deleting payroll...');

                    fetch(`{{ url('/payroll') }}/${hash}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Payroll has been deleted successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadPayrollData();
                                });
                            } else {
                                showErrorAlert(data.message || 'Delete failed');
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            showErrorAlert('Connection error: ' + error.message);
                        });
                }
            );
        };

        // ==================== INITIALIZATION ====================
        document.addEventListener('DOMContentLoaded', () => {
            // Get initial filter values from URL
            const urlParams = new URLSearchParams(window.location.search);

            const filterStatus = document.getElementById('filter-status');
            const filterYear = document.getElementById('filter-year');
            const filterSearch = document.getElementById('filter-search');

            if (filterStatus) filterStatus.value = urlParams.get('status') || '';
            if (filterYear) filterYear.value = urlParams.get('year') || '';
            if (filterSearch) filterSearch.value = urlParams.get('search') || '';

            currentPage = parseInt(urlParams.get('page')) || 1;

            setupEventListeners();
            loadPayrollData();
        });
    </script>
@endsection
