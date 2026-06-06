@extends('SRTDashboard.frame')

@section('content')
<style>
    .progress {
        border-radius: 10px;
        background-color: #e9ecef;
        overflow: hidden;
    }
    .progress-bar {
        transition: width 0.5s ease;
        font-weight: 600;
    }
    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-processing {
        background: #ffc107;
        color: #856404;
    }
    .status-pending {
        background: #6c757d;
        color: white;
    }
    .status-completed {
        background: #28a745;
        color: white;
    }
    .status-failed {
        background: #dc3545;
        color: white;
    }
    .refresh-btn {
        cursor: pointer;
        transition: transform 0.3s;
    }
    .refresh-btn:hover {
        transform: rotate(180deg);
    }
    .spinner-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <!-- Active Job Section - Always show if there's an active job -->
            @if($activeJob)
            <div class="card shadow-lg mb-4">
                <div class="card-header {{ $activeJob->status == 'processing' ? 'bg-primary' : 'bg-secondary' }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            @if($activeJob->status == 'processing')
                                <i class="fas fa-spinner fa-pulse me-2"></i>
                                Generating Reports...
                            @else
                                <i class="fas fa-clock me-2"></i>
                                Report Queued...
                            @endif
                        </h5>
                        <span class="status-badge status-{{ $activeJob->status }}">
                            {{ strtoupper($activeJob->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Progress Bar - Always visible when job exists -->
                    <div class="text-center mb-3">
                        <div id="processingSpinner" class="spinner-border text-primary mb-2" style="width: 2.5rem; height: 2.5rem; {{ $activeJob->status == 'processing' ? '' : 'display:none;' }}"></div>
                        <div id="pendingSpinner" class="spinner-border text-secondary mb-2" style="width: 2.5rem; height: 2.5rem; {{ $activeJob->status == 'pending' ? '' : 'display:none;' }}"></div>
                    </div>

                    <div class="progress mt-2" style="height: 40px;">
                        <div id="progressBar" class="progress-bar progress-bar-striped {{ $activeJob->status == 'processing' ? 'progress-bar-animated' : '' }} bg-success"
                            style="width: {{ $activeJob->total_students > 0 ? ($activeJob->processed_students / $activeJob->total_students) * 100 : 0 }}%; font-size: 14px; line-height: 40px;">
                            @if($activeJob->total_students > 0)
                                {{ round(($activeJob->processed_students / $activeJob->total_students) * 100) }}%
                            @else
                                0%
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <p class="mb-1">
                            <i class="fas fa-users me-1"></i>
                            <strong id="processedCount">{{ $activeJob->processed_students }}</strong>
                            of
                            <strong id="totalCount">{{ $activeJob->total_students }}</strong> students processed
                        </p>
                        <p id="statusMessage" class="small text-muted mb-0">
                            @if($activeJob->status == 'processing')
                                <i class="fas fa-cog fa-spin me-1"></i> Processing in background...
                            @else
                                <i class="fas fa-hourglass-half me-1"></i> Waiting for queue worker...
                            @endif
                        </p>
                        <p class="text-info small mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            You can leave this page and come back later. The report will be available here automatically.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Completed Reports Section -->
            @if($completedJobs->count() > 0)
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Available Reports ({{ $completedJobs->count() }})
                        </h5>
                        <i class="fas fa-sync-alt refresh-btn" onclick="window.location.reload()"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Generated On</th>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Students</th>
                                    <th>Size</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedJobs as $job)
                                @php
                                    $fileExists = file_exists($job->file_path);
                                    $fileSize = $fileExists ? round(filesize($job->file_path) / 1024, 2) : 0;
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($job->created_at)->format('d M Y, H:i') }}</td>
                                    <td>
                                        @if($job->report_type == 'individual')
                                            <span class="badge bg-primary">Individual</span>
                                        @else
                                            <span class="badge bg-info">Combined</span>
                                        @endif
                                    </td>
                                    <td>{{ $job->report_title ?? 'Student Reports' }}</td>
                                    <td>{{ $job->total_students }}</td>
                                    <td>
                                        @if($fileExists)
                                            <span class="text-success">{{ $fileSize }} KB</span>
                                        @else
                                            <span class="text-danger">File missing</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($fileExists)
                                            <a href="{{ route('reports.download', $job->job_id) }}"
                                               class="btn btn-sm btn-success me-1 btn-xs" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                        <button onclick="deleteReport('{{ $job->job_id }}')"
                                                class="btn btn-sm btn-danger btn-xs" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @elseif(!$activeJob)
            <div class="card shadow-lg">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-pdf fa-4x text-muted mb-3"></i>
                    <h5>No Reports Available</h5>
                    <p class="text-muted">Generate a bulk report to see it here.</p>
                    <a href="{{ url()->previous() }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i> Go Back
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar with Info -->
        <div class="col-md-4">
            <div class="card shadow-lg">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            How it works
                        </h5>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-circle-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Step 1:</strong> Click "Bulk Reports" button on any results page
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Step 2:</strong> Reports generate in background (you can leave this page)
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Step 3:</strong> Come back here to download when ready
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Step 4:</strong> Reports stay available for future use
                        </li>
                    </ul>
                    <hr>
                    <div class="alert alert-light mt-2">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        <strong>Queue Status:</strong>
                        <span id="queueStatus" class="float-end">
                            @if($activeJob)
                                @if($activeJob->status == 'processing')
                                    <span class="text-warning"><i class="fas fa-spinner fa-pulse"></i> Processing</span>
                                @else
                                    <span class="text-secondary"><i class="fas fa-clock"></i> Queued</span>
                                @endif
                            @else
                                <span class="text-success"><i class="fas fa-check"></i> Idle</span>
                            @endif
                        </span>
                    </div>
                    <p class="small text-muted mb-0 mt-2">
                        <i class="fas fa-clock me-1"></i>
                        Reports are stored for 7 days before automatic deletion.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let activeJobId = "{{ $activeJob ? $activeJob->job_id : '' }}";
let activeJobStatus = "{{ $activeJob ? $activeJob->status : '' }}";
let checkInterval;

function checkJobStatus() {
    if (!activeJobId) return;

    fetch(`/reports/job-status/${activeJobId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Job status update:', data);

        if (data.status === 'pending') {
            // Update UI for pending state
            document.getElementById('processingSpinner').style.display = 'none';
            document.getElementById('pendingSpinner').style.display = 'inline-block';
            document.getElementById('progressBar').className = 'progress-bar bg-success';
            document.getElementById('statusMessage').innerHTML = '<i class="fas fa-hourglass-half me-1"></i> Waiting for queue worker...';
            document.getElementById('queueStatus').innerHTML = '<span class="text-secondary"><i class="fas fa-clock"></i> Queued</span>';
        }
        else if (data.status === 'processing') {
            // Update UI for processing state
            document.getElementById('processingSpinner').style.display = 'inline-block';
            document.getElementById('pendingSpinner').style.display = 'none';
            document.getElementById('progressBar').className = 'progress-bar progress-bar-striped progress-bar-animated bg-success';
            document.getElementById('statusMessage').innerHTML = '<i class="fas fa-cog fa-spin me-1"></i> Processing in background...';
            document.getElementById('queueStatus').innerHTML = '<span class="text-warning"><i class="fas fa-spinner fa-pulse"></i> Processing</span>';

            // Update progress
            let percent = (data.processed_students / data.total_students) * 100;
            let progressBar = document.getElementById('progressBar');
            let processedSpan = document.getElementById('processedCount');
            let totalSpan = document.getElementById('totalCount');

            if (progressBar) {
                progressBar.style.width = percent + '%';
                progressBar.innerHTML = Math.round(percent) + '%';
            }
            if (processedSpan) processedSpan.innerText = data.processed_students;
            if (totalSpan) totalSpan.innerText = data.total_students;
        }
        else if (data.status === 'completed') {
            // Stop checking
            if (checkInterval) clearInterval(checkInterval);

            // Update queue status
            document.getElementById('queueStatus').innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Complete</span>';

            // Show success message and reload
            Swal.fire({
                icon: 'success',
                title: 'Report Ready!',
                text: 'Your report has been generated successfully. The page will reload to show your download.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        }
        else if (data.status === 'failed') {
            if (checkInterval) clearInterval(checkInterval);

            document.getElementById('queueStatus').innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle"></i> Failed</span>';

            Swal.fire({
                icon: 'error',
                title: 'Generation Failed',
                text: data.error_message || 'An error occurred. Please try again.',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error checking status:', error);
    });
}

function deleteReport(jobId) {
    Swal.fire({
        title: 'Delete Report?',
        text: 'Are you sure you want to delete this report? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/reports/delete/${jobId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', 'Report has been deleted.', 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', 'Failed to delete report', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'An error occurred while deleting.', 'error');
            });
        }
    });
}

// Start checking if there's an active job
if (activeJobId) {
    // Initial check immediately
    checkJobStatus();
    // Then check every 2 seconds for faster response
    checkInterval = setInterval(checkJobStatus, 2000);
}
</script>
@endsection
