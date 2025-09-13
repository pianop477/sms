@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient-start: #4361ee;
            --gradient-end: #3a0ca3;
            --card-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 8px 6px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .glass-card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-5px);
        }

        .header-section {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border-radius: 24px;
            padding: 8px;
            margin-bottom: 6px;
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .modern-table thead {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }

        .modern-table th {
            padding: 6px 7px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            border: none;
        }

        .modern-table td {
            padding: 6px 4px;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
            vertical-align: middle;
            transition: all 0.3s ease;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .modern-table tbody tr:hover {
            background: rgba(67, 97, 238, 0.05);
            transform: translateX(5px);
        }

        .badge-modern {
            padding: 4px 6px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-reset {
            background: linear-gradient(135deg, var(--danger), #b5179e);
            border: none;
            border-radius: 16px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(230, 57, 70, 0.3);
        }

        .btn-reset:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(230, 57, 70, 0.4);
            color: white;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(67, 97, 238, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.1);
            border-color: var(--primary);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .search-section {
            background: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 6px;
            }

            .header-section {
                padding: 6px;
            }

            .modern-table {
                font-size: 14px;
            }

            .modern-table th,
            .modern-table td {
                padding: 4px 8px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .btn-reset {
                width: 100%;
                padding: 0.8px 12px;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .slide-in {
            animation: slideInRight 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .security-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--danger), #b5179e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px rgba(230, 57, 70, 0.3);
        }
    </style>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="glass-card header-section fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">🔐 Password Reset Manager</h1>
                    <p class="lead mb-0 opacity-90 text-white"> Reset school managers' passwords securely</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-white text-primary p-3 rounded-pill">
                        <i class="fas fa-users me-2"></i>
                        {{ count($users) }} School Managers
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid fade-in">
            <div class="stat-card">
                <div class="security-icon">
                    <i class="fas fa-key text-white fa-2x"></i>
                </div>
                <h4 class="text-primary mb-1">{{ count($users) }}</h4>
                <small class="text-muted">Total Managers</small>
            </div>
            <div class="stat-card">
                <div class="security-icon" style="background: linear-gradient(135deg, var(--success), #0f9d58);">
                    <i class="fas fa-check-circle text-white fa-2x"></i>
                </div>
                <h4 class="text-primary mb-1">{{ $users->where('status', 1)->count() }}</h4>
                <small class="text-muted">Active Managers</small>
            </div>
            <div class="stat-card">
                <div class="security-icon" style="background: linear-gradient(135deg, var(--warning), #f77f00);">
                    <i class="fas fa-ban text-white fa-2x"></i>
                </div>
                <h4 class="text-primary mb-1">{{ $users->where('status', 0)->count() }}</h4>
                <small class="text-muted">Blocked Managers</small>
            </div>
            <div class="stat-card">
                <div class="security-icon" style="background: linear-gradient(135deg, var(--info), var(--primary));">
                    <i class="fas fa-school text-white fa-2x"></i>
                </div>
                <h4 class="text-primary mb-1">{{ $users->unique('school_name')->count() }}</h4>
                <small class="text-muted">Schools</small>
            </div>
        </div>

        <!-- Search Section -->
        <div class="glass-card search-section fade-in">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Search managers..." id="searchInput">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Managers Table -->
        <div class="glass-card fade-in">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <h4 class="text-primary mb-0">
                        <i class="fas fa-user-shield me-2"></i> School Managers
                    </h4>
                    <div class="badge bg-primary p-2 rounded-pill text-white">
                        <i class="fas fa-sync-alt me-2"></i> Real-time
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="modern-table table-responsive-md" id="myTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Manager Profile</th>
                                <th>Email</th>
                                <th>School</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="slide-in" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                                    <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle p-2 me-3">
                                                <i class="fas fa-user-tie text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-capitalize">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </div>
                                                <small class="text-muted">{{ $user->phone ?? 'No phone' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-primary me-2"></i>
                                            <span class="text-truncate">{{ $user->email }}</span>
                                        </div>
                                    </td>
                                    <td class="text-capitalize">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-school text-primary me-2"></i>
                                            {{ $user->school_name }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($user->status == 1)
                                            <span class="badge-modern bg-success text-white">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge-modern bg-secondary text-white">
                                                <i class="fas fa-times-circle me-1"></i> Blocked
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.update.password', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn-reset"
                                                    onclick="return confirm('Are you sure you want to reset password for {{ $user->first_name }} {{ $user->last_name }}?')"
                                                    title="Reset Password">
                                                <i class="fas fa-key me-2"></i>Reset
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($users->isEmpty())
                    <div class="text-center p-5">
                        <div class="empty-icon mb-4">
                            <i class="fas fa-user-slash fa-4x text-primary opacity-50"></i>
                        </div>
                        <h4 class="text-dark mb-3">No School Managers</h4>
                        <p class="text-muted mb-4">There are no school managers registered in the system.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('.modern-table tbody tr');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            // Add confirmation for reset action
            const resetButtons = document.querySelectorAll('.btn-reset');
            resetButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('⚠️ This will reset the password to default. Are you sure?')) {
                        e.preventDefault();
                    } else {
                        // Show loading state
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Resetting...';
                        this.disabled = true;

                        // Revert after 2 seconds if form doesn't submit
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }, 2000);
                    }
                });
            });

            // Add GSAP animations if available
            if (typeof gsap !== 'undefined') {
                gsap.from('.fade-in', {
                    duration: 1,
                    y: 30,
                    opacity: 0,
                    stagger: 0.2,
                    ease: "power3.out"
                });

                gsap.from('.slide-in', {
                    duration: 0.8,
                    x: 50,
                    opacity: 0,
                    stagger: 0.1,
                    ease: "power2.out"
                });
            }

            // Add real-time update simulation
            setInterval(() => {
                document.querySelectorAll('.badge-modern').forEach(badge => {
                    badge.style.opacity = '0.8';
                    setTimeout(() => {
                        badge.style.opacity = '1';
                    }, 500);
                });
            }, 30000);
        });
    </script>
@endsection
