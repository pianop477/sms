@extends('SRTDashboard.frame')

@section('content')
<style>
    /* Loading overlay styles */
    .global-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.75);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }

    .global-loading-content {
        background: white;
        padding: 30px 40px;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        animation: fadeInUp 0.3s ease;
        min-width: 320px;
    }

    .global-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid #e2e8f0;
        border-top: 4px solid #6f42c1;
        border-right: 4px solid #9b59b6;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin: 0 auto 20px auto;
    }

    .global-loading-text {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .global-loading-subtext {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 15px;
    }

    .progress-bar-container {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar-fill {
        width: 0%;
        height: 100%;
        background: linear-gradient(90deg, #6f42c1, #9b59b6);
        border-radius: 10px;
        animation: progressPulse 1.5s ease infinite;
    }

    @keyframes progressPulse {
        0%, 100% { opacity: 1; width: 30%; }
        50% { opacity: 0.6; width: 70%; }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .btn-loading {
        opacity: 0.7;
        cursor: wait;
        pointer-events: none;
    }

    .btn-loading .btn-text {
        display: none;
    }

    .btn-loading .btn-spinner {
        display: inline-block;
    }

    .btn-spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid white;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }

    /* Modal custom styles */
    .custom-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        justify-content: center;
        align-items: center;
    }

    .custom-modal-content {
        background: white;
        border-radius: 12px;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        animation: fadeInUp 0.2s ease;
    }

    .custom-modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 18px;
        background: #6f42c1;
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .custom-modal-body {
        padding: 20px;
        font-size: 14px;
        line-height: 1.5;
        color: #334155;
    }

    .custom-modal-footer {
        padding: 12px 20px;
        border-top: 1px solid #e2e8f0;
        text-align: right;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .custom-modal-btn {
        padding: 8px 20px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
    }

    .custom-modal-btn-cancel {
        background: #e2e8f0;
        color: #334155;
    }

    .custom-modal-btn-confirm {
        background: #6f42c1;
        color: white;
    }

    .custom-modal-btn-confirm:hover {
        background: #5a3396;
    }

    .custom-modal-btn-cancel:hover {
        background: #cbd5e1;
    }

    /* Toast notification for queue info */
    .queue-toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #0f3460;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 10001;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        animation: slideInRight 0.3s ease;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* My Reports Button Style */
    .btn-my-reports {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 15px;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
    }

    .btn-my-reports:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        color: white;
    }
</style>

<div class="card mt-2">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4 class="header-title text-capitalize">{{$reports->title }} - {{$year}}</h4>
            </div>
            <div class="col-md-4">
                <div class="btn-group float-right" role="group">
                    <a href="{{route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class])}}" class="btn btn-info btn-sm me-2">
                        <i class="fas fa-arrow-circle-left"></i> Back
                    </a>
                    <a href="{{ route('reports.status') }}" class="btn btn-my-reports btn-sm me-2">
                        <i class="fas fa-folder-open"></i>
                        <span>My Reports</span>
                        @php
                            $pendingReports = App\Models\ReportJob::where('user_id', Auth::id())
                                ->whereIn('status', ['pending', 'processing'])
                                ->count();
                        @endphp
                        @if($pendingReports > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $pendingReports }}</span>
                        @endif
                    </a>
                    <button type="button" id="bulkReportBtn" class="btn btn-primary btn-sm">
                        <i class="fas fa-file-pdf"></i>
                        <span class="btn-text">Bulk Reports</span>
                        <span class="btn-spinner"></span>
                    </button>
                </div>
            </div>
        </div>
        <hr>
        <iframe src="{{ $fileUrl }}" width="100%" height="600px" style="border: 1px solid #ddd; border-radius: 8px;" id="reportIframe"></iframe>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <i class="fas fa-file-pdf me-2"></i> Generate Bulk Reports
        </div>
        <div class="custom-modal-body">
            <p><strong>Generate Bulk Combined Reports for ALL students?</strong></p>
            <p style="margin-top: 12px; font-size: 13px;">This will generate individual combined reports for every student in this class.</p>
            <ul style="margin: 12px 0 0 20px; font-size: 12px;">
                <li>✓ Subject performance with rankings</li>
                <li>✓ Overall averages and grades</li>
                <li>✓ Class position with tie handling</li>
                <li>✓ QR code for verification</li>
            </ul>
            <div class="alert alert-info mt-3" style="font-size: 12px; padding: 10px;">
                <i class="fas fa-info-circle me-2"></i>
                <strong>How it works:</strong><br>
                Reports will be generated in the background. You will be redirected to a status page where you can monitor progress and download when ready.
            </div>
            <p style="margin-top: 12px; color: #e65100; font-size: 12px;">
                ⚠️ <strong>Note:</strong> For large classes, this may take a few minutes. You can continue using the system while reports generate.
            </p>
        </div>
        <div class="custom-modal-footer">
            <button id="modalCancelBtn" class="custom-modal-btn custom-modal-btn-cancel">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button id="modalConfirmBtn" class="custom-modal-btn custom-modal-btn-confirm">
                <i class="fas fa-download me-1"></i> Generate Reports
            </button>
        </div>
    </div>
</div>

<script>
    // Bulk report URL (redirects to queue processing)
    const bulkReportUrl = "{{ route('results.bulk.combined.reports', [
        'school' => $school,
        'year' => $year,
        'class' => $class,
        'report' => $report
    ]) }}";

    let isProcessing = false;

    function showModal() {
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function hideModal() {
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function showToast(message) {
        // Remove existing toast
        const existingToast = document.querySelector('.queue-toast');
        if (existingToast) existingToast.remove();

        // Create new toast
        const toast = document.createElement('div');
        toast.className = 'queue-toast';
        toast.innerHTML = `
            <i class="fas fa-clock fa-spin" style="font-size: 18px;"></i>
            <div>
                <strong>Report Generation Started</strong><br>
                <small>${message}</small>
            </div>
        `;
        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast) toast.remove();
        }, 5000);
    }

    function startBulkGeneration() {
        if (isProcessing) return;
        isProcessing = true;

        hideModal();

        const btn = document.getElementById('bulkReportBtn');
        if (btn) {
            btn.classList.add('btn-loading');
            btn.disabled = true;
        }

        // Show toast notification
        showToast('Your reports are being generated in the background. You will be redirected to track progress...');

        // Redirect to queue processing (controller will handle redirect to status page)
        window.location.href = bulkReportUrl;
    }

    // Set up event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('bulkReportBtn');

        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (!isProcessing) {
                    showModal();
                }
            });
        }

        // Modal buttons
        const confirmBtn = document.getElementById('modalConfirmBtn');
        const cancelBtn = document.getElementById('modalCancelBtn');

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                startBulkGeneration();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                hideModal();
            });
        }

        // Close modal when clicking outside
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    hideModal();
                }
            });
        }

        // Prevent iframe from capturing events
        const iframe = document.getElementById('reportIframe');
        if (iframe) {
            iframe.addEventListener('load', function() {
                console.log('Report iframe loaded');
            });
        }
    });
</script>

@endsection
