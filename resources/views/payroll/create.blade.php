{{-- resources/views/payroll/create.blade.php --}}

@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f94144;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* ==================== METHOD CARDS ==================== */
        .method-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
        }

        .method-card:hover {
            border-color: #4e73df;
            background: #f8f9fc;
        }

        .method-card.active {
            border-color: #4e73df;
            background: linear-gradient(135deg, #f8f9fc 0%, #eef2ff 100%);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.2);
        }

        .method-card input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            accent-color: #4e73df;
        }

        /* ==================== SECTION CARDS ==================== */
        .section-card {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #4e73df;
        }

        /* ==================== EMPLOYEE TABLE PREVIEW ==================== */
        .employee-preview-table {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
        }

        .employee-preview-table table {
            font-size: 0.85rem;
            margin-bottom: 0;
        }

        .employee-preview-table thead {
            position: sticky;
            top: 0;
            background: #f8f9fc;
            z-index: 10;
        }

        .employee-preview-table th {
            background: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
        }

        /* ==================== BUTTONS ==================== */
        .btn-preview {
            background: #eef2ff;
            color: #4e73df;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.3s;
        }

        .btn-preview:hover {
            background: #4e73df;
            color: white;
        }

        .btn-confirm {
            background: #28a745;
            color: white;
            border-radius: 8px;
            padding: 8px 20px;
        }

        .btn-confirm:hover {
            background: #218838;
        }

        /* ==================== LOADING ==================== */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #4e73df;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ==================== MANUAL EMPLOYEE ROW ==================== */
        .manual-employee-row {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            position: relative;
            transition: all 0.2s;
        }

        .manual-employee-row:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .remove-employee-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .remove-employee-btn:hover {
            color: #bd2130;
        }

        /* ==================== SCHEDULE PREVIEW ==================== */
        .selected-schedule-card {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }

        /* ==================== TOAST NOTIFICATION ==================== */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease;
        }

        .toast-notification.toast-error {
            background: #dc3545;
        }

        .toast-notification.toast-warning {
            background: #ffc107;
            color: #333;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Additional styles for preview tables */
        .preview-table-container {
            max-height: 500px;
            overflow-y: auto;
            margin-top: 15px;
        }

        .preview-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .preview-table th {
            background: #f1f5f9;
            padding: 10px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .preview-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .preview-table .text-end {
            text-align: right;
        }

        .summary-stats {
            background: #f8fafc;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .summary-item {
            font-size: 13px;
        }

        .summary-item strong {
            color: #1a3e6f;
        }

        .confirm-section {
            margin-top: 15px;
            text-align: right;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 fw-bold">
                                <i class="fas fa-plus-circle me-2 text-primary"></i> Generate New Payroll
                            </h4>
                            <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        <form action="{{ route('payroll.generate') }}" method="POST" enctype="multipart/form-data"
                            id="payrollForm">
                            @csrf

                            {{-- MONTH SELECTION --}}
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-calendar-alt me-1 text-primary"></i> Payroll Month
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="month" name="month" id="month" class="form-control"
                                        value="{{ old('month', date('Y-m')) }}" required>
                                    <small class="text-muted">Select the month for this payroll</small>
                                </div>
                            </div>

                            {{-- GENERATION METHOD CARDS --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold mb-3">
                                        <i class="fas fa-cogs me-1 text-primary"></i> Generation Method
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="method-card" data-method="contracts">
                                                <div class="d-flex align-items-start">
                                                    <input type="radio" name="generation_method" id="method_contracts"
                                                        value="contracts" checked>
                                                    <div class="ms-2">
                                                        <div class="fw-bold">
                                                            <i class="fas fa-file-contract text-primary me-1"></i> From
                                                            Contracts
                                                        </div>
                                                        <small class="text-muted">Pull data from active contracts</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="method-card" data-method="excel">
                                                <div class="d-flex align-items-start">
                                                    <input type="radio" name="generation_method" id="method_excel"
                                                        value="excel_upload">
                                                    <div class="ms-2">
                                                        <div class="fw-bold">
                                                            <i class="fas fa-file-excel text-success me-1"></i> Upload Excel
                                                        </div>
                                                        <small class="text-muted">Upload custom employee data</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="method-card" data-method="previous">
                                                <div class="d-flex align-items-start">
                                                    <input type="radio" name="generation_method" id="method_previous"
                                                        value="previous_batch">
                                                    <div class="ms-2">
                                                        <div class="fw-bold">
                                                            <i class="fas fa-history text-warning me-1"></i> Previous
                                                            Schedule
                                                        </div>
                                                        <small class="text-muted">Copy from previous payroll</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="method-card" data-method="manual">
                                                <div class="d-flex align-items-start">
                                                    <input type="radio" name="generation_method" id="method_manual"
                                                        value="manual_entry">
                                                    <div class="ms-2">
                                                        <div class="fw-bold">
                                                            <i class="fas fa-keyboard text-secondary me-1"></i> Manual Entry
                                                        </div>
                                                        <small class="text-muted">Add employees manually</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ==================== SECTION 1: CONTRACTS MODE ==================== --}}
                            <div id="contracts_section" class="section-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="fas fa-users text-primary me-1"></i> Employees with Active Contracts
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="fetchContractsBtn">
                                        <i class="fas fa-sync-alt me-1"></i> Sync Contracts Data
                                    </button>
                                </div>
                                <div id="contractsLoading" class="text-center py-3" style="display: none;">
                                    <div class="loading-spinner"></div> Fetching employees from contracts...
                                </div>
                                <div id="contractsResultContainer">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Click "Fetch Contracts Data" to load employees contracts.
                                    </div>
                                </div>
                                <input type="hidden" name="contracts_data" id="contracts_data" value="">
                            </div>

                            {{-- ==================== SECTION 2: EXCEL UPLOAD MODE ==================== --}}
                            <div id="excel_section" class="section-card" style="display: none;">
                                <h6 class="mb-3">
                                    <i class="fas fa-file-excel text-success me-1"></i> Upload Excel File
                                </h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="file" name="excel_file" id="excel_file" class="form-control"
                                            accept=".xlsx,.xls,.csv">
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Upload Excel file with columns: <strong>staff_id, basic_salary, allowances,
                                                department, contract_type</strong>
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="#" class="btn btn-sm btn-outline-success"
                                            id="downloadTemplateBtn">
                                            <i class="fas fa-download me-1"></i> Download Template
                                        </a>
                                    </div>
                                </div>
                                <div id="excelPreviewContainer" class="mt-3" style="display: none;"></div>
                                <input type="hidden" name="excel_data" id="excel_data" value="">
                            </div>

                            {{-- ==================== SECTION 3: PREVIOUS SCHEDULE MODE ==================== --}}
                            <div id="previous_section" class="section-card" style="display: none;">
                                <h6 class="mb-3">
                                    <i class="fas fa-history text-warning me-1"></i> Select Previous Payroll
                                </h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <select name="previous_batch_id" id="previous_batch_id" class="form-select">
                                            <option value="">-- Select Payroll --</option>
                                            @forelse($previousSchedules as $schedule)
                                                <option value="{{ $schedule['id'] }}" data-name="{{ $schedule['name'] }}"
                                                    data-month="{{ $schedule['month_name'] ?? $schedule['payroll_month'] }}"
                                                    data-employees="{{ $schedule['total_employees'] ?? 0 }}"
                                                    data-total="{{ $schedule['total_net'] ?? 0 }}"
                                                    data-date="{{ $schedule['generated_at'] ?? '' }}">
                                                    {{ $schedule['name'] }} -
                                                    {{ $schedule['month_name'] ?? $schedule['payroll_month'] }}
                                                    {{-- ({{ $schedule['total_employees'] ?? 0 }} employees) --}}
                                                </option>
                                            @empty
                                                <option value="" disabled> No previous payrolls found</option>
                                            @endforelse
                                        </select>
                                        <small class="text-muted">Select a previous payroll to view details</small>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-sm btn-primary w-100 text-white"
                                            id="loadScheduleBtn" disabled>
                                            <i class="fas fa-sync-alt me-1"></i> Sync schedule data
                                        </button>
                                    </div>
                                </div>
                                <div id="schedulePreviewContainer" class="mt-3" style="display: none;"></div>
                                <div id="scheduleConfirmContainer" class="mt-3" style="display: none;">
                                    <button type="button" class="btn btn-success btn-xs" id="confirmScheduleBtn">
                                        <i class="fas fa-check me-1"></i> Confirm
                                    </button>
                                </div>
                            </div>

                            {{-- ==================== SECTION 4: MANUAL ENTRY MODE ==================== --}}
                            {{-- <div id="manual_section" class="section-card" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user-plus text-secondary me-1"></i> Add Employees Manually
                                    </h6>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2"
                                            id="showManualFormBtn">
                                            <i class="fas fa-plus-circle me-1"></i> Add New Employee
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" id="searchEmployeeBtn">
                                            <i class="fas fa-search me-1"></i> Search Employee
                                        </button>
                                    </div>
                                </div>

                                <div id="manualAddForm" class="card border-primary mb-3" style="display: none;">
                                    <div class="card-header bg-primary text-white py-2">
                                        <strong><i class="fas fa-user-plus me-1"></i> Add Employee Manually</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <label class="small fw-bold">Staff ID <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="manual_staff_id"
                                                    class="form-control form-control-sm" placeholder="e.g., TCH001">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="small fw-bold">Employee Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="manual_name"
                                                    class="form-control form-control-sm" placeholder="Full name">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="small fw-bold">Staff Type</label>
                                                <select id="manual_staff_type" class="form-select form-select-sm">
                                                    <option value="teacher">Teacher</option>
                                                    <option value="transport">Transport Staff</option>
                                                    <option value="other_staff">Other Staff</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="small fw-bold">Basic Salary (TZS)</label>
                                                <input type="number" id="manual_basic_salary"
                                                    class="form-control form-control-sm" placeholder="e.g., 800000"
                                                    value="0">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="small fw-bold">Allowances (TZS)</label>
                                                <input type="number" id="manual_allowances"
                                                    class="form-control form-control-sm" placeholder="e.g., 100000"
                                                    value="0">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="small fw-bold">Department</label>
                                                <input type="text" id="manual_department"
                                                    class="form-control form-control-sm" placeholder="e.g., Science">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="small fw-bold">Contract Type</label>
                                                <select id="manual_contract_type" class="form-select form-select-sm">
                                                    <option value="new">New</option>
                                                    <option value="provision">Provision</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                id="addManualEmployeeBtn">
                                                <i class="fas fa-plus me-1"></i> Add Employee
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                id="cancelManualFormBtn">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="employeeSearchModal"
                                    style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999;">
                                    <div
                                        style="background: white; max-width: 600px; margin: 100px auto; border-radius: 12px; padding: 20px;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5><i class="fas fa-search text-primary me-1"></i> Search Employee</h5>
                                            <button type="button" class="btn-close"
                                                onclick="closeEmployeeSearch()"></button>
                                        </div>
                                        <input type="text" id="employeeSearchInput" class="form-control"
                                            placeholder="Enter Staff ID or Name (min 2 chars)">
                                        <div id="employeeSearchResults" class="mt-3"
                                            style="max-height: 300px; overflow-y: auto;"></div>
                                        <div class="mt-3 text-end">
                                            <button type="button" class="btn btn-secondary"
                                                onclick="closeEmployeeSearch()">Close</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="manualEmployeesContainer" class="mt-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        No employees added yet. Use "Add New Employee" or "Search Employee" to add
                                        employees.
                                    </div>
                                </div>
                                <div id="manualTableContainer" style="display: none;">
                                    <div class="preview-table-container">
                                        <table class="preview-table" id="manualPreviewTable">
                                            <thead>
                                                <th>#</th>
                                                <th>Staff ID</th>
                                                <th>Employee Name</th>
                                                <th>Staff Type</th>
                                                <th>Basic Salary</th>
                                                <th>Allowances</th>
                                                <th>Gross</th>
                                                <th>Department</th>
                                                <th>Contract Type</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </thead>
                                            </thead>
                                            <tbody id="manualTableBody"></tbody>
                                            < </table>
                                    </div>
                                    <div class="summary-stats" id="manualSummaryStats"></div>
                                    <div class="confirm-section">
                                        <button type="button" class="btn btn-success" id="confirmManualDataBtn">
                                            <i class="fas fa-check-circle me-1"></i> Confirm & Use Selected Employees
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="manual_employees" id="manual_employees" value="">
                            </div> --}}

                            {{-- ADVANCED FILTERS --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button class="btn btn-xs btn-link text-decoration-none" type="button"
                                        id="toggleFilters">
                                        <i class="fas fa-filter me-1"></i> Advanced Filters (Optional)
                                    </button>
                                </div>
                            </div>

                            <div id="filters_section" class="row mt-2" style="display: none;">
                                <div class="col-md-4">
                                    <label class="form-label">Staff Type</label>
                                    <select name="staff_type" id="staff_type" class="form-select">
                                        <option value="">All Staff Types</option>
                                        <option value="teacher">Teachers Only</option>
                                        <option value="transport">Transport Staff Only</option>
                                        <option value="other_staff">Other Staff Only</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" id="department" class="form-control"
                                        placeholder="e.g., Science, Mathematics">
                                </div>
                            </div>

                            {{-- SUBMIT BUTTON --}}
                            <div class="row mt-5">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                                        <i class="fas fa-cogs me-2"></i> Generate Payroll
                                    </button>
                                    <a href="{{ route('payroll.index') }}" class="btn btn-secondary px-5 ms-2">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // ==================== GLOBAL VARIABLES ====================
        let manualEmployeesList = [];
        let excelDataList = [];
        let selectedScheduleData = null;

        // ==================== HELPER FUNCTIONS ====================
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML =
                `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        }

        // ==================== METHOD SWITCHING ====================
        const methodCards = document.querySelectorAll('.method-card');
        const contractsSection = document.getElementById('contracts_section');
        const excelSection = document.getElementById('excel_section');
        const previousSection = document.getElementById('previous_section');
        const manualSection = document.getElementById('manual_section');

        function showSection(method) {
            if (contractsSection) contractsSection.style.display = 'none';
            if (excelSection) excelSection.style.display = 'none';
            if (previousSection) previousSection.style.display = 'none';
            if (manualSection) manualSection.style.display = 'none';
            if (method === 'contracts') contractsSection.style.display = 'block';
            else if (method === 'excel_upload') excelSection.style.display = 'block';
            else if (method === 'previous_batch') previousSection.style.display = 'block';
            else if (method === 'manual_entry') manualSection.style.display = 'block';
        }

        methodCards.forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    methodCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    showSection(radio.value);
                }
            });
        });

        document.querySelectorAll('input[name="generation_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const parentCard = this.closest('.method-card');
                methodCards.forEach(c => c.classList.remove('active'));
                if (parentCard) parentCard.classList.add('active');
                showSection(this.value);
            });
        });

        // ==================== CONTRACTS MODE ====================
        const fetchContractsBtn = document.getElementById('fetchContractsBtn');
        const contractsLoading = document.getElementById('contractsLoading');
        const contractsResultContainer = document.getElementById('contractsResultContainer');

        if (fetchContractsBtn) {
            fetchContractsBtn.addEventListener('click', async function() {
                const schoolId = {{ Auth::user()->school_id }};
                const staffType = document.getElementById('staff_type')?.value || '';
                const department = document.getElementById('department')?.value || '';

                contractsLoading.style.display = 'block';
                contractsResultContainer.innerHTML = '';

                try {
                    let url = '{{ route('api.employees.with-contracts') }}?school_id=' + schoolId;
                    if (staffType) url += '&staff_type=' + staffType;
                    if (department) url += '&department=' + department;

                    const response = await fetch(url, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Authorization': 'Bearer ' + '{{ session('finance_api_token') }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    contractsLoading.style.display = 'none';

                    if (data.success && data.data && data.data.length > 0) {
                        displayContractsData(data.data);
                    } else {
                        contractsResultContainer.innerHTML =
                            `<div class="alert alert-warning">No active contracts found.</div>`;
                    }
                } catch (error) {
                    contractsLoading.style.display = 'none';
                    contractsResultContainer.innerHTML =
                        `<div class="alert alert-danger">Failed to fetch contracts data.</div>`;
                }
            });
        }

        function displayContractsData(employees) {
            document.getElementById('contracts_data').value = JSON.stringify(employees);
            let html = `
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle me-1"></i>
                    <strong>${employees.length} employees found</strong> with active contracts.
                </div>
                <div class="employee-preview-table">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="selectAllContracts" checked></th>
                                <th>STAFF ID</th>
                                <th>EMPLOYEE NAME</th>
                                <th>STAFF TYPE</th>
                                <th class="text-end">BASIC SALARY</th>
                                <th class="text-end">ALLOWANCES</th>
                                <th>CONTRACT TYPE</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            employees.forEach((emp, index) => {
                html += `
                        <tr>
                            <td class="text-center"><input type="checkbox" class="contract-employee-checkbox" data-index="${index}" checked></td>
                            <td class="text-uppercase"><strong>${escapeHtml(emp.staff_id)}</strong></td>
                            <td class="text-uppercase">${escapeHtml(emp.employee_name)}</td>
                            <td class="text-uppercase">${escapeHtml(emp.staff_type)}</td>
                            <td class="text-end">${formatNumber(emp.basic_salary)}</td>
                            <td class="text-end">${formatNumber(emp.allowances)}</td>
                            <td class="text-uppercase"><span class="badge bg-${emp.contract_type === 'provision' ? 'warning' : 'success'}">${escapeHtml(emp.contract_type)}</span></td>
                        </tr>
                    `;
            });

            html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllContracts">Deselect All</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllContractsBtn">Select All</button>
                        </div>
                        <button type="button" class="btn btn-success" id="confirmContractsData">
                            <i class="fas fa-check me-1"></i> Confirm
                        </button>
                    </div>
                `;

            contractsResultContainer.innerHTML = html;

            // Event listeners
            document.getElementById('selectAllContractsBtn')?.addEventListener('click', () => {
                document.querySelectorAll('.contract-employee-checkbox').forEach(cb => cb.checked = true);
                updateContractCount();
            });

            document.getElementById('deselectAllContracts')?.addEventListener('click', () => {
                document.querySelectorAll('.contract-employee-checkbox').forEach(cb => cb.checked = false);
                updateContractCount();
            });

            document.getElementById('confirmContractsData')?.addEventListener('click', () => {
                const selected = [];
                document.querySelectorAll('.contract-employee-checkbox:checked').forEach(cb => {
                    selected.push(employees[parseInt(cb.dataset.index)]);
                });
                if (selected.length === 0) {
                    showToast('Please select at least one employee', 'warning');
                    return;
                }
                document.getElementById('contracts_data').value = JSON.stringify(selected);
                showToast(`${selected.length} employees confirmed for payroll`, 'success');
                document.getElementById('confirmContractsData').disabled = true;
                updateSubmitButtonState();
            });

            function updateContractCount() {
                const selected = document.querySelectorAll('.contract-employee-checkbox:checked').length;
                const btn = document.getElementById('confirmContractsData');
                if (btn && !btn.disabled) {
                    btn.innerHTML = `<i class="fas fa-check me-1"></i> Confirm (${selected} selected)`;
                }
            }

            document.querySelectorAll('.contract-employee-checkbox').forEach(cb => {
                cb.addEventListener('change', updateContractCount);
            });

            updateContractCount();
        }

        // ==================== EXCEL UPLOAD MODE WITH DUPLICATE VALIDATION ====================

        const downloadBtn = document.getElementById('downloadTemplateBtn');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const templateData = [
                    ['staff_id', 'basic_salary', 'allowances', 'department', 'contract_type'],
                    ['TCH-001', '800000', '100000', 'Science', 'new'],
                    ['DRV-001', '500000', '50000', 'Transport', 'new'],
                    ['STF-001', '400000', '0', 'Staff', 'provision']
                ];
                const csvContent = templateData.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
                const blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'payroll_template.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                showToast('Template downloaded. Fill with employee data.', 'success');
            });
        }

        const excelFileInput = document.getElementById('excel_file');
        const excelPreviewContainer = document.getElementById('excelPreviewContainer');

        // Function to fetch employee details from backend
        async function fetchEmployeeDetails(staffId) {
            try {
                const schoolId = {{ Auth::user()->school_id }};
                const response = await fetch('{{ route('api.employees.search') }}?school_id=' + schoolId + '&q=' +
                    encodeURIComponent(staffId), {
                        headers: {
                            'Authorization': 'Bearer ' + '{{ session('finance_api_token') }}',
                            'Accept': 'application/json'
                        }
                    });
                const data = await response.json();

                if (data.success && data.data && data.data.length > 0) {
                    // Find exact match by staff_id (case insensitive)
                    const emp = data.data.find(e => e.staff_id.toUpperCase() === staffId.toUpperCase());
                    if (emp) {
                        return {
                            employee_name: emp.employee_name,
                            staff_type: emp.staff_type,
                            basic_salary: emp.basic_salary,
                            allowances: emp.allowances,
                            bank_name: emp.bank_name,
                            bank_account_name: emp.bank_account_name,
                            bank_account_number: emp.bank_account_number,
                            phone: emp.phone,
                            email: emp.email,
                            exists_in_system: true
                        };
                    }
                }
                return null;
            } catch (e) {
                console.error('Error fetching employee details:', e);
                return null;
            }
        }

        // Function to check for duplicate staff_id in Excel data
        function checkDuplicateStaffIds(data) {
            const staffIds = [];
            const duplicateRows = [];

            data.forEach((item, index) => {
                const staffId = item.staff_id;
                if (staffIds.includes(staffId)) {
                    duplicateRows.push({
                        staff_id: staffId,
                        row_number: item.row_number,
                        index: index
                    });
                } else {
                    staffIds.push(staffId);
                }
            });

            return {
                hasDuplicates: duplicateRows.length > 0,
                duplicateRows: duplicateRows,
                uniqueStaffIds: staffIds
            };
        }

        function displayExcelPreview(data) {
            // Check for duplicates
            const duplicateCheck = checkDuplicateStaffIds(data);

            let html = `
            <div class="alert alert-${duplicateCheck.hasDuplicates ? 'danger' : 'success'}">
                <i class="fas ${duplicateCheck.hasDuplicates ? 'fa-exclamation-triangle' : 'fa-check-circle'} me-1"></i>
                <strong>${data.length} record(s) found.</strong>
                ${duplicateCheck.hasDuplicates ?
                    `<br><span class="text-danger"><i class="fas fa-times-circle me-1"></i> Found ${duplicateCheck.duplicateRows.length} duplicate staff ID(s)!</span>` :
                    '<span class="text-success"><i class="fas fa-check me-1"></i> No duplicates found.</span>'}
            </div>
        `;

            // Show duplicate details if any
            if (duplicateCheck.hasDuplicates) {
                html += `
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Duplicate Staff IDs Found:</strong>
                    <ul class="mb-0 mt-2">
                        ${duplicateCheck.duplicateRows.map(dup => `<li><strong>${dup.staff_id}</strong> appears at row ${dup.row_number}</li>`).join('')}
                    </ul>
                    <hr>
                    <small class="text-muted">Please remove duplicates from your Excel file and re-upload.</small>
                </div>
            `;
            }

            html += `
            <div class="preview-table-container">
                <table class="preview-table">
                    <thead>

                            <th style="width: 40px;"><input type="checkbox" id="selectAllExcel" ${!duplicateCheck.hasDuplicates ? 'checked' : ''} ${duplicateCheck.hasDuplicates ? 'disabled' : ''}></th>
                            <th>STAFF ID</th>
                            <th>EMPLOYEE NAME</th>
                            <th>STAFF TYPE</th>
                            <th class="text-end">BASIC SALARY</th>
                            <th class="text-end">ALLOWANCES</th>
                            <th class="text-end">GROSS PAY</th>
                            <th>DEPARTMENT</th>
                            <th>CONTRACT TYPE</th>
                            <th>STATUS</th>
                        </thead>
                        <tbody>
        `;

            data.forEach((emp, idx) => {
                const gross = (emp.basic_salary || 0) + (emp.allowances || 0);
                const statusBadge = emp.exists_in_system ?
                    '<span class="badge bg-success">✓ Found</span>' :
                    '<span class="badge bg-danger">✗ Not Found</span>';
                const isDuplicate = duplicateCheck.duplicateRows.some(dup => dup.index === idx);
                const rowClass = isDuplicate ?
                    'style="background-color: #fff3f3; border-left: 3px solid #dc3545;"' : '';

                // Show employee name with warning if not found
                const displayName = emp.exists_in_system ? emp.employee_name :
                    `${emp.staff_id} (EMPLOYEE NOT FOUND IN SYSTEM)`;

                html += `
                <tr ${rowClass}>
                    <td class="text-center">
                        <input type="checkbox" class="excel-checkbox" data-index="${idx}" ${!isDuplicate && !duplicateCheck.hasDuplicates && emp.exists_in_system ? 'checked' : ''} ${isDuplicate || !emp.exists_in_system ? 'disabled' : ''}>

                    <td class="text-uppercase">
                        <strong>${escapeHtml(emp.staff_id)}</strong>
                        ${isDuplicate ? '<span class="badge bg-danger ms-1">Duplicate</span>' : ''}

                    <td class="text-uppercase ${!emp.exists_in_system ? 'text-danger fw-bold' : ''}">
                        ${escapeHtml(displayName)}
                        ${!emp.exists_in_system ? '<i class="fas fa-exclamation-triangle ms-1" title="Employee not found in system"></i>' : ''}

                    <td class="text-uppercase">${escapeHtml(emp.staff_type)}
                    <td class="text-end">${formatNumber(emp.basic_salary)}
                    <td class="text-end">${formatNumber(emp.allowances)}
                    <td class="text-end"><strong>${formatNumber(gross)}</strong>
                    <td class="text-uppercase">${escapeHtml(emp.department)}
                    <td class="text-uppercase"><span class="badge bg-${emp.contract_type === 'provision' ? 'warning' : 'info'}">${escapeHtml(emp.contract_type)}</span>
                    <td class="text-center">${statusBadge}

            `;
            });

            html += `
                        </tbody>

                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" id="deselectAllExcel" ${duplicateCheck.hasDuplicates ? 'disabled' : ''}>Deselect All</button>
                        <button class="btn btn-sm btn-outline-primary" id="selectAllExcelBtn" ${duplicateCheck.hasDuplicates ? 'disabled' : ''}>Select All</button>
                    </div>
                    <button class="btn btn-${duplicateCheck.hasDuplicates ? 'secondary' : 'success'}" id="confirmExcelData" ${duplicateCheck.hasDuplicates ? 'disabled' : ''}>
                        <i class="fas fa-check me-1"></i> Confirm
                    </button>
                </div>
                <input type="hidden" id="excel_data" name="excel_data" value="">
        `;

            excelPreviewContainer.innerHTML = html;

            // Only attach event listeners if no duplicates
            if (!duplicateCheck.hasDuplicates) {
                const selectAllBtn = document.getElementById('selectAllExcelBtn');
                const deselectAllBtn = document.getElementById('deselectAllExcel');
                const confirmBtn = document.getElementById('confirmExcelData');

                function updateExcelCount() {
                    const selectedCount = document.querySelectorAll('.excel-checkbox:checked').length;
                    if (confirmBtn && !confirmBtn.disabled) {
                        confirmBtn.innerHTML =
                            `<i class="fas fa-check me-1"></i> Confirm`;
                    }
                }

                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', () => {
                        document.querySelectorAll('.excel-checkbox:not([disabled])').forEach(cb => cb.checked =
                            true);
                        updateExcelCount();
                    });
                }

                if (deselectAllBtn) {
                    deselectAllBtn.addEventListener('click', () => {
                        document.querySelectorAll('.excel-checkbox:not([disabled])').forEach(cb => cb.checked =
                            false);
                        updateExcelCount();
                    });
                }

                if (confirmBtn) {
                    confirmBtn.addEventListener('click', () => {
                        const selected = [];
                        document.querySelectorAll('.excel-checkbox:checked').forEach(cb => {
                            selected.push(data[parseInt(cb.dataset.index)]);
                        });

                        if (selected.length === 0) {
                            showToast('Please select at least one employee', 'warning');
                            return;
                        }

                        // Final duplicate check before confirming
                        const selectedStaffIds = selected.map(emp => emp.staff_id);
                        const finalDuplicateCheck = selectedStaffIds.filter((id, index) => selectedStaffIds.indexOf(
                            id) !== index);

                        if (finalDuplicateCheck.length > 0) {
                            showToast(
                                `Duplicate staff IDs found in selection: ${finalDuplicateCheck.join(', ')}. Please fix.`,
                                'warning');
                            return;
                        }

                        document.getElementById('excel_data').value = JSON.stringify(selected);
                        showToast(`${selected.length} employee(s) confirmed for payroll`, 'success');
                        confirmBtn.disabled = true;
                        confirmBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirmed!';
                        updateSubmitButtonState();
                    });
                }

                document.querySelectorAll('.excel-checkbox:not([disabled])').forEach(cb => {
                    cb.addEventListener('change', updateExcelCount);
                });
                updateExcelCount();
            } else {
                showToast('Please fix duplicate staff IDs in your Excel file and re-upload', 'warning');
                document.getElementById('excel_data').value = '';
                updateSubmitButtonState();
            }
        }

        if (excelFileInput) {
            excelFileInput.addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (!file) {
                    excelPreviewContainer.style.display = 'none';
                    return;
                }

                excelPreviewContainer.style.display = 'block';
                excelPreviewContainer.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-spinner fa-spin me-1"></i>
                    Processing "${file.name}"...<br>
                    <small class="text-muted">Fetching employee details from system...</small>
                </div>
            `;

                try {
                    const text = await file.text();
                    const lines = text.split('\n');
                    const headers = lines[0].split(',').map(h => h.replace(/"/g, '').trim());

                    let data = [];
                    const staffIdMap = new Map();

                    for (let i = 1; i < lines.length; i++) {
                        if (!lines[i].trim()) continue;

                        const values = lines[i].split(',').map(v => v.replace(/"/g, '').trim());
                        const row = {};
                        headers.forEach((header, idx) => {
                            row[header] = values[idx];
                        });

                        if (!row.staff_id) continue;

                        // Track duplicates
                        if (staffIdMap.has(row.staff_id)) {
                            staffIdMap.get(row.staff_id).push(i + 1);
                        } else {
                            staffIdMap.set(row.staff_id, [i + 1]);
                        }

                        // Fetch employee details from system
                        const employeeDetails = await fetchEmployeeDetails(row.staff_id);

                        data.push({
                            staff_id: row.staff_id,
                            employee_name: employeeDetails?.employee_name || '',
                            staff_type: employeeDetails?.staff_type || 'Teacher',
                            basic_salary: parseFloat(row.basic_salary) || 0,
                            allowances: parseFloat(row.allowances) || 0,
                            department: row.department || '',
                            contract_type: row.contract_type || 'new',
                            exists_in_system: !!employeeDetails,
                            row_number: i + 1
                        });
                    }

                    if (data.length === 0) {
                        excelPreviewContainer.innerHTML =
                            '<div class="alert alert-warning">No valid data found. Please check the format.</div>';
                        return;
                    }

                    displayExcelPreview(data);

                } catch (error) {
                    console.error('Error parsing file:', error);
                    excelPreviewContainer.innerHTML =
                        '<div class="alert alert-danger">Failed to parse file. Please use the template format.</div>';
                }
            });
        }

        // ==================== PREVIOUS SCHEDULE MODE ====================
        const previousSelect = document.getElementById('previous_batch_id');
        const loadScheduleBtn = document.getElementById('loadScheduleBtn');
        const schedulePreviewContainer = document.getElementById('schedulePreviewContainer');
        const scheduleConfirmContainer = document.getElementById('scheduleConfirmContainer');

        if (previousSelect) {
            previousSelect.addEventListener('change', function() {
                if (loadScheduleBtn) loadScheduleBtn.disabled = !this.value;
            });
        }

        if (loadScheduleBtn) {
            loadScheduleBtn.addEventListener('click', async function() {
                const batchId = previousSelect.value;
                if (!batchId) return;
                schedulePreviewContainer.innerHTML =
                    '<div class="text-center py-3"><div class="loading-spinner"></div> Loading payroll schedule data...</div>';
                schedulePreviewContainer.style.display = 'block';
                try {
                    const response = await fetch('{{ route('api.payroll.schedule.details') }}?batch_id=' +
                        batchId, {
                            headers: {
                                'Authorization': 'Bearer ' + '{{ session('finance_api_token') }}',
                                'Accept': 'application/json'
                            }
                        });
                    const data = await response.json();
                    if (data.success && data.data) {
                        displaySchedulePreview(data.data);
                        selectedScheduleData = data.data;
                        scheduleConfirmContainer.style.display = 'block';
                    } else {
                        schedulePreviewContainer.innerHTML =
                            '<div class="alert alert-warning">Could not load payroll schedule data.</div>';
                    }
                } catch (error) {
                    schedulePreviewContainer.innerHTML =
                        '<div class="alert alert-danger">Failed to load payroll schedule data.</div>';
                }
            });
        }

        function displaySchedulePreview(schedule) {
            let html = `
        <div class="selected-schedule-card">
            <h6 class="mb-2">📋 ${escapeHtml(schedule.name)} (${escapeHtml(schedule.month_name)})</h6>
            <div class="summary-stats mb-3">
                <div class="summary-item"><strong>Employees:</strong> ${schedule.total_employees}</div>
                <div class="summary-item"><strong>Total Gross:</strong> ${formatNumber(schedule.total_gross)}</div>
                <div class="summary-item"><strong>Total Net:</strong> ${formatNumber(schedule.total_net)}</div>
                <div class="summary-item"><strong>Total Tax:</strong> ${formatNumber(schedule.total_tax)}</div>
            </div>
            <div class="preview-table-container">
                <table class="preview-table">
                    <thead>
                            <th style="width: 50px;">#</th>
                            <th>STAFF ID</th>
                            <th>EMPLOYEE NAME</th>
                            <th class="text-end">BASIC SALARY</th>
                            <th class="text-end">ALLOWANCES</th>
                            <th class="text-end">GROSS PAY</th>
                            <th class="text-end">NSSF</th>
                            <th class="text-end">PAYE</th>
                            <th class="text-end">NET PAY</th>
                        </thead>
                        <tbody>
    `;

            (schedule.employees || []).forEach((emp, idx) => {
                html += `
            <tr>
                <td class="text-center">${idx + 1}</td>
                <td class="text-uppercase"><strong>${escapeHtml(emp.staff_id)}</strong></td>
                <td class="text-uppercase">${escapeHtml(emp.employee_name)}</td>
                <td class="text-end">${formatNumber(emp.basic_salary)}</td>
                <td class="text-end">${formatNumber(emp.allowances)}</td>
                <td class="text-end"><strong>${formatNumber(emp.gross)}</strong></td>
                <td class="text-end">${formatNumber(emp.nssf)}</td>
                <td class="text-end">${formatNumber(emp.paye)}</td>
                <td class="text-end text-success"><strong>${formatNumber(emp.net)}</strong></td>
            </tr>
        `;
            });

            html += `
                    </tbody>
                </table>
            </div>
        </div>
    `;

            schedulePreviewContainer.innerHTML = html;
        }

        const confirmScheduleBtn = document.getElementById('confirmScheduleBtn');
        if (confirmScheduleBtn) {
            confirmScheduleBtn.addEventListener('click', function() {
                if (selectedScheduleData) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'previous_batch_data';
                    hiddenInput.value = JSON.stringify(selectedScheduleData);
                    document.getElementById('payrollForm').appendChild(hiddenInput);
                    showToast('Schedule confirmed! Ready to generate payroll.', 'success');
                    this.disabled = true;
                    updateSubmitButtonState();
                }
            });
        }

        // ==================== FILTERS TOGGLE ====================
        const toggleFilters = document.getElementById('toggleFilters');
        const filtersSectionDiv = document.getElementById('filters_section');
        if (toggleFilters) {
            toggleFilters.addEventListener('click', function(e) {
                e.preventDefault();
                filtersSectionDiv.style.display = filtersSectionDiv.style.display === 'none' ? 'flex' : 'none';
                toggleFilters.innerHTML = filtersSectionDiv.style.display === 'none' ?
                    '<i class="fas fa-filter me-1"></i> Advanced Filters (Optional)' :
                    '<i class="fas fa-filter me-1"></i> Hide Filters';
            });
        }

        // ==================== FORM SUBMIT VALIDATION ====================
        const form = document.getElementById('payrollForm');
        const submitBtn = document.getElementById('submitBtn');

        function updateSubmitButtonState() {
            const method = document.querySelector('input[name="generation_method"]:checked')?.value;
            let isConfirmed = false;

            if (!method) {
                submitBtn.disabled = true;
                return;
            }

            switch (method) {
                case 'contracts':
                    const contractsData = document.getElementById('contracts_data').value;
                    isConfirmed = contractsData && contractsData !== '[]';
                    break;
                case 'excel_upload':
                    const excelData = document.getElementById('excel_data').value;
                    isConfirmed = excelData && excelData !== '[]';
                    break;
                case 'previous_batch':
                    const hasPreviousBatchData = document.querySelector('input[name="previous_batch_data"]');
                    isConfirmed = hasPreviousBatchData && hasPreviousBatchData.value !== '';
                    break;
                default:
                    isConfirmed = false;
                    break;
            }

            submitBtn.disabled = !isConfirmed;
            submitBtn.title = submitBtn.disabled ? 'Please confirm your selection first' : '';
        }

        function setupConfirmListeners() {
            const confirmContractsBtn = document.getElementById('confirmContractsData');
            if (confirmContractsBtn) {
                confirmContractsBtn.addEventListener('click', () => setTimeout(updateSubmitButtonState, 100));
            }

            const confirmExcelBtn = document.getElementById('confirmExcelData');
            if (confirmExcelBtn) {
                confirmExcelBtn.addEventListener('click', () => setTimeout(updateSubmitButtonState, 100));
            }

            const confirmScheduleBtn = document.getElementById('confirmScheduleBtn');
            if (confirmScheduleBtn) {
                confirmScheduleBtn.addEventListener('click', () => setTimeout(updateSubmitButtonState, 100));
            }

            document.querySelectorAll('input[name="generation_method"]').forEach(radio => {
                radio.addEventListener('change', updateSubmitButtonState);
            });

            updateSubmitButtonState();
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                const method = document.querySelector('input[name="generation_method"]:checked')?.value;
                let hasError = false;

                if (!method) {
                    showToast('Please select a generation method', 'warning');
                    e.preventDefault();
                    return;
                }

                switch (method) {
                    case 'contracts':
                        const contractsData = document.getElementById('contracts_data').value;
                        if (!contractsData || contractsData === '[]') {
                            showToast('Please fetch and confirm contracts data first', 'warning');
                            e.preventDefault();
                            hasError = true;
                        }
                        break;
                    case 'excel_upload':
                        const excelData = document.getElementById('excel_data').value;
                        if (!excelData || excelData === '[]') {
                            showToast('Please load and confirm Excel data first', 'warning');
                            e.preventDefault();
                            hasError = true;
                            break;
                        }
                        try {
                            const selectedEmployees = JSON.parse(excelData);
                            if (selectedEmployees.length === 0) {
                                showToast('Please select at least one employee from Excel', 'warning');
                                e.preventDefault();
                                hasError = true;
                                break;
                            }
                            const staffIds = selectedEmployees.map(emp => emp.staff_id);
                            const duplicates = staffIds.filter((id, index) => staffIds.indexOf(id) !== index);
                            if (duplicates.length > 0) {
                                showToast(`Duplicate staff IDs found: ${duplicates.join(', ')}. Please fix.`,
                                    'warning');
                                e.preventDefault();
                                hasError = true;
                                break;
                            }
                        } catch (err) {
                            showToast('Invalid Excel data. Please re-upload.', 'warning');
                            e.preventDefault();
                            hasError = true;
                        }
                        break;
                    case 'previous_batch':
                        const previousBatchId = document.getElementById('previous_batch_id').value;
                        if (!previousBatchId) {
                            showToast('Please select a previous schedule', 'warning');
                            e.preventDefault();
                            hasError = true;
                        }
                        if (!document.querySelector('input[name="previous_batch_data"]')) {
                            showToast('Please load and confirm the schedule first', 'warning');
                            e.preventDefault();
                            hasError = true;
                        }
                        break;
                }

                if (!hasError) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span> Generating Payroll...';
                }
            });

            window.addEventListener('load', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-cogs me-2"></i> Generate Payroll';
                setupConfirmListeners();
            });

            window.addEventListener('pageshow', updateSubmitButtonState);
        }
    </script>
@endsection
