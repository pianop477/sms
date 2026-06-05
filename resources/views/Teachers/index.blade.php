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
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        /* Container */
        .dashboard-container {
            max-width: 1600px;
            margin: 20px auto;
            padding: 0 20px;
        }

        /* Modern Card */
        .modern-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        /* Card Header */
        .card-header-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 20px 25px;
        }

        .header-content {
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
        }

        .header-title h3 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .header-title p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 0.85rem;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-modern {
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
        }

        .btn-modern:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px 25px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .stat-card i {
            font-size: 28px;
            color: white;
            margin-bottom: 8px;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: white;
        }

        .stat-card .stat-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Card Body */
        .card-body-modern {
            padding: 25px;
        }

        /* Table Container - For Desktop */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow-x: auto;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .data-table thead th {
            background: linear-gradient(135deg, #2b3d5c 0%, #1a2a44 100%);
            color: white;
            font-weight: 600;
            padding: 14px 12px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: 12px;
            border-bottom: 1px solid var(--gray-200);
            color: var(--dark);
            vertical-align: middle;
            font-size: 0.85rem;
        }

        .data-table tbody tr:hover {
            background: var(--gray-100);
        }

        /* Teacher Info in Table */
        .teacher-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .teacher-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Badges */
        .badge-id {
            background: var(--gray-100);
            color: var(--primary);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: monospace;
        }

        .gender-badge {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: inline-flex;
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

        .role-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.7rem;
            display: inline-block;
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

        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-active {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-blocked {
            background: #fed7d7;
            color: #742a2a;
        }

        /* Action Icons */
        .action-icons {
            display: flex;
            gap: 6px;
            justify-content: center;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
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
        }

        /* Cards Grid - For Mobile (hidden on desktop) */
        .cards-grid {
            display: none;
            flex-direction: column;
            gap: 15px;
        }

        .teacher-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .teacher-card-header {
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-100);
        }

        .teacher-card-avatar {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            object-fit: cover;
        }

        .teacher-card-info {
            flex: 1;
        }

        .teacher-card-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
        }

        .teacher-card-id {
            font-size: 11px;
            color: var(--primary);
            background: rgba(67, 97, 238, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 4px;
        }

        .teacher-card-body {
            padding: 15px;
        }

        .card-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 15px;
        }

        .card-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-info-item i {
            width: 32px;
            height: 32px;
            background: var(--gray-100);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .card-info-item .label {
            font-size: 10px;
            color: var(--gray-600);
        }

        .card-info-item .value {
            font-size: 13px;
            font-weight: 500;
            color: var(--dark);
        }

        .card-actions {
            display: flex;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px solid var(--gray-200);
        }

        .card-action-btn {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
            border-radius: 16px;
        }

        /* Modal */
        .modal-modern .modal-content {
            border-radius: 20px;
            border: none;
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px 20px;
        }

        .modal-modern .modal-body {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 5px;
            display: block;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        /* DataTables Override */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            padding: 6px 12px;
            margin-left: 8px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            border-radius: 8px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .table-container {
                display: none;
            }

            .cards-grid {
                display: flex;
            }

            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .action-group {
                justify-content: stretch;
            }

            .btn-modern {
                justify-content: center;
                flex: 1;
            }
        }

        @media (max-width: 576px) {
            .dashboard-container {
                padding: 10px;
            }

            .card-header-modern {
                padding: 15px;
            }

            .card-body-modern {
                padding: 15px;
            }

            .card-info-grid {
                grid-template-columns: 1fr;
            }

            .card-actions {
                flex-wrap: wrap;
            }

            .card-action-btn {
                min-width: calc(50% - 4px);
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
            }

            .modern-card {
                background: #2d3748;
            }

            .data-table tbody td {
                color: #e9ecef;
                border-bottom-color: #4a5568;
            }

            .data-table tbody tr:hover {
                background: #374151;
            }

            .teacher-card {
                background: #2d3748;
                border-color: #4a5568;
            }

            .teacher-card-header {
                background: #374151;
                border-bottom-color: #4a5568;
            }

            .teacher-card-name {
                color: #f8f9fa;
            }

            .card-info-item i {
                background: #374151;
                color: var(--primary);
            }

            .card-info-item .value {
                color: #f8f9fa;
            }

            .form-control {
                background: #374151;
                border-color: #4a5568;
                color: #f8f9fa;
            }

            .form-label {
                color: #f8f9fa;
            }

            .badge-id {
                background: #374151;
                color: #f8f9fa;
            }
        }
    </style>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Header -->
            <div class="card-header-modern">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="header-title">
                            <h3>Teachers Management</h3>
                            <p>Manage all teachers in your school</p>
                        </div>
                    </div>
                    <div class="action-group">
                        <div class="dropdown">
                            <button class="btn-modern dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{route('teachers.excel.export')}}"><i class="fas fa-file-excel text-success"></i> Excel</a></li>
                                <li><a class="dropdown-item" href="{{route('teachers.pdf.export')}}" target="_blank"><i class="fas fa-file-pdf text-danger"></i> PDF</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn-modern" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                            <i class="fas fa-user-plus"></i> Add Teacher
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            @php
                $activeCount = $teachers->filter(fn($t) => $t->status == 1)->count();
                $maleCount = $teachers->filter(fn($t) => strtolower($t->gender) == 'male')->count();
                $femaleCount = $teachers->filter(fn($t) => strtolower($t->gender) == 'female')->count();
                $roleTeacherCount = $teachers->filter(fn($t) => $t->role_id == 2)->count();
            @endphp
            <div class="stats-row">
                <div class="stat-card">
                    <i class="fas fa-user-check"></i>
                    <div class="stat-value">{{ $activeCount }}</div>
                    <div class="stat-label">Active Teachers</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <div class="stat-value">{{ $roleTeacherCount }}</div>
                    <div class="stat-label">Teaching Staff</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-male"></i>
                    <div class="stat-value">{{ $maleCount }}</div>
                    <div class="stat-label">Male</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-female"></i>
                    <div class="stat-value">{{ $femaleCount }}</div>
                    <div class="stat-label">Female</div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                @if ($teachers->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-chalkboard-teacher fa-3x mb-3" style="color: #ffc107;"></i>
                        <h6>No Teachers Found</h6>
                        <p class="text-muted">Click "Add Teacher" to register your first teacher</p>
                    </div>
                @else
                    <!-- Desktop Table View -->
                    <div class="table-container">
                        <table class="data-table" id="teachersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Staff ID</th>
                                    <th>Teacher</th>
                                    <th>Gender</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teachers as $teacher)
                                    @php
                                        $imageName = $teacher->image;
                                        $imagePath = storage_path('app/public/profile/' . $imageName);
                                        $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                            ? asset('storage/profile/' . $imageName)
                                            : asset('storage/profile/' . ($teacher->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));

                                        $roleClass = match($teacher->role_id) {
                                            1 => 'role-admin',
                                            2 => 'role-teacher',
                                            3 => 'role-staff',
                                            default => 'role-other'
                                        };

                                        $roleName = match($teacher->role_id) {
                                            1 => 'Admin',
                                            2 => 'Teacher',
                                            3 => 'Staff',
                                            default => 'Other'
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="badge-id">{{ strtoupper($teacher->member_id ?? 'N/A') }}</span></td>
                                        <td>
                                            <div class="teacher-info">
                                                <img src="{{ $avatarImage }}" alt="Avatar" class="teacher-avatar">
                                                <span class="fw-semibold">{{ ucwords(strtolower($teacher->first_name . ' ' . $teacher->last_name)) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="gender-badge {{ $teacher->gender == 'male' ? 'gender-male' : 'gender-female' }}">
                                                {{ strtoupper(substr($teacher->gender, 0, 1)) }}
                                            </div>
                                        </td>
                                        <td><span class="role-badge {{ $roleClass }}">{{ $roleName }}</span></td>
                                        <td><a href="tel:{{ $teacher->phone }}" class="text-decoration-none">{{ $teacher->phone }}</a></td>
                                        <td>{{ $teacher->joined ?? 'N/A' }}</td>
                                        <td>
                                            @if ($teacher->status == 1)
                                                <span class="status-badge status-active"><i class="fas fa-circle"></i> Active</span>
                                            @else
                                                <span class="status-badge status-blocked"><i class="fas fa-circle"></i> Blocked</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                                <a href="{{ route('teacher.profile', ['teacher' => Hashids::encode($teacher->id)]) }}" class="action-icon view" title="View"><i class="fas fa-eye"></i></a>
                                                @if ($teacher->status == 1)
                                                    <form action="{{ route('update.teacher.status', ['teacher' => Hashids::encode($teacher->id)]) }}" method="POST" class="d-inline" onsubmit="return confirm('Block {{ $teacher->first_name }}?')">
                                                        @csrf @method('PUT')
                                                        <button type="submit" class="action-icon warning" title="Block"><i class="fas fa-ban"></i></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('teachers.restore', ['teacher' => Hashids::encode($teacher->id)]) }}" method="POST" class="d-inline" onsubmit="return confirm('Unblock {{ $teacher->first_name }}?')">
                                                        @csrf @method('PUT')
                                                        <button type="submit" class="action-icon success" title="Unblock"><i class="fas fa-check"></i></button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('Teachers.remove', ['teacher' => Hashids::encode($teacher->id)]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ $teacher->first_name }}? This cannot be undone.')">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="action-icon danger" title="Delete"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards View -->
                    <div class="cards-grid">
                        @foreach ($teachers as $teacher)
                            @php
                                $imageName = $teacher->image;
                                $imagePath = storage_path('app/public/profile/' . $imageName);
                                $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                    ? asset('storage/profile/' . $imageName)
                                    : asset('storage/profile/' . ($teacher->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));

                                $roleClass = match($teacher->role_id) {
                                    1 => 'role-admin',
                                    2 => 'role-teacher',
                                    3 => 'role-staff',
                                    default => 'role-other'
                                };

                                $roleName = match($teacher->role_id) {
                                    1 => 'Admin',
                                    2 => 'Teacher',
                                    3 => 'Staff',
                                    default => 'Other'
                                };
                            @endphp
                            <div class="teacher-card">
                                <div class="teacher-card-header">
                                    <img src="{{ $avatarImage }}" alt="Avatar" class="teacher-card-avatar">
                                    <div class="teacher-card-info">
                                        <div class="teacher-card-name">{{ ucwords(strtolower($teacher->first_name . ' ' . $teacher->last_name)) }}</div>
                                        <div class="teacher-card-id"><i class="fas fa-id-card"></i> {{ strtoupper($teacher->member_id ?? 'N/A') }}</div>
                                    </div>
                                    @if ($teacher->status == 1)
                                        <span class="status-badge status-active"><i class="fas fa-circle"></i> Active</span>
                                    @else
                                        <span class="status-badge status-blocked"><i class="fas fa-circle"></i> Blocked</span>
                                    @endif
                                </div>
                                <div class="teacher-card-body">
                                    <div class="card-info-grid">
                                        <div class="card-info-item">
                                            <i class="fas fa-venus-mars"></i>
                                            <div><div class="label">Gender</div><div class="value">{{ ucfirst($teacher->gender) }}</div></div>
                                        </div>
                                        <div class="card-info-item">
                                            <i class="fas fa-briefcase"></i>
                                            <div><div class="label">Role</div><div class="value">{{ $roleName }}</div></div>
                                        </div>
                                        <div class="card-info-item">
                                            <i class="fas fa-phone"></i>
                                            <div><div class="label">Phone</div><div class="value"><a href="tel:{{ $teacher->phone }}">{{ $teacher->phone }}</a></div></div>
                                        </div>
                                        <div class="card-info-item">
                                            <i class="fas fa-calendar"></i>
                                            <div><div class="label">Joined</div><div class="value">{{ $teacher->joined ?? 'N/A' }}</div></div>
                                        </div>
                                    </div>
                                    <div class="card-actions">
                                        <a href="{{ route('teacher.profile', ['teacher' => Hashids::encode($teacher->id)]) }}" class="card-action-btn view"><i class="fas fa-eye"></i> View</a>
                                        @if ($teacher->status == 1)
                                            <form action="{{ route('update.teacher.status', ['teacher' => Hashids::encode($teacher->id)]) }}" method="POST" class="d-inline" style="flex:1" onsubmit="return confirm('Block {{ $teacher->first_name }}?')">
                                                @csrf @method('PUT')
                                                <button type="submit" class="card-action-btn warning"><i class="fas fa-ban"></i> Block</button>
                                            </form>
                                        @else
                                            <form action="{{ route('teachers.restore', ['teacher' => Hashids::encode($teacher->id)]) }}" method="POST" class="d-inline" style="flex:1" onsubmit="return confirm('Unblock {{ $teacher->first_name }}?')">
                                                @csrf @method('PUT')
                                                <button type="submit" class="card-action-btn success"><i class="fas fa-check"></i> Unblock</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('Teachers.remove', ['teacher' => Hashids::encode($teacher->id)]) }}" method="POST" class="d-inline" style="flex:1" onsubmit="return confirm('Delete {{ $teacher->first_name }}?')">
                                            @csrf @method('PUT')
                                            <button type="submit" class="card-action-btn danger"><i class="fas fa-trash"></i> Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Register New Teacher</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('Teachers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">First Name <span class="text-danger">*</span></label><input type="text" name="fname" class="form-control" required></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">Other Names <span class="text-danger">*</span></label><input type="text" name="lname" class="form-control" required></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-control" required><option value="">Select</option><option value="male">Male</option><option value="female">Female</option></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">Phone <span class="text-danger">*</span></label><input type="tel" name="phone" class="form-control" required></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">Qualification <span class="text-danger">*</span></label>
                                    <select name="qualification" class="form-control" required><option value="">Select</option><option value="1">Masters</option><option value="2">Degree</option><option value="3">Diploma</option><option value="4">Certificate</option></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">Date of Birth <span class="text-danger">*</span></label><input type="date" name="dob" class="form-control" required></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">Year Joined <span class="text-danger">*</span></label>
                                    <select name="joined" class="form-control" required><option value="">Select</option>@for ($year = date('Y'); $year >= 2010; $year--)<option value="{{ $year }}">{{ $year }}</option>@endfor</select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">Street/Village <span class="text-danger">*</span></label><input type="text" name="street" class="form-control" required></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label class="form-label">Profile Picture</label><input type="file" name="image" class="form-control" accept="image/*"><small class="text-muted">Optional - Max 2MB</small></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#teachersTable').DataTable({
                responsive: false,
                pageLength: 10,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                },
                columnDefs: [
                    { orderable: false, targets: [8] }
                ]
            });
        });
    </script>
@endsection
