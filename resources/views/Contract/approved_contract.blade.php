@extends('SRTDashboard.frame')
@section('content')

    <style>
        :root {
            --primary-700: #2d3748;
            --success-600: #059669;
            --success-700: #047857;
            --info-600: #2563eb;
            --warning-600: #d97706;
            --danger-600: #dc2626;
        }

        .page-header-activated {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 1.8rem 2.5rem;
            border-radius: 24px;
            margin: 1.5rem 0 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .staff-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view-contract {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .btn-view-contract:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
            color: white;
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 1.8rem 2.2rem;
            border: none;
            position: relative;
        }

        .modal-header-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #3b82f6, #8b5cf6);
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-title i {
            font-size: 2rem;
            color: #10b981;
        }

        .modal-body {
            padding: 2.2rem;
            background: #f8fafc;
        }

        /* Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.2rem;
            margin-bottom: 1.8rem;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #eef2f6;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .info-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.2rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .info-card-header i {
            font-size: 1.5rem;
            padding: 10px;
            border-radius: 14px;
        }

        .info-card-header h6 {
            font-size: 1rem;
            font-weight: 600;
            color: #334155;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .info-value {
            color: #0f172a;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .badge-status {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-expired {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Documents Section */
        .documents-section {
            background: white;
            border-radius: 20px;
            padding: 1.8rem;
            margin-top: 1rem;
            border: 1px solid #eef2f6;
        }

        .documents-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
            color: #0f172a;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .documents-title i {
            color: #10b981;
            font-size: 1.3rem;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .document-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .document-card:hover {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-color: #10b981;
            transform: translateY(-2px);
        }

        .document-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        .document-icon.pdf {
            background: #fee2e2;
            color: #dc2626;
        }

        .document-icon.doc {
            background: #dbeafe;
            color: #2563eb;
        }

        .document-icon.image {
            background: #fef3c7;
            color: #d97706;
        }

        .document-icon.qr {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .document-info {
            flex: 1;
        }

        .document-name {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
            font-size: 0.95rem;
        }

        .document-meta {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Timeline */
        .timeline-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .timeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #10b981;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-date {
            font-size: 0.8rem;
            color: #64748b;
        }

        .timeline-action {
            font-weight: 500;
            color: #0f172a;
        }

        /* Modal Footer */
        .modal-footer-custom {
            background: #f1f5f9;
            padding: 1.5rem 2.2rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .btn-close {
            background: white;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 0.7rem 2rem;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-close:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .documents-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Page Header (existing code remains the same) -->
    <div class="page-header-activated">
        <h4>
            <i class="fas fa-check-circle mr-2"></i>
            Activated Contracts - {{ $month }} {{ $year }}
        </h4>
        <a href="{{ route('contract.by.months', ['year' => $year]) }}" class="btn btn-light">
            <i class="fas fa-arrow-left mr-2"></i> Back to Months
        </a>
    </div>

    <!-- Table Section (existing code remains the same) -->
    @if ($allContracts->isEmpty())
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <h5> No Activated Contracts</h5>
            <p> There are no activated contracts for {{ $month }} {{ $year }}</p>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Staff Name</th>
                                        <th>Staff Type</th>
                                        <th>Contract Type</th>
                                        <th>Activated Date</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allContracts as $index => $contract)
                                        @php
                                            // Handle both array and object - convert to array safely
                                            $contractData = is_array($contract) ? $contract : (array) $contract;

                                            // Extract values with defaults
                                            $firstName = $contractData['first_name'] ?? 'Unknown';
                                            $lastName = $contractData['last_name'] ?? '';
                                            $staffId =
                                                $contractData['staff_id'] ?? ($contractData['applicant_id'] ?? 'N/A');
                                            $staffType = $contractData['staff_type'] ?? 'Staff';
                                            $contractType = $contractData['contract_type'] ?? 'standard';
                                            $activatedAt = $contractData['activated_at'] ?? now();
                                            $duration = $contractData['duration'] ?? 'N/A';
                                            $contractId = $contractData['id'] ?? null;

                                            // ===== CRITICAL: Get status correctly =====
                                            $status = $contractData['status'] ?? ($contract->status ?? 'unknown');

                                            // Determine display based on status
                                            $isActive = $status == 'activated';
                                            $isExpired = $status == 'expired';
                                            $isTerminated = $status == 'terminated';

                                            // For backward compatibility, also check is_active flag
                                            if (!$isActive && !$isExpired && !$isTerminated) {
                                                $isActive = $contractData['is_active'] ?? false;
                                            }

                                            // Log for debugging (remove in production)
                                            // \Log::info("Contract {$contractId} status: {$status}");

                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ ucfirst($firstName) }} {{ ucfirst($lastName) }}</strong>
                                                <br>
                                                <small class="text-muted">ID: {{ strtoupper($staffId) }}</small>
                                            </td>
                                            <td>
                                                <span class="staff-badge">
                                                    <i
                                                        class="fas fa-{{ $staffType == 'Teacher' ? 'chalkboard-teacher' : 'users' }} mr-2"></i>
                                                    {{ $staffType }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ ucfirst($contractType) }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($activatedAt)->format('d M Y') }}</td>
                                            <td>{{ $duration }} months</td>
                                            <td>
                                                @if ($isTerminated)
                                                    <span class="badge" style="background: #6c757d; color: white;">
                                                        <i class="fas fa-ban mr-2"></i> Terminated
                                                    </span>
                                                @elseif ($isActive)
                                                    <span class="badge" style="background: var(--success-600)">
                                                        <i class="fas fa-check-circle mr-2"></i> Active
                                                    </span>
                                                @elseif ($isExpired)
                                                    <span class="badge"
                                                        style="background: var(--danger-600)">
                                                        <i class="fas fa-hourglass-end mr-2"></i> Expired
                                                    </span>
                                                @else;
                                                    <span class="badge" style="background:var(--warning-600)">
                                                        <i class="fas fa-question-circle mr-2"></i> {{ ucfirst($status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn-view-contract" data-bs-toggle="modal"
                                                    data-bs-target="#contractModal{{ $contractId }}">
                                                    <i class="fas fa-eye mr-2"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enterprise Modal for Each Contract -->
        @foreach ($allContracts as $contract)
            @php
                // Handle both array and object
                $contractData = is_array($contract) ? $contract : (array) $contract;
                $contractId = $contractData['id'] ?? $contract->id;
                $firstName = $contractData['first_name'] ?? ($contract->first_name ?? 'Unknown');
                $lastName = $contractData['last_name'] ?? ($contract->last_name ?? '');
                $staffType = $contractData['staff_type'] ?? ($contract->staff_type ?? 'Staff');
                $staffId = $contractData['staff_id'] ?? ($contract->applicant_id ?? 'N/A');
                $phone = $contractData['phone'] ?? ($contract->phone ?? 'N/A');
                $contractType = $contractData['contract_type'] ?? ($contract->contract_type ?? 'standard');
                $duration = $contractData['duration'] ?? ($contract->duration ?? 0);
                $startDate = $contractData['start_date'] ?? ($contract->start_date ?? now());
                $endDate = $contractData['end_date'] ?? ($contract->end_date ?? now());
                $basicSalary = $contractData['basic_salary'] ?? ($contract->basic_salary ?? 0);
                $allowances = $contractData['allowances'] ?? ($contract->allowances ?? 0);
                $approvedBy = $contractData['approved_by'] ?? ($contract->approved_by ?? 'System');
                $activatedAt = $contractData['activated_at'] ?? ($contract->activated_at ?? now());
                $appliedAt = $contractData['applied_at'] ?? ($contract->applied_at ?? $activatedAt);
                $verifyToken = $contractData['verify_token'] ?? ($contract->verify_token ?? 'N/A');
                $contractFilePath = $contractData['contract_file_path'] ?? ($contract->contract_file_path ?? null);
                $applicantFilePath = $contractData['applicant_file_path'] ?? ($contract->applicant_file_path ?? null);
                $qrCodePath = $contractData['qr_code_path'] ?? ($contract->qr_code_path ?? null);
                $isActive = $contractData['is_active'] ?? ($contract->is_active ?? false);
                $netPay = $basicSalary + $allowances;
                $terminatedAt = $contractData['terminated_at'] ?? ($contract->terminated_at ?? null);
                // $contractStatus = $contractData['new_status'] ?? ($contract->new_status);
                $terminationReason = $contractData['termination_reason'] ?? ($contract->termination_reason ?? null);
                $terminationType = $contractData['termination_type'] ?? ($contract->termination_type ?? null);
                $terminatedBy = $contractData['terminated_by'] ?? ($contract->terminated_by ?? null);

                // Determine if contract can be terminated (only active contracts can be terminated)
                $canTerminate = $isActive && $contractData['status'] == 'activated';

                // Generate unique IDs for tabs
                $modalId = 'contractModal' . $contractId;
                $overviewTab = 'overview-' . $contractId;
                $financialTab = 'financial-' . $contractId;
                $documentsTab = 'documents-' . $contractId;
                $timelineTab = 'timeline-' . $contractId;
                $terminationTab = 'termination-' . $contractId; // New tab
                // dd($terminatedAt);
            @endphp

            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header-custom">
                            <div>
                                <h5 class="modal-title">
                                    <i class="fas fa-file-contract"></i>
                                    Contract Agreement Details
                                </h5>
                                <div class="mt-2" style="font-size: 0.9rem; opacity: 0.9;">
                                    {{ ucfirst($firstName ?? 'Unknown') }} {{ ucfirst($lastName ?? 'Unknown') }} •
                                    {{ ucfirst($staffType ?? 'Unknown') }} • ID: {{ strtoupper($staffId ?? 'N/A') }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                @php
                                    // FORCE STATUS FROM DATABASE AGAIN
                                    if (isset($contract) && $contract instanceof \Illuminate\Database\Eloquent\Model) {
                                        $freshContract = \App\Models\school_constracts::find($contract->id);
                                        $currentStatus = $freshContract ? $freshContract->status : 'unknown';
                                    } else {
                                        $currentStatus = $contractData['status'] ?? 'unknown';
                                    }
                                @endphp

                                @if ($currentStatus == 'terminated')
                                    <span class="badge-status" style="background: #fee2e2; color: #991b1b;">
                                        <i class="fas fa-ban mr-2"></i> Terminated
                                    </span>
                                @elseif ($currentStatus == 'activated')
                                    <span class="badge-status badge-active">
                                        <i class="fas fa-circle mr-2" style="font-size: 0.6rem;"></i> Active
                                    </span>
                                @elseif ($currentStatus == 'expired')
                                    <span class="badge-status badge-expired">
                                        <i class="fas fa-circle mr-2" style="font-size: 0.6rem;"></i> Expired
                                    </span>
                                @else
                                    <span class="badge-status" style="background: #6c757d; color: white;">
                                        <i class="fas fa-question-circle mr-2"></i> {{ ucfirst($currentStatus) }}
                                    </span>
                                @endif

                                <!-- Show status for verification -->
                                {{-- <small
                                    style="color: white; background: rgba(0,0,0,0.5); padding: 2px 8px; border-radius: 4px;">
                                    {{ $currentStatus }}
                                </small> --}}

                                <button type="button" class="btn btn-xs btn-danger float-right" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fas fa-close"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Navigation Tabs -->
                        <div class="px-4 pt-4" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <ul class="nav nav-tabs" id="tab-{{ $contractId }}" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="{{ $overviewTab }}-tab" data-bs-toggle="tab"
                                        href="#{{ $overviewTab }}" role="tab" aria-controls="{{ $overviewTab }}"
                                        aria-selected="true">
                                        <i class="fas fa-info-circle mr-2"></i> Overview
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="{{ $financialTab }}-tab" data-bs-toggle="tab"
                                        href="#{{ $financialTab }}" role="tab" aria-controls="{{ $financialTab }}"
                                        aria-selected="false">
                                        <i class="fas fa-coins mr-2"></i> Financial
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="{{ $documentsTab }}-tab" data-bs-toggle="tab"
                                        href="#{{ $documentsTab }}" role="tab" aria-controls="{{ $documentsTab }}"
                                        aria-selected="false">
                                        <i class="fas fa-file-alt mr-2"></i> Attachments
                                        @if ($contractFilePath || $applicantFilePath || $qrCodePath)
                                            <span class="badge badge-success ml-1">
                                                {{ ($contractFilePath ? 1 : 0) + ($applicantFilePath ? 1 : 0) + ($qrCodePath ? 1 : 0) }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="{{ $timelineTab }}-tab" data-bs-toggle="tab"
                                        href="#{{ $timelineTab }}" role="tab" aria-controls="{{ $timelineTab }}"
                                        aria-selected="false">
                                        <i class="fas fa-history mr-2"></i> Timeline
                                    </a>
                                </li>
                                <!-- Termination Tab - Only visible to managers/admins -->
                                @if (auth()->user()->usertype == 1 || auth()->user()->usertype == 2)
                                    <!-- Assuming 1=Admin, 2=Manager -->
                                    <li class="nav-item">
                                        <a class="nav-link" id="{{ $terminationTab }}-tab" data-bs-toggle="tab"
                                            href="#{{ $terminationTab }}" role="tab"
                                            aria-controls="{{ $terminationTab }}" aria-selected="false">
                                            <i class="fas fa-ban mr-2"></i> Termination
                                            @if ($terminatedAt)
                                                <span class="badge badge-danger ml-1">Terminated</span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Tab Content -->
                        <div class="modal-body">
                            <div class="tab-content" id="tab-content-{{ $contractId }}">
                                <!-- Overview Tab (existing code) -->
                                <div class="tab-pane fade show active" id="{{ $overviewTab }}" role="tabpanel"
                                    aria-labelledby="{{ $overviewTab }}-tab">
                                    <!-- ... existing overview content ... -->
                                    <div class="info-grid">
                                        <!-- Contract Information Card -->
                                        <div class="info-card">
                                            <div class="info-card-header">
                                                <i class="fas fa-file-signature"
                                                    style="color: #2563eb; background: #dbeafe;"></i>
                                                <h6> Contract Information</h6>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Contract Type:</span>
                                                <span class="info-value">
                                                    <span class="badge badge-info">
                                                        {{ ucfirst($contractType) }}
                                                        @if ($contractType == 'provision')
                                                            <i class="fas fa-hourglass-half ml-1"></i>
                                                        @else
                                                            <i class="fas fa-file-contract ml-1"></i>
                                                        @endif
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label"> Duration:</span>
                                                <span class="info-value">{{ $duration }} months</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label"> Start Date:</span>
                                                <span
                                                    class="info-value">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label"> End Date:</span>
                                                <span
                                                    class="info-value">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                                            </div>
                                            @if ($terminatedAt)
                                                <div class="info-row"
                                                    style="border-top: 2px solid #dc2626; margin-top: 8px;">
                                                    <span class="info-label text-danger"> Terminated On:</span>
                                                    <span
                                                        class="info-value text-danger">{{ \Carbon\Carbon::parse($terminatedAt)->format('d M Y') }}</span>
                                                </div>
                                            @endif
                                            <div class="info-row">
                                                <span class="info-label"> Time Remaining:</span>
                                                <span class="info-value text-success">
                                                    @php
                                                        $now = now();
                                                        $end = \Carbon\Carbon::parse($endDate);
                                                        $diff = $now->diffInDays($end, false);
                                                    @endphp
                                                    @if ($terminatedAt)
                                                        <span class="text-danger"> Terminated</span>
                                                    @elseif ($diff > 0)
                                                        {{ floor($diff / 30) }} months, {{ $diff % 30 }} days
                                                    @else
                                                        <span class="text-danger"> Expired</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Staff Information Card -->
                                        <div class="info-card">
                                            <div class="info-card-header">
                                                <i class="fas fa-id-card"
                                                    style="color: #8b5cf6; background: #ede9fe;"></i>
                                                <h6> Staff Information</h6>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label"> Full Name:</span>
                                                <span class="info-value">{{ ucfirst($firstName ?? 'Unknown') }}
                                                    {{ ucfirst($lastName ?? 'Unknown') }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label"> Staff Type:</span>
                                                <span class="info-value">{{ ucfirst($staffType ?? 'Unknown') }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label"> Staff ID:</span>
                                                <span class="info-value">{{ strtoupper($staffId ?? 'N/A') }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label"> Phone:</span>
                                                <span class="info-value">
                                                    @if ($phone && $phone != 'N/A')
                                                        <a href="tel:{{ $phone }}">{{ $phone }}</a>
                                                    @else
                                                        <span class="text-muted"> Not Available</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Activation Details Card -->
                                        <div class="info-card">
                                            <div class="info-card-header">
                                                <i class="fas fa-check-circle"
                                                    style="color: #059669; background: #d1fae5;"></i>
                                                <h6> Activation Details</h6>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Activated By:</span>
                                                <span class="info-value">{{ ucfirst($approvedBy ?? 'Unknown') }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Activated At:</span>
                                                <span
                                                    class="info-value">{{ \Carbon\Carbon::parse($activatedAt)->format('d M Y H:i') }}</span>
                                            </div>
                                            @if ($qrCodePath)
                                                <div class="info-row">
                                                    <span class="info-label"> QR Code:</span>
                                                    <span class="info-value">
                                                        <img src="{{ asset('storage/' . $qrCodePath) }}" alt="QR Code"
                                                            style="width: 50px; height: 50px; border-radius: 8px;">
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Quick Actions Card -->
                                        <div class="info-card">
                                            <div class="info-card-header">
                                                <i class="fas fa-bolt" style="color: #d97706; background: #fef3c7;"></i>
                                                <h6> Quick Actions</h6>
                                            </div>
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('contract.letter.pdf', ['id' => Hashids::encode($contractId)]) }}"
                                                    class="btn btn-outline-success btn-sm" target="_blank">
                                                    <i class="fas fa-file-pdf mr-2"></i> Download Approval Letter
                                                </a>
                                                @if ($contractFilePath)
                                                    <a href="{{ asset('storage/' . $contractFilePath) }}"
                                                        class="btn btn-outline-primary btn-sm" target="_blank">
                                                        <i class="fas fa-file-signature mr-2"></i> View Signed Contract
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Financial Tab (existing code) -->
                                <div class="tab-pane fade" id="{{ $financialTab }}" role="tabpanel"
                                    aria-labelledby="{{ $financialTab }}-tab">
                                    <!-- ... existing financial content ... -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-card h-100">
                                                <div class="info-card-header">
                                                    <i class="fas fa-calculator"
                                                        style="color: #059669; background: #d1fae5;"></i>
                                                    <h6> Salary Breakdown</h6>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label">Basic Salary:</span>
                                                    <span class="info-value">TZS {{ number_format($basicSalary) }}</span>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label">Other Allowances:</span>
                                                    <span class="info-value">TZS
                                                        {{ number_format($allowances) }}</span>
                                                </div>
                                                <div class="info-row"
                                                    style="border-top: 2px solid #059669; margin-top: 10px; padding-top: 10px;">
                                                    <span class="info-label fw-bold">Gross Pay:</span>
                                                    <span class="info-value fw-bold text-success"> TZS
                                                        {{ number_format($basicSalary + $allowances) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-card h-100">
                                                <div class="info-card-header">
                                                    <i class="fas fa-chart-pie"
                                                        style="color: #8b5cf6; background: #ede9fe;"></i>
                                                    <h6> Payment Summary</h6>
                                                </div>
                                                <div class="text-center mb-4">
                                                    <div style="font-size: 2.5rem; font-weight: 700; color: #059669;">
                                                        TZS {{ number_format($netPay) }}
                                                    </div>
                                                    <div class="text-muted"> Monthly Net Pay</div>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label"> Payment Frequency:</span>
                                                    <span class="info-value"> Monthly</span>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label"> Payment Method:</span>
                                                    <span class="info-value"> Cash/Bank Transfer</span>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label"> Contract Value:</span>
                                                    <span class="info-value">TZS
                                                        {{ number_format($netPay * $duration) }}</span>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label">Tax Status:</span>
                                                    <span class="info-value badge bg-warning">PAYE not
                                                            Applicable</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents Tab (existing code) -->
                                <div class="tab-pane fade" id="{{ $documentsTab }}" role="tabpanel"
                                    aria-labelledby="{{ $documentsTab }}-tab">
                                    <div class="documents-section" style="margin-top: 0;">
                                        <div class="documents-title">
                                            <i class="fas fa-folder-open"></i>
                                            Contract Documents
                                        </div>
                                        <div class="documents-grid">
                                            <!-- Application Letter -->
                                            @if ($applicantFilePath)
                                                <a href="{{ asset('storage/' . $applicantFilePath) }}"
                                                    class="document-card" target="_blank">
                                                    <div class="document-icon doc">
                                                        <i class="fas fa-file-alt"></i>
                                                    </div>
                                                    <div class="document-info">
                                                        <div class="document-name"> Application Letter</div>
                                                        <div class="document-meta">
                                                            <i class="far fa-calendar-alt mr-1"></i>
                                                            {{ \Carbon\Carbon::parse($appliedAt)->format('d M Y') }}
                                                        </div>
                                                    </div>
                                                    <i class="fas fa-download ml-2" style="color: #64748b;"></i>
                                                </a>
                                            @endif

                                            <!-- Approval Letter -->
                                            <a href="{{ route('contract.letter.pdf', ['id' => Hashids::encode($contractId)]) }}"
                                                class="document-card" target="_blank">
                                                <div class="document-icon pdf">
                                                    <i class="fas fa-file-pdf"></i>
                                                </div>
                                                <div class="document-info">
                                                    <div class="document-name">Approval Letter</div>
                                                    <div class="document-meta">
                                                        <i class="far fa-calendar-alt mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($activatedAt)->format('d M Y') }}
                                                    </div>
                                                </div>
                                                <i class="fas fa-download ml-2" style="color: #64748b;"></i>
                                            </a>

                                            <!-- Signed Contract -->
                                            @if ($contractFilePath)
                                                <a href="{{ asset('storage/' . $contractFilePath) }}"
                                                    class="document-card" target="_blank">
                                                    <div class="document-icon doc">
                                                        <i class="fas fa-file-signature"></i>
                                                    </div>
                                                    <div class="document-info">
                                                        <div class="document-name">Signed Contract</div>
                                                        <div class="document-meta">
                                                            <i class="far fa-calendar-alt mr-1"></i>
                                                            {{ \Carbon\Carbon::parse($activatedAt)->format('d M Y') }}
                                                        </div>
                                                    </div>
                                                    <i class="fas fa-download ml-2" style="color: #64748b;"></i>
                                                </a>
                                            @endif

                                            <!-- QR Code -->
                                            @if ($qrCodePath)
                                                <a href="{{ asset('storage/' . $qrCodePath) }}" class="document-card"
                                                    target="_blank">
                                                    <div class="document-icon qr">
                                                        <i class="fas fa-qrcode"></i>
                                                    </div>
                                                    <div class="document-info">
                                                        <div class="document-name">Verification QR Code</div>
                                                        <div class="document-meta">
                                                            <i class="fas fa-hashtag mr-1"></i>
                                                            {{ substr($verifyToken, 0, 8) }}...
                                                        </div>
                                                    </div>
                                                    <i class="fas fa-download ml-2" style="color: #64748b;"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Timeline Tab (existing code) -->
                                <div class="tab-pane fade" id="{{ $timelineTab }}" role="tabpanel"
                                    aria-labelledby="{{ $timelineTab }}-tab">
                                    <div class="documents-section" style="margin-top: 0;">
                                        <div class="documents-title">
                                            <i class="fas fa-history"></i>
                                            Contract Timeline & History
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-3">Key Events</h6>
                                                <div class="timeline-item">
                                                    <div class="timeline-dot" style="background: #3b82f6;"></div>
                                                    <div class="timeline-content">
                                                        <div class="timeline-date">
                                                            {{ \Carbon\Carbon::parse($appliedAt)->format('d M Y H:i') }}
                                                        </div>
                                                        <div class="timeline-action">Application Submitted</div>
                                                        <div class="text-muted small">Initial contract application</div>
                                                    </div>
                                                </div>
                                                <div class="timeline-item">
                                                    <div class="timeline-dot" style="background: #eab308;"></div>
                                                    <div class="timeline-content">
                                                        <div class="timeline-date">
                                                            {{ \Carbon\Carbon::parse($activatedAt)->format('d M Y H:i') }}
                                                        </div>
                                                        <div class="timeline-action">Contract Activated</div>
                                                        <div class="text-muted small">Approved by
                                                            {{ ucfirst($approvedBy ?? 'Unknown') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="timeline-item">
                                                    <div class="timeline-dot" style="background: #10b981;"></div>
                                                    <div class="timeline-content">
                                                        <div class="timeline-date">
                                                            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</div>
                                                        <div class="timeline-action">Contract Started</div>
                                                        <div class="text-muted small">Effective date</div>
                                                    </div>
                                                </div>
                                                @if ($terminatedAt)
                                                    <div class="timeline-item">
                                                        <div class="timeline-dot" style="background: #dc2626;"></div>
                                                        <div class="timeline-content">
                                                            <div class="timeline-date">
                                                                {{ \Carbon\Carbon::parse($terminatedAt)->format('d M Y H:i') }}
                                                            </div>
                                                            <div class="timeline-action text-danger">Contract Terminated
                                                            </div>
                                                            <div class="text-muted small">Reason:
                                                                {{ $terminationReason ?? 'Not specified' }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-3">Contract Status</h6>
                                                <div class="info-card">
                                                    <div class="info-row">
                                                        <span class="info-label">Current Status:</span>
                                                        @if ($terminatedAt)
                                                             <span class="badge bg-danger text-white">Terminated</span>
                                                        @elseif($isActive)
                                                            <span class="info-value badge bg-success text-white">Active</span>
                                                        @else
                                                            <span class="info-value badge bg-danger text-white">Expired</span>
                                                        @endif
                                                    </div>
                                                    @if (!$terminatedAt)
                                                        <div class="info-row">
                                                            <span class="info-label">Progress:</span>
                                                            <span class="info-value">
                                                                @php
                                                                    $total = \Carbon\Carbon::parse(
                                                                        $startDate,
                                                                    )->diffInDays($endDate);
                                                                    $elapsed = \Carbon\Carbon::parse(
                                                                        $startDate,
                                                                    )->diffInDays(now());
                                                                    $percentage =
                                                                        $total > 0
                                                                            ? min(100, round(($elapsed / $total) * 100))
                                                                            : 0;
                                                                @endphp
                                                                <div class="progress" style="height: 8px; width: 100%;">
                                                                    <div class="progress-bar bg-success"
                                                                        role="progressbar"
                                                                        style="width: {{ $percentage }}%;"
                                                                        aria-valuenow="{{ $percentage }}"
                                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <small class="text-muted">{{ $percentage }}%
                                                                    complete</small>
                                                            </span>
                                                        </div>
                                                        <div class="info-row">
                                                            <span class="info-label">Days Elapsed:</span>
                                                            <span class="info-value">{{ $elapsed }} days</span>
                                                        </div>
                                                        <div class="info-row">
                                                            <span class="info-label">Days Remaining:</span>
                                                            <span class="info-value">{{ max(0, $total - $elapsed) }}
                                                                days</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Termination Tab (NEW) -->
                                @if (auth()->user()->usertype == 2 || auth()->user()->usertype == 3)
                                    <div class="tab-pane fade" id="{{ $terminationTab }}" role="tabpanel"
                                        aria-labelledby="{{ $terminationTab }}-tab">
                                        <div class="documents-section" style="margin-top: 0;">
                                            <div class="documents-title">
                                                <i class="fas fa-ban text-danger"></i>
                                                Contract Termination Management
                                            </div>

                                            @if ($terminatedAt)
                                                <!-- Show Termination Details if already terminated -->
                                                <div class="alert alert-danger">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    <strong> This contract was terminated on
                                                        {{ \Carbon\Carbon::parse($terminatedAt)->format('d M Y H:i') }}</strong>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="info-card">
                                                            <div class="info-card-header">
                                                                <i class="fas fa-gavel"
                                                                    style="color: #dc2626; background: #fee2e2;"></i>
                                                                <h6> Termination Details</h6>
                                                            </div>
                                                            <div class="info-row">
                                                                <span class="info-label"> Termination Type:</span>
                                                                <span
                                                                    class="info-value">{{ ucfirst($terminationType ?? 'Not specified') }}</span>
                                                            </div>
                                                            <div class="info-row">
                                                                <span class="info-label"> Terminated By:</span>
                                                                <span
                                                                    class="info-value">{{ ucfirst($terminatedBy ?? 'Unknown') }}</span>
                                                            </div>
                                                            <div class="info-row">
                                                                <span class="info-label"> Reason:</span>
                                                                <span
                                                                    class="info-value">{{ $terminationReason ?? 'Not specified' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="info-card">
                                                            <div class="info-card-header">
                                                                <i class="fas fa-file-alt"
                                                                    style="color: #2563eb; background: #dbeafe;"></i>
                                                                <h6>Termination Document</h6>
                                                            </div>
                                                            @if ($contractData['termination_document_path'] ?? null)
                                                                <a href="{{ asset('storage/' . $contractData['termination_document_path']) }}"
                                                                    class="btn btn-outline-danger btn-block"
                                                                    target="_blank">
                                                                    <i class="fas fa-file-pdf mr-2"></i> View Termination
                                                                    Letter
                                                                </a>
                                                            @else
                                                                <p class="text-muted text-center"> No termination document
                                                                    uploaded</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($canTerminate)
                                                <!-- Termination Form with Bootstrap 5 Styling -->
                                                <div class="alert alert-warning d-flex align-items-center mb-4"
                                                    role="alert">
                                                    <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                                                    <div>
                                                        <strong>Warning:</strong> Terminating a contract is irreversible.
                                                        Please ensure you have all necessary documentation.
                                                    </div>
                                                </div>

                                                <form
                                                    action="{{ route('contract.terminate', ['id' => Hashids::encode($contractId)]) }}"
                                                    method="POST" enctype="multipart/form-data"
                                                    class="termination-form">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="card border-0 shadow-sm mb-4">
                                                        <div
                                                            class="card-header bg-danger bg-opacity-10 text-danger fw-bold py-3">
                                                            <i class="fas fa-ban me-2"></i> Termination Details
                                                        </div>
                                                        <div class="card-body p-4">
                                                            <div class="row g-4">
                                                                <!-- Termination Type -->
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold">
                                                                        <i class="fas fa-tag me-2 text-danger"></i>
                                                                        Termination
                                                                        Type <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select name="termination_type"
                                                                        class="form-select form-select-lg" required>
                                                                        <option value="" selected disabled>-- Select
                                                                            Termination Type --</option>
                                                                        <option value="mutual" class="py-2">🤝 Mutual
                                                                            Agreement</option>
                                                                        <option value="resignation" class="py-2">📝
                                                                            Resignation</option>
                                                                        <option value="dismissal" class="py-2">⚖️
                                                                            Dismissal</option>
                                                                        <option value="breach" class="py-2">⚠️ Contract
                                                                            Breach</option>
                                                                        <option value="end_of_contract" class="py-2">⏳
                                                                            End of Contract (Early)</option>
                                                                        <option value="other" class="py-2">🔄 Other
                                                                        </option>
                                                                    </select>
                                                                    <div class="form-text text-muted"> Select the
                                                                        appropriate termination reason</div>
                                                                </div>

                                                                <!-- Effective Date -->
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold">
                                                                        <i
                                                                            class="fas fa-calendar-alt me-2 text-danger"></i>
                                                                        Effective
                                                                        Date <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <span
                                                                            class="input-group-text bg-danger bg-opacity-10 border-danger border-opacity-25">
                                                                            <i class="fas fa-calendar-day text-danger"></i>
                                                                        </span>
                                                                        <input type="date" name="effective_date"
                                                                            class="form-control form-control-lg"
                                                                            value="{{ date('Y-m-d') }}"
                                                                            min="{{ date('Y-m-d') }}" required>
                                                                    </div>
                                                                    <div class="form-text text-muted"> The date when
                                                                        termination takes effect</div>
                                                                </div>

                                                                <!-- Reason for Termination -->
                                                                <div class="col-12">
                                                                    <label class="form-label fw-semibold">
                                                                        <i class="fas fa-align-left me-2 text-danger"></i>
                                                                        Reason
                                                                        for Termination <span class="text-danger">*</span>
                                                                    </label>
                                                                    <textarea name="termination_reason" class="form-control" rows="4" required
                                                                        placeholder="Provide detailed reason for termination..." style="resize: vertical; min-height: 120px;"></textarea>
                                                                    <div class="form-text text-muted"> Be specific about
                                                                        the
                                                                        circumstances leading to termination</div>
                                                                </div>

                                                                <!-- Termination Letter Upload - Bootstrap 5 Enhanced -->
                                                                <div class="col-12">
                                                                    <label class="form-label fw-semibold">
                                                                        <i class="fas fa-file-upload me-2 text-danger"></i>
                                                                        Termination
                                                                        Letter (Optional)
                                                                    </label>
                                                                    <div class="upload-area p-4 border-2 border-dashed rounded-3 text-center"
                                                                        style="background-color: #f8f9fa; cursor: pointer; transition: all 0.3s;"
                                                                        onclick="document.getElementById('terminationDoc{{ $contractId }}').click();">

                                                                        <input type="file" name="document"
                                                                            class="d-none"
                                                                            id="terminationDoc{{ $contractId }}"
                                                                            accept=".pdf,.doc,.docx"
                                                                            onchange="updateFileName(this, 'fileLabel{{ $contractId }}', 'fileIcon{{ $contractId }}')">

                                                                        <div class="mb-3">
                                                                            <i class="fas fa-cloud-upload-alt fa-3x text-danger"
                                                                                id="fileIcon{{ $contractId }}"></i>
                                                                        </div>
                                                                        <h6 class="fw-bold"
                                                                            id="fileLabel{{ $contractId }}"> Choose
                                                                            file
                                                                            or drag here</h6>
                                                                        <p class="text-muted small mb-2"> Supported
                                                                            formats:
                                                                            PDF, DOC, DOCX (Max 2MB)</p>
                                                                        <span
                                                                            class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                                                            <i class="fas fa-paperclip me-1"></i> Click to
                                                                            browse
                                                                        </span>
                                                                    </div>

                                                                    <!-- Selected File Preview -->
                                                                    <div id="filePreview{{ $contractId }}"
                                                                        class="mt-3 d-none">
                                                                        <div class="alert alert-success d-flex align-items-center py-2"
                                                                            role="alert">
                                                                            <i class="fas fa-check-circle me-2"></i>
                                                                            <span
                                                                                id="selectedFileName{{ $contractId }}"></span>
                                                                            <button type="button"
                                                                                class="btn-close ms-auto"
                                                                                onclick="clearFile('terminationDoc{{ $contractId }}', 'fileLabel{{ $contractId }}', 'fileIcon{{ $contractId }}', 'filePreview{{ $contractId }}')">
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <small class="text-muted mt-2 d-block">
                                                                        <i class="fas fa-info-circle me-1"></i>
                                                                        Upload termination letter/document if available
                                                                    </small>
                                                                </div>

                                                                <!-- Additional Notes -->
                                                                <div class="col-12">
                                                                    <label class="form-label fw-semibold">
                                                                        <i class="fas fa-pen me-2 text-danger"></i>
                                                                        Additional
                                                                        Notes
                                                                    </label>
                                                                    <textarea name="notes" class="form-control" rows="3" placeholder="Any additional information or context..."></textarea>
                                                                    <div class="form-text text-muted"> Optional: Add any
                                                                        relevant notes</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Action Buttons -->
                                                    <div class="d-flex gap-3 justify-content-end mt-4">
                                                        <button type="button" class="btn btn-outline-secondary px-5 py-2"
                                                            data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-2"></i> Cancel
                                                        </button>
                                                        <button type="submit" class="btn btn-danger px-5 py-2"
                                                            onclick="return confirm('⚠️ Are you sure you want to terminate this contract? This action cannot be undone.')">
                                                            <i class="fas fa-ban me-2"></i> Terminate Contract
                                                        </button>
                                                    </div>
                                                </form>

                                                <!-- Bootstrap 5 JavaScript for enhanced file input -->
                                                <script>
                                                    function updateFileName(input, labelId, iconId) {
                                                        const fileLabel = document.getElementById(labelId);
                                                        const fileIcon = document.getElementById(iconId);
                                                        const filePreview = document.getElementById('filePreview' + input.id.replace('terminationDoc', ''));
                                                        const fileNameSpan = document.getElementById('selectedFileName' + input.id.replace('terminationDoc', ''));

                                                        if (input.files && input.files[0]) {
                                                            const file = input.files[0];
                                                            const fileName = file.name;
                                                            const fileSize = (file.size / 1024).toFixed(2);

                                                            // Update label and icon
                                                            fileLabel.innerHTML = `<strong>${fileName}</strong> <small class="text-muted">(${fileSize} KB)</small>`;
                                                            fileIcon.className = 'fas fa-file-pdf fa-3x text-danger';

                                                            // Show preview
                                                            filePreview.classList.remove('d-none');
                                                            fileNameSpan.textContent = fileName;

                                                            // Change upload area style
                                                            input.closest('.upload-area').style.borderColor = '#dc3545';
                                                            input.closest('.upload-area').style.backgroundColor = '#fff5f5';
                                                        } else {
                                                            // Reset
                                                            fileLabel.innerHTML = 'Choose file or drag here';
                                                            fileIcon.className = 'fas fa-cloud-upload-alt fa-3x text-danger';
                                                            filePreview.classList.add('d-none');
                                                            input.closest('.upload-area').style.borderColor = '#dee2e6';
                                                            input.closest('.upload-area').style.backgroundColor = '#f8f9fa';
                                                        }
                                                    }

                                                    function clearFile(inputId, labelId, iconId, previewId) {
                                                        const input = document.getElementById(inputId);
                                                        const fileLabel = document.getElementById(labelId);
                                                        const fileIcon = document.getElementById(iconId);
                                                        const filePreview = document.getElementById(previewId);

                                                        input.value = '';
                                                        fileLabel.innerHTML = 'Choose file or drag here';
                                                        fileIcon.className = 'fas fa-cloud-upload-alt fa-3x text-danger';
                                                        filePreview.classList.add('d-none');

                                                        // Reset upload area style
                                                        input.closest('.upload-area').style.borderColor = '#dee2e6';
                                                        input.closest('.upload-area').style.backgroundColor = '#f8f9fa';
                                                    }

                                                    // Drag and drop functionality
                                                    document.querySelectorAll('.upload-area').forEach(area => {
                                                        area.addEventListener('dragover', (e) => {
                                                            e.preventDefault();
                                                            area.style.borderColor = '#dc3545';
                                                            area.style.backgroundColor = '#fff5f5';
                                                        });

                                                        area.addEventListener('dragleave', (e) => {
                                                            e.preventDefault();
                                                            area.style.borderColor = '#dee2e6';
                                                            area.style.backgroundColor = '#f8f9fa';
                                                        });

                                                        area.addEventListener('drop', (e) => {
                                                            e.preventDefault();
                                                            const input = area.querySelector('input[type="file"]');
                                                            input.files = e.dataTransfer.files;

                                                            // Trigger change event
                                                            const event = new Event('change', {
                                                                bubbles: true
                                                            });
                                                            input.dispatchEvent(event);

                                                            area.style.borderColor = '#dee2e6';
                                                            area.style.backgroundColor = '#f8f9fa';
                                                        });
                                                    });
                                                </script>

                                                <!-- Additional Bootstrap 5 Styles -->
                                                <style>
                                                    .border-2 {
                                                        border-width: 2px !important;
                                                    }

                                                    .border-dashed {
                                                        border-style: dashed !important;
                                                    }

                                                    .upload-area:hover {
                                                        border-color: #dc3545 !important;
                                                        background-color: #fff5f5 !important;
                                                        transform: translateY(-2px);
                                                        box-shadow: 0 0.5rem 1rem rgba(220, 53, 69, 0.15);
                                                    }

                                                    .form-select-lg,
                                                    .form-control-lg {
                                                        border-radius: 0.5rem;
                                                        border: 2px solid #e9ecef;
                                                        transition: all 0.3s;
                                                    }

                                                    .form-select-lg:focus,
                                                    .form-control-lg:focus {
                                                        border-color: #dc3545;
                                                        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
                                                    }

                                                    .input-group-text {
                                                        border: 2px solid #e9ecef;
                                                        border-right: none;
                                                    }

                                                    .input-group .form-control-lg {
                                                        border-left: none;
                                                    }

                                                    .btn-outline-secondary:hover {
                                                        background-color: #6c757d;
                                                        color: white;
                                                        transform: translateY(-2px);
                                                        box-shadow: 0 0.5rem 1rem rgba(108, 117, 125, 0.15);
                                                    }

                                                    .btn-danger {
                                                        background: linear-gradient(135deg, #dc3545 0%, #b91c1c 100%);
                                                        border: none;
                                                        transition: all 0.3s;
                                                    }

                                                    .btn-danger:hover {
                                                        transform: translateY(-2px);
                                                        box-shadow: 0 0.5rem 1rem rgba(220, 53, 69, 0.3);
                                                    }

                                                    .card-header {
                                                        border-bottom: 2px solid rgba(220, 53, 69, 0.2);
                                                    }

                                                    .alert-warning {
                                                        background-color: #fff3cd;
                                                        border-left: 4px solid #ffc107;
                                                        border-radius: 0.5rem;
                                                    }

                                                    /* Animation for file upload */
                                                    .upload-area {
                                                        transition: all 0.3s ease;
                                                    }

                                                    @keyframes pulse {
                                                        0% {
                                                            transform: scale(1);
                                                        }

                                                        50% {
                                                            transform: scale(1.02);
                                                        }

                                                        100% {
                                                            transform: scale(1);
                                                        }
                                                    }

                                                    .upload-area.highlight {
                                                        animation: pulse 0.5s ease;
                                                    }
                                                </style>
                                            @else
                                                <!-- Contract cannot be terminated -->
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    This contract cannot be terminated because it is not active.
                                                    @if (!$isActive)
                                                        <br>Current status: <strong>Expired</strong>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer-custom">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times mr-2"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

@endsection
