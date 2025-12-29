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
            padding: 8px 4px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--card-shadow);
            /* transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); */
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
            margin-bottom: 8px;
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
            /* background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent); */
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

        .form-select {
            border: 2px solid rgba(67, 97, 238, 0.2);
            border-radius: 12px;
            padding: 10px 14px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            background-color: white;
        }

        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.1);
        }

        .modern-table thead {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }

        .modern-table th {
            padding: 6px 8px;
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
            padding: 4px 8px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 4px;
            justify-content: center;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: none;
            font-size: 14px;
        }

        .btn-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--info), var(--primary));
            border: none;
            border-radius: 16px;
            padding: 6px 8px;
            color: white;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.3);
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(67, 97, 238, 0.4);
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

        .gender-badge {
            width: 35px;
            height: 35px;
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

        .modal-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-control {
            border: 2px solid rgba(67, 97, 238, 0.2);
            border-radius: 12px;
            padding: 6px 8px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
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
                padding: 6px 4px;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-icon {
                width: 35px;
                height: 35px;
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

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 4px;
            margin-bottom: 8px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 6px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
    </style>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="glass-card header-section fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">👑 Super Admin Accounts</h1>
                    <p class="lead mb-0 opacity-90 text-white"> Manage system administrators and their permissions</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-white text-primary p-3 rounded-pill">
                        <i class="fas fa-users me-2"></i>
                        {{ count($users) }} Admin Users
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="admin-stats fade-in">
            <div class="stat-card">
                <div class="text-primary fw-bold fs-3">{{ count($users) }}</div>
                <small class="text-muted">Total Admins</small>
            </div>
            <div class="stat-card">
                <div class="text-success fw-bold fs-3">{{ $users->where('status', 1)->count() }}</div>
                <small class="text-muted">Active Admins</small>
            </div>
            <div class="stat-card">
                <div class="text-danger fw-bold fs-3">{{ $users->where('status', 0)->count() }}</div>
                <small class="text-muted">Blocked Admins</small>
            </div>
            <div class="stat-card">
                <div class="text-warning fw-bold fs-3">{{ $users->where('gender', 'male')->count() }}</div>
                <small class="text-muted">Male Admins</small>
            </div>
        </div>

        <!-- Admin Users Table -->
        <div class="glass-card fade-in">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <h4 class="text-primary mb-0">
                        <i class="fas fa-user-shield me-2"></i>Administrator Accounts
                    </h4>
                    <button class="btn-modern" data-toggle="modal" data-target=".bd-example-modal-lg">
                        <i class="fas fa-user-plus me-2"></i>New Admin
                    </button>
                </div>

                <div class="table-responsive p-3">
                    <table class="modern-table table-responsive-md" id="myTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Admin User</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="slide-in" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                                    <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-bold text-capitalize">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="gender-badge {{ $user->gender[0] == 'm' ? 'male-badge' : 'female-badge' }}">
                                            {{ strtoupper($user->gender[0]) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {{ $user->phone }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="text-truncate">{{ $user->email }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($user->status == 1)
                                            <span class="badge-modern bg-success">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge-modern bg-danger">
                                                <i class="fas fa-times-circle me-1"></i> Blocked
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->id == 1)
                                            <span class="badge-modern bg-warning">
                                                <i class="fas fa-crown me-1"></i> Super User
                                            </span>
                                        @else
                                            <div class="action-buttons">
                                                @if ($user->status == 1)
                                                    <a href="{{ route('admin.account.edit', ['user' => Hashids::encode($user->id)]) }}"
                                                       class="btn-icon bg-info text-white" title="View Profile">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.account.block', ['user' => Hashids::encode($user->id)]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn-icon bg-warning text-dark"
                                                                onclick="return confirm('Are you sure you want to Block {{ $user->first_name }} {{ $user->last_name }}?')"
                                                                title="Block User">
                                                            <i class="ti-na"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.account.unblock', ['user' => Hashids::encode($user->id)]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn-icon bg-success text-white"
                                                                onclick="return confirm('Are you sure you want to Unblock {{ $user->first_name }} {{ $user->last_name }}?')"
                                                                title="Unblock User">
                                                            <i class="fas fa-refresh"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.account.destroy', ['user' => Hashids::encode($user->id)]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-icon bg-danger text-white"
                                                                onclick="return confirm('Are you sure you want to Delete {{ $user->first_name }} {{ $user->last_name }}?')"
                                                                title="Delete User">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add New User Modal -->
        <div class="modal fade bd-example-modal-lg">
            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-glass">
                    <div class="modal-header" style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end)); color: white;">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>Admin Registration Form
                        </h5>
                        <button type="button" class="btn btn-xs btn-danger" data-dismiss="modal" aria-label="Close"><i class="fas fa-close"></i></button>
                    </div>
                    <div class="modal-body p-4">
                        <form class="needs-validation" novalidate action="{{ route('admin.accounts.registration') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">
                                        <i class="fas fa-user me-2"></i>First Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="fname" class="form-control" id="firstName"
                                           placeholder="First name" value="{{ old('fname') }}" required>
                                    @error('fname')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">
                                        <i class="fas fa-user me-2"></i>Last Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="lname" class="form-control" id="lastName"
                                           placeholder="Last name" value="{{ old('lname') }}" required>
                                    @error('lname')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="email" name="email" class="form-control" id="email"
                                               placeholder="email@example.com" value="{{ old('email') }}" required>
                                    </div>
                                    @error('email')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">
                                        <i class="fas fa-venus-mars me-2"></i>Gender <span class="text-danger">*</span>
                                    </label>
                                    <select name="gender" id="gender" class="form-select" required>
                                        <option value="">-- select gender --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-2"></i>Mobile Phone <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="phone" class="form-control" id="phone"
                                           placeholder="0712 456 789" value="{{ old('phone') }}" required>
                                    @error('phone')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">
                                    <i class="fas fa-times me-2"></i> Close
                                </button>
                                <button type="submit" id="saveButton" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i> Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Creating Admin...
                `;

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Submit';
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
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
