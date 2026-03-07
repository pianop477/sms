@extends('SRTDashboard.frame')
@section('content')

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --danger-gradient: linear-gradient(135deg, #f54b64 0%, #f78361 100%);
        }

        .page-header {
            background: var(--success-gradient);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1.5rem 0 2rem;
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
            font-size: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .page-header h4 i {
            margin-right: 10px;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.2));
        }

        .btn-create {
            background: white;
            color: #185a9d;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            background: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Modal Customization */
        .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .modal-header .close {
            color: white;
            opacity: 1;
            text-shadow: none;
        }

        .modal-header .close:hover {
            opacity: 0.8;
        }

        .modal-title {
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .modal-title i {
            margin-right: 10px;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        /* Form Controls */
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .form-control,
        .form-control-static {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
        }

        select.form-control {
            cursor: pointer;
        }

        /* Table Styling */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            margin-top: 1.5rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
            border: none;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f5;
        }

        /* Badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
        }

        .badge-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .badge-info {
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            color: white;
        }

        .badge-danger {
            background: linear-gradient(135deg, #f54b64 0%, #f78361 100%);
            color: white;
        }

        .badge-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }

        .badge-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-manage {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .btn-manage:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-manage i {
            margin-right: 5px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 5px 3px;
        }

        .empty-state i {
            font-size: 2rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #adb5bd;
            max-width: 400px;
            margin: 0 auto 2rem;
        }

        /* Detail Cards */
        .detail-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
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
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .attachment-link:hover {
            background: #667eea;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .attachment-link i {
            margin-right: 5px;
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table tbody tr {
            animation: slideIn 0.5s ease forwards;
        }
    </style>

    <div class="page-header">
        <h4>
            <i class="fas fa-file-contract"></i>
            My Contracts Catalog
        </h4>
        <button type="button" class="btn btn-create" data-toggle="modal" data-target=".bd-example-modal-lg">
            <i class="fas fa-plus-circle mr-2"></i>
            Apply New Contract
        </button>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- Apply Contract Modal --}}
                    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-pen-fancy"></i>
                                        Contract Application Form
                                    </h5>
                                    <button type="button" class="btn btn-xs btn-danger" data-dismiss="modal" aria-label="Close">
                                        <i class="fas fa-close"></i>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate action="{{ route('contract.store') }}"
                                        method="POST" enctype="multipart/form-data" id="contractForm">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Contract Type</label>
                                                <select name="contract_type" required class="form-control"
                                                    style="padding: 3px;">
                                                    <option value="" disabled selected>-- Select Contract Type --
                                                    </option>
                                                    <option value="provision">⏳ Provision</option>
                                                    <option value="new">📄 New Contract</option>
                                                </select>
                                                @error('contract_type')
                                                    <div class="text-danger small mt-1">
                                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label for="applicationLetter" class="form-label">
                                                    Application Letter
                                                </label>

                                                <input type="file" name="application_letter" id="applicationLetter"
                                                    class="form-control @error('application_letter') is-invalid @enderror"
                                                    accept=".pdf" required>

                                                @error('application_letter')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="modal-footer px-0 pb-0">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                <i class="fas fa-times-circle mr-2"></i>
                                                Cancel
                                            </button>
                                            <button type="submit" id="saveButton" class="btn btn-primary">
                                                <i class="fas fa-paper-plane mr-2"></i>
                                                Submit Application
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contracts Table --}}
                    <div class="table-container">
                        @if ($contracts->isEmpty())
                            <div class="empty-state">
                                <i class="fas fa-file-contract"></i>
                                <h5>No Contracts Found</h5>
                                <p>You haven't applied for any contracts yet.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Contract Type</th>
                                            <th>Job Title</th>
                                            <th>Applied at</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contracts as $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="font-weight-bold">
                                                        {{ ucfirst($row->contract_type) }} Contract
                                                    </span>
                                                </td>
                                                <td>{{ ucwords(strtolower($row->job_title ?? 'Not Specified')) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($row->applied_at)->format('d-m-Y H:i') }}</td>
                                                {{-- Status Badge --}}
                                                <td>
                                                    @switch($row->status)
                                                        @case('pending')
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-clock mr-1"></i> Pending
                                                            </span>
                                                        @break

                                                        @case('approved')
                                                            <span class="badge badge-info">
                                                                <i class="fas fa-check-circle mr-1"></i> Reviewed
                                                            </span>
                                                        @break

                                                        @case('rejected')
                                                            <span class="badge badge-secondary">
                                                                <i class="fas fa-times-circle mr-1"></i> Rejected
                                                            </span>
                                                        @break

                                                        @case('activated')
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check-circle mr-1"></i> Activated
                                                            </span>
                                                        @break

                                                        @case('expired')
                                                            <span class="badge badge-warning"
                                                                style="background: #ffc107; color: #856404;">
                                                                <i class="fas fa-hourglass-end mr-1"></i> Expired
                                                            </span>
                                                        @break

                                                        @case('terminated')
                                                            <span class="badge" style="background: #6c757d; color: white;">
                                                                <i class="fas fa-ban mr-1"></i> Terminated
                                                            </span>
                                                        @break

                                                        @default
                                                            <span class="badge badge-secondary">{{ ucfirst($row->status) }}</span>
                                                    @endswitch
                                                </td>

                                                {{-- Actions --}}
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-manage" data-toggle="modal"
                                                            data-target="#contractModal{{ $row->id }}">
                                                            <i class="fas fa-wrench"></i> Manage
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if (method_exists($contracts, 'links'))
                                <div class="mt-4 d-flex justify-content-end">
                                    {{ $contracts->links() }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dynamic Contract Modals --}}
    @foreach ($contracts as $contract)
        {{-- PENDING STATUS MODAL --}}
        @if ($contract->status == 'pending')
            <div class="modal fade" id="contractModal{{ $contract->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <h5 class="modal-title">
                                <i class="fas fa-clock"></i>
                                Contract Details - Pending Review
                            </h5>
                            <button type="button" class="btn btn-xs btn-danger" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Contract Type</div>
                                        <div class="detail-value">{{ ucfirst($contract->contract_type) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Job Title</div>
                                        <div class="detail-value">
                                            {{ ucwords(strtolower($contract->job_title ?? 'Not Specified')) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Applied Date</div>
                                        <div class="detail-value">
                                            {{ \Carbon\Carbon::parse($contract->applied_at)->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Last Updated</div>
                                        <div class="detail-value">
                                            {{ \Carbon\Carbon::parse($contract->updated_at)->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Status</div>
                                        <div class="detail-value">
                                            <span class="badge badge-warning">Pending Review</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Holder</div>
                                        <div class="detail-value text-capitalize">
                                            @if ($contract->holder_id)
                                                {{ App\Models\User::find($contract->holder_id)->first_name ?? 'Unknown' }}
                                                {{ App\Models\User::find($contract->holder_id)->last_name ?? '' }}
                                            @else
                                                <span class="text-muted">Application sent - Awaiting assignment</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="detail-card">
                                        <div class="detail-label mb-2">Application Attachment</div>
                                        <a href="{{ asset('storage/' . $contract->applicant_file_path) }}"
                                            target="_blank" class="attachment-link">
                                            <i class="fas fa-file-pdf"></i> View Application Letter
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                            <form action="{{ route('contract.delete', ['id' => Hashids::encode($contract->id)]) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this application?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- REJECTED STATUS MODAL --}}
        @if ($contract->status == 'rejected')
            <div class="modal fade" id="contractModal{{ $contract->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                            <h5 class="modal-title">
                                <i class="fas fa-times-circle"></i>
                                Contract Details - Rejected
                            </h5>
                            <button type="button" class="btn btn-xs btn-danger" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Contract Type</div>
                                        <div class="detail-value">{{ ucfirst($contract->contract_type) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Job Title</div>
                                        <div class="detail-value">
                                            {{ ucwords(strtolower($contract->job_title ?? 'Not Specified')) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Applied Date</div>
                                        <div class="detail-value">
                                            {{ \Carbon\Carbon::parse($contract->applied_at)->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Rejected Date</div>
                                        <div class="detail-value">
                                            {{ \Carbon\Carbon::parse($contract->rejected_at)->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="detail-card" style="border-left-color: #dc3545;">
                                        <div class="detail-label text-danger">Rejection Remarks</div>
                                        <div class="detail-value">{{ $contract->remarks ?? 'No remarks provided' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Reviewed By</div>
                                        <div class="detail-value">
                                            @if ($contract->approved_by)
                                                {{ App\Models\User::where('email', $contract->approved_by)->first()->first_name ?? $contract->approved_by }}
                                            @else
                                                System
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Status</div>
                                        <div class="detail-value">
                                            <span class="badge badge-secondary">Rejected</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Holder</div>
                                        <div class="detail-value text-capitalize">
                                            @if ($contract->holder_id)
                                                {{ App\Models\User::find($contract->holder_id)->first_name ?? 'Unknown' }}
                                                {{ App\Models\User::find($contract->holder_id)->last_name ?? '' }}
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Is Active</div>
                                        <div class="detail-value">{{ $contract->is_active ? 'Yes' : 'No' }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="detail-card">
                                        <div class="detail-label mb-2">Application Attachment</div>
                                        <a href="{{ asset('storage/' . $contract->applicant_file_path) }}"
                                            target="_blank" class="attachment-link">
                                            <i class="fas fa-file-pdf"></i> View Application Letter
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('contract.reapply', ['id' => Hashids::encode($contract->id)]) }}"
                                class="btn btn-primary">
                                <i class="fas fa-redo-alt"></i> Re-apply
                            </a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Temporary debug --}}
        @if (session()->has('reapply_data'))
            <script>
                console.log('Reapply data exists:', @json(session('reapply_data')));
            </script>
        @endif
        @if (session('show_reapply_modal'))
            <script>
                console.log('show_reapply_modal flag is true');
            </script>
        @endif

        {{-- ACTIVATED/EXPIRED/TERMINATED STATUS MODAL --}}
        @if ($contract->status == 'activated' || $contract->status == 'expired' || $contract->status == 'terminated')
            <div class="modal fade" id="contractModal{{ $contract->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header"
                            style="background: {{ $contract->status == 'terminated' ? 'linear-gradient(135deg, #6c757d 0%, #495057 100%)' : 'linear-gradient(135deg, #28a745 0%, #20c997 100%)' }};">
                            <h5 class="modal-title">
                                <i class="fas {{ $contract->status == 'terminated' ? 'fa-ban' : 'fa-check-circle' }}"></i>
                                Contract Details - {{ ucfirst($contract->status) }}
                            </h5>
                            <button type="button" class="btn btn-xs btn-danger" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs mb-4" id="contractTab{{ $contract->id }}" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="details-tab{{ $contract->id }}" data-toggle="tab"
                                        href="#details{{ $contract->id }}" role="tab">
                                        <i class="fas fa-info-circle mr-2"></i> Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="documents-tab{{ $contract->id }}" data-toggle="tab"
                                        href="#documents{{ $contract->id }}" role="tab">
                                        <i class="fas fa-file-alt mr-2"></i> Attachments
                                    </a>
                                </li>
                                @if ($contract->status == 'terminated' || isset($contract->terminated_at))
                                    <li class="nav-item">
                                        <a class="nav-link" id="termination-tab{{ $contract->id }}" data-toggle="tab"
                                            href="#termination{{ $contract->id }}" role="tab">
                                            <i class="fas fa-ban mr-2"></i> Termination
                                        </a>
                                    </li>
                                @endif
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!-- Details Tab -->
                                <div class="tab-pane active" id="details{{ $contract->id }}" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Contract Type</div>
                                                <div class="detail-value">{{ ucfirst($contract->contract_type) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Job Title</div>
                                                <div class="detail-value">
                                                    {{ ucwords(strtolower($contract->job_title ?? 'Not Specified')) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Start Date</div>
                                                <div class="detail-value">
                                                    {{ \Carbon\Carbon::parse($contract->start_date)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">End Date</div>
                                                <div class="detail-value">
                                                    {{ \Carbon\Carbon::parse($contract->end_date)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Duration</div>
                                                <div class="detail-value">{{ $contract->duration }} Months</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Basic Salary</div>
                                                <div class="detail-value">{{ number_format($contract->basic_salary, 2) }}
                                                    TZS</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Allowances</div>
                                                <div class="detail-value">{{ number_format($contract->allowances, 2) }}
                                                    TZS</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Net Pay</div>
                                                <div class="detail-value text-success fw-bold">
                                                    TZS
                                                    {{ number_format(($contract->basic_salary ?? 0) + ($contract->allowances ?? 0), 2) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Approved By</div>
                                                <div class="detail-value">
                                                    {{ ucwords(strtolower($contract->approved_by ?? 'System')) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Activated At</div>
                                                <div class="detail-value">
                                                    {{ \Carbon\Carbon::parse($contract->activated_at)->format('d M Y, H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Status</div>
                                                <div class="detail-value">
                                                    @if ($contract->status == 'terminated')
                                                        <span class="badge badge-secondary">Terminated</span>
                                                    @elseif($contract->status == 'activated')
                                                        <span class="badge badge-success">Activated</span>
                                                    @elseif($contract->status == 'expired')
                                                        <span class="badge badge-warning">Expired</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents Tab -->
                                <div class="tab-pane fade" id="documents{{ $contract->id }}" role="tabpanel">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="detail-card">
                                                <div class="detail-label mb-3">Contract Documents</div>
                                                <div class="d-flex flex-wrap gap-3">
                                                    <!-- Application Letter -->
                                                    @if ($contract->applicant_file_path)
                                                        <a href="{{ asset('storage/' . $contract->applicant_file_path) }}"
                                                            class="btn btn-info mr-2 mb-2" target="_blank">
                                                            <i class="fas fa-file-alt mr-2"></i> Application Letter
                                                        </a>
                                                    @endif

                                                    <!-- Approval Letter -->
                                                    <a href="{{ route('contract.approval.letter', ['id' => Hashids::encode($contract->id)]) }}"
                                                        class="btn btn-success mr-2 mb-2" target="_blank">
                                                        <i class="fas fa-file-pdf mr-2"></i> Approval Letter
                                                    </a>

                                                    <!-- Signed Contract Document -->
                                                    @if ($contract->contract_file_path)
                                                        <a href="{{ asset('storage/' . $contract->contract_file_path) }}"
                                                            class="btn btn-primary mr-2 mb-2" target="_blank">
                                                            <i class="fas fa-file-signature mr-2"></i> Signed Contract
                                                        </a>
                                                    @endif

                                                    <!-- Termination Document (if terminated) -->
                                                    @if (isset($contract->termination_document) && $contract->termination_document)
                                                        <a href="{{ asset('storage/' . $contract->termination_document) }}"
                                                            class="btn btn-danger mr-2 mb-2" target="_blank">
                                                            <i class="fas fa-ban mr-2"></i> Termination Letter
                                                        </a>
                                                    @endif

                                                    <!-- QR Code -->
                                                    @if ($contract->qr_code_path)
                                                        <a href="{{ asset('storage/' . $contract->qr_code_path) }}"
                                                            class="btn btn-secondary mr-2 mb-2" target="_blank">
                                                            <i class="fas fa-qrcode mr-2"></i> QR Code
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Termination Tab (only for terminated contracts) -->
                                @if ($contract->status == 'terminated' || isset($contract->terminated_at))
                                    <div class="tab-pane fade" id="termination{{ $contract->id }}" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="detail-card" style="border-left-color: #dc3545;">
                                                    <div class="detail-label text-danger">Termination Date</div>
                                                    <div class="detail-value">
                                                        {{ $contract->terminated_at ? \Carbon\Carbon::parse($contract->terminated_at)->format('d M Y, H:i') : 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="detail-card" style="border-left-color: #dc3545;">
                                                    <div class="detail-label text-danger">Termination Type</div>
                                                    <div class="detail-value">
                                                        @switch($contract->termination_type)
                                                            @case('mutual')
                                                                Mutual Agreement
                                                            @break

                                                            @case('resignation')
                                                                Resignation
                                                            @break

                                                            @case('dismissal')
                                                                Dismissal
                                                            @break

                                                            @case('breach')
                                                                Contract Breach
                                                            @break

                                                            @case('end_of_contract')
                                                                End of Contract (Early)
                                                            @break

                                                            @default
                                                                {{ ucfirst($contract->termination_type ?? 'Not specified') }}
                                                        @endswitch
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="detail-card" style="border-left-color: #dc3545;">
                                                    <div class="detail-label text-danger">Terminated By</div>
                                                    <div class="detail-value">
                                                        {{ ucfirst($contract->terminated_by ?? 'System') }}</div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="detail-card" style="border-left-color: #dc3545;">
                                                    <div class="detail-label text-danger">Termination Reason</div>
                                                    <div class="detail-value">
                                                        {{ $contract->termination_reason ?? 'No reason provided' }}</div>
                                                </div>
                                            </div>
                                            @if (isset($contract->termination_notes) && $contract->termination_notes)
                                                <div class="col-12">
                                                    <div class="detail-card" style="border-left-color: #dc3545;">
                                                        <div class="detail-label text-danger">Additional Notes</div>
                                                        <div class="detail-value">{{ $contract->termination_notes }}</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- DEFAULT MODAL FOR OTHER STATUSES (APPROVED, ETC) --}}
        @if (!in_array($contract->status, ['pending', 'rejected', 'activated']))
            <div class="modal fade" id="contractModal{{ $contract->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header" style="background: var(--primary-gradient);">
                            <h5 class="modal-title">
                                <i class="fas fa-file-contract"></i>
                                Contract Details - {{ ucfirst($contract->status) }}
                            </h5>
                            <button type="button" class="btn btn-danger btn-xs" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Contract Type</div>
                                        <div class="detail-value">{{ ucfirst($contract->contract_type) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Job Title</div>
                                        <div class="detail-value">
                                            {{ ucwords(strtolower($contract->job_title ?? 'Not Specified')) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Start Date</div>
                                        <div class="detail-value">
                                            {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d M Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">End Date</div>
                                        <div class="detail-value">
                                            {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d M Y') : 'N/A' }}
                                        </div>
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
                                        <div class="detail-label">Approved By</div>
                                        <div class="detail-value">
                                            {{ ucwords(strtolower($contract->approved_by ?? 'N/A')) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Status</div>
                                        <div class="detail-value">
                                            @switch($contract->status)
                                                @case('approved')
                                                    <span class="badge badge-info">Reviewed</span>
                                                @break

                                                @default
                                                    <span class="badge badge-secondary">{{ $contract->status }}</span>
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <div class="detail-label">Holder</div>
                                        <div class="detail-value text-capitalize">
                                            @if ($contract->holder_id)
                                                {{ App\Models\User::find($contract->holder_id)->first_name ?? 'Unknown' }}
                                                {{ App\Models\User::find($contract->holder_id)->last_name ?? '' }}
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="detail-card">
                                        <div class="detail-label mb-2">Attachments</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ asset('storage/' . $contract->applicant_file_path) }}"
                                                target="_blank" class="attachment-link mr-2">
                                                <i class="fas fa-file-pdf"></i> Application Letter
                                            </a>
                                            @if ($contract->contract_file_path)
                                                <a href="{{ asset('storage/' . $contract->contract_file_path) }}"
                                                    target="_blank" class="attachment-link mr-2">
                                                    <i class="fas fa-file-contract"></i> Unsigned Contract Document
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    {{-- Reapply Contract Modal --}}
    <div class="modal fade" id="reapplyContractModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h5 class="modal-title">
                        <i class="fas fa-redo-alt"></i>
                        Re-apply Contract Form
                    </h5>
                    <button type="button" class="btn btn-xs btn-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-4" id="reapply-remarks-container" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Previous Rejection Remarks:</strong>
                        <p id="reapply-remarks" class="mb-0 mt-2"></p>
                    </div>

                    <form class="needs-validation" novalidate action="{{ route('contract.store') }}" method="POST"
                        enctype="multipart/form-data" id="reapplyContractForm">
                        @csrf

                        <input type="hidden" name="is_reapply" id="reapply_is_reapply" value="1">
                        <input type="hidden" name="original_contract_id" id="original_contract_id" value="">

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Contract Type</label>
                                <select name="contract_type" id="reapply_contract_type" required class="form-control">
                                    <option value="" disabled selected>-- Select Contract Type --</option>
                                    <option value="provision">⏳ Provision</option>
                                    <option value="new">📄 New Contract</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Application Letter (PDF/DOC)</label>
                                <div class="custom-file">
                                    <input type="file" name="application_letter" required class="custom-file-input"
                                        id="reapply_application_letter" accept=".pdf">
                                    <label class="custom-file-label" for="reapply_application_letter">
                                        Choose file...
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-light" data-dismiss="modal">
                                <i class="fas fa-times-circle mr-2"></i>
                                Cancel
                            </button>
                            <button type="submit" id="reapplySaveButton" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit Re-application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Custom file input label update for both forms
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            // Form validation and submission for Apply Form
            $('#contractForm').on('submit', function(e) {
                e.preventDefault();

                let form = this;
                let submitBtn = $('#saveButton');

                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Submitting...');

                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    submitBtn.prop('disabled', false);
                    submitBtn.html('<i class="fas fa-paper-plane mr-2"></i>Submit Application');

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields correctly.',
                        confirmButtonColor: '#667eea',
                        confirmButtonText: 'OK'
                    });

                    return;
                }

                form.submit();
            });

            // Form validation and submission for Reapply Form
            $('#reapplyContractForm').on('submit', function(e) {
                e.preventDefault();

                let form = this;
                let submitBtn = $('#reapplySaveButton');

                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Submitting...');

                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    submitBtn.prop('disabled', false);
                    submitBtn.html('<i class="fas fa-paper-plane mr-2"></i>Submit Re-application');

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields correctly.',
                        confirmButtonColor: '#667eea',
                        confirmButtonText: 'OK'
                    });

                    return;
                }

                form.submit();
            });

            // Function to get URL parameter
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            // AUTO-OPEN REAPPLY MODAL
            @if (session('show_reapply_modal') || session('reapply_active'))
                setTimeout(function() {
                    @if (session('reapply_data'))
                        var reapplyData = @json(session('reapply_data'));

                        console.log('Reapply data received:', reapplyData);

                        // Fill the reapply form
                        if (reapplyData.contract_type) {
                            $('#reapply_contract_type').val(reapplyData.contract_type);
                        }

                        if (reapplyData.contract_id) {
                            $('#original_contract_id').val(reapplyData.contract_id);
                        }

                        // Show remarks if available
                        if (reapplyData.remarks) {
                            $('#reapply-remarks').text(reapplyData.remarks);
                            $('#reapply-remarks-container').show();
                        } else {
                            $('#reapply-remarks-container').hide();
                        }

                        // Open the reapply modal
                        $('#reapplyContractModal').modal('show');

                        // Log success
                        console.log('Reapply modal opened successfully');
                    @endif
                }, 500);
            @endif

            // Auto-open modal if URL has open_modal parameter
            if (getUrlParameter('open_modal') === 'true') {
                setTimeout(function() {
                    $('#reapplyContractModal').modal('show');
                }, 500);
            }

            // Clear reapply form when modal is closed
            $('#reapplyContractModal').on('hidden.bs.modal', function() {
                $('#reapplyContractForm')[0].reset();
                $('#reapply-remarks-container').hide();
                $('#reapply-remarks').text('');
                $('#original_contract_id').val('');
                $('.custom-file-label').html('Choose file...');

                // Remove URL parameter without refreshing
                if (window.history.replaceState) {
                    var url = window.location.href.split('?')[0];
                    window.history.replaceState({
                        path: url
                    }, '', url);
                }
            });


            // Success Message
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

            // Error Message
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#f54b64',
                    confirmButtonText: 'OK'
                });
            @endif

            // Info Message from reapply
            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: '{{ session('info') }}',
                    confirmButtonColor: '#667eea',
                    timer: 5000
                });
            @endif

            // DataTable initialization
            @if (!$contracts->isEmpty())
                $('#myTable').DataTable({
                    "paging": false,
                    "ordering": true,
                    "info": false,
                    "searching": true,
                    "language": {
                        "search": "<i class='fas fa-search'></i> Search:",
                        "searchPlaceholder": "Filter contracts..."
                    }
                });
            @endif
        });
    </script>
@endpush
