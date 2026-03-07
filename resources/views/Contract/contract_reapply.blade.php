@extends('SRTDashboard.frame')
@section('content')
    <style>
        .reapply-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 16px;
            margin: 1.5rem 0 2rem;
        }

        .original-contract-card {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>

    <div class="container-fluid">
        <div class="reapply-header">
            <h4>
                <i class="fas fa-redo-alt mr-2"></i>
                Re-apply Contract
            </h4>
            <p class="mb-0">Please correct the issues below and resubmit your application</p>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <!-- Original Contract Details -->
                        <div class="original-contract-card">
                            <h5 class="text-danger mb-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Original Contract Details (Rejected)
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Contract Type:</strong> {{ ucfirst($oldContract->contract_type) }}</p>
                                    <p><strong>Applied Date:</strong>
                                        {{ \Carbon\Carbon::parse($oldContract->applied_at)->format('d M Y') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Job Title:</strong> {{ $oldContract->job_title ?? 'N/A' }}</p>
                                    <p><strong>Rejected Date:</strong>
                                        {{ \Carbon\Carbon::parse($oldContract->rejected_at)->format('d M Y') }}</p>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-danger">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <strong>Rejection Remarks:</strong>
                                        <p class="mb-0 mt-2">{{ $oldContract->remarks ?? 'No remarks provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reapplication Form -->
                        <form action="{{ route('contract.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="is_reapply" value="1">
                            <input type="hidden" name="original_contract_id" value="{{ $oldContract->id }}">

                            @if($authToken ?? null)
                                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Contract Type</label>
                                    <select name="contract_type" required class="form-control">
                                        <option value="provision"
                                            {{ $oldContract->contract_type == 'provision' ? 'selected' : '' }}>⏳ Provision
                                        </option>
                                        <option value="new" {{ $oldContract->contract_type == 'new' ? 'selected' : '' }}>
                                            📄 New Contract</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Application Letter (PDF/DOC)</label>
                                    <input type="file" name="application_letter" required class="form-control"
                                        accept=".pdf,.doc,.docx">
                                    <small class="text-muted">Upload corrected application letter</small>
                                </div>
                            </div>

                            <div class="text-right">
                                <a href="{{ route('contract.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane mr-2"></i>Submit Re-application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Custom file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
@endpush
