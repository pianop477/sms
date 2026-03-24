{{-- resources/views/payroll/create.blade.php --}}

@extends('SRTDashboard.frame')

@section('content')
    <style>
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
                                {{session('error')}}
                            </div>
                        @endif
                        <form action="{{ route('payroll.generate') }}" method="POST" enctype="multipart/form-data"
                            id="payrollForm">
                            @csrf

                            {{-- ==================== MONTH SELECTION ==================== --}}
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

                            {{-- ==================== GENERATION METHOD CARDS ==================== --}}
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
                                        <i class="fas fa-database me-1"></i> Fetch Contracts Data
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
                                            Upload Excel file with columns: <strong>staff_id, basic_salary,
                                                allowances(JSON), department, contract_type</strong>
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
                                                    ({{ $schedule['total_employees'] ?? 0 }} employees)
                                                </option>
                                            @empty
                                                <option value="" disabled>No previous payrolls found</option>
                                            @endforelse
                                        </select>
                                        <small class="text-muted">Select a previous payroll to copy all data from</small>
                                    </div>
                                </div>
                                <div id="schedulePreviewContainer" class="mt-3" style="display: none;"></div>
                                <div id="scheduleConfirmContainer" class="mt-3" style="display: none;">
                                    <button type="button" class="btn btn-sm btn-success" id="confirmScheduleBtn">
                                        <i class="fas fa-check me-1"></i> Use This Schedule
                                    </button>
                                </div>
                            </div>

                            {{-- ==================== SECTION 4: MANUAL ENTRY MODE ==================== --}}
                            <div id="manual_section" class="section-card" style="display: none;">
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

                                {{-- Manual Add Form --}}
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
                                                    class="form-control form-control-sm" placeholder="e.g., 800000">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="small fw-bold">Allowances</label>
                                                <input type="text" id="manual_allowances"
                                                    class="form-control form-control-sm" placeholder='{"housing":200000}'>
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

                                {{-- Employee Search Modal --}}
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

                                {{-- Manual Employees List --}}
                                <div id="manualEmployeesContainer" class="mt-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        No employees added yet. Use "Add New Employee" or "Search Employee" to add
                                        employees.
                                    </div>
                                </div>
                                <input type="hidden" name="manual_employees" id="manual_employees" value="">
                            </div>

                            {{-- ==================== ADVANCED FILTERS ==================== --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button class="btn btn-sm btn-link text-decoration-none" type="button"
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

                            {{-- ==================== SUBMIT BUTTON ==================== --}}
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
        // console.log('JavaScript loaded successfully');

        // ==================== HELPER FUNCTIONS ====================
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
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

        // ==================== METHOD SWITCHING ====================
        const methodCards = document.querySelectorAll('.method-card');
        const contractsSection = document.getElementById('contracts_section');
        const excelSection = document.getElementById('excel_section');
        const previousSection = document.getElementById('previous_section');
        const manualSection = document.getElementById('manual_section');

        function showSection(method) {
            contractsSection.style.display = 'none';
            excelSection.style.display = 'none';
            previousSection.style.display = 'none';
            manualSection.style.display = 'none';

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

        const defaultRadio = document.querySelector('input[name="generation_method"]:checked');
        if (defaultRadio) {
            const defaultCard = defaultRadio.closest('.method-card');
            if (defaultCard) defaultCard.classList.add('active');
        }

        // ==================== CONTRACTS MODE ====================
        const fetchContractsBtn = document.getElementById('fetchContractsBtn');
        const contractsLoading = document.getElementById('contractsLoading');
        const contractsResultContainer = document.getElementById('contractsResultContainer');

        fetchContractsBtn?.addEventListener('click', async function() {
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
                    document.getElementById('contracts_data').value = JSON.stringify(data.data);

                    let html = `
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle me-1"></i>
                        <strong>${data.data.length} employees found</strong> with active contracts.
                    </div>
                    <div class="employee-preview-table">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAllContracts" checked></th>
                                    <th>Staff ID</th>
                                    <th>Name</th>
                                    <th>Staff Type</th>
                                    <th>Basic Salary</th>
                                    <th>Allowance</th>
                                    <th>Contract Type</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                    data.data.forEach((emp, index) => {
                        html += `
                        <tr>
                            <td><input type="checkbox" class="contract-employee-checkbox" data-index="${index}" checked></td>
                            <td>${escapeHtml(emp.staff_id)}</td>
                            <td>${escapeHtml(emp.employee_name)}</td>
                            <td>${emp.staff_type}</td>
                            <td>TZS ${Number(emp.basic_salary).toLocaleString()}</td>
                            <td>TZS ${Number(emp.allowances).toLocaleString()}</td>
                            <td><span class="badge bg-${emp.contract_type === 'provision' ? 'warning' : 'success'}">${emp.contract_type}</span></td>
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
                            <i class="fas fa-check me-1"></i> Confirm & Use Selected (${data.data.length} employees)
                        </button>
                    </div>
                `;

                    contractsResultContainer.innerHTML = html;

                    function updateSelectionCount() {
                        const selected = document.querySelectorAll('.contract-employee-checkbox:checked')
                        .length;
                        const confirmBtn = document.getElementById('confirmContractsData');
                        if (confirmBtn && !confirmBtn.disabled) {
                            confirmBtn.innerHTML =
                                `<i class="fas fa-check me-1"></i> Confirm & Use Selected (${selected} employees)`;
                        }
                    }

                    document.getElementById('selectAllContractsBtn')?.addEventListener('click', () => {
                        document.querySelectorAll('.contract-employee-checkbox').forEach(cb => cb
                            .checked = true);
                        updateSelectionCount();
                    });

                    document.getElementById('deselectAllContracts')?.addEventListener('click', () => {
                        document.querySelectorAll('.contract-employee-checkbox').forEach(cb => cb
                            .checked = false);
                        updateSelectionCount();
                    });

                    document.getElementById('confirmContractsData')?.addEventListener('click', () => {
                        const selectedEmployees = [];
                        document.querySelectorAll('.contract-employee-checkbox:checked').forEach(cb => {
                            const index = parseInt(cb.dataset.index);
                            selectedEmployees.push(data.data[index]);
                        });

                        if (selectedEmployees.length === 0) {
                            showToast('Please select at least one employee', 'warning');
                            return;
                        }

                        document.getElementById('contracts_data').value = JSON.stringify(
                            selectedEmployees);
                        const confirmBtn = document.getElementById('confirmContractsData');
                        confirmBtn.innerHTML =
                            `<i class="fas fa-check-circle me-1"></i> Confirmed! ${selectedEmployees.length} employees selected`;
                        confirmBtn.classList.remove('btn-success');
                        confirmBtn.classList.add('btn-secondary');
                        confirmBtn.disabled = true;
                        showToast(`${selectedEmployees.length} employees confirmed for payroll`,
                            'success');
                    });

                    document.querySelectorAll('.contract-employee-checkbox').forEach(cb => cb.addEventListener(
                        'change', updateSelectionCount));

                } else {
                    contractsResultContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        No active contracts found for this school. Please check contract status or use another method.
                    </div>
                `;
                }

            } catch (error) {
                contractsLoading.style.display = 'none';
                contractsResultContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-1"></i>
                    Failed to fetch contracts data. Error: ${error.message}
                    <button class="btn btn-sm btn-outline-danger mt-2" onclick="location.reload()">Retry</button>
                </div>
            `;
            }
        });

        // ==================== EXCEL MODE ====================
        document.getElementById('downloadTemplateBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            const templateData = [
                ['staff_id', 'basic_salary', 'allowances', 'department', 'contract_type'],
                ['TCH001', '800000', '100000', 'Science', 'new'],
                ['TCH002', '600000', '150000', 'Mathematics', 'provision'],
                ['DRV001', '500000', '50000', 'Transport', 'new']
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
            showToast('Template downloaded. Please fill with employee data.', 'success');
        });

        const excelFileInput = document.getElementById('excel_file');
        const excelPreviewContainer = document.getElementById('excelPreviewContainer');

        excelFileInput?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                excelPreviewContainer.style.display = 'none';
                return;
            }
            excelPreviewContainer.style.display = 'block';
            excelPreviewContainer.innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-1"></i>
                File "${file.name}" selected (${(file.size / 1024).toFixed(2)} KB)
                <button type="button" class="btn btn-sm btn-outline-danger ms-3" id="clearExcelFileBtn">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        `;
            document.getElementById('clearExcelFileBtn')?.addEventListener('click', () => {
                excelFileInput.value = '';
                excelPreviewContainer.style.display = 'none';
            });
        });

        // ==================== PREVIOUS SCHEDULE MODE ====================
        const previousSelect = document.getElementById('previous_batch_id');
        const schedulePreviewContainer = document.getElementById('schedulePreviewContainer');
        const scheduleConfirmContainer = document.getElementById('scheduleConfirmContainer');

        previousSelect?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const batchId = this.value;
            if (!batchId) {
                schedulePreviewContainer.style.display = 'none';
                scheduleConfirmContainer.style.display = 'none';
                return;
            }
            const name = selectedOption.dataset.name || 'N/A';
            const month = selectedOption.dataset.month || 'N/A';
            const employees = selectedOption.dataset.employees || 0;
            const total = selectedOption.dataset.total || 0;
            const date = selectedOption.dataset.date || '';

            schedulePreviewContainer.innerHTML = `
            <div class="selected-schedule-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong><i class="fas fa-check-circle text-success"></i> Selected Schedule</strong><br>
                        <span class="fw-bold">${escapeHtml(name)}</span><br>
                        <small>Month: ${month}</small><br>
                        <small>Generated: ${date}</small>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-primary mb-1">${employees} employees</div><br>
                        <div class="badge bg-success">Total Net: TZS ${Number(total).toLocaleString()}</div>
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-info-circle"></i> This payroll will copy ALL data from the selected schedule.
                </div>
            </div>
        `;
            schedulePreviewContainer.style.display = 'block';
            scheduleConfirmContainer.style.display = 'block';

            document.getElementById('confirmScheduleBtn')?.addEventListener('click', function() {
                showToast('Schedule selected! Ready to generate payroll.', 'success');
                document.getElementById('submitBtn').classList.add('btn-pulse');
                setTimeout(() => document.getElementById('submitBtn').classList.remove('btn-pulse'), 2000);
            });
        });

        // ==================== MANUAL ENTRY MODE ====================
        const manualAddFormDiv = document.getElementById('manualAddForm');
        const showManualFormBtn = document.getElementById('showManualFormBtn');
        const cancelManualFormBtn = document.getElementById('cancelManualFormBtn');
        const addManualEmployeeBtn = document.getElementById('addManualEmployeeBtn');
        const manualEmployeesContainerDiv = document.getElementById('manualEmployeesContainer');
        const manualEmployeesInputHidden = document.getElementById('manual_employees');

        showManualFormBtn?.addEventListener('click', () => {
            manualAddFormDiv.style.display = 'block';
            showManualFormBtn.style.display = 'none';
        });

        cancelManualFormBtn?.addEventListener('click', () => {
            manualAddFormDiv.style.display = 'none';
            showManualFormBtn.style.display = 'block';
            document.getElementById('manual_staff_id').value = '';
            document.getElementById('manual_name').value = '';
            document.getElementById('manual_basic_salary').value = '';
            document.getElementById('manual_allowances').value = '';
            document.getElementById('manual_department').value = '';
        });

        addManualEmployeeBtn?.addEventListener('click', () => {
            const staffId = document.getElementById('manual_staff_id').value.trim();
            const name = document.getElementById('manual_name').value.trim();
            const staffType = document.getElementById('manual_staff_type').value;
            const basicSalary = parseFloat(document.getElementById('manual_basic_salary').value) || 0;
            let allowances = {};
            try {
                const allowancesStr = document.getElementById('manual_allowances').value;
                if (allowancesStr) allowances = JSON.parse(allowancesStr);
            } catch (e) {
                showToast('Invalid JSON for allowances. Use format: {"housing":200000}', 'warning');
                return;
            }
            const department = document.getElementById('manual_department').value;
            const contractType = document.getElementById('manual_contract_type').value;

            if (!staffId) {
                showToast('Staff ID is required', 'warning');
                return;
            }
            if (!name) {
                showToast('Employee name is required', 'warning');
                return;
            }
            if (manualEmployeesList.some(emp => emp.staff_id === staffId)) {
                showToast('Employee already added', 'warning');
                return;
            }

            manualEmployeesList.push({
                staff_id: staffId,
                employee_name: name,
                staff_type: staffType,
                basic_salary: basicSalary,
                allowances: allowances,
                department: department,
                contract_type: contractType
            });
            updateManualDisplay();
            cancelManualFormBtn.click();
            showToast('Employee added successfully', 'success');
        });

        function updateManualDisplay() {
            if (manualEmployeesList.length === 0) {
                manualEmployeesContainerDiv.innerHTML =
                    `<div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> No employees added yet.</div>`;
                manualEmployeesInputHidden.value = '';
                return;
            }
            let html =
                `<div class="mb-2 d-flex justify-content-between"><strong>Employees (${manualEmployeesList.length})</strong>
            <button type="button" class="btn btn-sm btn-danger" onclick="clearAllManualEmployees()"><i class="fas fa-trash"></i> Clear All</button></div>`;
            manualEmployeesList.forEach((emp, idx) => {
                html += `<div class="manual-employee-row">
                <button type="button" class="remove-employee-btn" onclick="removeManualEmployee(${idx})"><i class="fas fa-times-circle"></i></button>
                <div class="row">
                    <div class="col-md-3"><small>Staff ID</small><br><strong>${escapeHtml(emp.staff_id)}</strong></div>
                    <div class="col-md-3"><small>Name</small><br><strong>${escapeHtml(emp.employee_name)}</strong></div>
                    <div class="col-md-2"><small>Type</small><br><span class="badge bg-secondary">${emp.staff_type}</span></div>
                    <div class="col-md-2"><small>Basic</small><br>TZS ${Number(emp.basic_salary).toLocaleString()}</div>
                    <div class="col-md-2"><small>Allowances</small><br>${Object.keys(emp.allowances).length || 'None'}</div>
                </div>
            </div>`;
            });
            manualEmployeesContainerDiv.innerHTML = html;
            manualEmployeesInputHidden.value = JSON.stringify(manualEmployeesList);
        }

        window.removeManualEmployee = function(idx) {
            manualEmployeesList.splice(idx, 1);
            updateManualDisplay();
        };
        window.clearAllManualEmployees = function() {
            if (confirm('Remove all?')) {
                manualEmployeesList = [];
                updateManualDisplay();
            }
        };

        // Search Modal
        const searchEmployeeBtn = document.getElementById('searchEmployeeBtn');
        const employeeSearchModal = document.getElementById('employeeSearchModal');

        function openEmployeeSearch() {
            employeeSearchModal.style.display = 'block';
            document.getElementById('employeeSearchInput').focus();
        }

        function closeEmployeeSearch() {
            employeeSearchModal.style.display = 'none';
            document.getElementById('employeeSearchInput').value = '';
            document.getElementById('employeeSearchResults').innerHTML = '';
        }
        searchEmployeeBtn?.addEventListener('click', openEmployeeSearch);

        document.getElementById('employeeSearchInput')?.addEventListener('input', async function(e) {
            const query = e.target.value;
            const resultsDiv = document.getElementById('employeeSearchResults');
            if (query.length < 2) {
                resultsDiv.innerHTML = '<div class="text-muted">Type at least 2 characters</div>';
                return;
            }
            resultsDiv.innerHTML = '<div class="loading-spinner"></div> Searching...';
            try {
                const schoolId = {{ Auth::user()->school_id ?? 1 }};
                const response = await fetch('{{ route('api.employees.search') }}?school_id=' + schoolId +
                    '&q=' + encodeURIComponent(query), {
                        headers: {
                            'Authorization': 'Bearer ' + '{{ session('finance_api_token') }}',
                            'Accept': 'application/json'
                        }
                    });
                const data = await response.json();
                if (data.success && data.data.length > 0) {
                    let html = '<div class="list-group">';
                    data.data.forEach(emp => {
                        html +=
                            `<div class="list-group-item list-group-item-action" style="cursor:pointer;" onclick="addManualEmployeeFromSearch('${emp.staff_id}', '${emp.employee_name}', '${emp.staff_type}', ${emp.basic_salary || 0})">
                        <div class="d-flex justify-content-between"><div><strong>${emp.employee_name}</strong><br><small>ID: ${emp.staff_id} | ${emp.staff_type}</small></div>
                        <div><small>Basic: TZS ${Number(emp.basic_salary || 0).toLocaleString()}</small></div></div></div>`;
                    });
                    html += '</div>';
                    resultsDiv.innerHTML = html;
                } else {
                    resultsDiv.innerHTML = '<div class="alert alert-warning">No employees found</div>';
                }
            } catch (e) {
                resultsDiv.innerHTML = '<div class="alert alert-danger">Search failed</div>';
            }
        });

        window.addManualEmployeeFromSearch = function(staffId, name, staffType, basicSalary) {
            if (manualEmployeesList.some(emp => emp.staff_id === staffId)) {
                showToast('Employee already added', 'warning');
                closeEmployeeSearch();
                return;
            }
            manualEmployeesList.push({
                staff_id: staffId,
                employee_name: name,
                staff_type: staffType,
                basic_salary: basicSalary,
                allowances: {},
                department: '',
                contract_type: 'new'
            });
            updateManualDisplay();
            closeEmployeeSearch();
            showToast('Employee added', 'success');
        };

        // ==================== FILTERS TOGGLE ====================
        const toggleFilters = document.getElementById('toggleFilters');
        const filtersSectionDiv = document.getElementById('filters_section');
        toggleFilters?.addEventListener('click', function(e) {
            e.preventDefault();
            if (filtersSectionDiv.style.display === 'none') {
                filtersSectionDiv.style.display = 'flex';
                toggleFilters.innerHTML = '<i class="fas fa-filter me-1"></i> Hide Filters';
            } else {
                filtersSectionDiv.style.display = 'none';
                toggleFilters.innerHTML = '<i class="fas fa-filter me-1"></i> Advanced Filters (Optional)';
            }
        });

        // ==================== FORM SUBMIT - PREPARE DATA ====================
        const form = document.getElementById('payrollForm');
        const submitBtn = document.getElementById('submitBtn');

        form?.addEventListener('submit', function(e) {
            const method = document.querySelector('input[name="generation_method"]:checked').value;

            // Clear any existing hidden fields for contracts data
            let existingContractsField = document.querySelector('input[name="contracts_data_post"]');
            if (existingContractsField) existingContractsField.remove();

            if (method === 'contracts') {
                const contractsData = document.getElementById('contracts_data').value;
                if (!contractsData || contractsData === '[]') {
                    e.preventDefault();
                    showToast('Please fetch and confirm contracts data first', 'warning');
                    return;
                }

                // Add a new hidden field with the contracts data
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'contracts_data_post'; // Different name to avoid conflict
                input.value = contractsData;
                form.appendChild(input);

                // Also add a flag to indicate this is from frontend selection
                const flag = document.createElement('input');
                flag.type = 'hidden';
                flag.name = 'use_frontend_selection';
                flag.value = 'true';
                form.appendChild(flag);
            }

            if (method === 'excel_upload') {
                const excelFile = document.getElementById('excel_file').files[0];
                if (!excelFile) {
                    e.preventDefault();
                    showToast('Please select an Excel file to upload', 'warning');
                    return;
                }
            }

            if (method === 'previous_batch') {
                const previousBatchId = document.getElementById('previous_batch_id').value;
                if (!previousBatchId) {
                    e.preventDefault();
                    showToast('Please select a previous schedule', 'warning');
                    return;
                }
            }

            if (method === 'manual_entry') {
                const manualData = document.getElementById('manual_employees').value;
                if (!manualData || manualData === '[]') {
                    e.preventDefault();
                    showToast('Please add at least one employee manually', 'warning');
                    return;
                }
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span> Generating Payroll...';
        });
    </script>
@endsection
