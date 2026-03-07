@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --danger-gradient: linear-gradient(135deg, #f54b64 0%, #f78361 100%);
            --info-gradient: linear-gradient(135deg, #36b9cc 0%, #4e73df 100%);
            --orange-gradient: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        .page-header {
            background: var(--success-gradient);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(25deg);
        }

        .page-header h4 {
            margin: 0;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }

        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
            border-left: 4px solid;
            margin-bottom: 1rem;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stats-card.pending {
            border-left-color: #f6c23e;
        }

        .stats-card.waiting {
            border-left-color: #f39c12;
        }

        .stats-card.activated {
            border-left-color: #36b9cc;
        }

        .stats-card.rejected {
            border-left-color: #e74a3b;
        }

        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== UNIFORM CARD HEIGHT WITH SCROLLABLE CONTENT ===== */
        .uniform-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
            height: 600px;
            /* Fixed height for all cards */
            display: flex;
            flex-direction: column;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%);
            color: white;
            padding: 1.2rem 1.5rem;
            border: none;
            flex-shrink: 0;
            /* Prevents header from shrinking */
        }

        .card-body-scrollable {
            padding: 1.5rem;
            overflow-y: auto;
            /* Makes content scrollable */
            flex: 1;
            /* Takes remaining space */
            min-height: 0;
            /* Important for flex child scrolling */
        }

        /* Custom scrollbar styling */
        .card-body-scrollable::-webkit-scrollbar {
            width: 6px;
        }

        .card-body-scrollable::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .card-body-scrollable::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%);
            border-radius: 10px;
        }

        .card-body-scrollable::-webkit-scrollbar-thumb:hover {
            background: #4e73df;
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .list-group-item {
            border: 1px solid #e3e6f0;
            padding: 1rem 1.5rem;
            transition: all 0.3s;
            border-radius: 10px !important;
            margin-bottom: 0.5rem;
        }

        .list-group-item:hover {
            background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
            border-color: #4e73df;
            transform: translateX(5px);
        }

        .badge-counter {
            background: #4e73df;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
        }

        /* Table Styling */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(78, 115, 223, 0.05);
            transform: scale(1.01);
        }

        /* Badges */
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }

        .badge-pending {
            background: linear-gradient(135deg, #f6c23e 0%, #f39c12 100%);
            color: white;
        }

        .badge-approved {
            background: linear-gradient(135deg, #1cc88a 0%, #185a9d 100%);
            color: white;
        }

        .badge-rejected {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
            color: white;
        }

        .badge-activated {
            background: linear-gradient(135deg, #36b9cc 0%, #4e73df 100%);
            color: white;
        }

        .badge-waiting {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }

        /* Buttons */
        .btn-manage {
            background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
            width: 100%;
        }

        .btn-manage:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(78, 115, 223, 0.4);
            color: white;
        }

        .btn-approve {
            background: linear-gradient(135deg, #1cc88a 0%, #185a9d 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-reject {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-upload-signed {
            background: linear-gradient(135deg, #36b9cc 0%, #4e73df 100%);
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 0.25rem;
        }

        .btn-upload-signed:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(52, 152, 219, 0.4);
            color: white;
        }

        .btn-view {
            background: linear-gradient(135deg, #6c757d 0%, #5a5c69 100%);
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(108, 117, 125, 0.4);
            color: white;
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .modal-header .close {
            color: white;
            opacity: 1;
            text-shadow: none;
        }

        .modal-body {
            padding: 2rem;
        }

        /* Detail Cards */
        .detail-card {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #4e73df;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.2rem;
        }

        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: #343a40;
        }

        .attachment-link {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 50px;
            color: #4e73df;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .attachment-link:hover {
            background: #4e73df;
            color: white;
            text-decoration: none;
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-control-custom:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .signature-status {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .status-waiting {
            background-color: #f39c12;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.7;
                transform: scale(1.1);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
        }

        .nav-tabs .nav-link.active {
            color: #4e73df;
            border-bottom: 3px solid #4e73df;
            background: transparent;
        }

        .custom-file-label {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }

        .custom-file-input:focus~.custom-file-label {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Empty state styling */
        .empty-state-scrollable {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 300px;
            text-align: center;
            color: #6c757d;
        }

        .empty-state-scrollable i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }

        /* Ensure all cards have same height */
        .row.equal-height-cards {
            display: flex;
            flex-wrap: wrap;
        }

        .row.equal-height-cards>[class*='col-'] {
            display: flex;
            flex-direction: column;
        }
    </style>

    <div class="page-header">
        <h4>
            <i class="fas fa-file-contract"></i>
             CONTRACT MANAGEMENT DASHBOARD
        </h4>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card pending">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Pending Requests</h6>
                        <h3 class="mb-0">{{ $contractRequests->count() }}</h3>
                    </div>
                    <div class="icon-circle" style="background: linear-gradient(135deg, #f6c23e 0%, #f39c12 100%);">
                        <i class="fas fa-clock fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card waiting">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Waiting Signature</h6>
                        <h3 class="mb-0">{{ $approvedContracts->count() }}</h3>
                    </div>
                    <div class="icon-circle" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                        <i class="fas fa-file-signature fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card activated">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Activated</h6>
                        <h3 class="mb-0">{{ $contracts->where('status', 'activated')->count() }}</h3>
                    </div>
                    <div class="icon-circle" style="background: linear-gradient(135deg, #36b9cc 0%, #4e73df 100%);">
                        <i class="fas fa-check-double fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card rejected">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Rejected</h6>
                        <h3 class="mb-0">{{ $contracts->where('status', 'rejected')->count() }}</h3>
                    </div>
                    <div class="icon-circle" style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);">
                        <i class="fas fa-times-circle fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Three Cards with Uniform Height and Scrollable Content -->
    <div class="row equal-height-cards">
        <!-- Left Column - Activated Contracts Archive -->
        <div class="col-lg-3">
            <div class="uniform-card">
                <div class="card-header-custom">
                    <h5 class="header-title text-white">
                        <i class="fas fa-archive me-2"></i> ACTIVATED ARCHIVE
                    </h5>
                </div>
                <div class="card-body-scrollable">
                    @if ($contractsByYear->isEmpty())
                        <div class="empty-state-scrollable">
                            <i class="fas fa-archive"></i>
                            <h6>No activated contracts yet</h6>
                            <p class="small">Approved contracts will appear here after activation</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach ($contractsByYear as $year => $contracts)
                                <a href="{{ route('contract.by.months', ['year' => $year]) }}"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        {{ $year }}
                                    </span>
                                    <span class="badge-counter">{{ $contracts->count() }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Middle Column - Approved Contracts Waiting for Signature -->
        <div class="col-lg-4">
            <div class="uniform-card">
                <div class="card-header-custom" style="background: var(--orange-gradient);">
                    <h5 class="header-title text-white">
                        <i class="fas fa-file-signature me-2"></i> WAITING FOR SIGNATURE ({{ $approvedContracts->count() }})
                    </h5>
                </div>
                <div class="card-body-scrollable">
                    @if ($approvedContracts->isEmpty())
                        <div class="empty-state-scrollable">
                            <i class="fas fa-file-signature"></i>
                            <h6>No contracts waiting for signature</h6>
                            <p class="small">Approved contracts will appear here</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="bg-warning text-white">
                                    <tr>
                                        <th>APPLICANT</th>
                                        <th>APPROVED</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($approvedContracts as $contract)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <strong>{{ ucwords(strtolower($contract->first_name ?? 'Unknown')) }}
                                                            {{ ucwords(strtolower($contract->last_name ?? 'Staff')) }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <span class="signature-status status-waiting"></span>
                                                            Awaiting signature
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                {{ \Carbon\Carbon::parse($contract->approved_at)->format('d-m-Y') }}
                                                <br>
                                                <small class="badge bg-light">{{ $contract->duration ?? 'N/A' }}
                                                    months</small>
                                            </td>
                                            <td class="align-middle">
                                                <button type="button" class="btn-upload-signed btn-sm" data-toggle="modal"
                                                    data-target="#uploadSignedModal{{ $contract->id }}">
                                                    <i class="fas fa-upload me-1"></i> Upload
                                                </button>
                                                <button type="button" class="btn-view btn-sm mt-1" data-toggle="modal"
                                                    data-target="#viewApprovedModal{{ $contract->id }}">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - New Contract Requests -->
        <div class="col-lg-5">
            <div class="uniform-card">
                <div class="card-header-custom">
                    <h5 class="header-title text-white">
                        <i class="fas fa-inbox me-2"></i> NEW REQUESTS ({{ $contractRequests->count() }})
                    </h5>
                </div>
                <div class="card-body-scrollable">
                    @if ($contractRequests->isEmpty())
                        <div class="empty-state-scrollable">
                            <i class="fas fa-inbox"></i>
                            <h6>No pending contract requests</h6>
                            <p class="small">New applications will appear here</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover" id="myTable">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>APPLICANT</th>
                                        <th>APPLIED</th>
                                        <th>TYPE</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contractRequests as $row)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <strong>{{ ucwords(strtolower($row->first_name ?? 'Unknown')) }}
                                                            {{ ucwords(strtolower($row->last_name ?? 'Staff')) }}</strong>
                                                        <br>
                                                        <small class="text-muted">ID:
                                                            {{ strtoupper($row->staff_id ?? 'N/A') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                {{ \Carbon\Carbon::parse($row->applied_at)->format('d-m-Y H:i') }}
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge bg-light text-dark">
                                                    {{ ucfirst($row->contract_type ?? 'N/A') }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <button type="button" class="btn-manage" data-toggle="modal"
                                                    data-target="#manageModal{{ $row->id }}">
                                                    <i class="fas fa-tasks me-1"></i> MANAGE
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODALS FOR APPROVED CONTRACTS ==================== -->
    @foreach ($approvedContracts as $contract)
        <!-- View Approved Contract Modal -->
        <div class="modal fade" id="viewApprovedModal{{ $contract->id }}" tabindex="-1" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: var(--orange-gradient);">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-file-contract me-2"></i>
                            APPROVED CONTRACT DETAILS
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <div class="detail-label">Applicant Name</div>
                                    <div class="detail-value">
                                        {{ ucwords(strtolower($contract->first_name ?? 'Unknown')) }}
                                        {{ ucwords(strtolower($contract->last_name ?? 'Staff')) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <div class="detail-label">Staff ID</div>
                                    <div class="detail-value">{{ strtoupper($contract->staff_id ?? 'N/A') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <div class="detail-label">Job Title</div>
                                    <div class="detail-value">{{ $contract->job_title ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <div class="detail-label">Duration</div>
                                    <div class="detail-value">{{ $contract->duration ?? 'N/A' }} Months</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <div class="detail-label">Basic Salary</div>
                                    <div class="detail-value">{{ number_format($contract->basic_salary ?? 0, 2) }} TZS
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <div class="detail-label">Allowances</div>
                                    <div class="detail-value">{{ number_format($contract->allowances ?? 0, 2) }} TZS</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <div class="detail-label">Approved By</div>
                                    <div class="detail-value">{{ ucwords(strtolower($contract->approved_by ?? 'N/A')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card">
                                    <div class="detail-label">Approved At</div>
                                    <div class="detail-value">
                                        {{ $contract->approved_at ? \Carbon\Carbon::parse($contract->approved_at)->format('d-m-Y H:i') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-card">
                                    <div class="detail-label mb-2">Documents</div>
                                    <div class="d-flex flex-wrap">
                                        @if ($contract->applicant_file_path)
                                            <a href="{{ asset('storage/' . $contract->applicant_file_path) }}"
                                                target="_blank" class="attachment-link">
                                                <i class="fas fa-file-pdf mr-2"></i> Application Letter
                                            </a>
                                        @endif
                                        @if ($contract->contract_file_path)
                                            <a href="{{ asset('storage/' . $contract->contract_file_path) }}"
                                                target="_blank" class="attachment-link">
                                                <i class="fas fa-file-contract mr-2"></i> Unsigned Contract
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if ($contract->remarks)
                                <div class="col-12">
                                    <div class="detail-card">
                                        <div class="detail-label">Remarks</div>
                                        <div class="detail-value">{{ ucfirst(strtolower($contract->remarks ?? 'None')) }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn-upload-signed" data-toggle="modal"
                            data-target="#uploadSignedModal{{ $contract->id }}" data-dismiss="modal">
                            <i class="fas fa-upload me-2"></i> Upload Signed Contract
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Signed Contract Modal -->
        <div class="modal fade" id="uploadSignedModal{{ $contract->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: var(--info-gradient);">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-file-signature me-2"></i>
                            UPLOAD SIGNED CONTRACT
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('contract.upload.signed', ['id' => Hashids::encode($contract->id)]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>{{ ucwords(strtolower($contract->first_name ?? 'Unknown')) }}
                                    {{ ucwords(strtolower($contract->last_name ?? 'Staff')) }}</strong>
                                - Upload the signed contract document to activate.
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label fw-bold">Signed Contract Document <span
                                        class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="signed_contract" class="custom-file-input"
                                        id="signedContract{{ $contract->id }}" required accept=".pdf,.doc,.docx">
                                    <label class="custom-file-label" for="signedContract{{ $contract->id }}">
                                        Choose signed contract...
                                    </label>
                                </div>
                                <small class="text-muted">Upload the signed PDF/DOC document</small>
                            </div>

                            <div class="modal-footer px-0 pb-0">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-check-circle me-2"></i> ACTIVATE CONTRACT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- ==================== MODALS FOR PENDING CONTRACTS ==================== -->
    @foreach ($contractRequests as $row)
        <!-- Management Modal for Pending Contracts -->
        <div class="modal fade" id="manageModal{{ $row->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-signature me-2"></i>
                            MANAGE CONTRACT: {{ ucwords(strtolower($row->first_name ?? 'Unknown')) }}
                            {{ ucwords(strtolower($row->last_name ?? 'Staff')) }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Applicant Details Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user-circle me-2"></i> APPLICANT INFORMATION
                                </h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="detail-card">
                                            <div class="detail-label">Full Name</div>
                                            <div class="detail-value">
                                                {{ ucwords(strtolower($row->first_name ?? 'Unknown')) }}
                                                {{ ucwords(strtolower($row->last_name ?? 'Staff')) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-card">
                                            <div class="detail-label">ID</div>
                                            <div class="detail-value">{{ strtoupper($row->staff_id ?? 'N/A') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-card">
                                            <div class="detail-label">Gender</div>
                                            <div class="detail-value">{{ ucwords(strtolower($row->gender ?? 'N/A')) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-card">
                                            <div class="detail-label">Contract Type</div>
                                            <div class="detail-value">{{ ucfirst($row->contract_type ?? 'N/A') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Application Letter Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-paperclip me-2"></i> APPLICATION DOCUMENTS
                                </h6>
                                <div class="detail-card">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger fa-2x mr-3"></i>
                                        <div class="mr-3">
                                            <strong>Application Letter</strong>
                                            <br>
                                            <small>Uploaded:
                                                {{ $row->applied_at ? \Carbon\Carbon::parse($row->applied_at)->format('d M Y H:i') : 'N/A' }}</small>
                                        </div>
                                        <div class="ms-auto">
                                            @if ($row->applicant_file_path)
                                                <a href="{{ asset('storage/' . $row->applicant_file_path) }}"
                                                    target="_blank" class="attachment-link">
                                                    <i class="fas fa-eye mr-2"></i> PREVIEW
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Tabs -->
                        <ul class="nav nav-tabs mb-4" id="actionTabs{{ $row->id }}" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="approve-tab{{ $row->id }}" data-toggle="tab"
                                    href="#approve{{ $row->id }}" role="tab">
                                    <i class="fas fa-check-circle text-success me-2"></i> APPROVE CONTRACT
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="reject-tab{{ $row->id }}" data-toggle="tab"
                                    href="#reject{{ $row->id }}" role="tab">
                                    <i class="fas fa-times-circle text-danger me-2"></i> REJECT APPLICATION
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Approve Tab -->
                            <div class="tab-pane fade show active" id="approve{{ $row->id }}" role="tabpanel">
                                <form action="{{ route('contract.approval', ['id' => Hashids::encode($row->id)]) }}"
                                    method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold">Job Title <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="job_title"
                                                    class="form-control form-control-custom" required
                                                    placeholder="e.g. Senior Teacher"
                                                    value="{{ old('job_title', $row->job_title) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold">Duration (Months) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="duration"
                                                    class="form-control form-control-custom" required min="1"
                                                    max="60" placeholder="e.g. 12"
                                                    value="{{ old('duration', $row->duration) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold">Basic Salary (TZS) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="basic_salary"
                                                    class="form-control form-control-custom" required step="0.01"
                                                    placeholder="e.g. 500000"
                                                    value="{{ old('basic_salary', $row->basic_salary) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold">Allowances (TZS) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="allowances"
                                                    class="form-control form-control-custom" required step="0.01"
                                                    placeholder="e.g. 100000"
                                                    value="{{ old('allowances', $row->allowances) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold">Remarks / Additional Notes</label>
                                                <textarea name="remarks" class="form-control form-control-custom" rows="2"
                                                    placeholder="Any special instructions...">{{ old('remarks', $row->remarks) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Contract Document (Unsigned)
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="file" name="contract_file" class="form-control"
                                                    id="contractFile{{ $row->id }}" required
                                                    accept=".pdf,.doc,.docx">

                                                <small class="text-muted">
                                                    Upload the official contract document (PDF, DOC, DOCX)
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer px-0 pb-0">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fas fa-times me-2"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-success"
                                            id="approveBtn{{ $row->id }}">
                                            <i class="fas fa-check-circle me-2"></i> APPROVE & SEND CONTRACT
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Reject Tab -->
                            <div class="tab-pane fade" id="reject{{ $row->id }}" role="tabpanel">
                                <form action="{{ route('contract.rejection', ['id' => Hashids::encode($row->id)]) }}"
                                    method="POST" class="needs-validation" novalidate>
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Rejection will send feedback to the applicant with your remarks.
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold text-danger">Reason for Rejection <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="remarks" class="form-control form-control-custom" rows="4" required
                                                    placeholder="Please provide detailed reason for rejection...">{{ old('remarks', $row->remarks ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer px-0 pb-0">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fas fa-times me-2"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times-circle me-2"></i> REJECT APPLICATION
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Custom file input label update
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            // SweetAlert for success/error messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: true,
                    confirmButtonColor: '#43cea2',
                    timer: 5000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#e74a3b'
                });
            @endif

            // Form validation for all forms
            $('form.needs-validation').on('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass('was-validated');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields correctly.',
                        confirmButtonColor: '#f6c23e'
                    });
                } else {
                    var btn = $(this).find('button[type="submit"]');
                    btn.prop('disabled', true);
                    btn.html('<span class="spinner-border spinner-border-sm me-2"></span> PROCESSING...');
                }
            });

            // Initialize DataTable for pending requests
            @if (!$contractRequests->isEmpty())
                $('#myTable').DataTable({
                    "paging": true,
                    "pageLength": 10,
                    "ordering": true,
                    "info": false,
                    "searching": true,
                    "language": {
                        "search": "<i class='fas fa-search me-2'></i> Search:",
                        "searchPlaceholder": "Filter contracts..."
                    }
                });
            @endif

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
