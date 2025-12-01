@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-table {
            background-color: white;
        }

        .progress-table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .progress-table th {
            padding: 15px 10px;
            font-weight: 600;
        }

        .progress-table td {
            padding: 15px 10px;
            vertical-align: middle;
        }

        .btn-xs {
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.35rem;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-buttons a, .action-buttons button {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid #e3e6f0;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ccc !important;
            border-radius: 4px !important;
            padding: 6px 12px !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .student-info-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .badge-stream {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-stream-A {
            background-color: #e8f0fe;
            color: #1a73e8;
        }

        .badge-stream-B {
            background-color: #e6f4ea;
            color: #0f9d58;
        }

        .badge-stream-C {
            background-color: #fce8e6;
            color: #d93025;
        }

        .form-control:focus, .select2-container--focus .select2-selection {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="header-title">Payment Batches</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#addBillModal">
                                        <i class="fas fa-upload me-1"></i> Upload Batch
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Teachers Table -->
                        <div class="single-table">
                            <!-- Search Form -->
                            <div class="row mb-3">
                                <div class="col-md-2 mb-3">
                                    <form method="GET" action="{{ url()->current() }}">
                                        <!-- Hidden fields to preserve search parameter -->
                                        @if(request('search'))
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                        @endif

                                        @php
                                            $currentYear  = (int) date('Y');
                                            $start        = 2024;
                                            $end          = $currentYear + 1; // mwaka mmoja mbele
                                            $selectedYear = (int) request('year', $currentYear);
                                        @endphp

                                        <select name="year" id="selectYear" class="form-control-custom" onchange="this.form.submit()">
                                            <option value="">-- Filter by Year --</option>

                                            @for ($y = $end; $y >= $start; $y--)
                                                <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <!-- Table Section -->
                            <div class="table-responsive">
                                <table class="table table-hover progress-table table-responsive-md">
                                    <thead>
                                        <tr>
                                            <th scope="col">Batch Name</th>
                                            <th scope="col">Batch Number</th>
                                            <th scope="col">Year</th>
                                            <th scope="col">Issued by</th>
                                            <th scope="col">Issued at</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($batch as $row)
                                            <tr>
                                                <td class="fw-bold">{{ strtoupper($row->batch_name) }}</td>
                                                <td>
                                                    {{strtoupper($row->batch_number)}}
                                                </td>
                                                <td class="text-capitalize">
                                                   {{ $row->year }}
                                                </td>
                                                <td>
                                                    @php
                                                        $user = \App\Models\User::find($row->created_by);
                                                    @endphp
                                                    {{ ucwords(strtolower($user->first_name . ' ' . $user->last_name)) }}
                                                </td>
                                                <td>
                                                    {{ $row->created_at }}
                                                </td>
                                                <td class="">
                                                    <ul class="d-flex justify-content-center">
                                                        <li class="mr-3">
                                                            <a href="{{route('batch.download', ['batch' => Hashids::encode($row->id)])}}" class="btn btn-outline-primary btn-xs" onclick="return confirm('Are you sure you want to download this batch?')">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{route('batch.delete', ['batch' => Hashids::encode($row->id)])}}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn p-1 btn-outline-danger" onclick="return confirm('Are you sure you want to delete this Batch?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    @if(request('search'))
                                                        No Batch found for"
                                                        @if(request('year'))
                                                            in {{ request('year') }}
                                                        @endif
                                                    @elseif(request('year'))
                                                        No Batch found for year {{ request('year') }}
                                                    @else
                                                        No Batch found in the current academic year
                                                    @endif
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
    </div>
    <style>
        .btn-action {
            background-color: #cccccc;
            border: 1px solid #ced4da;
            color: #212529;
            font-weight: bold
        }
    </style>

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addBillModal" tabindex="-1" aria-labelledby="addBillModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBillModalLabel">Upload New Bill Batch</h5>
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <!-- Success/Error Message Display Area -->
                    <div id="uploadMessage" style="display: none;"></div>

                    <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data" id="billUploadForm" data-no-preloader>
                        @csrf
                        <!-- File Upload Section -->
                        <div class="row" id="fileUploadInput">
                            <div class="col-md-6">
                                <label for="uploadFile" class="form-label">Upload Excel File <span class="text-danger">*</span></label>
                                <input type="file" name="upload_file" class="form-control-custom" id="uploadFile"
                                    accept=".xlsx,.xls" required>
                                <div class="form-text text-muted">Only Excel files (.xlsx, .xls) are allowed</div>
                                <div class="invalid-feedback" id="fileError">Please select a valid Excel file</div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex float-right btn py-4">
                                    <a href="{{ route('template.export') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-download"></i> Download Bill Template
                                    </a>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Data Preview Section -->
                        <div class="row" id="previewSection" style="display: none;">
                            <div class="col-12">
                                <div id="dataPreviewArea">
                                    <!-- Data will be loaded here via AJAX -->
                                </div>
                            </div>
                        </div>

                        <!-- Batch Details Section -->
                        <div class="row mt-4" id="batchDetailsSection" style="display: none;">
                            <div class="col-12">
                                <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i> Batch Details</h6>
                                <div class="row" id="batchFormInputs">
                                    <div class="col-md-4 mb-3">
                                        <label for="batchName" class="form-label">Batch Name <span class="text-danger">*</span></label>
                                        <input type="text" name="batch_name" class="form-control-custom" id="batchName"
                                            value="{{ old('batch_name') }}" placeholder="e.g., OCTOBER 2024 SCHOOL FEES" required>
                                        <div class="invalid-feedback">Please provide a batch name</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="batch_number" class="form-label">Batch Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control-custom" name="batch_number" id="batch_number"
                                            placeholder="BILL2024-001" value="{{ old('batch_number') }}" required>
                                        <div class="invalid-feedback">Please provide a batch number</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control-custom" name="academic_year" id="academic_year"
                                            placeholder="2024" value="{{ old('academic_year') }}" min="2024" max="" required>
                                        <div class="invalid-feedback">Please provide a valid academic year</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="uploadButton" class="btn btn-success" disabled>
                                <i class="fas fa-upload me-2"></i> Upload Bills
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const uploadForm = document.getElementById('billUploadForm');
        const uploadFile = document.getElementById('uploadFile');
        const previewSection = document.getElementById('previewSection');
        const batchDetailsSection = document.getElementById('batchDetailsSection');
        const uploadButton = document.getElementById('uploadButton');
        const dataPreviewArea = document.getElementById('dataPreviewArea');
        const academicYearField = document.getElementById('academic_year');

        let extractedData = null;
        let validationErrors = [];
        let currentPage = 1;
        const recordsPerPage = 10;

        // File upload handler
        uploadFile.addEventListener('change', function() {
            const file = this.files[0];
            const fileError = document.getElementById('fileError');

            if (file) {
                // Validate file type
                const validTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
                if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls)$/)) {
                    fileError.textContent = 'Please select a valid Excel file (.xlsx or .xls)';
                    fileError.style.display = 'block';
                    resetForm();
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    fileError.textContent = 'File size must be less than 5MB';
                    fileError.style.display = 'block';
                    resetForm();
                    return;
                }

                fileError.style.display = 'none';
                extractAndPreviewData(file);
            } else {
                resetForm();
            }
        });

        // Extract and preview data
        function extractAndPreviewData(file) {
            const formData = new FormData();
            formData.append('upload_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            // Show loading state
            dataPreviewArea.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading data...</span>
                    </div>
                    <p class="mt-2">Extracting and validating bill data...</p>
                </div>
            `;

            previewSection.style.display = 'block';
            batchDetailsSection.style.display = 'none';
            uploadButton.disabled = true;

            // AJAX call to extract and validate bill data
            fetch('{{ route("batch.preview") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    extractedData = data.data;
                    validationErrors = data.errors || [];

                    displayDataPreview(extractedData, validationErrors, currentPage);

                    // Auto-set academic year from file data if available
                    if (extractedData.length > 0 && extractedData[0].ACADEMIC_YEAR) {
                        academicYearField.value = extractedData[0].ACADEMIC_YEAR;
                    }

                    // Show batch details if no critical errors
                    if (validationErrors.length === 0 || validationErrors.every(error => error.type === 'warning')) {
                        showBatchDetails();
                    }
                } else {
                    dataPreviewArea.innerHTML = `
                        <div class="alert alert-danger">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Extraction Failed</h6>
                            <p class="mb-0">${data.message || 'Failed to extract data from the file.'}</p>
                        </div>
                    `;

                    showSweetAlert('error', 'Extraction Failed', data.message || 'Failed to extract data from the file.');
                }
            })
            .catch(error => {
                dataPreviewArea.innerHTML = `
                    <div class="alert alert-danger">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Network Error</h6>
                        <p class="mb-0">Failed to process the file. Please try again.</p>
                    </div>
                `;

                showSweetAlert('error', 'Network Error', 'Failed to process the file. Please try again.');
            });
        }

        // Display data preview with validation
        function displayDataPreview(data, errors, page = 1) {
            if (!data || data.length === 0) {
                dataPreviewArea.innerHTML = `
                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> No Data Found</h6>
                        <p class="mb-0">The uploaded file doesn't contain any valid bill data.</p>
                    </div>
                `;
                return;
            }

            const totalPages = Math.ceil(data.length / recordsPerPage);
            const startIndex = (page - 1) * recordsPerPage;
            const endIndex = Math.min(startIndex + recordsPerPage, data.length);
            const pageData = data.slice(startIndex, endIndex);
            const columns = Object.keys(data[0] || {});

            let html = '';

            // Validation Summary
            if (errors.length > 0) {
                const criticalErrors = errors.filter(e => e.type === 'error');
                const warnings = errors.filter(e => e.type === 'warning');

                html += `
                    <div class="validation-summary alert ${criticalErrors.length > 0 ? 'alert-danger' : 'alert-warning'} mb-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-${criticalErrors.length > 0 ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                            Bill Data Validation Results
                        </h6>
                        <div class="validation-errors">
                            ${criticalErrors.length > 0 ? `
                                <p class="mb-1"><strong>Critical Errors (${criticalErrors.length}):</strong></p>
                                <ul class="mb-2">
                                    ${criticalErrors.map(error => `<li>${error.message}</li>`).join('')}
                                </ul>
                            ` : ''}
                            ${warnings.length > 0 ? `
                                <p class="mb-1"><strong>Warnings (${warnings.length}):</strong></p>
                                <ul>
                                    ${warnings.map(warning => `<li>${warning.message}</li>`).join('')}
                                </ul>
                            ` : ''}
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="alert alert-success mb-3">
                        <h6 class="alert-heading"><i class="fas fa-check-circle me-2"></i> Bill Data Validation Successful</h6>
                        <p class="mb-0">All bill data has been validated successfully. Ready for upload.</p>
                    </div>
                `;
            }

            // Data Table Preview
            html += `
                <div class="table-responsive">
                    <table class="table table-bordered preview-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                ${columns.map(col => `<th>${formatColumnHeader(col)}</th>`).join('')}
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pageData.map((row, index) => {
                                const actualRowIndex = startIndex + index + 1;
                                const rowErrors = errors.filter(error => error.row === actualRowIndex);
                                const status = getRowStatus(rowErrors);

                                return `
                                    <tr class="${getRowClass(rowErrors)}">
                                        <td class="text-muted">${actualRowIndex}</td>
                                        ${columns.map(col => `<td>${formatCellValue(row[col], col)}</td>`).join('')}
                                        <td>
                                            <span class="badge ${getStatusBadgeClass(status)}">
                                                ${status}
                                            </span>
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            `;

            // Pagination Controls
            if (totalPages > 1) {
                html += `
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Showing ${startIndex + 1}-${endIndex} of ${data.length} bill records
                            </small>
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item ${page === 1 ? 'disabled' : ''}">
                                    <button class="page-link" onclick="changePage(${page - 1})" ${page === 1 ? 'disabled' : ''}>
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                </li>
                                ${Array.from({length: totalPages}, (_, i) => i + 1).map(p => `
                                    <li class="page-item ${p === page ? 'active' : ''}">
                                        <button class="page-link" onclick="changePage(${p})">${p}</button>
                                    </li>
                                `).join('')}
                                <li class="page-item ${page === totalPages ? 'disabled' : ''}">
                                    <button class="page-link" onclick="changePage(${page + 1})" ${page === totalPages ? 'disabled' : ''}>
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                `;
            }

            dataPreviewArea.innerHTML = html;
        }

        // Format column headers for display
        function formatColumnHeader(header) {
            const headerMap = {
                'ADMISSION_NUMBER': 'Admission No',
                'CONTROL_NUMBER': 'Control No',
                'ACADEMIC_YEAR': 'Academic Year',
                'SERVICE_NAME': 'Service',
                'AMOUNT': 'Amount',
                'DUE_DATE': 'Due Date'
            };
            return headerMap[header] || header;
        }

        // Format cell values based on column type
        function formatCellValue(value, column) {
            if (value === null || value === undefined || value === '') {
                return '<span class="text-muted">-</span>';
            }

            // Format amount column
            if (column === 'AMOUNT') {
                // Convert to number if it's string with commas
                let numericValue = value;
                if (typeof value === 'string') {
                    numericValue = value.replace(/,/g, '');
                }
                if (!isNaN(numericValue)) {
                    return 'TZS ' + parseFloat(numericValue).toLocaleString();
                }
            }

            // Format date column
            if (column === 'DUE_DATE') {
                // Handle Excel serial dates (numbers)
                if (typeof value === 'number') {
                    try {
                        // Convert Excel serial date to readable format
                        const unixTimestamp = (value - 25569) * 86400;
                        const date = new Date(unixTimestamp * 1000);
                        return date.toLocaleDateString('en-US');
                    } catch (e) {
                        return value;
                    }
                }
                // Handle string dates
                if (typeof value === 'string') {
                    if (value.includes('/')) {
                        return value; // Already in date format
                    } else if (value.includes('-')) {
                        // Convert from yyyy-mm-dd to mm/dd/yyyy
                        const parts = value.split(' ')[0].split('-');
                        if (parts.length === 3) {
                            return `${parts[1]}/${parts[2]}/${parts[0]}`;
                        }
                    }
                }
            }

            return value.toString();
        }

        // Get row status
        function getRowStatus(rowErrors) {
            if (rowErrors.some(error => error.type === 'error')) {
                return 'Error';
            } else if (rowErrors.some(error => error.type === 'warning')) {
                return 'Warning';
            }
            return 'Valid';
        }

        // Get row class based on errors
        function getRowClass(rowErrors) {
            if (rowErrors.some(error => error.type === 'error')) {
                return 'table-danger';
            } else if (rowErrors.some(error => error.type === 'warning')) {
                return 'table-warning';
            }
            return 'table-success';
        }

        // Get status badge class
        function getStatusBadgeClass(status) {
            switch(status) {
                case 'Error': return 'bg-danger';
                case 'Warning': return 'bg-warning';
                case 'Valid': return 'bg-success';
                default: return 'bg-secondary';
            }
        }

        // Show batch details
        function showBatchDetails() {
            batchDetailsSection.style.display = 'block';

            // Auto-generate batch number if empty
            const batchNumberField = document.getElementById('batch_number');
            if (!batchNumberField.value) {
                const now = new Date();
                const timestamp = now.getFullYear() +
                                String(now.getMonth() + 1).padStart(2, '0') +
                                String(now.getDate()).padStart(2, '0') +
                                String(now.getHours()).padStart(2, '0') +
                                String(now.getMinutes()).padStart(2, '0');
                batchNumberField.value = `BCH${timestamp}`;
            }

            validateBatchDetails();
        }

        // Reset form
        function resetForm() {
            previewSection.style.display = 'none';
            batchDetailsSection.style.display = 'none';
            uploadButton.disabled = true;
            extractedData = null;
            validationErrors = [];
            currentPage = 1;
            uploadButton.innerHTML = '<i class="fas fa-upload me-2"></i> Upload Bills';
        }

        // Change page
        window.changePage = function(page) {
            if (extractedData) {
                currentPage = page;
                displayDataPreview(extractedData, validationErrors, currentPage);
            }
        }

        // Validate batch details
        function validateBatchDetails() {
            const batchName = document.getElementById('batchName').value.trim();
            const batchNumber = document.getElementById('batch_number').value.trim();
            const academicYear = document.getElementById('academic_year').value.trim();

            const hasCriticalErrors = validationErrors.some(error => error.type === 'error');
            const isValid = !hasCriticalErrors && batchName && batchNumber && academicYear &&
                            academicYear >= 2020 && academicYear <= 2030;

            uploadButton.disabled = !isValid;
            return isValid;
        }

        // Form submission - AJAX HANDLING
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateBatchDetails()) {
                showSweetAlert('warning', 'Validation Error', 'Please fix all critical errors and fill all required fields correctly.');
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('batch_name', document.getElementById('batchName').value);
            formData.append('batch_number', document.getElementById('batch_number').value);
            formData.append('academic_year', document.getElementById('academic_year').value);
            formData.append('extracted_data', JSON.stringify(extractedData));
            formData.append('_token', '{{ csrf_token() }}');

            // Show loading state
            uploadButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Uploading Bills...';
            uploadButton.disabled = true;

            // AJAX call to store bills
            fetch('{{ route("batch.store") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSweetAlert('success', 'Success!', data.message, true);

                    // Show detailed warning if there are errors
                    if (data.errors && data.errors.length > 0) {
                        setTimeout(() => {
                            showSweetAlert('warning', 'Upload Completed with Warnings',
                                data.message + '\n\n' + data.errors.join('\n'), false);
                        }, 1000);
                    }

                    // Reset form after successful upload
                    setTimeout(() => {
                        resetForm();
                        uploadForm.reset();
                        $('#addBillModal').modal('hide');

                        // Reload the page or update the batches list
                        window.location.reload();
                    }, 3000);

                } else {
                    showSweetAlert('error', 'Upload Failed', data.message);
                    uploadButton.innerHTML = '<i class="fas fa-upload me-2"></i> Upload Bills';
                    uploadButton.disabled = false;
                }
            })
            .catch(error => {
                showSweetAlert('error', 'Network Error', 'Failed to upload bills. Please check your connection and try again.');
                uploadButton.innerHTML = '<i class="fas fa-upload me-2"></i> Upload Bills';
                uploadButton.disabled = false;
            });
        });

        // SweetAlert function
        function showSweetAlert(icon, title, text, showConfirmButton = false) {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                toast: true,
                position: 'top-end',
                showConfirmButton: showConfirmButton,
                timer: showConfirmButton ? null : 5000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInRight'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutRight'
                }
            });
        }

        // Real-time validation
        document.getElementById('batchName').addEventListener('input', validateBatchDetails);
        document.getElementById('batch_number').addEventListener('input', validateBatchDetails);
        document.getElementById('academic_year').addEventListener('input', validateBatchDetails);
    });
</script>

    <style>
        .preview-table {
            font-size: 0.8rem;
        }

        .preview-table th {
            background-color: #4e73df;
            color: white;
            font-weight: 600;
            padding: 8px 6px;
            white-space: nowrap;
        }

        .preview-table td {
            padding: 6px 4px;
            vertical-align: middle;
        }

        .data-error {
            background-color: #f8d7da !important;
            border-left: 3px solid #dc3545;
        }

        .data-warning {
            background-color: #fff3cd !important;
            border-left: 3px solid #ffc107;
        }

        .data-success {
            background-color: #d1edff !important;
            border-left: 3px solid #0d6efd;
        }

        .validation-summary {
            border-radius: 0.375rem;
            padding: 1rem;
        }

        .validation-errors {
            max-height: 150px;
            overflow-y: auto;
            font-size: 0.875rem;
        }

        .validation-errors ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }

        .pagination-sm .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .page-link {
            color: #4e73df;
        }

        .page-link:hover {
            color: #2e59d9;
        }

        /* Responsive design for table */
        @media (max-width: 768px) {
            .preview-table {
                font-size: 0.7rem;
            }

            .preview-table th,
            .preview-table td {
                padding: 4px 2px;
            }
        }
    </style>

@endsection
