@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #6c8cff;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --white: #ffffff;

            /* Enterprise-grade colors */
            --brand-primary: #2c3e50;
            --brand-accent: #3498db;
            --brand-success: #27ae60;
            --brand-warning: #f39c12;
            --brand-danger: #e74c3c;
            --brand-info: #3498db;

            /* Shadows */
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.15);

            /* Transitions */
            --transition-base: all 0.3s ease;
            --transition-smooth: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            color: var(--dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Loading States */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition-base);
        }

        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .spinner-enterprise {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(44, 62, 80, 0.1);
            border-left-color: var(--brand-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Dashboard Header */
        .dashboard-headers {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            border-radius: 20px;
            padding: 0.7rem;
            margin-bottom: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .dashboard-headers::before {
            content: '📊';
            font-size: 8rem;
            position: absolute;
            right: 20px;
            bottom: -20px;
            opacity: 0.1;
            transform: rotate(10deg);
        }

        .dashboard-headers h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .dashboard-headers p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        /* Statistics Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: var(--shadow-md);
            transition: var(--transition-smooth);
            overflow: hidden;
            position: relative;
            height: 100%;
            min-height: 160px;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--brand-primary), var(--brand-accent));
        }

        .stat-card .card-body {
            padding: 1.8rem;
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            color: white;
            font-size: 1.8rem;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--secondary);
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .stat-card .stat-trend {
            font-size: 0.85rem;
            color: var(--success);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Main Card */
        .enterprise-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: var(--shadow-lg);
            transition: var(--transition-base);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .enterprise-card:hover {
            box-shadow: var(--shadow-xl);
        }

        .card-header-enterprise {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            color: white;
            border-bottom: none;
        }

        .card-header-enterprise h4 {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body-enterprise {
            padding: 2rem;
        }

        /* Advanced Table */
        .advanced-table-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .advanced-table {
            width: 100%;
            background: white;
            border-collapse: collapse;
        }

        .advanced-table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .advanced-table th {
            padding: 1rem 1.5rem;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--dark);
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            white-space: nowrap;
        }

        .advanced-table td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .advanced-table tbody tr {
            transition: var(--transition-base);
        }

        .advanced-table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            cursor: pointer;
        }

        .advanced-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Student Avatar */
        .student-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: var(--shadow-sm);
        }

        .student-info {
            margin-left: 12px;
        }

        .student-name {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
            font-size: 1rem;
        }

        .student-middle {
            font-size: 0.8rem;
            color: var(--secondary);
        }

        .class-badge {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        /* Action Buttons */
        .action-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            transition: var(--transition-smooth);
            border: none;
            cursor: pointer;
            margin: 0 4px;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .action-btn.manage {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
        }

        /* Enterprise Modal */
        .enterprise-modal .modal-content {
            border: none;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .enterprise-modal .modal-header {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            padding: 1.5rem 2rem;
            border: none;
        }

        .enterprise-modal .modal-title {
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .enterprise-modal .modal-body {
            padding: 2rem;
        }

        .enterprise-modal .modal-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Enterprise Form Controls */
        .form-group-enterprise {
            margin-bottom: 1.5rem;
        }

        .form-label-enterprise {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-label-enterprise i {
            margin-right: 8px;
            color: var(--brand-accent);
        }

        .form-control-enterprise {
            width: 100%;
            padding: 0.8rem 1.2rem;
            font-size: 0.95rem;
            border: 2px solid #eef2f6;
            border-radius: 12px;
            background-color: white;
            transition: var(--transition-base);
            color: var(--dark);
        }

        .form-control-enterprise:focus {
            outline: none;
            border-color: var(--brand-accent);
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.15);
        }

        .form-control-enterprise.error {
            border-color: var(--brand-danger);
        }

        .form-control-enterprise.success {
            border-color: var(--brand-success);
        }

        select.form-control-enterprise {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23343a40' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
        }

        .error-message {
            color: var(--brand-danger);
            font-size: 0.8rem;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .success-message {
            color: var(--brand-success);
            font-size: 0.8rem;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Enterprise Buttons */
        .btn-enterprise {
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: var(--transition-smooth);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .btn-enterprise::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-enterprise:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-enterprise-primary {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            color: white;
        }

        .btn-enterprise-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
        }

        .btn-enterprise-success {
            background: linear-gradient(135deg, var(--brand-success) 0%, #2ecc71 100%);
            color: white;
        }

        .btn-enterprise-danger {
            background: linear-gradient(135deg, var(--brand-danger) 0%, #c0392b 100%);
            color: white;
        }

        .btn-enterprise:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast-enterprise {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow-xl);
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            min-width: 300px;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toast-enterprise.success {
            border-left-color: var(--brand-success);
        }

        .toast-enterprise.error {
            border-left-color: var(--brand-danger);
        }

        .toast-enterprise.warning {
            border-left-color: var(--brand-warning);
        }

        .toast-enterprise.info {
            border-left-color: var(--brand-info);
        }

        .toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .toast-icon.success {
            background: var(--brand-success);
        }

        .toast-icon.error {
            background: var(--brand-danger);
        }

        .toast-icon.warning {
            background: var(--brand-warning);
        }

        .toast-icon.info {
            background: var(--brand-info);
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 0.9rem;
            color: var(--secondary);
        }

        .toast-close {
            cursor: pointer;
            color: var(--secondary);
            transition: var(--transition-base);
        }

        .toast-close:hover {
            color: var(--dark);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            font-size: 5rem;
            color: rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--secondary);
            margin-bottom: 1.5rem;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .dashboard-headers h1 {
                font-size: 1.8rem;
            }

            .stat-card .stat-value {
                font-size: 2rem;
            }

            .card-body-enterprise {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .dashboard-headers {
                padding: 0.65rem;
            }

            .dashboard-headers h1 {
                font-size: 1.5rem;
            }

            .advanced-table th,
            .advanced-table td {
                padding: 0.8rem 1rem;
            }

            .student-avatar {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }

            .action-btn {
                width: 32px;
                height: 32px;
                font-size: 0.85rem;
            }

            .toast-enterprise {
                min-width: 250px;
            }
        }

        @media (max-width: 576px) {
            .stat-card .card-body {
                padding: 1.2rem;
            }

            .stat-card .stat-value {
                font-size: 1.5rem;
            }

            .stat-card .stat-icon {
                width: 45px;
                height: 45px;
                font-size: 1.4rem;
            }

            .btn-enterprise {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
            }
        }

        /* Print Styles */
        @media print {

            .stat-card,
            .enterprise-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .btn-enterprise,
            .toast-container,
            .action-btn {
                display: none;
            }
        }
    </style>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-enterprise"></div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="container-fluid px-3 px-md-4 py-3 py-md-4">
        <!-- Dashboard Header -->
        <div class="dashboard-headers">
            <h2>
                Welcome back, {{ ucwords(strtolower(Auth::user()->first_name)) }}!
            </h2>
            <p class="text-white"> Manage your children's Academic progress and other related information</p>
        </div>

        <!-- Statistics Row -->
        <div class="row g-4 mb-4">
            <div class="col-xl-4">
                <div class="stat-card" onclick="window.location.href='#children-list'">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-label">My Children</div>
                        <div class="stat-value">{{ count($students) }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span>Enrolled in school</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Children List Section -->
            <div class="col-xl-8 col-md-6">
                <div class="enterprise-card" id="children-list">
                    <div class="card-header-enterprise">
                        <h4>
                            <i class="fas fa-users me-2"></i>
                            Children List
                            <span class="badge bg-light text-dark ms-2">{{ count($students) }}</span>
                        </h4>
                    </div>
                    <div class="card-body-enterprise">
                        <div class="advanced-table-container">
                            <table class="advanced-table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>Student Information</th>
                                        <th>Class</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($students as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="student-avatar">
                                                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                                    </div>
                                                    <div class="student-info">
                                                        <div class="student-name">
                                                            {{ ucwords(strtolower($student->first_name . ' ' . $student->last_name)) }}
                                                        </div>
                                                        <div class="student-middle">
                                                            {{ ucwords(strtolower($student->middle_name)) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="class-badge">
                                                    {{ strtoupper($student->class_code) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('students.profile', ['student' => Hashids::encode($student->id)]) }}"
                                                    class="action-btn manage" data-bs-toggle="tooltip"
                                                    title="Manage Student Profile">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="fas fa-child"></i>
                                                    </div>
                                                    <h5>No Children Registered</h5>
                                                    <p class="text-muted">Please contact the school administration to
                                                        register
                                                        your children.</p>
                                                </div>
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
    </div>

    <!-- Student Registration Modal -->
    <div class="modal fade enterprise-modal" id="studentRegistrationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-graduate"></i>
                        Student Registration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{ route('register.student') }}" method="POST"
                        enctype="multipart/form-data" id="studentRegistrationForm">
                        @csrf

                        <!-- Name Section -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group-enterprise">
                                    <label class="form-label-enterprise">
                                        <i class="fas fa-user"></i>First Name
                                    </label>
                                    <input type="text" name="fname" class="form-control-enterprise" id="firstName"
                                        placeholder="Enter first name" value="{{ old('fname') }}" required>
                                    @error('fname')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-enterprise">
                                    <label class="form-label-enterprise">
                                        <i class="fas fa-user"></i>Middle Name
                                    </label>
                                    <input type="text" name="middle" class="form-control-enterprise" id="middleName"
                                        placeholder="Enter middle name" value="{{ old('middle') }}" required>
                                    @error('middle')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-enterprise">
                                    <label class="form-label-enterprise">
                                        <i class="fas fa-user"></i>Last Name
                                    </label>
                                    <input type="text" name="lname" class="form-control-enterprise" id="lastName"
                                        placeholder="Enter last name" value="{{ old('lname') }}" required>
                                    @error('lname')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Personal Details -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group-enterprise">
                                    <label class="form-label-enterprise">
                                        <i class="fas fa-venus-mars"></i>Gender
                                    </label>
                                    <select name="gender" id="gender" class="form-control-enterprise" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                    @error('gender')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-enterprise">
                                    <label class="form-label-enterprise">
                                        <i class="fas fa-calendar"></i>Date of Birth
                                    </label>
                                    <input type="date" name="dob" class="form-control-enterprise" id="dob"
                                        value="{{ old('dob') }}"
                                        min="{{ \Carbon\Carbon::now()->subYears(17)->format('Y-m-d') }}"
                                        max="{{ \Carbon\Carbon::now()->subYears(2)->format('Y-m-d') }}" required>
                                    @error('dob')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-enterprise">
                                    <label class="form-label-enterprise">
                                        <i class="fas fa-school"></i>Class
                                    </label>
                                    <select name="grade" id="grade" class="form-control-enterprise" required>
                                        <option value="">-- Select Class --</option>
                                        @forelse ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ old('grade') == $class->id ? 'selected' : '' }}>
                                                {{ $class->class_name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No classes available</option>
                                        @endforelse
                                    </select>
                                    @error('grade')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group-enterprise">
                                    <label class="form-label-enterprise">
                                        <i class="fas fa-users"></i>Stream
                                    </label>
                                    <select name="group" id="stream" class="form-control-enterprise" required>
                                        <option value="">-- Select Stream --</option>
                                        <option value="a" {{ old('group') == 'a' ? 'selected' : '' }}>Stream A
                                        </option>
                                        <option value="b" {{ old('group') == 'b' ? 'selected' : '' }}>Stream B
                                        </option>
                                        <option value="c" {{ old('group') == 'c' ? 'selected' : '' }}>Stream C
                                        </option>
                                    </select>
                                    @error('group')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-enterprise">
                                    <label class="form-label-enterprise">
                                        <i class="fas fa-bus"></i>School Bus
                                    </label>
                                    <select name="driver" id="busNumber" class="form-control-enterprise">
                                        <option value="">-- Select Bus (Optional) --</option>
                                        @forelse ($buses as $bus)
                                            <option value="{{ $bus->id }}"
                                                {{ old('driver') == $bus->id ? 'selected' : '' }}>
                                                Bus No. {{ $bus->bus_no }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No buses available</option>
                                        @endforelse
                                    </select>
                                    @error('driver')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-enterprise">
                                    <label class="form-label-enterprise">
                                        <i class="fas fa-camera"></i>Photo
                                    </label>
                                    <input type="file" name="image" class="form-control-enterprise"
                                        id="studentPhoto" accept="image/*">
                                    @error('image')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-4 px-0 pb-0">
                            <button type="button" class="btn-enterprise btn-enterprise-secondary"
                                data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn-enterprise btn-enterprise-primary" id="saveButton">
                                <span id="submitText">Register Child</span>
                                <span id="submitSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Form submission handling
            const form = document.getElementById("studentRegistrationForm");
            const saveButton = document.getElementById("saveButton");
            const submitText = document.getElementById("submitText");
            const submitSpinner = document.getElementById("submitSpinner");
            const loadingOverlay = document.getElementById("loadingOverlay");

            if (form && saveButton) {
                form.addEventListener("submit", function(event) {
                    // Let the form submit normally - no preventDefault
                    // Just show loading state
                    saveButton.disabled = true;
                    submitText.textContent = "Processing...";
                    submitSpinner.classList.remove("d-none");
                    if (loadingOverlay) loadingOverlay.classList.add("show");
                });
            }

            // Reset form state when modal is closed
            const modal = document.getElementById('studentRegistrationModal');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    if (form) {
                        form.classList.remove("was-validated");
                        if (saveButton) {
                            saveButton.disabled = false;
                            submitText.textContent = "Register Child";
                            submitSpinner.classList.add("d-none");
                        }
                        if (loadingOverlay) loadingOverlay.classList.remove("show");
                    }
                });
            }

            // Toast notification system
            window.showToast = function(type, title, message) {
                const toastContainer = document.getElementById('toastContainer');
                if (!toastContainer) return;

                const toast = document.createElement('div');
                toast.className = `toast-enterprise ${type}`;

                const icons = {
                    success: 'fa-check-circle',
                    error: 'fa-exclamation-circle',
                    warning: 'fa-exclamation-triangle',
                    info: 'fa-info-circle'
                };

                toast.innerHTML = `
                <div class="toast-icon ${type}">
                    <i class="fas ${icons[type] || 'fa-info-circle'}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <div class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </div>
            `;

                toastContainer.appendChild(toast);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 5000);
            };

            // Show any session messages as toasts
            @if (session('success'))
                showToast('success', 'Success', '{{ session('success') }}');
            @endif

            @if (session('error'))
                showToast('error', 'Error', '{{ session('error') }}');
            @endif

            @if ($errors->any())
                showToast('error', 'Validation Error', 'Please check the form and try again');
            @endif
        });

        // Authorization check
        @if (Auth::user()->usertype != 4)
            window.location.href = '/error-page';
        @endif

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
@endsection
