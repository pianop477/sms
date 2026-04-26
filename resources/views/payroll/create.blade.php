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
            max-height: 450px;
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
            padding: 10px;
        }

        .employee-preview-table td {
            padding: 10px;
            vertical-align: middle;
        }

        /* Editable cells styling */
        .editable-cell {
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .editable-cell:hover {
            background-color: #eef2ff;
            box-shadow: inset 0 0 0 1px #4e73df;
        }

        .editable-cell.editing {
            padding: 0;
            background-color: white;
            cursor: default;
        }

        .editable-cell.editing:hover {
            box-shadow: none;
        }

        /* Inline editor styles */
        .inline-editor {
            width: 100%;
            min-width: 100px;
        }

        .inline-editor-input {
            width: 100%;
            padding: 6px 8px;
            border: 2px solid #4e73df;
            border-radius: 6px;
            font-size: 0.85rem;
            outline: none;
            transition: all 0.2s;
        }

        .inline-editor-input:focus {
            border-color: #3a56d4;
            box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
        }

        .inline-editor-select {
            width: 100%;
            padding: 6px 8px;
            border: 2px solid #4e73df;
            border-radius: 6px;
            font-size: 0.85rem;
            background-color: white;
            outline: none;
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

        /* Preview table styles */
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

        .selected-schedule-card {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
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
                            <div class="alert alert-danger">{{ session('error') }}</div>
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
                                </div>
                            </div>

                            {{-- GENERATION METHOD CARDS --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold mb-3">Generation Method <span
                                            class="text-danger">*</span></label>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="method-card" data-method="contracts">
                                                <div class="d-flex align-items-start">
                                                    <input type="radio" name="generation_method" id="method_contracts"
                                                        value="contracts" checked>
                                                    <div class="ms-2">
                                                        <div class="fw-bold"><i
                                                                class="fas fa-file-contract text-primary me-1"></i> From
                                                            Contracts</div>
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
                                                        <div class="fw-bold"><i
                                                                class="fas fa-file-excel text-success me-1"></i> Upload
                                                            Excel</div>
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
                                                        <div class="fw-bold"><i
                                                                class="fas fa-history text-warning me-1"></i> Previous
                                                            Schedule</div>
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
                                                        <div class="fw-bold"><i
                                                                class="fas fa-keyboard text-secondary me-1"></i> Manual
                                                            Entry</div>
                                                        <small class="text-muted">Add employees manually</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 1: CONTRACTS MODE --}}
                            <div id="contracts_section" class="section-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0"><i class="fas fa-users text-primary me-1"></i> Employees with Active
                                        Contracts</h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="fetchContractsBtn">
                                        <i class="fas fa-sync-alt me-1"></i> Load Data
                                    </button>
                                </div>
                                <div id="contractsLoading" class="text-center py-3" style="display: none;">
                                    <div class="loading-spinner"></div> Fetching employees...
                                </div>
                                <div id="contractsResultContainer">
                                    <div class="alert alert-info">Click "Load Data" to load employees.</div>
                                </div>
                                <input type="hidden" name="contracts_data" id="contracts_data" value="">
                            </div>

                            {{-- SECTION 2: EXCEL UPLOAD MODE --}}
                            <div id="excel_section" class="section-card" style="display: none;">
                                <h6 class="mb-3"><i class="fas fa-file-excel text-success me-1"></i> Upload Excel File
                                </h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="file" name="excel_file" id="excel_file" class="form-control"
                                            accept=".xlsx,.xls,.csv">
                                        <small class="text-muted mt-1 d-block">Columns: staff_id, basic_salary, allowances,
                                            department, contract_type</small>
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

                            {{-- SECTION 3: PREVIOUS SCHEDULE MODE --}}
                            <div id="previous_section" class="section-card" style="display: none;">
                                <h6 class="mb-3"><i class="fas fa-history text-warning me-1"></i> Select Previous
                                    Payroll</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <select name="previous_batch_id" id="previous_batch_id" class="form-control">
                                            <option value="">-- Select Payroll --</option>
                                            @forelse($previousSchedules as $schedule)
                                                <option value="{{ $schedule['id'] }}">{{ $schedule['name'] }} -
                                                    {{ $schedule['month_name'] ?? $schedule['payroll_month'] }}</option>
                                            @empty
                                                <option disabled>No previous payrolls found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-sm btn-primary w-100" id="loadScheduleBtn"
                                            disabled>
                                            <i class="fas fa-sync-alt me-1"></i> Load Schedule
                                        </button>
                                    </div>
                                </div>
                                <div id="schedulePreviewContainer" class="mt-3" style="display: none;"></div>
                                <div id="scheduleConfirmContainer" class="mt-3" style="display: none;">
                                    <button type="button" class="btn btn-success" id="confirmScheduleBtn">
                                        <i class="fas fa-check me-1"></i> Confirm Schedule
                                    </button>
                                </div>
                            </div>

                            {{-- ADVANCED FILTERS --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button class="btn btn-xs btn-link text-decoration-none" type="button"
                                        id="toggleFilters">
                                        <i class="fas fa-filter me-1"></i> Advanced Filters
                                    </button>
                                </div>
                            </div>
                            <div id="filters_section" class="row mt-2" style="display: none;">
                                <div class="col-md-4">
                                    <label class="form-label">Staff Type</label>
                                    <select name="staff_type" id="staff_type" class="form-control">
                                        <option value="">All Staff Types</option>
                                        <option value="teacher">Teachers Only</option>
                                        <option value="transport">Transport Staff Only</option>
                                        <option value="other_staff">Other Staff Only</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" id="department" class="form-control"
                                        placeholder="e.g., Science">
                                </div>
                            </div>

                            {{-- SUBMIT BUTTON --}}
                            <div class="row mt-5">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary px-5" id="submitBtn" disabled>
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
        // ==================== PURE JAVASCRIPT VERSION - PRODUCTION READY ====================

        document.addEventListener('DOMContentLoaded', function() {

            // ==================== GLOBAL VARIABLES ====================
            let contractsMasterData = [];
            let excelMasterData = [];
            let selectedScheduleData = null;
            let currentEditingCell = null;

            let isContractsConfirmed = false;
            let isExcelConfirmed = false;
            let isScheduleConfirmed = false;

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
                toast.innerHTML = `<div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle'}"></i>
                <span>${message}</span>
            </div>`;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 2500);
            }

            function formatNumber(num) {
                if (num === null || num === undefined || isNaN(num)) return '0';
                return new Intl.NumberFormat().format(num);
            }

            function parseNumber(value) {
                if (!value && value !== 0) return 0;
                const cleaned = String(value).replace(/[^0-9.-]/g, '');
                const parsed = parseFloat(cleaned);
                return isNaN(parsed) ? 0 : parsed;
            }

            function safeSetAttribute(element, attr, value) {
                if (element && attr && value !== null && value !== undefined) {
                    element.setAttribute(attr, String(value));
                }
            }

            // ==================== SYNC FUNCTIONS ====================
            function syncExcelDataToHiddenInput() {
                const excelDataHidden = document.getElementById('excel_data_hidden');
                if (!excelDataHidden) return 0;

                const selectedEmployees = [];
                document.querySelectorAll('.excel-checkbox:checked:not([disabled])').forEach(cb => {
                    const idx = parseInt(cb.getAttribute('data-idx'));
                    if (excelMasterData[idx] && excelMasterData[idx].exists_in_system) {
                        selectedEmployees.push(excelMasterData[idx]);
                    }
                });

                if (selectedEmployees.length > 0) {
                    excelDataHidden.value = JSON.stringify(selectedEmployees);
                } else {
                    excelDataHidden.value = '';
                }
                return selectedEmployees.length;
            }

            function syncContractsDataToHiddenInput() {
                const contractsDataInput = document.getElementById('contracts_data');
                if (!contractsDataInput) return 0;

                // ✅ Remove duplicates by staff_id
                const uniqueMap = new Map();
                document.querySelectorAll('.contract-checkbox:checked').forEach(cb => {
                    const idx = parseInt(cb.getAttribute('data-idx'));
                    if (contractsMasterData[idx]) {
                        const staffId = contractsMasterData[idx].staff_id;
                        if (!uniqueMap.has(staffId)) {
                            uniqueMap.set(staffId, contractsMasterData[idx]);
                        }
                    }
                });

                const selectedEmployees = Array.from(uniqueMap.values());
                contractsDataInput.value = selectedEmployees.length > 0 ? JSON.stringify(selectedEmployees) : '';
                return selectedEmployees.length;
            }

            // ==================== INLINE EDITING ====================
            function makeEditable(cell, field, currentValue, employeeId, employeeData, mode) {
                if (!cell) return;
                if (currentEditingCell && currentEditingCell !== cell) saveCurrentEdit();

                const originalDisplay = cell.innerHTML;
                cell.classList.add('editing');
                safeSetAttribute(cell, 'data-original', originalDisplay);
                safeSetAttribute(cell, 'data-field', field);
                safeSetAttribute(cell, 'data-employee-id', String(employeeId));

                let empDataStr = '';
                try {
                    empDataStr = JSON.stringify(employeeData);
                } catch (e) {
                    empDataStr = '{}';
                }
                safeSetAttribute(cell, 'data-employee-data', empDataStr);
                safeSetAttribute(cell, 'data-mode', mode);
                safeSetAttribute(cell, 'data-current-value', String(currentValue !== undefined && currentValue !==
                    null ? currentValue : ''));

                let inputHtml = '';
                const safeValue = (currentValue !== undefined && currentValue !== null) ? currentValue : '';

                if (field === 'basic_salary' || field === 'allowances') {
                    inputHtml =
                        `<input type="number" class="inline-editor-input" value="${safeValue}" step="1000" min="0" style="text-align: right;">`;
                } else if (field === 'staff_type') {
                    inputHtml = `<select class="inline-editor-select">
                    <option value="Teacher" ${safeValue === 'Teacher' ? 'selected' : ''}>Teacher</option>
                    <option value="Transport Staff" ${safeValue === 'Transport Staff' ? 'selected' : ''}>Transport Staff</option>
                    <option value="Other Staff" ${safeValue === 'Other Staff' ? 'selected' : ''}>Other Staff</option>
                </select>`;
                } else if (field === 'contract_type') {
                    inputHtml = `<select class="inline-editor-select">
                    <option value="new" ${safeValue === 'new' ? 'selected' : ''}>New</option>
                    <option value="provision" ${safeValue === 'provision' ? 'selected' : ''}>Provision</option>
                </select>`;
                } else {
                    inputHtml =
                        `<input type="text" class="inline-editor-input" value="${escapeHtml(String(safeValue))}">`;
                }

                cell.innerHTML = `<div class="inline-editor">${inputHtml}</div>`;
                const input = cell.querySelector('input, select');
                if (input) {
                    input.focus();
                    if (input.tagName === 'INPUT') input.select();

                    const handleKeydown = function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            saveCurrentEdit();
                            moveToNextCell(cell);
                        } else if (e.key === 'Escape') {
                            e.preventDefault();
                            cancelCurrentEdit();
                        } else if (e.key === 'Tab') {
                            e.preventDefault();
                            saveCurrentEdit();
                            moveToNextCell(cell, e.shiftKey);
                        }
                    };
                    const handleBlur = function() {
                        saveCurrentEdit();
                    };
                    input.removeEventListener('keydown', handleKeydown);
                    input.removeEventListener('blur', handleBlur);
                    input.addEventListener('keydown', handleKeydown);
                    input.addEventListener('blur', handleBlur);
                }
                currentEditingCell = cell;
            }

            function saveCurrentEdit() {
                if (!currentEditingCell) return;
                const cell = currentEditingCell;
                const field = cell.getAttribute('data-field');
                const employeeId = cell.getAttribute('data-employee-id');
                const mode = cell.getAttribute('data-mode');

                if (!field || !employeeId) {
                    cancelCurrentEdit();
                    return;
                }

                let employeeData = {};
                const empDataAttr = cell.getAttribute('data-employee-data');
                if (empDataAttr) {
                    try {
                        employeeData = JSON.parse(empDataAttr);
                    } catch (e) {}
                }

                const originalValue = cell.getAttribute('data-current-value') || '';
                const originalHtml = cell.getAttribute('data-original') || '';
                const input = cell.querySelector('input, select');
                if (!input) {
                    cancelCurrentEdit();
                    return;
                }

                let newValue;
                if (field === 'basic_salary' || field === 'allowances') {
                    newValue = parseNumber(input.value);
                    if (newValue < 0) {
                        showToast('Amount cannot be negative', 'warning');
                        cancelCurrentEdit();
                        return;
                    }
                } else if (field === 'staff_type' || field === 'contract_type') {
                    newValue = input.value;
                } else {
                    newValue = input.value;
                }

                if (String(newValue) !== String(originalValue)) {
                    employeeData[field] = newValue;

                    if (field === 'basic_salary' || field === 'allowances') {
                        const basic = parseNumber(employeeData.basic_salary);
                        const allowances = parseNumber(employeeData.allowances);
                        const newGross = basic + allowances;
                        employeeData.gross_pay = newGross;
                        const row = cell.closest('tr');
                        if (row) {
                            const grossCell = row.querySelector('.gross-pay-cell');
                            if (grossCell) {
                                grossCell.innerHTML =
                                    `<strong class="text-primary">${formatNumber(newGross)}</strong> <span class="badge bg-warning text-dark ms-1">Auto</span>`;
                            }
                        }
                    }

                    if (mode === 'contracts') {
                        const idx = contractsMasterData.findIndex(e => String(e.id) === String(employeeId));
                        if (idx !== -1) contractsMasterData[idx] = employeeData;
                        updateContractSelectionSummary();
                        syncContractsDataToHiddenInput();
                    } else if (mode === 'excel') {
                        const idx = excelMasterData.findIndex(e => String(e.staff_id) === String(employeeId));
                        if (idx !== -1) excelMasterData[idx] = employeeData;
                        updateExcelSelectionSummary();
                        syncExcelDataToHiddenInput();
                    }

                    let displayValue;
                    if (field === 'basic_salary' || field === 'allowances') {
                        displayValue =
                            `<strong class="text-${field === 'basic_salary' ? 'primary' : 'success'}">${formatNumber(newValue)}</strong> <span class="badge bg-warning text-dark ms-1">Edited</span>`;
                    } else if (field === 'staff_type') {
                        displayValue =
                            `<span class="badge bg-secondary">${escapeHtml(newValue)}</span> <span class="badge bg-warning text-dark ms-1">Edited</span>`;
                    } else if (field === 'contract_type') {
                        const badgeClass = newValue === 'provision' ? 'warning' : (newValue === 'probation' ?
                            'secondary' : 'info');
                        displayValue =
                            `<span class="badge bg-${badgeClass}">${escapeHtml(newValue)}</span> <span class="badge bg-warning text-dark ms-1">Edited</span>`;
                    } else {
                        displayValue =
                            `${escapeHtml(newValue)} <span class="badge bg-warning text-dark ms-1">Edited</span>`;
                    }

                    cell.classList.remove('editing');
                    cell.innerHTML = displayValue;
                    attachCellClickHandler(cell, field, newValue, employeeId, employeeData, mode);
                    showToast(`${field.replace(/_/g, ' ').toUpperCase()} updated`, 'success', 1500);
                } else {
                    cell.classList.remove('editing');
                    cell.innerHTML = originalHtml;
                    attachCellClickHandler(cell, field, originalValue, employeeId, employeeData, mode);
                }

                cell.removeAttribute('data-original');
                cell.removeAttribute('data-field');
                cell.removeAttribute('data-employee-id');
                cell.removeAttribute('data-employee-data');
                cell.removeAttribute('data-mode');
                cell.removeAttribute('data-current-value');
                currentEditingCell = null;
            }

            function attachCellClickHandler(cell, field, value, employeeId, employeeData, mode) {
                if (cell._clickHandler) cell.removeEventListener('click', cell._clickHandler);
                cell._clickHandler = function(e) {
                    e.stopPropagation();
                    if (this.classList.contains('editing')) return;
                    makeEditable(this, field, value, employeeId, employeeData, mode);
                };
                cell.addEventListener('click', cell._clickHandler);
            }

            function cancelCurrentEdit() {
                if (!currentEditingCell) return;
                const cell = currentEditingCell;
                const originalHtml = cell.getAttribute('data-original') || '';
                cell.classList.remove('editing');
                cell.innerHTML = originalHtml;
                cell.removeAttribute('data-original');
                cell.removeAttribute('data-field');
                cell.removeAttribute('data-employee-id');
                cell.removeAttribute('data-employee-data');
                cell.removeAttribute('data-mode');
                cell.removeAttribute('data-current-value');
                currentEditingCell = null;
            }

            function moveToNextCell(cell, isShift = false) {
                const row = cell.closest('tr');
                if (!row) return;
                const editableCells = Array.from(row.querySelectorAll('.editable-cell'));
                const currentIndex = editableCells.indexOf(cell);
                let nextIndex = isShift ? currentIndex - 1 : currentIndex + 1;
                if (nextIndex >= 0 && nextIndex < editableCells.length) {
                    const nextCell = editableCells[nextIndex];
                    const field = nextCell.getAttribute('data-field');
                    const currentValue = nextCell.getAttribute('data-value');
                    const employeeId = nextCell.getAttribute('data-id');
                    let employeeData = {};
                    const empDataAttr = nextCell.getAttribute('data-employee-data');
                    if (empDataAttr) {
                        try {
                            employeeData = JSON.parse(empDataAttr);
                        } catch (e) {}
                    }
                    const mode = nextCell.getAttribute('data-mode');
                    if (field && employeeId) makeEditable(nextCell, field, currentValue, employeeId, employeeData,
                        mode);
                }
            }

            function attachEditableHandlers() {
                document.querySelectorAll('.editable-cell').forEach(cell => {
                    const field = cell.getAttribute('data-field');
                    const value = cell.getAttribute('data-value');
                    const employeeId = cell.getAttribute('data-id');
                    let employeeData = {};
                    const empDataAttr = cell.getAttribute('data-employee-data');
                    if (empDataAttr) {
                        try {
                            employeeData = JSON.parse(empDataAttr);
                        } catch (e) {}
                    }
                    const mode = cell.getAttribute('data-mode');
                    if (field && employeeId) attachCellClickHandler(cell, field, value, employeeId,
                        employeeData, mode);
                });
            }

            // ==================== SELECTION SUMMARY FUNCTIONS ====================
            function updateContractSelectionSummary() {
                // ✅ Remove duplicates by staff_id
                const uniqueMap = new Map();
                document.querySelectorAll('.contract-checkbox:checked').forEach(cb => {
                    const idx = parseInt(cb.getAttribute('data-idx'));
                    if (contractsMasterData[idx]) {
                        const staffId = contractsMasterData[idx].staff_id;
                        if (!uniqueMap.has(staffId)) {
                            uniqueMap.set(staffId, contractsMasterData[idx]);
                        }
                    }
                });

                const selectedEmployees = Array.from(uniqueMap.values());
                const selected = selectedEmployees.length;
                let totalGross = 0;

                selectedEmployees.forEach(emp => {
                    totalGross += parseNumber(emp.basic_salary) + parseNumber(emp.allowances);
                });

                const contractsDataInput = document.getElementById('contracts_data');
                if (contractsDataInput) contractsDataInput.value = JSON.stringify(selectedEmployees);

                const selectedSpan = document.getElementById('contractSelectedCount');
                if (selectedSpan) {
                    selectedSpan.innerHTML =
                        `<i class="fas fa-users me-1"></i> ${selected} selected | Total: <strong>TZS ${formatNumber(totalGross)}</strong>`;
                    selectedSpan.className = selected > 0 ? 'badge bg-primary p-2' : 'badge bg-secondary p-2';
                }

                const summaryDiv = document.getElementById('contracts-summary');
                if (summaryDiv) {
                    if (selected > 0) {
                        summaryDiv.innerHTML = `
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-chart-line me-1"></i>
                            <strong>Selected Employees:</strong> ${selected} |
                            <strong>Total Gross Pay:</strong> <strong class="text-success">TZS ${formatNumber(totalGross)}</strong>
                        </div>
                    `;
                    } else {
                        summaryDiv.innerHTML = `
                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            No employees selected. Please select at least one employee.
                        </div>
                    `;
                    }
                }

                updateSubmitButtonState();
            }

            function updateExcelSelectionSummary() {
                const selectedCheckboxes = document.querySelectorAll('.excel-checkbox:checked:not([disabled])');
                const selected = selectedCheckboxes.length;
                let totalGross = 0;
                let selectedEmployees = [];

                selectedCheckboxes.forEach(cb => {
                    const idx = parseInt(cb.getAttribute('data-idx'));
                    if (excelMasterData[idx] && excelMasterData[idx].exists_in_system) {
                        selectedEmployees.push(excelMasterData[idx]);
                        totalGross += parseNumber(excelMasterData[idx].basic_salary) + parseNumber(
                            excelMasterData[idx].allowances);
                    }
                });

                const excelDataHidden = document.getElementById('excel_data_hidden');
                if (excelDataHidden) {
                    excelDataHidden.value = selectedEmployees.length > 0 ? JSON.stringify(selectedEmployees) : '';
                }

                const selectedSpan = document.getElementById('excelSelectedCount');
                if (selectedSpan) selectedSpan.innerHTML =
                    `<i class="fas fa-users me-1"></i> ${selected} selected | Total: TZS ${formatNumber(totalGross)}`;

                const summaryDiv = document.getElementById('excel-summary');
                if (summaryDiv) summaryDiv.innerHTML =
                    `<div class="alert alert-info mt-2"><i class="fas fa-chart-line me-1"></i><strong>Selected:</strong> ${selected} | <strong>Total Gross:</strong> TZS ${formatNumber(totalGross)}</div>`;

                updateSubmitButtonState();
            }

            // ==================== METHOD SWITCHING ====================
            function showSection(method) {
                const contractsSection = document.getElementById('contracts_section');
                const excelSection = document.getElementById('excel_section');
                const previousSection = document.getElementById('previous_section');
                if (contractsSection) contractsSection.style.display = 'none';
                if (excelSection) excelSection.style.display = 'none';
                if (previousSection) previousSection.style.display = 'none';
                if (method === 'contracts' && contractsSection) contractsSection.style.display = 'block';
                else if (method === 'excel_upload' && excelSection) excelSection.style.display = 'block';
                else if (method === 'previous_batch' && previousSection) previousSection.style.display = 'block';
            }

            document.querySelectorAll('.method-card').forEach(card => {
                card.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        document.querySelectorAll('.method-card').forEach(c => c.classList.remove(
                            'active'));
                        this.classList.add('active');
                        showSection(radio.value);
                        isContractsConfirmed = false;
                        isExcelConfirmed = false;
                        isScheduleConfirmed = false;
                        updateSubmitButtonState();
                    }
                });
            });

            document.querySelectorAll('input[name="generation_method"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    showSection(this.value);
                });
            });

            const activeMethod = document.querySelector('input[name="generation_method"]:checked');
            if (activeMethod) showSection(activeMethod.value);

            // ==================== CONTRACTS MODE ====================
            const fetchContractsBtn = document.getElementById('fetchContractsBtn');
            if (fetchContractsBtn) {
                fetchContractsBtn.addEventListener('click', async function() {
                    const schoolId = {{ Auth::user()->school_id }};
                    const loading = document.getElementById('contractsLoading');
                    const resultContainer = document.getElementById('contractsResultContainer');
                    if (loading) loading.style.display = 'block';
                    if (resultContainer) resultContainer.innerHTML = '';
                    isContractsConfirmed = false;

                    try {
                        let url = '{{ route('api.employees.with-contracts') }}?school_id=' + schoolId;
                        const staffType = document.getElementById('staff_type')?.value || '';
                        const department = document.getElementById('department')?.value || '';
                        if (staffType) url += '&staff_type=' + staffType;
                        if (department) url += '&department=' + department;

                        const response = await fetch(url, {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Authorization': 'Bearer ' +
                                    '{{ session('finance_api_token') }}'
                            }
                        });
                        const data = await response.json();
                        if (loading) loading.style.display = 'none';

                        if (data.success && data.data && data.data.length) {
                            // ✅ Ensure no duplicates at fetch time
                            const uniqueMap = new Map();
                            data.data.forEach(emp => {
                                const staffId = emp.staff_id;
                                if (!uniqueMap.has(staffId)) {
                                    uniqueMap.set(staffId, emp);
                                }
                            });

                            contractsMasterData = Array.from(uniqueMap.values()).map(emp => ({
                                ...emp,
                                gross_pay: parseNumber(emp.basic_salary) + parseNumber(emp
                                    .allowances)
                            }));

                            if (uniqueMap.size !== data.data.length) {
                                showToast(`${data.data.length - uniqueMap.size} duplicate(s) removed`,
                                    'warning');
                            }

                            displayContractsData(contractsMasterData);
                        } else if (resultContainer) {
                            resultContainer.innerHTML =
                                '<div class="alert alert-warning">No active contracts found.</div>';
                        }
                    } catch (error) {
                        if (loading) loading.style.display = 'none';
                        if (resultContainer) resultContainer.innerHTML =
                            `<div class="alert alert-danger">Error: ${error.message}</div>`;
                    }
                });
            }

            function displayContractsData(employees) {
                const contractsDataInput = document.getElementById('contracts_data');
                if (contractsDataInput) contractsDataInput.value = '';
                isContractsConfirmed = false;

                let html = `
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle me-1"></i>
                    <strong>${employees.length} employees found</strong> with active contracts
                    <small class="d-block mt-1 text-muted">
                        <i class="fas fa-edit me-1"></i> Click any editable field to modify.
                        Press <kbd>Enter</kbd> to save, <kbd>Tab</kbd> to move next, or click outside to save.
                    </small>
                </div>
                <div id="contracts-summary"></div>
                <div class="employee-preview-table">
                    <table class="table table-bordered table-hover" style="font-size: 13px;">
                        <thead style="background: #f8f9fc;">
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="selectAllContracts" checked style="cursor: pointer;">
                                </th>
                                <th>STAFF ID</th>
                                <th>EMPLOYEE NAME</th>
                                <th style="width: 130px;">STAFF TYPE</th>
                                <th class="text-end" style="width: 150px;">BASIC SALARY (TZS)</th>
                                <th class="text-end" style="width: 150px;">ALLOWANCES (TZS)</th>
                                <th class="text-end" style="width: 150px;">GROSS PAY (TZS)</th>
                                <th>DEPARTMENT</th>
                                <th style="width: 130px;">CONTRACT TYPE</th>
                            </tr>
                        </thead>
                        <tbody>`;

                employees.forEach((emp, idx) => {
                    const gross = parseNumber(emp.basic_salary) + parseNumber(emp.allowances);
                    const contractBadge = emp.contract_type === 'provision' ? 'warning' : (emp
                        .contract_type === 'probation' ? 'secondary' : 'info');

                    html += `
                            <tr data-employee="${emp.id}" data-index="${idx}">
                                <td class="text-center" style="vertical-align: middle;">
                                    <input type="checkbox" class="contract-checkbox" data-idx="${idx}" checked style="cursor: pointer;">
                                </td>
                                <td class="text-uppercase fw-bold">${escapeHtml(emp.staff_id)}</td>
                                <td class="text-uppercase">${escapeHtml(emp.employee_name)}</td>
                                <td class="editable-cell" data-field="staff_type" data-value="${escapeHtml(emp.staff_type || 'Teacher')}" data-id="${emp.id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="contracts" style="cursor: pointer; background: #fff8e7;">
                                    <span class="badge bg-secondary">${escapeHtml(emp.staff_type || 'Teacher')}</span>
                                </td>
                                <td class="text-end editable-cell" data-field="basic_salary" data-value="${emp.basic_salary}" data-id="${emp.id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="contracts" style="cursor: pointer; background: #e8f0fe;">
                                    <strong class="text-primary">${formatNumber(emp.basic_salary)}</strong>
                                </td>
                                <td class="text-end editable-cell" data-field="allowances" data-value="${emp.allowances}" data-id="${emp.id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="contracts" style="cursor: pointer; background: #e6f7e6;">
                                    <strong class="text-success">${formatNumber(emp.allowances)}</strong>
                                </td>
                                <td class="text-end gross-pay-cell">
                                    <strong class="text-primary">${formatNumber(gross)}</strong>
                                </td>
                                <td class="editable-cell" data-field="department" data-value="${escapeHtml(emp.department || '')}" data-id="${emp.id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="contracts" style="cursor: pointer; background: #fff8e7;">
                                    ${escapeHtml(emp.department || '—')}
                                </td>
                                <td class="editable-cell" data-field="contract_type" data-value="${emp.contract_type}" data-id="${emp.id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="contracts" style="cursor: pointer; background: #fff8e7;">
                                    <span class="badge bg-${contractBadge}">${escapeHtml(emp.contract_type)}</span>
                                </td>
                            </tr>
                        `;
                });

                html += `
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllContracts">
                            <i class="fas fa-times-circle me-1"></i> Deselect All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllContractsBtn">
                            <i class="fas fa-check-circle me-1"></i> Select All
                        </button>
                    </div>
                    <div>
                        <span id="contractSelectedCount" class="badge bg-secondary p-2">0 selected</span>
                        <button type="button" class="btn btn-success ms-3" id="confirmContractsData">
                            <i class="fas fa-check-double me-1"></i> Confirm Selection
                        </button>
                    </div>
                </div>
            `;

                const resultContainer = document.getElementById('contractsResultContainer');
                if (resultContainer) resultContainer.innerHTML = html;
                attachEditableHandlers();

                const selectAllBtn = document.getElementById('selectAllContractsBtn');
                const deselectAllBtn = document.getElementById('deselectAllContracts');
                const confirmBtn = document.getElementById('confirmContractsData');

                function updateSelection() {
                    // ✅ Remove duplicates by staff_id
                    const uniqueMap = new Map();
                    document.querySelectorAll('.contract-checkbox:checked').forEach(cb => {
                        const idx = parseInt(cb.getAttribute('data-idx'));
                        if (contractsMasterData[idx]) {
                            const staffId = contractsMasterData[idx].staff_id;
                            if (!uniqueMap.has(staffId)) {
                                uniqueMap.set(staffId, contractsMasterData[idx]);
                            }
                        }
                    });

                    const selectedEmployees = Array.from(uniqueMap.values());
                    const selected = selectedEmployees.length;
                    let totalGross = 0;

                    selectedEmployees.forEach(emp => {
                        totalGross += parseNumber(emp.basic_salary) + parseNumber(emp.allowances);
                    });

                    const selectedSpan = document.getElementById('contractSelectedCount');
                    if (selectedSpan) {
                        selectedSpan.innerHTML =
                            `<i class="fas fa-users me-1"></i> ${selected} selected | Total: <strong>TZS ${formatNumber(totalGross)}</strong>`;
                        selectedSpan.className = selected > 0 ? 'badge bg-primary p-2' : 'badge bg-secondary p-2';
                    }

                    const summaryDiv = document.getElementById('contracts-summary');
                    if (summaryDiv) {
                        if (selected > 0) {
                            summaryDiv.innerHTML = `
                            <div class="alert alert-info mt-2">
                                <i class="fas fa-chart-line me-1"></i>
                                <strong>Selected Employees:</strong> ${selected} |
                                <strong>Total Gross Pay:</strong> <strong class="text-success">TZS ${formatNumber(totalGross)}</strong>
                            </div>
                        `;
                        } else {
                            summaryDiv.innerHTML = `
                            <div class="alert alert-warning mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                No employees selected. Please select at least one employee.
                            </div>
                        `;
                        }
                    }

                    const contractsDataInput = document.getElementById('contracts_data');
                    if (contractsDataInput) {
                        contractsDataInput.value = JSON.stringify(selectedEmployees);
                    }
                }

                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', () => {
                        document.querySelectorAll('.contract-checkbox').forEach(cb => cb.checked = true);
                        updateSelection();
                    });
                }

                if (deselectAllBtn) {
                    deselectAllBtn.addEventListener('click', () => {
                        document.querySelectorAll('.contract-checkbox').forEach(cb => cb.checked = false);
                        updateSelection();
                    });
                }

                document.querySelectorAll('.contract-checkbox').forEach(cb => {
                    cb.addEventListener('change', updateSelection);
                });

                if (confirmBtn) {
                    confirmBtn.addEventListener('click', () => {
                        // ✅ Remove duplicates by staff_id
                        const uniqueMap = new Map();
                        document.querySelectorAll('.contract-checkbox:checked').forEach(cb => {
                            const idx = parseInt(cb.getAttribute('data-idx'));
                            if (contractsMasterData[idx]) {
                                const staffId = contractsMasterData[idx].staff_id;
                                if (!uniqueMap.has(staffId)) {
                                    uniqueMap.set(staffId, contractsMasterData[idx]);
                                }
                            }
                        });

                        const uniqueSelected = Array.from(uniqueMap.values());

                        if (uniqueSelected.length === 0) {
                            showToast('Please select at least one employee to confirm', 'warning');
                            return;
                        }

                        if (contractsDataInput) contractsDataInput.value = JSON.stringify(uniqueSelected);
                        showToast(`${uniqueSelected.length} employee(s) confirmed for payroll`, 'success');
                        confirmBtn.disabled = true;
                        confirmBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirmed!';
                        isContractsConfirmed = true;
                        updateSubmitButtonState();
                    });
                }

                updateSelection();
            }

            // ==================== EXCEL UPLOAD MODE ====================
            const downloadBtn = document.getElementById('downloadTemplateBtn');
            if (downloadBtn) {
                downloadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const csv = ['"staff_id","basic_salary","allowances","department","contract_type"',
                        '"TCH-001","800000","100000","Science","new"',
                        '"DRV-001","500000","50000","Transport","new"',
                        '"STF-001","400000","0","Staff","provision"'
                    ].join('\n');
                    const blob = new Blob([csv], {
                        type: 'text/csv'
                    });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'payroll_template.csv';
                    link.click();
                    URL.revokeObjectURL(link.href);
                    showToast('Template downloaded');
                });
            }

            async function fetchEmployeeDetailsBatch(staffIds) {
                if (!staffIds.length) return {};
                try {
                    const schoolId = {{ Auth::user()->school_id }};
                    const response = await fetch('{{ route('api.employees.batch') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Authorization': 'Bearer ' + '{{ session('finance_api_token') }}'
                        },
                        body: JSON.stringify({
                            school_id: schoolId,
                            staff_ids: staffIds
                        })
                    });
                    const data = await response.json();
                    if (data.success && data.data) {
                        const caseInsensitiveMap = {};
                        for (const [key, value] of Object.entries(data.data)) {
                            caseInsensitiveMap[key] = value;
                            caseInsensitiveMap[key.toUpperCase()] = value;
                            caseInsensitiveMap[key.toLowerCase()] = value;
                        }
                        return caseInsensitiveMap;
                    }
                    return {};
                } catch (e) {
                    console.error('Batch fetch error:', e);
                    return {};
                }
            }

            const excelFileInput = document.getElementById('excel_file');
            const excelPreviewContainer = document.getElementById('excelPreviewContainer');

            if (excelFileInput) {
                excelFileInput.addEventListener('change', async function(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    if (excelPreviewContainer) {
                        excelPreviewContainer.style.display = 'block';
                        excelPreviewContainer.innerHTML =
                            '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-1"></i> Processing file...</div>';
                    }
                    isExcelConfirmed = false;

                    try {
                        const text = await file.text();
                        const lines = text.split('\n');
                        const headers = lines[0].split(',').map(h => h.replace(/"/g, '').trim()
                            .toLowerCase());
                        let excelData = [];
                        for (let i = 1; i < lines.length; i++) {
                            if (!lines[i].trim()) continue;
                            const values = lines[i].split(',').map(v => v.replace(/"/g, '').trim());
                            const row = {};
                            headers.forEach((header, idx) => {
                                row[header] = values[idx] || '';
                            });
                            if (row.staff_id) {
                                excelData.push({
                                    staff_id: row.staff_id.toUpperCase().trim(),
                                    basic_salary: parseFloat(row.basic_salary) || 0,
                                    allowances: parseFloat(row.allowances) || 0,
                                    department: row.department || '',
                                    contract_type: (row.contract_type || 'new').toLowerCase(),
                                    row_number: i + 1
                                });
                            }
                        }
                        if (!excelData.length) {
                            if (excelPreviewContainer) excelPreviewContainer.innerHTML =
                                '<div class="alert alert-warning">No valid data found.</div>';
                            return;
                        }

                        const uniqueStaffIds = [...new Set(excelData.map(item => item.staff_id))];
                        const employeeMap = await fetchEmployeeDetailsBatch(uniqueStaffIds);
                        excelMasterData = excelData.map(item => {
                            const systemData = employeeMap[item.staff_id] || {};
                            return {
                                staff_id: item.staff_id,
                                employee_name: systemData.employee_name || systemData.name ||
                                    '',
                                first_name: systemData.first_name || '',
                                last_name: systemData.last_name || '',
                                staff_type: systemData.staff_type || 'Teacher',
                                basic_salary: item.basic_salary,
                                allowances: item.allowances,
                                department: item.department || systemData.department || '',
                                contract_type: item.contract_type || systemData.contract_type ||
                                    'new',
                                exists_in_system: !!(systemData.employee_name || systemData
                                    .name),
                                gross_pay: item.basic_salary + item.allowances,
                                row_number: item.row_number
                            };
                        });
                        displayExcelPreview(excelMasterData);
                    } catch (error) {
                        console.error('Error:', error);
                        if (excelPreviewContainer) excelPreviewContainer.innerHTML =
                            `<div class="alert alert-danger">Error: ${error.message}</div>`;
                    }
                });
            }

            function displayExcelPreview(data) {
                const hasDuplicates = new Set(data.map(i => i.staff_id)).size !== data.length;
                if (!excelPreviewContainer) return;

                let html = `<div class="alert alert-${hasDuplicates ? 'danger' : 'success'}">
                <i class="fas ${hasDuplicates ? 'fa-exclamation-triangle' : 'fa-check-circle'} me-1"></i>
                <strong>${data.length} record(s) loaded.</strong>
                ${hasDuplicates ? '<br><span class="text-danger">⚠️ Duplicate staff IDs found!</span>' : ''}
                <small class="d-block mt-1">💡 Click any editable field to modify. Press Enter, Tab, or click outside to save.</small>
            </div>
            <div id="excel-summary"></div>`;

                if (!hasDuplicates) {
                    html += `<div class="preview-table-container">
                    <table class="preview-table">
                        <thead>
                            <tr>
                                <th style="width:40px"><input type="checkbox" id="selectAllExcel" checked></th>
                                <th>STAFF ID</th><th>EMPLOYEE NAME</th><th>STAFF TYPE</th>
                                <th class="text-end">BASIC SALARY</th><th class="text-end">ALLOWANCES</th>
                                <th class="text-end">GROSS PAY</th><th>DEPARTMENT</th><th>CONTRACT TYPE</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>`;

                    data.forEach((emp, idx) => {
                        const gross = parseNumber(emp.basic_salary) + parseNumber(emp.allowances);
                        const contractBadge = emp.contract_type === 'provision' ? 'warning' : (emp
                            .contract_type === 'probation' ? 'secondary' : 'info');
                        const statusBadge = emp.exists_in_system ?
                            '<span class="badge bg-success">✓ Found</span>' :
                            '<span class="badge bg-danger">✗ Not Found</span>';
                        html += `<tr>
                        <td class="text-center"><input type="checkbox" class="excel-checkbox" data-idx="${idx}" ${emp.exists_in_system ? 'checked' : 'disabled'}></td>
                        <td class="text-uppercase"><strong>${escapeHtml(emp.staff_id)}</strong></td>
                        <td class="${!emp.exists_in_system ? 'text-danger' : ''}">${escapeHtml(emp.employee_name || '—')}</td>
                        <td class="editable-cell" data-field="staff_type" data-value="${escapeHtml(emp.staff_type)}" data-id="${emp.staff_id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="excel">
                            <span class="badge bg-secondary">${escapeHtml(emp.staff_type)}</span>
                        </td>
                        <td class="text-end editable-cell" data-field="basic_salary" data-value="${emp.basic_salary}" data-id="${emp.staff_id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="excel">
                            <strong class="text-primary">${formatNumber(emp.basic_salary)}</strong>
                        </td>
                        <td class="text-end editable-cell" data-field="allowances" data-value="${emp.allowances}" data-id="${emp.staff_id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="excel">
                            <strong class="text-success">${formatNumber(emp.allowances)}</strong>
                        </td>
                        <td class="text-end gross-pay-cell"><strong class="text-primary">${formatNumber(gross)}</strong></td>
                        <td class="editable-cell" data-field="department" data-value="${escapeHtml(emp.department || '')}" data-id="${emp.staff_id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="excel">
                            ${escapeHtml(emp.department || '—')}
                        </td>
                        <td class="editable-cell" data-field="contract_type" data-value="${emp.contract_type}" data-id="${emp.staff_id}" data-employee-data='${JSON.stringify(emp).replace(/'/g, "&#39;")}' data-mode="excel">
                            <span class="badge bg-${contractBadge}">${escapeHtml(emp.contract_type)}</span>
                        </td>
                        <td class="text-center">${statusBadge}</td>
                    </tr>`;
                    });

                    html += `</tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllExcel">Deselect All</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllExcelBtn">Select All</button>
                    </div>
                    <div>
                        <span id="excelSelectedCount"></span>
                        <button type="button" class="btn btn-success ms-3" id="confirmExcelData">Confirm Selection</button>
                    </div>
                </div>
                <input type="hidden" id="excel_data_hidden" name="excel_data_hidden">`;
                }

                excelPreviewContainer.innerHTML = html;
                attachEditableHandlers();

                if (!hasDuplicates) {
                    const selectAllExcel = document.getElementById('selectAllExcelBtn');
                    const deselectAllExcel = document.getElementById('deselectAllExcel');
                    const confirmExcel = document.getElementById('confirmExcelData');

                    function updateSelection() {
                        const selected = document.querySelectorAll('.excel-checkbox:checked:not([disabled])')
                        .length;
                        let totalGross = 0;
                        document.querySelectorAll('.excel-checkbox:checked:not([disabled])').forEach(cb => {
                            const idx = parseInt(cb.getAttribute('data-idx'));
                            if (excelMasterData[idx] && excelMasterData[idx].exists_in_system) {
                                totalGross += parseNumber(excelMasterData[idx].basic_salary) + parseNumber(
                                    excelMasterData[idx].allowances);
                            }
                        });

                        const selectedSpan = document.getElementById('excelSelectedCount');
                        if (selectedSpan) selectedSpan.innerHTML =
                            `<i class="fas fa-users me-1"></i> ${selected} selected | Total: TZS ${formatNumber(totalGross)}`;

                        const summaryDiv = document.getElementById('excel-summary');
                        if (summaryDiv) summaryDiv.innerHTML =
                            `<div class="alert alert-info mt-2"><i class="fas fa-chart-line me-1"></i><strong>Selected:</strong> ${selected} | <strong>Total Gross:</strong> TZS ${formatNumber(totalGross)}</div>`;

                        syncExcelDataToHiddenInput();
                    }

                    if (selectAllExcel) selectAllExcel.addEventListener('click', () => {
                        document.querySelectorAll('.excel-checkbox:not([disabled])').forEach(cb => cb
                            .checked = true);
                        updateSelection();
                    });
                    if (deselectAllExcel) deselectAllExcel.addEventListener('click', () => {
                        document.querySelectorAll('.excel-checkbox:not([disabled])').forEach(cb => cb
                            .checked = false);
                        updateSelection();
                    });
                    document.querySelectorAll('.excel-checkbox').forEach(cb => cb.addEventListener('change',
                        updateSelection));

                    if (confirmExcel) {
                        confirmExcel.addEventListener('click', () => {
                            const selected = [];
                            document.querySelectorAll('.excel-checkbox:checked:not([disabled])').forEach(
                                cb => {
                                    const idx = parseInt(cb.getAttribute('data-idx'));
                                    if (excelMasterData[idx] && excelMasterData[idx].exists_in_system) {
                                        selected.push(excelMasterData[idx]);
                                    }
                                });
                            if (!selected.length) {
                                showToast('Please select at least one valid employee to confirm',
                                'warning');
                                return;
                            }
                            const excelDataHidden = document.getElementById('excel_data_hidden');
                            if (excelDataHidden) excelDataHidden.value = JSON.stringify(selected);
                            showToast(`${selected.length} employee(s) confirmed for payroll`, 'success');
                            confirmExcel.disabled = true;
                            isExcelConfirmed = true;
                            updateSubmitButtonState();
                        });
                    }
                    updateSelection();
                }
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
                    const batchId = previousSelect?.value;
                    if (!batchId) return;
                    if (schedulePreviewContainer) {
                        schedulePreviewContainer.style.display = 'block';
                        schedulePreviewContainer.innerHTML =
                            '<div class="text-center py-3"><div class="loading-spinner"></div> Loading...</div>';
                    }
                    isScheduleConfirmed = false;

                    try {
                        const response = await fetch(
                            '{{ route('api.payroll.schedule.details') }}?batch_id=' + batchId, {
                                headers: {
                                    'Authorization': 'Bearer ' +
                                        '{{ session('finance_api_token') }}'
                                }
                            });
                        const data = await response.json();
                        if (data.success && data.data) {
                            selectedScheduleData = data.data;
                            let html = `<div class="selected-schedule-card">
                            <h6>📋 ${escapeHtml(data.data.name)} (${escapeHtml(data.data.month_name)})</h6>
                            <div class="summary-stats mb-2">
                                <div class="summary-item"><strong>Employees:</strong> ${data.data.total_employees}</div>
                                <div class="summary-item"><strong>Total Gross:</strong> ${formatNumber(data.data.total_gross)}</div>
                            </div>
                            <div class="preview-table-container"><table class="preview-table"><thead>
                                <tr><th>#</th><th>STAFF ID</th><th>EMPLOYEE NAME</th>
                                <th class="text-end">BASIC</th><th class="text-end">ALLOWANCES</th>
                                <th class="text-end">GROSS</th></tr></thead><tbody>`;
                            (data.data.employees || []).forEach((emp, i) => {
                                html += `<tr><td class="text-center">${i+1}</td>
                                <td class="text-uppercase"><strong>${escapeHtml(emp.staff_id)}</strong></td>
                                <td class="text-uppercase">${escapeHtml(emp.employee_name)}</td>
                                <td class="text-end">${formatNumber(emp.basic_salary)}</td>
                                <td class="text-end">${formatNumber(emp.allowances)}</td>
                                <td class="text-end"><strong>${formatNumber(emp.gross)}</strong></td>
                            </tr>`;
                            });
                            html += `</tbody></table></div></div>`;
                            if (schedulePreviewContainer) schedulePreviewContainer.innerHTML = html;
                            if (scheduleConfirmContainer) scheduleConfirmContainer.style.display =
                                'block';
                        } else if (schedulePreviewContainer) {
                            schedulePreviewContainer.innerHTML =
                                '<div class="alert alert-warning">Could not load data</div>';
                        }
                    } catch (e) {
                        if (schedulePreviewContainer) schedulePreviewContainer.innerHTML =
                            '<div class="alert alert-danger">Error loading data</div>';
                    }
                });
            }

            const confirmScheduleBtn = document.getElementById('confirmScheduleBtn');
            if (confirmScheduleBtn) {
                confirmScheduleBtn.addEventListener('click', function() {
                    if (selectedScheduleData) {
                        let hidden = document.querySelector('input[name="previous_batch_data"]');
                        if (!hidden) {
                            hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = 'previous_batch_data';
                            const payrollForm = document.getElementById('payrollForm');
                            if (payrollForm) payrollForm.appendChild(hidden);
                        }
                        hidden.value = JSON.stringify(selectedScheduleData);
                        showToast('Schedule confirmed', 'success');
                        this.disabled = true;
                        isScheduleConfirmed = true;
                        updateSubmitButtonState();
                    }
                });
            }

            // ==================== FILTERS TOGGLE ====================
            const toggleFilters = document.getElementById('toggleFilters');
            const filtersSection = document.getElementById('filters_section');
            if (toggleFilters && filtersSection) {
                toggleFilters.addEventListener('click', function(e) {
                    e.preventDefault();
                    filtersSection.style.display = filtersSection.style.display === 'none' ? 'flex' :
                    'none';
                    this.innerHTML = filtersSection.style.display === 'none' ?
                        '<i class="fas fa-filter me-1"></i> Advanced Filters' :
                        '<i class="fas fa-filter me-1"></i> Hide Filters';
                });
            }

            // ==================== FORM SUBMIT VALIDATION ====================
            const submitBtn = document.getElementById('submitBtn');

            function updateSubmitButtonState() {
                const method = document.querySelector('input[name="generation_method"]:checked')?.value;
                let isConfirmed = false;
                let selectedCount = 0;

                if (!method) {
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-cogs me-2"></i> Generate Payroll';
                    }
                    return;
                }

                if (method === 'contracts') {
                    isConfirmed = isContractsConfirmed;
                    if (isConfirmed) {
                        try {
                            const contractsData = document.getElementById('contracts_data')?.value;
                            if (contractsData) selectedCount = JSON.parse(contractsData).length;
                        } catch (e) {}
                    }
                } else if (method === 'excel_upload') {
                    isConfirmed = isExcelConfirmed;
                    if (isConfirmed) {
                        try {
                            const excelData = document.getElementById('excel_data_hidden')?.value;
                            if (excelData) selectedCount = JSON.parse(excelData).length;
                        } catch (e) {}
                    }
                } else if (method === 'previous_batch') {
                    isConfirmed = isScheduleConfirmed;
                    if (isConfirmed) selectedCount = 1;
                }

                if (submitBtn) {
                    submitBtn.disabled = !isConfirmed;
                    if (isConfirmed) {
                        submitBtn.title = `Generate payroll for ${selectedCount} selected employee(s)`;
                        submitBtn.innerHTML =
                        `<i class="fas fa-cogs me-2"></i> Generate Payroll (${selectedCount})`;
                    } else {
                        submitBtn.title = 'Please select and confirm employees first';
                        submitBtn.innerHTML = '<i class="fas fa-cogs me-2"></i> Generate Payroll';
                    }
                }
            }

            const payrollForm = document.getElementById('payrollForm');
            if (payrollForm) {
                payrollForm.addEventListener('submit', function(e) {
                    const method = document.querySelector('input[name="generation_method"]:checked')?.value;

                    if (method === 'excel_upload') {
                        const selectedCount = syncExcelDataToHiddenInput();
                        const excelDataHidden = document.getElementById('excel_data_hidden');

                        if (!isExcelConfirmed) {
                            showToast('Please select and confirm employees from Excel first', 'warning');
                            e.preventDefault();
                            return;
                        }
                        if (!excelDataHidden || !excelDataHidden.value || excelDataHidden.value === '[]') {
                            showToast(
                                'No employees selected. Please check your selection and click Confirm.',
                                'warning');
                            e.preventDefault();
                            return;
                        }
                    }

                    if (!method) {
                        showToast('Please select a generation method', 'warning');
                        e.preventDefault();
                        return;
                    }

                    if (method === 'contracts' && !isContractsConfirmed) {
                        showToast('Please select and confirm employees from contracts first', 'warning');
                        e.preventDefault();
                        return;
                    }

                    if (method === 'previous_batch' && !isScheduleConfirmed) {
                        showToast('Please load and confirm a previous schedule first', 'warning');
                        e.preventDefault();
                        return;
                    }

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
                    }
                });
            }

            // Initialize
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-cogs me-2"></i> Generate Payroll';
            }
            updateSubmitButtonState();

        });
    </script>
@endsection
