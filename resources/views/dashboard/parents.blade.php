@extends('SRTDashboard.frame')

@section('content')
<style>
    :root {
        /* Senior Palette: Deeper, desaturated professional tones */
        --brand-primary: #2563eb;
        --brand-dark: #1e3a8a;
        --brand-accent: #3b82f6;
        --surface-card: #ffffff;
        --bg-main: #f1f5f9;
        --text-main: #0f172a;
        --text-muted: #64748b;

        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);

        --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background-color: var(--bg-main);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--text-main);
    }

    /* --- UX Fix: Top Progress Bar (Better than a blocking overlay) --- */
    .loading-bar {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        background: var(--brand-accent);
        width: 0;
        z-index: 9999;
        transition: width 0.4s ease;
    }

    /* --- Enhanced Header --- */
    .dashboard-headers {
        background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand-primary) 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: var(--shadow-md);
        position: relative;
    }

    .dashboard-headers h5 {
        font-weight: 700;
        letter-spacing: -0.025em;
        margin-bottom: 0.5rem;
    }

    /* --- Stat Card Refinement --- */
    .stat-card {
        background: var(--surface-card);
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        transition: var(--transition);
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand-accent);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: #eff6ff;
        color: var(--brand-primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    /* --- The Professional Table --- */
    .enterprise-card {
        background: var(--surface-card);
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .card-header-enterprise {
        background: #f8fafc;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .advanced-table th {
        background: #f8fafc;
        padding: 0.75rem 1.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        border-bottom: 1px solid #e2e8f0;
    }

    .advanced-table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        transition: var(--transition);
    }

    .advanced-table tbody tr:hover td {
        background-color: #fdfdfd;
    }

    /* --- Student Avatar & Info --- */
    .student-avatar {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: linear-gradient(45deg, var(--brand-primary), var(--brand-accent));
        color: white;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .student-name {
        font-weight: 600;
        color: var(--text-main);
        font-size: 0.95rem;
    }

    .class-badge {
        background: #f1f5f9;
        color: var(--brand-dark);
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid #e2e8f0;
    }

    /* --- Action Buttons: High-end UX --- */
    .action-btn.manage {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #fff;
        color: var(--brand-primary);
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        text-decoration: none;
    }

    .action-btn.manage:hover {
        background: var(--brand-primary);
        color: white;
        border-color: var(--brand-primary);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    /* --- Fix: Mobile Responsive Labels --- */
    @media (max-width: 768px) {
        .advanced-table thead { display: none; }
        .advanced-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.5rem;
        }
        .advanced-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            padding: 0.5rem 1rem;
        }
        .advanced-table td::before {
            content: attr(data-label);
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--text-muted);
        }
    }
</style>

<div class="loading-bar" id="topLoadingBar"></div>

<div class="container-fluid px-4 py-4">
    <div class="dashboard-headers">
        <div class="row align-items-center">
            <div class="col">
                <h5>Welcome back, {{ ucwords(strtolower(Auth::user()->first_name)) }}</h5>
                <p class="mb-0 opacity-75 text-white">
                    <i class="fas fa-info-circle me-1"></i>
                    Select a student profile below to view subjects, attendance, academic reports, payment info and other related details.
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-3 col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="text-muted small fw-bold text-uppercase">Children Enrolled</div>
                <div class="h2 fw-bold mb-1">{{ count($students) }}</div>
                <div class="text-success small"><i class="fas fa-check-circle me-1"></i> Verified Account</div>
            </div>
        </div>

        <div class="col-xl-9 col-md-8">
            <div class="enterprise-card">
                <div class="card-header-enterprise">
                    <h6 class="mb-0 fw-bold">Student Directory</h6>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table advanced-table mb-0">
                        <thead>
                            <tr>
                                <th>Student Detail</th>
                                <th>Grade / Class</th>
                                <th class="text-end">View Profile</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr>
                                    <td data-label="Student">
                                        <div class="d-flex align-items-center">
                                            <div class="student-avatar me-3">
                                                {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="student-name">{{ ucwords(strtolower($student->first_name . ' ' . $student->last_name)) }}</div>
                                                <div class="text-muted small">{{ ucwords(strtolower($student->middle_name)) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Class">
                                        <span class="class-badge">
                                            {{ strtoupper($student->class_code) }}
                                        </span>
                                    </td>
                                    <td data-label="Action" class="text-end">
                                        <a href="{{ route('students.profile', ['student' => Hashids::encode($student->id)]) }}"
                                           class="action-btn manage"
                                           data-bs-toggle="tooltip"
                                           title="Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <i class="fas fa-user-slash fa-3x text-light mb-3"></i>
                                        <h6 class="text-muted">No students linked to this account.</h6>
                                        <p class="small text-muted">Please contact administration to update your records.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(el) {
            return new bootstrap.Tooltip(el);
        });

        // UX: Show top progress bar on any link click inside the dashboard
        const loadingBar = document.getElementById('topLoadingBar');
        document.querySelectorAll('.action-btn, .btn').forEach(btn => {
            btn.addEventListener('click', function() {
                loadingBar.style.width = '70%';
            });
        });

        // Guard Clause for Authorization
        @if (Auth::user()->usertype != 4)
            window.location.href = '/error-page';
        @endif
    });
</script>
@endsection
