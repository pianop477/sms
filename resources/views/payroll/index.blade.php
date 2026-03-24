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

        .btn-filter {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 8px 15px;
            transition: all 0.3s;
        }

        .btn-filter:hover,
        .btn-filter.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
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

        .status-cancelled {
            background: #ffebee;
            color: #c62828;
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
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="fas fa-calculator me-2 text-primary"></i> Payroll Management
                        </h4>
                        <p class="text-muted mb-0">Manage and track all payroll batches</p>
                    </div>
                    <a href="{{ route('payroll.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Generate Payroll
                    </a>
                </div>

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card bg-primary-custom text-white">
                            <div class="card-body">
                                <div class="card-title">Total Payrolls</div>
                                <div class="card-value">{{ number_format($statistics['total_batches'] ?? 0) }}</div>
                            </div>
                            <i class="fas fa-calculator card-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card bg-success-custom text-white">
                            <div class="card-body">
                                <div class="card-title">Finalized</div>
                                <div class="card-value">{{ number_format($statistics['finalized_count'] ?? 0) }}</div>
                            </div>
                            <i class="fas fa-check-circle card-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card bg-warning-custom text-white">
                            <div class="card-body">
                                <div class="card-title">Draft</div>
                                <div class="card-value">{{ number_format($statistics['draft_count'] ?? 0) }}</div>
                            </div>
                            <i class="fas fa-pen-fancy card-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card bg-info-custom text-white">
                            <div class="card-body">
                                <div class="card-title">Total Employees</div>
                                <div class="card-value">{{ number_format($statistics['total_employees'] ?? 0) }}</div>
                            </div>
                            <i class="fas fa-users card-icon"></i>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="filter-bar">
                    <div class="row align-items-center">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <select id="filter-status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="calculated" {{ request('status') == 'calculated' ? 'selected' : '' }}>
                                    Calculated</option>
                                <option value="finalized" {{ request('status') == 'finalized' ? 'selected' : '' }}>Finalized
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <input type="text" id="filter-year" class="form-control form-control-sm"
                                placeholder="Year (e.g., 2024)" value="{{ request('year') }}">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <input type="text" id="filter-search" class="form-control form-control-sm"
                                placeholder="Search by batch number or name" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm w-100" id="apply-filters">
                                    <i class="fas fa-search me-1"></i> Apply
                                </button>
                                <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm w-100">
                                    <i class="fas fa-times me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payroll Table --}}
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-payroll mb-0">
                                <thead>
                                    <tr>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($batches['data'] ?? [] as $index => $batch)
                                        <tr>
                                            <td class="ps-3 fw-semibold">{{ $batches['from'] + $index }}</td>
                                            <td>
                                                <span class="fw-semibold">{{ $batch['batch_number'] }}</span>
                                                {{-- <small class="text-muted">ID: {{ $batch['id'] }}</small> --}}
                                            </td>
                                            <td>{{ $batch['name'] }}</td>
                                            <td>
                                                <i class="far fa-calendar-alt me-1 text-muted"></i>
                                                {{ \Carbon\Carbon::parse($batch['payroll_month'] . '-01')->format('F Y') }}
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-light text-dark">{{ $batch['payroll_employees_count'] ?? 0 }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-medium">
                                                    {{ number_format($batch['summary']['total_gross_salary'] ?? 0, 0) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-success">
                                                    {{ number_format($batch['summary']['total_net_salary'] ?? 0, 0) }}</span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $statusClass =
                                                        [
                                                            'draft' => 'status-draft',
                                                            'calculated' => 'status-calculated',
                                                            'finalized' => 'status-finalized',
                                                            'cancelled' => 'status-cancelled',
                                                        ][$batch['status']] ?? 'status-draft';
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">
                                                    <i
                                                        class="fas
                                                {{ $batch['status'] == 'finalized' ? 'fa-check-circle' : ($batch['status'] == 'calculated' ? 'fa-calculator' : ($batch['status'] == 'draft' ? 'fa-pen' : 'fa-times-circle')) }}
                                                me-1"></i>
                                                    {{ ucfirst($batch['status']) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ ucwords(strtolower($batch['generated_by'])) }}
                                            </td>
                                            {{-- resources/views/payroll/index.blade.php --}}
                                            <td class="text-center">
                                                <div class="action-btns">
                                                    {{-- ✅ View button - using hash --}}
                                                    <a href="{{ route('payroll.show', $batch['hash']) }}"
                                                        class="action-btn btn-view" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    {{-- ✅ Calculate button for draft batches --}}
                                                    @if ($batch['status'] == 'draft')
                                                        <button type="button" class="action-btn btn-calculate"
                                                            onclick="calculatePayroll('{{ $batch['hash'] }}')"
                                                            title="Calculate Payroll">
                                                            <i class="fas fa-calculator"></i>
                                                        </button>

                                                        {{-- ✅ Delete button for draft batches --}}
                                                        <button type="button" class="action-btn btn-danger"
                                                            onclick="deletePayroll('{{ $batch['hash'] }}')"
                                                            title="Delete Payroll">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif

                                                    {{-- ✅ Finalize button for calculated batches --}}
                                                    @if ($batch['status'] == 'calculated')
                                                        <button type="button" class="action-btn btn-finalize"
                                                            onclick="finalizePayroll('{{ $batch['hash'] }}')"
                                                            title="Finalize Payroll">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    @endif

                                                    {{-- ✅ Download buttons for finalized batches --}}
                                                    @if ($batch['status'] == 'finalized')
                                                        <a href="{{ route('payroll.download-slips', $batch['hash']) }}"
                                                            class="action-btn btn-download" title="Download Salary Slips">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <a href="{{ route('payroll.download-summary', $batch['hash']) }}"
                                                            class="action-btn btn-export" title="Download Summary">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <style>
                                            /* Add these styles */
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

                                            /* Delete button styling */
                                            .btn-danger {
                                                background: #dc3545;
                                                color: white;
                                            }

                                            .btn-danger:hover {
                                                background: #c82333;
                                                color: white;
                                                transform: translateY(-2px);
                                            }
                                        </style>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h5 class="mt-2">No Payroll Batches Found</h5>
                                                <p class="text-muted mb-3">Get started by generating your first payroll</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Pagination --}}
                @if (isset($batches['last_page']) && $batches['last_page'] > 1)
                    <div class="pagination-custom d-flex justify-content-end mt-3">
                        <nav>
                            <ul class="pagination">
                                <li class="page-item {{ $batches['current_page'] == 1 ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $batches['prev_page_url'] ?? '#' }}">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                @for ($i = 1; $i <= $batches['last_page']; $i++)
                                    <li class="page-item {{ $batches['current_page'] == $i ? 'active' : '' }}">
                                        <a class="page-link"
                                            href="{{ route('payroll.index', array_merge(request()->query(), ['page' => $i])) }}">
                                            {{ $i }}
                                        </a>
                                    </li>
                                @endfor
                                <li
                                    class="page-item {{ $batches['current_page'] == $batches['last_page'] ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $batches['next_page_url'] ?? '#' }}">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Apply filters
        document.getElementById('apply-filters').addEventListener('click', function() {
            let status = document.getElementById('filter-status').value;
            let year = document.getElementById('filter-year').value;
            let search = document.getElementById('filter-search').value;

            let url = new URL(window.location.href);
            if (status) url.searchParams.set('status', status);
            else url.searchParams.delete('status');
            if (year) url.searchParams.set('year', year);
            else url.searchParams.delete('year');
            if (search) url.searchParams.set('search', search);
            else url.searchParams.delete('search');
            url.searchParams.set('page', '1');

            window.location.href = url.toString();
        });

        // Calculate payroll function
        function calculatePayroll(batchId) {
            if (confirm(
                    'Are you sure you want to calculate this payroll? This will compute PAYE, NSSF, and net salaries.')) {
                const button = event.currentTarget;
                const originalHtml = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                button.disabled = true;

                fetch('{{ url('/payroll') }}/' + batchId + '/calculate', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                            button.innerHTML = originalHtml;
                            button.disabled = false;
                        }
                    })
                    .catch(error => {
                        alert('Connection error: ' + error);
                        button.innerHTML = originalHtml;
                        button.disabled = false;
                    });
            }
        }

        // Finalize payroll function
        function finalizePayroll(batchId) {
            if (confirm('Are you sure you want to finalize this payroll? This action cannot be undone.')) {
                const button = event.currentTarget;
                const originalHtml = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                button.disabled = true;

                fetch('{{ url('/payroll') }}/' + batchId + '/finalize', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                            button.innerHTML = originalHtml;
                            button.disabled = false;
                        }
                    })
                    .catch(error => {
                        alert('Connection error: ' + error);
                        button.innerHTML = originalHtml;
                        button.disabled = false;
                    });
            }
        }
        // Enter key on search
        document.getElementById('filter-search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('apply-filters').click();
            }
        });
    </script>

@endsection
