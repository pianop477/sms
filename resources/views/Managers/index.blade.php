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
            padding: 10px 5px;
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
            padding: 10px;
            margin-bottom: 10px;
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
            padding: 1.8px 6px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 6px;
            letter-spacing: 0.5px;
            border: none;
        }

        .modern-table td {
            padding: 6px 8px;
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

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 6px 8px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .bg-success {
            background: linear-gradient(135deg, #4cc9f0, #4895ef);
        }

        .bg-success::before {
            background: white;
        }

        .bg-danger {
            background: linear-gradient(135deg, #f72585, #b5179e);
        }

        .bg-danger::before {
            background: white;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .gender-badge {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
        }

        .male-badge {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
        }

        .female-badge {
            background: linear-gradient(135deg, #f72585, #b5179e);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 4px;
        }

        .btn-icon {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .search-section {
            padding: 5px;
            background: rgba(67, 97, 238, 0.05);
            border-radius: 16px;
            margin-bottom: 6px;
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 10px;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 5px;
            }

            .header-section {
                padding: 6px;
            }

            .modern-table {
                font-size: 12px;
            }

            .modern-table th,
            .modern-table td {
                padding: 6px 4px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .stats-overview {
                grid-template-columns: 1fr;
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
    </style>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="glass-card header-section fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">👨‍💼 School Administrators</h1>
                    <p class="lead mb-0 opacity-90 text-white">Manage all school administrators and managers</p>
                </div>
                <div class="col-md-4 text-md-end float-right">
                    <div class="badge bg-white text-primary p-3 rounded-pill">
                        <i class="fas fa-users me-2"></i>
                        {{ count($managers) }} Total Admin
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-overview fade-in">
            <div class="stat-item">
                <div class="text-primary fw-bold fs-4">{{ count($managers) }}</div>
                <small class="text-muted">Total Managers</small>
            </div>
            <div class="stat-item">
                <div class="text-success fw-bold fs-4">{{ $managers->where('status', 1)->count() }}</div>
                <small class="text-muted">Active Managers</small>
            </div>
            <div class="stat-item">
                <div class="text-danger fw-bold fs-4">{{ $managers->where('status', 0)->count() }}</div>
                <small class="text-muted">Blocked Managers</small>
            </div>
        </div>

        <!-- Search and Filters Section -->
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
                            <i class="fas fa-filter me-2"></i>Filters
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Manager
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Managers Table -->
        <div class="glass-card fade-in">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="modern-table table-responsive-md">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>School Manager</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>School</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($managers as $manager)
                                <tr class="slide-in" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                                    <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle p-2 me-3">
                                                <i class="fas fa-user-tie text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-capitalize">
                                                    {{ $manager->first_name }} {{ $manager->last_name }}
                                                </div>
                                                {{-- <small class="text-muted">Manager ID: {{ $manager->id }}</small> --}}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="gender-badge {{ $manager->gender[0] == 'm' ? 'male-badge' : 'female-badge' }}">
                                            {{ strtoupper($manager->gender[0]) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone text-primary me-2"></i>
                                            {{ $manager->phone }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-primary me-2"></i>
                                            <span class="text-truncate" style="max-width: 150px;">{{ $manager->email }}</span>
                                        </div>
                                    </td>
                                    <td class="text-capitalize">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-school text-primary me-2"></i>
                                             {{ $manager->school_name }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($manager->status == 1)
                                            <span class="status-badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="status-badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i> Blocked
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon bg-info text-white" title="View Profile">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-icon bg-warning text-white" title="Edit Manager">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-icon bg-danger text-white" title="Delete Manager">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Empty State (if no managers) -->
        @if ($managers->isEmpty())
            <div class="glass-card text-center p-5 fade-in">
                <div class="empty-icon mb-4">
                    <i class="fas fa-user-tie fa-4x text-primary opacity-50"></i>
                </div>
                <h4 class="text-dark mb-3">No Managers Found</h4>
                <p class="text-muted mb-4">There are no school managers registered in the system yet.</p>
                <button class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Add First Manager
                </button>
            </div>
        @endif
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

            // Add hover effects
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(8px)';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
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
        });
    </script>
@endsection
