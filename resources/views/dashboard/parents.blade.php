@extends('SRTDashboard.frame')

@section('content')
<style>
    :root {
        /* Senior Palette: Softer, more accessible colors */
        --brand-primary: #2563eb;
        --brand-dark: #1e3a8a;
        --brand-accent: #3b82f6;
        --surface-card: #ffffff;
        --bg-main: #f1f5f9;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --success-bg: #dcfce7;
        --success-text: #166534;

        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);

        --transition: all 0.2s ease;
    }

    body {
        background-color: var(--bg-main);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* Simple loading indicator - non-intrusive */
    .loading-bar {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        background: var(--brand-accent);
        width: 0;
        z-index: 9999;
        transition: width 0.3s ease;
    }

    /* Welcome Header - Clean and friendly - FIXED CONTRAST */
    .welcome-header {
        background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand-primary) 100%);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-md);
    }

    .welcome-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: white;
    }

    .welcome-header h2 i {
        color: white;
    }

    .welcome-header p {
        font-size: 0.9rem;
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.95);
        background: rgba(0, 0, 0, 0.15);
        display: inline-block;
        padding: 0.4rem 1rem;
        border-radius: 30px;
        backdrop-filter: blur(5px);
    }

    .welcome-header p i {
        color: white;
    }

    /* Simple Stat Card - Large and clear */
    .stat-card {
        background: var(--surface-card);
        border-radius: 20px;
        border: none;
        padding: 1.25rem;
        transition: var(--transition);
        height: 100%;
        box-shadow: var(--shadow-sm);
        text-align: center;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .stat-icon {
        width: 55px;
        height: 55px;
        background: #eff6ff;
        color: var(--brand-primary);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .verified-badge {
        display: inline-block;
        background: var(--success-bg);
        color: var(--success-text);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    /* Student Card Container - Simple card design */
    .students-card {
        background: var(--surface-card);
        border-radius: 20px;
        border: none;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        height: 100%;
    }

    .card-header-simple {
        background: var(--surface-card);
        padding: 1rem 1.25rem;
        border-bottom: 2px solid #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .card-header-simple h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        color: var(--text-main);
    }

    .refresh-btn {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 0.4rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        color: var(--text-muted);
        transition: var(--transition);
        cursor: pointer;
    }

    .refresh-btn:hover {
        background: var(--brand-primary);
        color: white;
        border-color: var(--brand-primary);
    }

    /* Student Items - Card-based layout */
    .student-list {
        padding: 0.5rem;
    }

    .student-item {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border: 1px solid #eef2f6;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .student-item:hover {
        border-color: var(--brand-accent);
        box-shadow: var(--shadow-sm);
        transform: translateX(4px);
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 2;
        min-width: 180px;
    }

    /* Student Profile Image Styling */
    .student-avatar-img {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        flex-shrink: 0;
    }

    .student-avatar-img-alt {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .student-details {
        flex: 1;
    }

    .student-name {
        font-weight: 600;
        font-size: 1rem;
        color: var(--text-main);
        margin-bottom: 0.2rem;
    }

    .student-middle {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .class-badge {
        background: #f0f2f5;
        color: var(--brand-dark);
        padding: 0.35rem 0.9rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .view-btn {
        background: var(--brand-primary);
        color: white;
        border: none;
        padding: 0.5rem 1.2rem;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .view-btn:hover {
        background: var(--brand-dark);
        transform: translateY(-2px);
        color: white;
    }

    /* Empty State - Friendly message */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-icon {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }

    .empty-message {
        font-size: 0.85rem;
        color: var(--text-muted);
        max-width: 280px;
        margin: 0 auto;
    }

    /* Simple Tooltip */
    [data-tooltip] {
        position: relative;
        cursor: pointer;
    }

    [data-tooltip]:before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        white-space: nowrap;
        display: none;
        z-index: 10;
        margin-bottom: 5px;
    }

    [data-tooltip]:hover:before {
        display: block;
    }

    /* Mobile Optimizations */
    @media (max-width: 768px) {
        .welcome-header {
            padding: 1rem;
        }

        .welcome-header h2 {
            font-size: 1.2rem;
        }

        .welcome-header p {
            font-size: 0.75rem;
            padding: 0.3rem 0.8rem;
        }

        .stat-number {
            font-size: 1.5rem;
        }

        .student-item {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .student-info {
            flex-direction: column;
            text-align: center;
        }

        .class-badge {
            justify-content: center;
        }

        .view-btn {
            justify-content: center;
            width: 100%;
        }

        .card-header-simple {
            flex-direction: column;
            text-align: center;
        }
    }

    /* Large screens optimization */
    @media (min-width: 1200px) {
        .student-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .student-list::-webkit-scrollbar {
            width: 5px;
        }

        .student-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .student-list::-webkit-scrollbar-thumb {
            background: var(--brand-accent);
            border-radius: 10px;
        }
    }

    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .student-item {
        animation: fadeIn 0.3s ease-out;
    }
</style>

<!-- Simple Loading Bar -->
<div class="loading-bar" id="topLoadingBar"></div>

<div class="container-fluid px-3 px-md-4 py-3 py-md-4">
    <!-- Welcome Header - Fixed Contrast -->
    <div class="welcome-header">
        <div class="row align-items-center">
            <div class="col-12">
                <h2>
                    <i class="fas fa-smile-wink me-2"></i>
                    Welcome, {{ ucwords(strtolower(Auth::user()->first_name)) }}!
                </h2>
                <p class="text-warning">
                    <i class="fas fa-info-circle me-1"></i>
                    Select a student below to view their academic progress, attendance, reports, and more.
                </p>
            </div>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        <!-- Stats Column - Simple counter -->
        <div class="col-lg-4 col-md-12 mb-3 mb-lg-0">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-number">
                    {{ count($students) }}
                </div>
                <div class="stat-label">
                    <i class="fas fa-child me-1"></i> Children Enrolled
                </div>
                <div class="verified-badge">
                    <i class="fas fa-check-circle me-1"></i> Verified Parent Account
                </div>
            </div>
        </div>

        <!-- Students List Column -->
        <div class="col-lg-8 col-md-12">
            <div class="students-card">
                <div class="card-header-simple">
                    <h5>
                        <i class="fas fa-users me-2"></i>
                        Your Children
                    </h5>
                    <button class="refresh-btn" onclick="window.location.reload()" data-tooltip="Refresh student list">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>

                <div class="student-list">
                    @forelse ($students as $student)
                        @php
                            // Determine student image path
                            $studentImage = null;
                            if (!empty($student->image) && file_exists(storage_path('app/public/students/' . $student->image))) {
                                $studentImage = asset('storage/students/' . $student->image);
                            } else {
                                $studentImage = asset('storage/students/student.jpg');
                            }
                        @endphp
                        <div class="student-item">
                            <div class="student-info">
                                <img src="{{ $studentImage }}" class="student-avatar-img" alt="Student Photo">
                                <div class="student-details">
                                    <div class="student-name">
                                        {{ ucwords(strtolower($student->first_name . ' ' . $student->last_name)) }}
                                    </div>
                                    @if($student->middle_name)
                                        <div class="student-middle">
                                            {{ ucwords(strtolower($student->middle_name)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="class-badge">
                                <i class="fas fa-graduation-cap me-1"></i>
                                Class {{ strtoupper($student->class_code ?? 'N/A') }}
                            </div>

                            <a href="{{ route('students.profile', ['student' => Hashids::encode($student->id)]) }}"
                               class="view-btn"
                               data-tooltip="View complete profile">
                                <i class="fas fa-eye me-1"></i> View Profile
                            </a>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <div class="empty-title">
                                No Students Found
                            </div>
                            <div class="empty-message">
                                No students are currently linked to your account. Please contact the school administration for assistance.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Helpful Tips Section - Friendly guidance -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-light border-0 shadow-sm" style="background: #f8fafc; border-radius: 16px;">
                <div class="d-flex align-items-center gap-3 flex-wrap flex-md-nowrap">
                    <div class="text-center">
                        <i class="fas fa-lightbulb text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <strong class="d-block mb-1">Need Help?</strong>
                        <small class="text-muted">
                            Click on "View Profile" next to any student to see their subjects, attendance records, exam results, fee payments, and more.
                            If you experience any issues, please contact the school support team.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Simple loading bar for navigation
        const loadingBar = document.getElementById('topLoadingBar');

        // Show loading bar when clicking on any view button or refresh button
        document.querySelectorAll('.view-btn, .refresh-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                loadingBar.style.width = '60%';

                // For refresh button, ensure we show loading
                if(this.classList.contains('refresh-btn')) {
                    setTimeout(() => {
                        loadingBar.style.width = '100%';
                    }, 100);
                }
            });
        });

        // Hide loading bar after page loads
        window.addEventListener('load', function() {
            loadingBar.style.width = '100%';
            setTimeout(() => {
                loadingBar.style.width = '0';
            }, 300);
        });

        // Simple tooltip fallback for older browsers
        var tooltipElements = document.querySelectorAll('[data-tooltip]');
        if(tooltipElements.length > 0 && typeof bootstrap !== 'undefined') {
            try {
                var tooltipTriggerList = [].slice.call(tooltipElements);
                tooltipTriggerList.map(function(el) {
                    return new bootstrap.Tooltip(el);
                });
            } catch(e) {
                // Fallback - CSS tooltip already handles it
                console.log('Tooltip JS not loaded, using CSS fallback');
            }
        }

        // Authorization Check
        @if (Auth::user()->usertype != 4)
            window.location.href = '/error-page';
        @endif
    });

    // Simple function to handle any AJAX or navigation delays
    window.addEventListener('beforeunload', function() {
        const loadingBar = document.getElementById('topLoadingBar');
        if(loadingBar) {
            loadingBar.style.width = '80%';
        }
    });
</script>

<!-- Optional: Add Font Awesome if not already present -->
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@endsection
