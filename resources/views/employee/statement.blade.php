{{-- resources/views/employee/statement.blade.php --}}

@extends('SRTDashboard.frame')

@section('content')
    <style>
        .search-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            color: white;
        }

        .search-card label {
            color: white;
            font-weight: 500;
        }

        .search-card .form-control,
        .search-card .form-select {
            border-radius: 8px;
            border: none;
            padding: 10px 15px;
        }

        .btn-search {
            background: white;
            color: #4e73df;
            padding: 10px 25px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-download {
            background: #28a745;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            border: none;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        /* Loading spinner */
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

        /* Results section */
        .results-container {
            display: none;
        }

        .employee-info,
        .summary-stats,
        .statement-card {
            margin-bottom: 20px;
        }

        .employee-info {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 20px;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }

        .stat-box {
            background: #f8f9fc;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-box .stat-value {
            font-size: 22px;
            font-weight: 800;
            color: #4e73df;
        }

        .stat-box .stat-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
        }

        .table-statement th {
            background: #f8f9fc;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 64px;
            color: #d1d3e2;
            margin-bottom: 20px;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Employee Payment Statement
                        </h4>
                        <p class="text-muted mb-0">View and download employee salary payment history</p>
                    </div>
                    <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>

                {{-- Search Form (without staff_type) --}}
                <div class="search-card">
                    <h5 class="mb-3"><i class="fas fa-search me-2"></i> Search Employee Statement</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Staff ID <span class="text-danger">*</span></label>
                            <input type="text" id="staff_id" class="form-control" placeholder="e.g., TCH-001, SSC-0880">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">From Month</label>
                            <input type="month" id="from_month" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">To Month</label>
                            <input type="month" id="to_month" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Year</label>
                            <input type="text" id="year" class="form-control" placeholder="e.g., 2024">
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-search w-100" id="searchBtn">
                                <i class="fas fa-search me-2"></i> Search Statement
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Loading Indicator --}}
                <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                    <div class="loading-spinner"></div>
                    <p class="mt-2 text-muted">Fetching employee statement...</p>
                </div>

                {{-- Results Container --}}
                <div id="resultsContainer" class="results-container"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const searchBtn = document.getElementById('searchBtn');
        const staffIdInput = document.getElementById('staff_id');
        const fromMonthInput = document.getElementById('from_month');
        const toMonthInput = document.getElementById('to_month');
        const yearInput = document.getElementById('year');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const resultsContainer = document.getElementById('resultsContainer');

        function formatNumber(num) {
            return new Intl.NumberFormat().format(num);
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

        // ✅ SweetAlert Toast Notifications
        function showToast(message, type = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            let icon = 'success';
            let bgColor = '#28a745';

            if (type === 'error') {
                icon = 'error';
                bgColor = '#dc3545';
            } else if (type === 'warning') {
                icon = 'warning';
                bgColor = '#ffc107';
            } else if (type === 'info') {
                icon = 'info';
                bgColor = '#17a2b8';
            }

            Toast.fire({
                icon: icon,
                title: message,
                background: bgColor,
                color: '#fff'
            });
        }

        // ✅ SweetAlert Loading
        function showLoadingAlert(message = 'Processing...') {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // ✅ SweetAlert Confirmation Dialog
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

        // ✅ SweetAlert Success
        function showSuccessAlert(message, reload = false) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            }).then(() => {
                if (reload) location.reload();
            });
        }

        // ✅ SweetAlert Error
        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }

        // ✅ SweetAlert Info
        function showInfoAlert(title, message) {
            Swal.fire({
                icon: 'info',
                title: title,
                text: message,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }

        // Search Statement Function
        async function searchStatement() {
            const staffId = staffIdInput.value.trim();

            console.log('Search started:', {
                staffId,
                fromMonth: fromMonthInput.value,
                toMonth: toMonthInput.value,
                year: yearInput.value
            });

            if (!staffId) {
                showToast('Please enter Staff ID', 'warning');
                return;
            }

            const params = new URLSearchParams();
            params.append('staff_id', staffId);
            if (fromMonthInput.value) params.append('from_month', fromMonthInput.value);
            if (toMonthInput.value) params.append('to_month', toMonthInput.value);
            if (yearInput.value) params.append('year', yearInput.value);

            console.log('Request params:', params.toString());

            // Show loading
            loadingIndicator.style.display = 'block';
            resultsContainer.style.display = 'none';
            resultsContainer.innerHTML = '';

            try {
                const url = '{{ route('employee.statement.search') }}?' + params.toString();
                console.log('Fetching URL:', url);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                console.log('Response status:', response.status);

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);

                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response received:', text.substring(0, 500));

                    if (text.includes('login') || text.includes('Login') || response.status === 401) {
                        showToast('Your session has expired. Please login to finance system.', 'warning');
                        setTimeout(() => {
                            window.location.href = '{{ route('login') }}';
                        }, 2000);
                    } else {
                        showErrorAlert('Server error. Please try again later.');
                    }
                    return;
                }

                const data = await response.json();
                console.log('Response data:', data);

                if (!data.success) {
                    console.error('API returned error:', data);
                    if (data.redirect) {
                        showToast(data.message || 'Please login to finance system', 'warning');
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                        return;
                    }
                    showErrorAlert(data.message || 'Failed to fetch statement');
                    return;
                }

                console.log('Statement data received:', {
                    employee: data.employee,
                    summary: data.summary,
                    statementCount: data.statement?.length
                });

                displayResults(data);

            } catch (error) {
                console.error('Fetch error:', error);
                showErrorAlert('Connection error: ' + error.message);
            } finally {
                loadingIndicator.style.display = 'none';
            }
        }

        function displayResults(data) {
            const employee = data.employee || {};
            const summary = data.summary || {};
            const statement = data.statement || [];

            if (statement.length === 0) {
                resultsContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <h5>No Payment Records Found</h5>
                    <p class="text-muted">No salary payment records found for this employee.</p>
                </div>
            `;
                resultsContainer.style.display = 'block';
                return;
            }

            // Build PDF download URL
            const pdfParams = new URLSearchParams();
            pdfParams.append('staff_id', employee.staff_id);
            pdfParams.append('staff_type', employee.staff_type);
            if (fromMonthInput.value) pdfParams.append('from_month', fromMonthInput.value);
            if (toMonthInput.value) pdfParams.append('to_month', toMonthInput.value);
            if (yearInput.value) pdfParams.append('year', yearInput.value);

            const pdfUrl = '{{ route('employee.statement.pdf') }}?' + pdfParams.toString();
            console.log('PDF URL:', pdfUrl);

            let html = `
            <div class="employee-info">
                <div class="row">
                    <div class="col-md-8">
                        <h5><i class="fas fa-user-circle me-2"></i> Employee Information</h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="info-row"><strong>Full Name:</strong> ${escapeHtml(employee.name || 'N/A')}</div>
                                <div class="info-row"><strong>Staff ID:</strong> ${escapeHtml(employee.staff_id || 'N/A')}</div>
                                <div class="info-row"><strong>Staff Type:</strong> ${escapeHtml(employee.staff_type || 'N/A')}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row"><strong>Bank Name:</strong> ${escapeHtml(employee.bank_name || 'N/A')}</div>
                                <div class="info-row"><strong>Account Number:</strong> ${escapeHtml(employee.bank_account || 'N/A')}</div>
                                <div class="info-row"><strong>Period:</strong> ${summary.first_payment || 'N/A'} - ${summary.last_payment || 'N/A'}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="${pdfUrl}" class="btn btn-download mt-4" target="_blank">
                            <i class="fas fa-download me-2"></i> Download PDF Statement
                        </a>
                    </div>
                </div>
            </div>

            <div class="summary-stats">
                <div class="stat-box"><div class="stat-value">${summary.total_months || 0}</div><div class="stat-label">Total Months</div></div>
                <div class="stat-box"><div class="stat-value">TZS ${formatNumber(summary.total_gross || 0)}</div><div class="stat-label">Total Gross</div></div>
                <div class="stat-box"><div class="stat-value">TZS ${formatNumber(summary.total_nssf || 0)}</div><div class="stat-label">Total NSSF</div></div>
                <div class="stat-box"><div class="stat-value">TZS ${formatNumber(summary.total_paye || 0)}</div><div class="stat-label">Total PAYE</div></div>
                <div class="stat-box"><div class="stat-value">TZS ${formatNumber(summary.total_heslb || 0)}</div><div class="stat-label">Total HESLB</div></div>
                <div class="stat-box"><div class="stat-value text-success">TZS ${formatNumber(summary.total_net || 0)}</div><div class="stat-label">Total Net Paid</div></div>
            </div>

            <div class="statement-card">
                <h5 class="mb-3"><i class="fas fa-table me-2"></i> Monthly Payment Breakdown</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-statement">
                        <thead>
                            <tr><th>#</th><th>Month</th><th>Payment Date</th><th class="text-end">Basic Salary</th><th class="text-end">Allowances</th>
                            <th class="text-end">Gross Pay</th><th class="text-end">NSSF</th><th class="text-end">PAYE</th>
                            <th class="text-end">HESLB</th><th class="text-end">Net Pay</th><th class="text-end">Amount Paid</th></thead>
                        <tbody>
        `;

            statement.forEach((row, idx) => {
                html += `
                <tr>
                    <td class="text-center">${idx + 1}</td>
                    <td>${escapeHtml(row.month_name || row.month)}</td>
                    <td>${escapeHtml(row.payment_date || 'N/A')}</td>
                    <td class="text-end">${formatNumber(row.basic_salary || 0)}</td>
                    <td class="text-end">${formatNumber(row.total_allowances || 0)}</td>
                    <td class="text-end fw-bold">${formatNumber(row.gross_salary || 0)}</td>
                    <td class="text-end">${formatNumber(row.deductions?.nssf || 0)}</td>
                    <td class="text-end">${formatNumber(row.deductions?.paye || 0)}</td>
                    <td class="text-end">${formatNumber(row.deductions?.heslb || 0)}</td>
                    <td class="text-end text-success fw-bold">${formatNumber(row.net_salary || 0)}</td>
                    <td class="text-end text-primary fw-bold">${formatNumber(row.amount_paid || 0)}</td>
                </tr>
            `;
            });

            html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                                <td class="text-end fw-bold">${formatNumber(summary.total_gross || 0)}</td>
                                <td class="text-end fw-bold">${formatNumber(summary.total_nssf || 0)}</td>
                                <td class="text-end fw-bold">${formatNumber(summary.total_paye || 0)}</td>
                                <td class="text-end fw-bold">${formatNumber(summary.total_heslb || 0)}</td>
                                <td class="text-end fw-bold">${formatNumber(summary.total_net || 0)}</td>
                                <td class="text-end fw-bold">${formatNumber(summary.total_paid || 0)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        `;

            resultsContainer.innerHTML = html;
            resultsContainer.style.display = 'block';

            // ✅ Show success notification
            showToast(`Statement for ${employee.name || employee.staff_id} loaded successfully`, 'success');
        }

        // Event listeners
        searchBtn.addEventListener('click', searchStatement);
        staffIdInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchStatement();
        });
    </script>
@endsection
