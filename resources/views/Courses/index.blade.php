@extends('SRTDashboard.frame')

@section('content')
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
            --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.2);
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
            max-width: 1400px;
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
            box-shadow: 0 30px 50px rgba(0, 0, 0, 0.2);
        }

        /* Card Header */
        .card-header-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 30px 35px;
            position: relative;
            overflow: hidden;
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
            height: 4px;
            background: linear-gradient(90deg, var(--warning), var(--success), var(--accent));
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-title {
            color: white;
            margin: 0;
        }

        .header-title h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .class-highlight {
            background: var(--warning);
            color: var(--dark);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 1rem;
            margin-left: 10px;
            display: inline-block;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-modern {
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .btn-modern::before {
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

        .btn-modern:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-info-modern {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--success) 0%, #4cc9f0 100%);
        }

        /* Card Body */
        .card-body-modern {
            padding: 35px;
        }

        /* Table Container */
        .table-container-modern {
            background: white;
            border-radius: 20px;
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            font-weight: 600;
            padding: 18px 15px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
        }

        .table-modern tbody td {
            padding: 18px 15px;
            border-bottom: 1px solid #e9ecef;
            color: #495057;
            vertical-align: middle;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
        }

        .table-modern tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
            box-shadow: var(--shadow-sm);
        }

        /* Subject Info */
        .subject-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .subject-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .subject-details {
            display: flex;
            flex-direction: column;
        }

        .subject-name {
            font-weight: 700;
            color: var(--dark);
        }

        .subject-code {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Teacher Info */
        .teacher-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .teacher-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #36b9cc 0%, #1a8a9e 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Status Badge */
        .badge-modern {
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }

        .badge-success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
        }

        .badge-danger {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            color: white;
        }

        /* Action Icons */
        .action-icons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .action-icon.edit {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
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
            transform: translateY(-3px) rotate(360deg);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Empty State */
        .empty-state-modern {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
            border-radius: 20px;
            border: 2px dashed #ffc107;
        }

        .empty-state-modern i {
            font-size: 70px;
            color: #ffc107;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Modal Modern */
        .modal-modern .modal-content {
            border-radius: 30px;
            border: none;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 25px 30px;
        }

        .modal-modern .modal-body {
            padding: 30px;
        }

        .modal-modern .modal-footer {
            border: none;
            padding: 20px 30px;
            background: #f8f9fa;
        }

        /* Form Controls */
        .form-group-modern {
            margin-bottom: 25px;
        }

        .form-label-modern {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control-modern {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control-modern:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            height: 54px !important;
            border: 2px solid #e9ecef !important;
            border-radius: 15px !important;
            padding: 12px 18px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: #495057 !important;
            font-size: 1rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px !important;
            right: 15px !important;
        }

        .select2-dropdown {
            border: 2px solid #e9ecef !important;
            border-radius: 15px !important;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .select2-results__option {
            padding: 12px 15px !important;
            font-size: 0.95rem;
        }

        .select2-results__option--highlighted {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
        }

        /* Error Message */
        .error-message {
            color: var(--danger);
            font-size: 0.85rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Modal Buttons */
        .btn-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .btn-modal-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-modal-secondary:hover {
            background: #5a6268;
            transform: translateY(-3px);
        }

        .btn-modal-success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-modal-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(28, 200, 138, 0.4);
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70px;
            height: 70px;
            border: 5px solid #f3f3f3;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 9999;
            display: none;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 15px;
            padding: 15px 25px;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 15px;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            z-index: 10000;
            border-left: 5px solid;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-success {
            border-left-color: #28a745;
        }

        .toast-error {
            border-left-color: var(--danger);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-left {
                flex-direction: column;
            }

            .header-title h3 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                margin: 15px auto;
            }

            .table-modern {
                display: block;
                overflow-x: auto;
            }

            .action-icons {
                flex-wrap: wrap;
            }

            .action-group {
                width: 100%;
                justify-content: center;
            }

            .btn-modern {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .card-header-modern {
                padding: 20px;
            }

            .header-title h3 {
                font-size: 1.2rem;
                flex-direction: column;
            }

            .class-highlight {
                margin-left: 0;
                margin-top: 10px;
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

            .subject-name {
                color: #e9ecef;
            }

            .teacher-avatar {
                background: linear-gradient(135deg, #1a8a9e 0%, #0e5f6f 100%);
            }

            .form-control-modern {
                background: #2b3035;
                border-color: #495057;
                color: #e9ecef;
            }

            .modal-modern .modal-content {
                background: rgba(33, 37, 41, 0.95);
            }

            .modal-modern .modal-footer {
                background: #2b3035;
            }

            .form-label-modern {
                color: #e9ecef;
            }

            .select2-container--default .select2-selection--single {
                background: #2b3035 !important;
                border-color: #495057 !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #e9ecef !important;
            }

            .select2-dropdown {
                background: #2b3035 !important;
                border-color: #495057 !important;
            }

            .select2-results__option {
                color: #e9ecef !important;
            }
        }
    </style>

    <div class="animated-bg"></div>
    <div class="particles"></div>
    <div class="loading-spinner" id="loadingSpinner"></div>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Header -->
            <div class="card-header-modern">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="header-title">
                            <h3>
                                <span>Subject List</span>
                                @if(!isset($message) && isset($class))
                                    <span class="class-highlight">{{strtoupper($class->class_name)}}</span>
                                @endif
                            </h3>
                        </div>
                    </div>
                    <div class="action-group">
                        <a href="{{route('courses.index')}}" class="btn-modern btn-info-modern">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </a>
                        @if(!isset($message))
                            <button type="button" class="btn-modern btn-primary-modern" data-bs-toggle="modal" data-bs-target="#assignModal">
                                <i class="fas fa-plus"></i>
                                <span>Assign Subject</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                @if(isset($message))
                    <div class="empty-state-modern">
                        <i class="fas fa-info-circle"></i>
                        <h6 class="mt-4">{{ $message }}</h6>
                        <p class="text-muted">Please check back later or contact administrator</p>
                    </div>
                @elseif ($classCourse->isEmpty())
                    <div class="empty-state-modern">
                        <i class="fas fa-book-open"></i>
                        <h6 class="mt-4">No Subjects Assigned</h6>
                        <p class="text-muted">Click "Assign Subject" to add subjects to this class</p>
                    </div>
                @else
                    <div class="table-container-modern">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Subject Information</th>
                                    <th>Subject Teacher</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classCourse as $course)
                                    <tr>
                                        <td><span class="fw-bold">{{ $loop->iteration }}</span></td>
                                        <td>
                                            <div class="subject-info">
                                                <div class="subject-icon">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                                <div class="subject-details">
                                                    <span class="subject-name text-uppercase">{{ strtoupper($course->course_name) }}</span>
                                                    <span class="subject-code">{{ strtoupper($course->course_code) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="teacher-info">
                                                <div class="teacher-avatar">
                                                    {{ strtoupper(substr($course->first_name, 0, 1)) }}{{ strtoupper(substr($course->last_name, 0, 1)) }}
                                                </div>
                                                <span class="text-capitalize">{{ ucwords(strtolower($course->first_name)) }} {{ ucwords(strtolower($course->last_name)) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($course->status == 1)
                                                <span class="badge-modern badge-success">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @else
                                                <span class="badge-modern badge-danger">
                                                    <i class="fas fa-ban me-1"></i> Blocked
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                                @if ($course->status == 1)
                                                    <a href="{{route('courses.assign', ['id' => Hashids::encode($course->id)])}}"
                                                       class="action-icon edit"
                                                       title="Edit Assignment">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <form action="{{route('block.assigned.course', ['id' => Hashids::encode($course->id)])}}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('⚠️ Are you sure you want to block {{strtoupper($course->course_name)}}?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon warning" title="Block Subject">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{route('unblock.assigned.course', ['id' => Hashids::encode($course->id)])}}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('✅ Unblock {{strtoupper($course->course_name)}}?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon success" title="Unblock Subject">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{route('courses.delete', ['id' => Hashids::encode($course->id)])}}"
                                                   class="action-icon danger"
                                                   title="Delete Permanently"
                                                   onclick="return confirm('⚠️ Delete {{strtoupper($course->course_name)}} permanently? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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

    <!-- Modern Modal -->
    <div class="modal fade modal-modern" id="assignModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Assign Teaching Subject
                    </h5>
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form class="needs-validation" novalidate action="{{route('course.assign')}}" method="POST">
                    @csrf
                    <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-book me-2 text-primary"></i>
                                        Select Subject
                                    </label>
                                    <select name="course_id" id="courseSelect" class="form-control-modern select2" required>
                                        <option value="" disabled selected>-- Search or select subject --</option>
                                        @if ($courses->isEmpty())
                                            <option value="" disabled class="text-danger">No subjects available</option>
                                        @else
                                            @foreach ($courses as $course)
                                                <option value="{{$course->id}}">{{ ucwords(strtolower($course->course_name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('course_id')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{$message}}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-layer-group me-2 text-primary"></i>
                                        Class
                                    </label>
                                    <select name="class_id" id="classSelect" class="form-control-modern" required>
                                        <option value="{{$class->id}}" selected>{{$class->class_name}}</option>
                                    </select>
                                    @error('class_id')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{$message}}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-user-tie me-2 text-primary"></i>
                                        Select Teacher
                                    </label>
                                    <select name="teacher_id" id="teacherSelect" class="form-control-modern select2" required>
                                        <option value="" disabled selected>-- Search or select teacher --</option>
                                        @if ($teachers->isEmpty())
                                            <option value="" disabled class="text-danger">No teachers available</option>
                                        @else
                                            @foreach ($teachers as $teacher)
                                                <option value="{{$teacher->id}}">{{ ucwords(strtolower($teacher->first_name)) }} {{ ucwords(strtolower($teacher->last_name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('teacher_id')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{$message}}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-2 text-info"></i>
                                Assign a subject to this class with a specific teacher. Each subject can have one teacher per class.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </button>
                        <button type="submit" class="btn-modal-success" id="saveButton">
                            <i class="fas fa-save me-2"></i>
                            Assign Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize particles
            createParticles();

            // Initialize Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('#courseSelect').select2({
                    placeholder: "Search subject...",
                    allowClear: true,
                    dropdownParent: $('#assignModal'),
                    width: '100%'
                });

                $('#teacherSelect').select2({
                    placeholder: "Search teacher...",
                    allowClear: true,
                    dropdownParent: $('#assignModal'),
                    width: '100%'
                });
            }

            // Form handling
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");
            const loadingSpinner = document.getElementById('loadingSpinner');

            if (form && submitButton) {
                form.addEventListener("submit", function(event) {
                    event.preventDefault();

                    // Show loading
                    loadingSpinner.style.display = 'block';

                    // Disable button
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Assigning...
                    `;

                    // Validate form
                    if (!form.checkValidity()) {
                        form.classList.add("was-validated");
                        loadingSpinner.style.display = 'none';
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Assign Subject';

                        showToast('Please fill all required fields', 'error');

                        // Scroll to first invalid
                        const firstInvalid = form.querySelector(':invalid');
                        if (firstInvalid) {
                            firstInvalid.scrollIntoView({behavior: 'smooth', block: 'center'});
                        }
                        return;
                    }

                    // Submit after delay
                    setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }

            // Create floating particles
            function createParticles() {
                const particlesContainer = document.querySelector('.particles');
                for (let i = 0; i < 30; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.width = Math.random() * 10 + 3 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 20 + 's';
                    particle.style.animationDuration = Math.random() * 10 + 15 + 's';
                    particlesContainer.appendChild(particle);
                }
            }

            // Toast notification
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast-notification toast-${type}`;
                toast.innerHTML = `
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} fa-2x"></i>
                    <span>${message}</span>
                `;
                document.body.appendChild(toast);

                setTimeout(() => toast.classList.add('show'), 100);
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Input animations
            document.querySelectorAll('.form-control-modern').forEach(input => {
                input.addEventListener('focus', () => {
                    input.style.transform = 'translateY(-2px)';
                });
                input.addEventListener('blur', () => {
                    input.style.transform = 'translateY(0)';
                });
            });

            // Reset button state on page show
            window.addEventListener("pageshow", function() {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Assign Subject';
                }
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }
            });
        });
    </script>
@endsection
