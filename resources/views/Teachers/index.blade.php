@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f94144;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 70% 30%, rgba(67, 97, 238, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 30% 70%, rgba(63, 55, 201, 0.1) 0%, transparent 30%);
            animation: rotate 60s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(100px, -100px) scale(1.2); }
            50% { transform: translate(200px, 0) scale(0.8); }
            75% { transform: translate(100px, 100px) scale(1.1); }
        }

        /* Main Container */
        .dashboard-container {
            max-width: 1600px;
            margin: 30px auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Modern Card */
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Card Header */
        .card-header-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 20px 25px;
            position: relative;
            overflow: visible;
        }

        .card-header-modern::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 60%);
            /* animation: rotate 20s linear infinite; */
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-title {
            color: white;
            margin: 0;
        }

        .header-title h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .header-title p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Action Buttons - FIXED: Not expanding */
        .action-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            position: relative;
            z-index: 100;
        }

        .btn-modern {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            color: white;
            white-space: nowrap;
        }

        .btn-modern i {
            font-size: 1rem;
        }

        .btn-export {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-export:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .btn-add {
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .btn-add:hover {
            background: rgba(255, 255, 255, 0.35);
        }

        /* Dropdown - FIXED: Positioned correctly */
        .dropdown-modern {
            position: relative;
        }

        .dropdown-modern .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 5px;
            min-width: 180px;
            border: none;
            border-radius: 10px;
            box-shadow: var(--shadow-lg);
            padding: 8px;
            background: white;
            z-index: 1050;
        }

        .dropdown-modern .dropdown-item {
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--dark);
        }

        .dropdown-modern .dropdown-item:hover {
            background: var(--gradient-1);
            color: white;
        }

        /* Card Body */
        .card-body-modern {
            padding: 25px;
        }

        /* Table Container */
        .table-container-modern {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Modern Table - FIXED: Better header colors */
        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #2b3d5c 0%, #1a2a44 100%);
            /* color: white; */
            font-weight: 600;
            padding: 12px 12px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 12px 12px;
            border-bottom: 1px solid #edf2f7;
            color: #4a5568;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table-modern tbody tr {
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: #f7fafc;
        }

        /* Teacher Info - FIXED: Removed duplicate ID */
        .teacher-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .teacher-avatar-modern {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }

        .teacher-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }

        /* Member ID Badge - FIXED: Compact */
        .member-id-badge {
            background: #edf2f7;
            color: #4a5568;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: monospace;
            display: inline-block;
            white-space: nowrap;
        }

        /* Gender Badge - FIXED: Compact */
        .gender-badge {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .gender-male {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .gender-female {
            background: linear-gradient(135deg, #e83e8c 0%, #c2185b 100%);
        }

        /* Role Badge - FIXED: Compact padding */
        .role-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-block;
            white-space: nowrap;
        }

        .role-admin {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            color: white;
        }

        .role-teacher {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
        }

        .role-staff {
            background: linear-gradient(135deg, #36b9cc 0%, #1a8a9e 100%);
            color: white;
        }

        .role-other {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
        }

        /* Phone Link */
        .phone-link {
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .phone-link i {
            font-size: 0.8rem;
        }

        /* Year Badge */
        .year-badge {
            background: linear-gradient(135deg, #36b9cc 0%, #1a8a9e 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
        }

        /* Status Badge - FIXED: Compact padding */
        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .status-badge i {
            font-size: 0.7rem;
        }

        .status-active {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-blocked {
            background: #fed7d7;
            color: #742a2a;
        }

        /* Action Icons - FIXED: Compact and no wrapping */
        .action-icons {
            display: flex;
            gap: 4px;
            justify-content: center;
            flex-wrap: nowrap;
        }

        .action-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .action-icon.view {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .action-icon.warning {
            background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%);
        }

        .action-icon.success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }

        .action-icon.danger {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        }

        .action-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Empty State */
        .empty-state-modern {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
            border-radius: 16px;
            border: 2px dashed #ffc107;
        }

        .empty-state-modern i {
            font-size: 50px;
            color: #ffc107;
            margin-bottom: 15px;
        }

        /* Modal Styles */
        .modal-modern .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px 20px;
        }

        .modal-modern .modal-body {
            padding: 20px;
        }

        .modal-modern .modal-footer {
            border: none;
            padding: 15px 20px;
            background: #f8f9fa;
        }

        /* Form Controls - Compact */
        .form-group-modern {
            margin-bottom: 15px;
        }

        .form-label-modern {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 4px;
            font-size: 0.85rem;
        }

        .form-control-modern {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-modern:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .table-modern {
                display: block;
                overflow-x: auto;
            }

            .action-icons {
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .action-group {
                justify-content: stretch;
            }

            .btn-modern {
                flex: 1;
                justify-content: center;
            }

            .dropdown-modern .dropdown-menu {
                right: auto;
                left: 0;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
            }

            .modern-card {
                background: rgba(33, 37, 41, 0.95);
            }

            .table-modern tbody td {
                color: #e9ecef;
                border-bottom-color: #495057;
            }

            .table-modern tbody tr:hover {
                background: #343a40;
            }

            .teacher-name {
                color: #e9ecef;
            }

            .member-id-badge {
                background: #495057;
                color: #e9ecef;
            }

            .status-active {
                background: #22543d;
                color: #c6f6d5;
            }

            .status-blocked {
                background: #742a2a;
                color: #fed7d7;
            }

            .form-control-modern {
                background: #2b3035;
                border-color: #495057;
                color: #e9ecef;
            }
        }
    </style>

    <div class="animated-bg"></div>
    <div class="particles"></div>
    <div class="loading-spinner" id="loadingSpinner"></div>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Header - FIXED: Export button not expanding -->
            <div class="card-header-modern">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="header-title">
                            <h3>Teachers Management</h3>
                        </div>
                    </div>
                    <div class="action-group">
                        <!-- Export Dropdown - FIXED: Proper positioning -->
                        <div class="dropdown dropdown-modern">
                            <button class="btn-modern btn-export dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download"></i>
                                <span>Export</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{route('teachers.excel.export')}}">
                                        <i class="fas fa-file-excel text-success"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{route('teachers.pdf.export')}}" target="_blank">
                                        <i class="fas fa-file-pdf text-danger"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Add Teacher Button -->
                        <button type="button" class="btn-modern btn-add" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                            <i class="fas fa-plus"></i>
                            <span>Add Teacher</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                @if ($teachers->isEmpty())
                    <div class="empty-state-modern">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h6>No Teachers Found</h6>
                        <p class="text-muted small">Click "Add Teacher" to register your first teacher</p>
                    </div>
                @else
                    <div class="table-container-modern">
                        <table class="table-modern" id="myTable">
                            <thead>
                                <tr>
                                    <th class="text-white">#</th>
                                    <th>Staff ID</th>
                                    <th>Teacher</th>
                                    <th>Gender</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teachers as $teacher)
                                    <tr>
                                        <td><span class="fw-bold">{{ $loop->iteration }}</span></td>
                                        <td>
                                            <span class="member-id-badge">{{ strtoupper($teacher->member_id) }}</span>
                                        </td>
                                        <td>
                                            <div class="teacher-info">
                                                @php
                                                    $imageName = $teacher->image;
                                                    $imagePath = storage_path('app/public/profile/' . $imageName);
                                                    $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                                        ? asset('storage/profile/' . $imageName)
                                                        : asset('storage/profile/' . ($teacher->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                                                @endphp
                                                <img src="{{ $avatarImage }}" alt="Avatar" class="teacher-avatar-modern">
                                                <span class="teacher-name">{{ ucwords(strtolower($teacher->first_name . ' ' . $teacher->last_name)) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="gender-badge {{ $teacher->gender == 'male' ? 'gender-male' : 'gender-female' }}">
                                                {{ strtoupper(substr($teacher->gender, 0, 1)) }}
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $roleClass = match($teacher->role_id) {
                                                    1 => 'role-admin',
                                                    2 => 'role-teacher',
                                                    3 => 'role-staff',
                                                    default => 'role-other'
                                                };
                                            @endphp
                                            <span class="role-badge {{ $roleClass }}">
                                                {{ ucwords(strtolower($teacher->role_name)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="tel:{{ $teacher->phone }}" class="phone-link">
                                                <i class="fas fa-phone"></i>
                                                {{ $teacher->phone }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="year-badge">
                                                {{ $teacher->joined }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($teacher->status == 1)
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-circle"></i> Active
                                                </span>
                                            @else
                                                <span class="status-badge status-blocked">
                                                    <i class="fas fa-circle"></i> Blocked
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                                <a href="{{ route('teacher.profile', ['teacher' => Hashids::encode($teacher->id)]) }}"
                                                   class="action-icon view"
                                                   title="View Profile">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if ($teacher->status == 1)
                                                    <form action="{{ route('update.teacher.status', ['teacher' => Hashids::encode($teacher->id)]) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Block {{ $teacher->first_name }}?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon warning" title="Block">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('teachers.restore', ['teacher' => Hashids::encode($teacher->id)]) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Unblock {{ $teacher->first_name }}?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon success" title="Unblock">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('Teachers.remove', ['teacher' => Hashids::encode($teacher->id)]) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Delete {{ $teacher->first_name }}? This cannot be undone.')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="action-icon danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Teacher Modal -->
    <div class="modal fade modal-modern" id="addTeacherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register New Teacher</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form class="needs-validation" novalidate action="{{ route('Teachers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">First Name</label>
                                    <input type="text" name="fname" class="form-control-modern" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Other Names</label>
                                    <input type="text" name="lname" class="form-control-modern" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Email</label>
                                    <input type="email" name="email" class="form-control-modern">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Gender</label>
                                    <select name="gender" class="form-control-modern" required>
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Phone</label>
                                    <input type="text" name="phone" class="form-control-modern" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Qualification</label>
                                    <select name="qualification" class="form-control-modern" required>
                                        <option value="">Select</option>
                                        <option value="1">Masters</option>
                                        <option value="2">Degree</option>
                                        <option value="3">Diploma</option>
                                        <option value="4">Certificate</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control-modern" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Year Joined</label>
                                    <select name="joined" class="form-control-modern" required>
                                        <option value="">Select</option>
                                        @for ($year = date('Y'); $year >= 2010; $year--)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label class="form-label-modern">Street/Village</label>
                            <input type="text" name="street" class="form-control-modern" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="saveButton">Save Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Form handling
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (form && submitButton) {
                form.addEventListener("submit", function(event) {
                    event.preventDefault();

                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Saving...';

                    if (!form.checkValidity()) {
                        form.classList.add("was-validated");
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'Save Teacher';
                        return;
                    }

                    setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }
        });
    </script>
@endsection
