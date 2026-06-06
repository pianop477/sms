@extends('SRTDashboard.frame')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    :root {
        --primary: #4e54c8;
        --secondary: #8f94fb;
        --info: #17a2b8;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --light: #f8f9fa;
        --dark: #343a40;
    }

    body {
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        margin-top: 20px;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .card-header-custom {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 5px 10px;
        position: relative;
        overflow: hidden;
    }

    .card-header-custom::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
        transform: rotate(30deg);
    }

    .header-title {
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
        font-size: 24px;
    }

    .class-highlight {
        color: #ffd700;
        font-weight: 700;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        text-transform: uppercase;
    }

    .exam-highlight {
        color: #ffd700;
        font-weight: 600;
    }

    .date-highlight {
        color: #ffd700;
        font-weight: 600;
    }

    .card-body {
        padding: 10px;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s;
        backdrop-filter: blur(5px);
        position: relative;
        z-index: 1;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .btn-excel {
        background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-excel:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        color: white;
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

    .pdf-container {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.5);
        background: white;
        position: relative;
    }

    .pdf-controls {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
    }

    .pdf-title {
        font-weight: 600;
        color: var(--primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pdf-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .pdf-btn {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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
        cursor: pointer;
    }

    .pdf-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78, 84, 200, 0.3);
        color: white;
        text-decoration: none;
    }

    .pdf-btn:disabled {
        opacity: 0.6;
        transform: none;
        cursor: not-allowed;
    }

    .pdf-btn-download {
        background: linear-gradient(135deg, var(--info) 0%, #5bc0de 100%);
    }

    .pdf-btn-print {
        background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);
    }

    .pdf-btn-rollback {
        background: linear-gradient(135deg, var(--warning) 0%, #b33f41 100%);
    }

    .pdf-export-excel {
        background: linear-gradient(135deg, var(--success) 0%, #28a745 100%);
    }

    .pdf-btn-bulk {
        background: linear-gradient(135deg, #6f42c1 0%, #9b59b6 100%);
    }

    .pdf-viewer {
        width: 100%;
        height: 600px;
        border: none;
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

    .floating-icons {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 60px;
        opacity: 0.1;
        color: white;
        z-index: 0;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
        border-radius: 15px;
    }

    /* Global loading overlay for bulk report generation */
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

    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .info-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 50px;
        padding: 5px 15px;
        font-size: 14px;
        font-weight: 600;
        margin-left: 15px;
    }

    .result-info {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 14px;
    }

    .info-value {
        font-weight: 700;
        color: var(--primary);
        font-size: 16px;
    }

    /* Custom Modal Styles */
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

    @media (max-width: 768px) {
        .pdf-controls {
            flex-direction: column;
            gap: 15px;
        }
        .pdf-actions {
            width: 100%;
            justify-content: center;
        }
        .header-title {
            font-size: 20px;
        }
        .info-grid {
            grid-template-columns: 1fr;
        }
        .pdf-viewer {
            height: 500px;
        }
    }
</style>

<div class="">
    <div class="glass-card">
        <div class="card-header-custom">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="header-title">
                        <i class="fas fa-file-pdf me-2"></i>
                        <span class="class-highlight"> {{ strtoupper($results->first()->class_name) }}</span>
                        <span class="exam-highlight">{{ $results->first()->exam_type }}</span> Results -
                        <span class="date-highlight">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</span>
                    </h4>
                </div>
                <div class="col-md-4">
                    <div class="btn-group float-right" role="group">
                        <a href="{{ route('results.monthsByExamType', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month]) }}"
                            class="btn btn-back btn-xs me-2">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
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
            <i class="fas fa-chart-bar floating-icons"></i>
        </div>
        <div class="card-body">
            <!-- Result Information -->
            <div class="result-info">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Class</span>
                        <span class="info-value">{{ strtoupper($results->first()->class_name) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Examination Type</span>
                        <span class="info-value text-capitalize">{{ $results->first()->exam_type }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Academic Year</span>
                        <span class="info-value">{{ $year }}</span>
                    </div>
                </div>
            </div>

            <!-- PDF Viewer -->
            <div class="pdf-container">
                <div class="pdf-controls">
                    <h5 class="pdf-title">
                        <i class="fas fa-graduation-cap"></i>
                        Examination Results Document
                        <span class="info-badge">PDF</span>
                    </h5>
                    <div class="pdf-actions">
                        <a href="{{ $fileUrl }}"
                            download="Exam_Results_{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}.pdf"
                            class="pdf-btn pdf-btn-download">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                        <a href="{{ url()->current() }}?export_excel=1" class="pdf-btn pdf-export-excel">
                            <i class="fas fa-file-excel"></i> Download Excel
                        </a>
                        <button onclick="printPDF()" class="pdf-btn pdf-btn-print">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button type="submit" class="pdf-btn pdf-btn-rollback" data-bs-toggle="modal"
                            data-bs-target="#addExamModal">
                            <i class="fas fa-undo-alt"></i> Rollback
                        </button>
                    </div>
                </div>

                <div class="position-relative">
                    <div class="loading-overlay" id="pdfLoading">
                        <div class="spinner"></div>
                    </div>
                    <iframe src="{{ $fileUrl }}" class="pdf-viewer" id="pdfViewer" onload="hideLoader()"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <i class="fas fa-file-pdf me-2"></i> Generate Bulk Student Reports
        </div>
        <div class="custom-modal-body">
            <p><strong>Generate individual PDF reports for ALL students in this class?</strong></p>
            <p style="margin-top: 12px; font-size: 13px;">The reports will include:</p>
            <ul style="margin: 8px 0 0 20px; font-size: 13px;">
                <li>✓ Subject performance per student</li>
                <li>✓ Overall grades and averages</li>
                <li>✓ Division (for marking style 3)</li>
                <li>✓ Head teacher's remarks</li>
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

<!-- Modal -->
<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rollback Examination Results</h5>
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="rollbackForm">
                    @csrf
                    <input type="hidden" name="school" value="{{ Hashids::encode($schools->id) }}">
                    <input type="hidden" name="class" value="{{ Hashids::encode($class_id[0]) }}">
                    <input type="hidden" name="examType" value="{{ Hashids::encode($exam_id[0]) }}">
                    <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($date)->toDateString() }}">

                    <div class="mb-3">
                        <label class="form-label">Select Teacher *</label>
                        <select id="teacher_id" name="teacher_id" class="form-control-custom" required>
                            <option value="">Select Teacher</option>
                            @foreach ($teachers as $teacher)
                            <option value="{{ $teacher['id'] }}">
                                {{ ucwords(strtolower($teacher['name'])) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Subject *</label>
                        <select id="course_id" name="course_id" class="form-control-custom" disabled required>
                            <option value="">First select a teacher</option>
                        </select>
                    </div>

                    <div class="alert alert-warning">
                        <strong>Warning</strong>
                        <ul class="mb-0 mt-2">
                            <li>⏩ Rollback will move results to temporary storage</li>
                            <li>⏩ Teacher can correct results again</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="submitRollback(event)">
                    <i class="fas fa-undo"></i> Rollback Results
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Bulk report URL (redirects to queue processing)
    const bulkReportUrl = "{{ route('results.bulk.student.reports', [
        'school' => Hashids::encode($schools->id),
        'year' => $year,
        'class' => Hashids::encode($class_id[0]),
        'examType' => Hashids::encode($exam_id[0]),
        'month' => $month,
        'date' => $date
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
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Generating...';
        }

        // Show toast notification
        showToast('Your reports are being generated in the background. You will be redirected to track progress...');

        // Redirect to queue processing (controller will handle redirect to status page)
        window.location.href = bulkReportUrl;
    }

    function confirmBulkReport() {
        if (isProcessing) return;
        showModal();
    }

    // Set up event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('bulkReportBtn');

        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                confirmBulkReport();
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
    });

    function hideLoader() {
        const loader = document.getElementById('pdfLoading');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    function printPDF() {
        const pdfFrame = document.getElementById('pdfViewer');
        if (pdfFrame) {
            pdfFrame.contentWindow.print();
        }
    }

    setTimeout(hideLoader, 5000);

    // Rollback functions
    function submitRollback(event) {
        event.preventDefault();

        const teacherSelect = document.getElementById('teacher_id');
        const courseSelect = document.getElementById('course_id');

        if (!teacherSelect.value || !courseSelect.value) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select both teacher and subject',
            });
            return;
        }

        const payload = {
            teacher_id: teacherSelect.value,
            course_id: courseSelect.value,
            school: document.querySelector('input[name="school"]').value,
            class: document.querySelector('input[name="class"]').value,
            examType: document.querySelector('input[name="examType"]').value,
            date: document.querySelector('input[name="date"]').value,
            _token: "{{ csrf_token() }}"
        };

        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to rollback these results.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, rollback!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                performRollback(payload, event);
            }
        });
    }

    function performRollback(payload, event) {
        const button = event.target;
        button.disabled = true;
        button.innerHTML = 'Rolling back...';

        fetch("{{ route('results.rollback') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-undo"></i> Rollback Results';

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                }).then(() => {
                    if (data.has_remaining_results) {
                        window.location.reload();
                    } else {
                        window.location.href = '{{ route('results.general', ['school' => Hashids::encode(Auth::user()->school_id)]) }}';
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }
        })
        .catch(error => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-undo"></i> Rollback Results';
            console.error('Rollback error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while rolling back results.',
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const teacherSelect = document.getElementById('teacher_id');
        const courseSelect = document.getElementById('course_id');

        if (teacherSelect) {
            teacherSelect.addEventListener('change', function() {
                const teacherId = this.value;

                if (!teacherId) {
                    courseSelect.innerHTML = '<option value="">First select a teacher</option>';
                    courseSelect.disabled = true;
                    return;
                }

                courseSelect.innerHTML = '<option value="">Loading subjects...</option>';
                courseSelect.disabled = true;

                const payload = {
                    teacher_id: teacherId,
                    school: document.querySelector('input[name="school"]').value,
                    class: document.querySelector('input[name="class"]').value,
                    examType: document.querySelector('input[name="examType"]').value,
                    date: document.querySelector('input[name="date"]').value,
                    _token: "{{ csrf_token() }}"
                };

                fetch("{{ route('results.getCoursesByTeacher') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    courseSelect.innerHTML = '<option value="">Select Subject</option>';
                    if (!data.length) {
                        courseSelect.innerHTML = '<option value="">No subjects found</option>';
                        return;
                    }
                    data.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = course.name.toUpperCase();
                        courseSelect.appendChild(option);
                    });
                    courseSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching subjects:', error);
                    courseSelect.innerHTML = '<option value="">Error loading subjects</option>';
                });
            });
        }
    });
</script>
@endsection
