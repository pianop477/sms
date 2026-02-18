@extends('SRTDashboard.frame')

@section('content')
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

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

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
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

        .card-header-modern::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--warning), var(--success), var(--accent));
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

        /* Action Buttons Group */
        .action-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            position: relative;
            z-index: 100;
        }

        .btn-modern {
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
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
            font-size: 0.9rem;
        }

        .btn-promote {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-export {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.35);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-add {
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        /* Dropdown Menu */
        .dropdown-modern .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            padding: 8px;
            background: white;
            min-width: 150px;
        }

        .dropdown-modern .dropdown-item {
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--dark);
        }

        .dropdown-modern .dropdown-item:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        /* Card Body */
        .card-body-modern {
            padding: 25px;
        }

        /* Stats Card */
        .stats-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 20px;
            color: white;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-label {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-bottom: 2px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
        }

        /* Batch Update Form */
        .batch-form {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .form-label-modern {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 4px;
            font-size: 0.85rem;
        }

        .form-select-modern {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            width: 100%;
        }

        .btn-warning-modern {
            background: linear-gradient(135deg, var(--warning) 0%, #f4b619 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-warning-modern:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(248, 150, 30, 0.3);
        }

        .btn-warning-modern:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .selected-counter {
            background: #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.85rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Table Container */
        .table-container-modern {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Modern Table */
        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #2b3d5c 0%, #1a2a44 100%);
            /* color: white; */
            font-weight: 600;
            padding: 12px 12px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #edf2f7;
            color: #4a5568;
            vertical-align: middle;
            font-size: 0.85rem;
        }

        .table-modern tbody tr {
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: #f7fafc;
        }

        /* Student Info */
        .student-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .student-avatar-modern {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }

        .student-name {
            font-weight: 500;
            color: var(--dark);
            font-size: 0.85rem;
        }

        /* Gender Badge */
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

        /* Stream Badge */
        .stream-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-block;
        }

        .stream-A {
            background: #e8f0fe;
            color: #1a73e8;
        }

        .stream-B {
            background: #e6f4ea;
            color: #0f9d58;
        }

        .stream-C {
            background: #fce8e6;
            color: #d93025;
        }

        /* Action Icons */
        .action-icons {
            display: flex;
            gap: 4px;
            justify-content: flex-end;
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
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .action-icon.edit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .action-icon.view {
            background: linear-gradient(135deg, #36b9cc 0%, #1a8a9e 100%);
        }

        .action-icon.delete {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        }

        .action-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Checkbox */
        .checkbox-modern {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        /* Empty State */
        .empty-state-modern {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
            border-radius: 16px;
            border: 2px dashed #ffc107;
        }

        /* Modal Modern */
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
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-modern .modal-footer {
            border: none;
            padding: 15px 20px;
            background: #f8f9fa;
        }

        /* Form Controls */
        .form-group-modern {
            margin-bottom: 15px;
        }

        .form-label-modern {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 4px;
            font-size: 0.85rem;
        }

        .form-label-modern .required {
            color: var(--danger);
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

        .note-text {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 5px 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: var(--dark) !important;
        }

        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: var(--shadow-md);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .table-modern {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 992px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .action-group {
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .stats-card .row {
                flex-direction: column;
                gap: 15px;
            }

            .batch-form .row {
                flex-direction: column;
                gap: 10px;
            }

            .selected-counter {
                width: 100%;
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

            .student-name {
                color: #e9ecef;
            }

            .batch-form {
                background: linear-gradient(135deg, #2b3035 0%, #343a40 100%);
            }

            .form-label-modern {
                color: #e9ecef;
            }

            .form-select-modern,
            .form-control-modern {
                background: #2b3035;
                border-color: #495057;
                color: #e9ecef;
            }

            .selected-counter {
                background: #2b3035;
                color: #e9ecef;
            }

            .dropdown-modern .dropdown-menu {
                background: #2b3035;
            }

            .dropdown-modern .dropdown-item {
                color: #e9ecef;
            }
        }
    </style>

    <div class="animated-bg"></div>
    <div class="particles"></div>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Header -->
            <div class="card-header-modern">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="header-title">
                            <h3>{{ $classId->class_name }} - {{ $classId->class_code }}</h3>
                        </div>
                    </div>
                    <div class="action-group">
                        @if ($students->isNotEmpty())
                            @if (auth()->user()->usertype != 5)
                                <button type="button" class="btn-modern btn-promote" data-bs-toggle="modal"
                                    data-bs-target="#promoteModal">
                                    <i class="fas fa-exchange-alt"></i>
                                    <span>Promote</span>
                                </button>
                            @endif

                            <div class="dropdown dropdown-modern">
                                <button class="btn-modern btn-export dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-download"></i>
                                    <span>Export</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('students.export.excel', ['class' => Hashids::encode($classId->id)]) }}">
                                            <i class="fas fa-file-excel text-success"></i> Excel
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('export.student.pdf', ['class' => Hashids::encode($classId->id)]) }}"
                                            target="_blank">
                                            <i class="fas fa-file-pdf text-danger"></i> PDF
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif

                        <a href="{{ route('classes.list', ['class' => Hashids::encode($classId->id)]) }}"
                            class="btn-modern btn-back">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </a>

                        @if (auth()->user()->usertype != 5)
                            <button type="button" class="btn-modern btn-add" data-bs-toggle="modal"
                                data-bs-target="#addStudentModal">
                                <i class="fas fa-plus"></i>
                                <span>New Student</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                <!-- Stats Card -->
                <div class="stats-card">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <div class="stat-label">Total Students</div>
                                    <div class="stat-value">{{ $students->count() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-male"></i>
                                </div>
                                <div>
                                    <div class="stat-label">Boys</div>
                                    <div class="stat-value">
                                        {{ $students->filter(fn($s) => strtolower($s->gender) === 'male')->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-female"></i>
                                </div>
                                <div>
                                    <div class="stat-label">Girls</div>
                                    <div class="stat-value">
                                        {{ $students->filter(fn($s) => strtolower($s->gender) === 'female')->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Batch Update Form -->
                <form id="batchForm" action="{{ route('students.batchUpdateStream') }}" method="POST" class="batch-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label class="form-label-modern">Transfer Student Stream</label>
                            <select name="new_stream" class="form-select-modern" required>
                                <option value="">-- Select Stream --</option>
                                <option value="A">Stream A</option>
                                <option value="B">Stream B</option>
                                <option value="C">Stream C</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn-warning-modern" id="updateStreamBtn" disabled>
                                <i class="fas fa-random me-1"></i> Shift Stream
                            </button>
                        </div>

                        <div class="col-md-5">
                            <div class="selected-counter" id="selectedCount">
                                <i class="fas fa-users"></i>
                                <span>0 students selected</span>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Students Table -->
                @if ($students->isEmpty())
                    <div class="empty-state-modern">
                        <i class="fas fa-users"></i>
                        <h6>No Students Found</h6>
                        <p class="text-muted small">Click "New Student" to add your first student</p>
                    </div>
                @else
                    <div class="table-container-modern">
                        <table class="table-modern" id="myTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <input type="checkbox" id="selectAll" class="checkbox-modern">
                                    </th>
                                    <th>Adm #</th>
                                    <th>Student</th>
                                    <th>Middle Name</th>
                                    <th>Surname</th>
                                    <th class="text-center">Gender</th>
                                    <th class="text-center">Stream</th>
                                    <th>Date of Birth</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="student[]" value="{{ $student->id }}"
                                                class="student-checkbox checkbox-modern">
                                        </td>
                                        <td class="fw-bold text-uppercase">{{ $student->admission_number }}</td>
                                        <td>
                                            <div class="student-info">
                                                @php
                                                    $imageName = $student->image;
                                                    $imagePath = storage_path('app/public/students/' . $imageName);
                                                    $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                                        ? asset('storage/students/' . $imageName)
                                                        : asset('storage/students/student.jpg');
                                                @endphp
                                                <img src="{{ $avatarImage }}" class="student-avatar-modern" alt="Student">
                                                <span class="student-name">{{ ucwords(strtolower($student->first_name)) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-capitalize">{{ ucwords(strtolower($student->middle_name)) }}</td>
                                        <td class="text-capitalize">{{ ucwords(strtolower($student->last_name)) }}</td>
                                        <td class="text-center">
                                            <span class="gender-badge {{ strtolower($student->gender) == 'male' ? 'gender-male' : 'gender-female' }}">
                                                {{ strtoupper(substr($student->gender, 0, 1)) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="stream-badge stream-{{ strtoupper($student->group) }}">
                                                {{ strtoupper($student->group) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <div class="action-icons">
                                                <a href="{{ route('students.modify', ['students' => Hashids::encode($student->id)]) }}"
                                                    class="action-icon edit" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                                <a href="{{ route('manage.student.profile', ['student' => Hashids::encode($student->id)]) }}"
                                                    class="action-icon view" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if (auth()->user()->usertype != 5)
                                                    <form method="POST"
                                                        action="{{ route('Students.destroy', ['student' => Hashids::encode($student->id)]) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button class="action-icon delete"
                                                            onclick="return confirm('Move {{ strtoupper($student->first_name) }} to trash?')"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
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

    <!-- Promote Students Modal -->
    <div class="modal fade modal-modern" id="promoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Promote Students</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Select class to promote students to</p>
                    <form class="needs-validation" novalidate
                        action="{{ route('promote.student.class', ['class' => Hashids::encode($classId->id)]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group-modern">
                            <label class="form-label-modern">Class Name <span class="required">*</span></label>
                            <select name="class_id" id="classSelect" class="form-control-modern" required>
                                <option value="">-- Select Class --</option>
                                @if ($classes->isEmpty())
                                    <option value="" disabled>No more classes found</option>
                                    <option value="0" class="text-success fw-bold">🎓 Graduate Class 🎉</option>
                                @else
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                    <option value="0" class="text-success fw-bold">🎓 Graduate Class 🎉</option>
                                @endif
                            </select>
                        </div>

                        <div id="graduationYearField" class="form-group-modern" style="display: none;">
                            <label class="form-label-modern">Graduation Year <span class="required">*</span></label>
                            <input type="number" name="graduation_year" id="graduation_year"
                                class="form-control-modern" placeholder="e.g 2025"
                                min="{{ date('Y') - 5 }}" max="{{ date('Y') }}">
                            <div class="note-text">Enter graduation year</div>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('Promote this class?')">Upgrade</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade modal-modern" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student - {{ $classId->class_name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate
                        action="{{ route('student.store', ['class' => Hashids::encode($classId->id)]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">First Name <span class="required">*</span></label>
                                    <input type="text" class="form-control-modern" name="fname" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Middle Name <span class="required">*</span></label>
                                    <input type="text" class="form-control-modern" name="middle" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Last Name <span class="required">*</span></label>
                                    <input type="text" class="form-control-modern" name="lname" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Gender <span class="required">*</span></label>
                                    <select class="form-control-modern" name="gender" required>
                                        <option value="">-- Select --</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Date of Birth <span class="required">*</span></label>
                                    <input type="date" class="form-control-modern" name="dob" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Parent/Guardian <span class="required">*</span></label>
                                    <select name="parent" id="parentSelect" class="form-control-modern" required>
                                        <option value="">-- Select --</option>
                                        @foreach ($parents as $parent)
                                            <option value="{{ $parent->id }}">
                                                {{ ucwords($parent->first_name . ' ' . $parent->last_name) }} - {{ $parent->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Stream <span class="required">*</span></label>
                                    <select class="form-control-modern" name="group" required>
                                        <option value="">-- Select --</option>
                                        <option value="a">Stream A</option>
                                        <option value="b">Stream B</option>
                                        <option value="c">Stream C</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Bus Number</label>
                                    <select name="driver" class="form-control-modern">
                                        <option value="">-- Select --</option>
                                        @foreach ($buses as $bus)
                                            <option value="{{ $bus->id }}">Bus No. {{ $bus->bus_no }}</option>
                                        @endforeach
                                    </select>
                                    <div class="note-text">Optional - if using school bus</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Photo</label>
                                    <input type="file" class="form-control-modern" name="image" accept="image/*">
                                    <div class="note-text">Optional - Max 2MB</div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create particles
            createParticles();

            // Initialize Select2
            $('#parentSelect').select2({
                placeholder: "Search Parent...",
                allowClear: true,
                dropdownParent: $('#addStudentModal')
            });

            // Batch form variables
            const batchForm = document.getElementById('batchForm');
            const updateStreamBtn = document.getElementById('updateStreamBtn');
            const selectAllCheckbox = document.getElementById('selectAll');
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');
            const selectedCountDiv = document.getElementById('selectedCount');

            // Update selection state
            function updateSelectionState() {
                const selectedStudents = document.querySelectorAll('.student-checkbox:checked');
                const count = selectedStudents.length;

                selectedCountDiv.innerHTML = `
                    <i class="fas fa-users"></i>
                    <span>${count} student${count !== 1 ? 's' : ''} selected</span>
                `;

                updateStreamBtn.disabled = count === 0;
                selectAllCheckbox.checked = count > 0 && count === studentCheckboxes.length;
                selectAllCheckbox.indeterminate = count > 0 && count < studentCheckboxes.length;
            }

            // Select all
            selectAllCheckbox.addEventListener('change', function() {
                studentCheckboxes.forEach(cb => cb.checked = this.checked);
                updateSelectionState();
            });

            // Individual checkboxes
            studentCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectionState));

            // Batch form submit
            batchForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const selected = document.querySelectorAll('.student-checkbox:checked');
                const newStream = document.querySelector('select[name="new_stream"]').value;

                if (selected.length === 0) {
                    alert('Select at least one student');
                    return;
                }

                if (!newStream) {
                    alert('Select a stream');
                    return;
                }

                if (confirm(`Move ${selected.length} student(s) to Stream ${newStream}?`)) {
                    selected.forEach(cb => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'students[]';
                        input.value = cb.value;
                        batchForm.appendChild(input);
                    });
                    this.submit();
                }
            });

            // Graduation year field
            const classSelect = document.getElementById('classSelect');
            const gradYearField = document.getElementById('graduationYearField');
            const gradYearInput = document.getElementById('graduation_year');

            if (classSelect) {
                classSelect.addEventListener('change', function() {
                    if (this.value === '0') {
                        gradYearField.style.display = 'block';
                        gradYearInput.setAttribute('required', 'required');
                    } else {
                        gradYearField.style.display = 'none';
                        gradYearInput.removeAttribute('required');
                        gradYearInput.value = '';
                    }
                });
            }

            // Form validation
            document.querySelectorAll('.needs-validation').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (classSelect && classSelect.value === '0') {
                        gradYearInput.setAttribute('required', 'required');
                    } else {
                        gradYearInput?.removeAttribute('required');
                    }

                    if (!this.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    this.classList.add('was-validated');
                });
            });

            // Create floating particles
            function createParticles() {
                const container = document.querySelector('.particles');
                if (!container) return;

                for (let i = 0; i < 20; i++) {
                    const p = document.createElement('div');
                    p.className = 'particle';
                    p.style.width = Math.random() * 10 + 3 + 'px';
                    p.style.height = p.style.width;
                    p.style.left = Math.random() * 100 + '%';
                    p.style.top = Math.random() * 100 + '%';
                    p.style.animationDelay = Math.random() * 20 + 's';
                    p.style.animationDuration = Math.random() * 10 + 15 + 's';
                    container.appendChild(p);
                }
            }

            // Initialize
            updateSelectionState();
        });
    </script>
@endsection
