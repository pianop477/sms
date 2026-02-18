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
        height: 100%;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 50px rgba(0, 0, 0, 0.2);
    }

    /* Card Headers */
    .card-header-modern {
        padding: 25px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        flex-wrap: wrap;
        gap: 15px;
    }

    .gradient-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }

    .gradient-info {
        background: linear-gradient(135deg, #36b9cc 0%, #1a8a9e 100%);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .header-title {
        color: white;
        margin: 0;
        font-size: 1.4rem;
        font-weight: 700;
    }

    .header-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
    }

    /* Add Button */
    .btn-add-modern {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .btn-add-modern::before {
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

    .btn-add-modern:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-3px);
        color: white;
    }

    .btn-add-modern:hover::before {
        width: 300px;
        height: 300px;
    }

    /* Card Body */
    .card-body-modern {
        padding: 30px;
    }

    /* Class List */
    .class-list-modern {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .class-item-modern {
        background: white;
        border-radius: 15px;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .class-item-modern:hover {
        transform: translateX(10px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .class-link-modern {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        color: var(--dark);
        text-decoration: none;
        gap: 15px;
    }

    .class-icon {
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

    .class-info {
        flex: 1;
    }

    .class-name {
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 4px;
        font-size: 1.1rem;
    }

    .class-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .class-arrow {
        color: var(--primary);
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .class-item-modern:hover .class-arrow {
        transform: translateX(5px);
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
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
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
        gap: 10px;
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
        display: block;
        font-size: 0.95rem;
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
        .card-header-modern {
            flex-direction: column;
            text-align: center;
        }

        .header-left {
            flex-direction: column;
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

        .btn-add-modern {
            width: 100%;
            justify-content: center;
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

        .class-item-modern {
            background: #2b3035;
            border-color: #495057;
        }

        .class-name {
            color: #e9ecef;
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
    }
</style>

<div class="animated-bg"></div>
<div class="particles"></div>
<div class="loading-spinner" id="loadingSpinner"></div>

<div class="dashboard-container">
    <div class="row">
        <!-- Classes List Section -->
        <div class="col-lg-4 mb-4">
            <div class="modern-card">
                <div class="card-header-modern gradient-primary">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h3 class="header-title">Subjects by Classes</h3>
                            <p class="header-subtitle">
                                <i class="fas fa-info-circle"></i>
                                Select class to view subjects
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body-modern">
                    @if ($classes->isEmpty())
                        <div class="empty-state-modern">
                            <i class="fas fa-layer-group"></i>
                            <h6 class="mt-4">No Classes Found</h6>
                            <p class="text-muted">Please create classes first</p>
                        </div>
                    @else
                        <ul class="class-list-modern">
                            @foreach ($classes as $class)
                            <li class="class-item-modern">
                                <a href="{{route('courses.view.class', ['id' => Hashids::encode($class->id)])}}" class="class-link-modern">
                                    <div class="class-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="class-info">
                                        <div class="class-name text-uppercase">{{strtoupper($class->class_name)}}</div>
                                    </div>
                                    <div class="class-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Subjects List Section -->
        <div class="col-lg-8 mb-4">
            <div class="modern-card">
                <div class="card-header-modern gradient-info">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <h3 class="header-title">All Registered Subjects</h3>
                            <p class="header-subtitle">
                                <i class="fas fa-database"></i>
                                Total: {{$subjects->count()}} subjects
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-add-modern" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                        <i class="fas fa-plus"></i>
                        <span>New Subject</span>
                    </button>
                </div>
                <div class="card-body-modern">
                    @if ($subjects->isEmpty())
                        <div class="empty-state-modern">
                            <i class="fas fa-book-open"></i>
                            <h6 class="mt-4">No Subjects Registered</h6>
                            <p class="text-muted">Click "New Subject" to add your first subject</p>
                        </div>
                    @else
                        <div class="table-container-modern">
                            <table class="table-modern" id="myTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Subject Information</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subjects as $course)
                                        <tr>
                                            <td><span class="fw-bold">{{$loop->iteration}}</span></td>
                                            <td>
                                                <div class="subject-info">
                                                    <div class="subject-icon">
                                                        <i class="fas fa-book"></i>
                                                    </div>
                                                    <div class="subject-details">
                                                        <span class="subject-name text-uppercase">{{strtoupper($course->course_name)}}</span>
                                                        <span class="subject-code">{{strtoupper($course->course_code)}}</span>
                                                    </div>
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
                                                        <form action="{{route('courses.block', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit"
                                                                    class="action-icon warning"
                                                                    title="Block Subject"
                                                                    onclick="return confirm('⚠️ Are you sure you want to block {{strtoupper($course->course_name)}}?')">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{route('courses.unblock', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit"
                                                                    class="action-icon success"
                                                                    title="Unblock Subject"
                                                                    onclick="return confirm('✅ Unblock {{strtoupper($course->course_name)}}?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{route('courses.destroy', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="action-icon danger"
                                                                    title="Delete Permanently"
                                                                    onclick="return confirm('⚠️ Delete {{strtoupper($course->course_name)}} permanently? This action cannot be undone.')">
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
    </div>
</div>

<!-- Modern Modal -->
<div class="modal fade modal-modern" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Register New Subject
                </h5>
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="needs-validation" novalidate action="{{route('course.registration')}}" method="POST">
                @csrf
                <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-book me-2 text-primary"></i>
                                    Subject Name
                                </label>
                                <input type="text"
                                       name="sname"
                                       class="form-control-modern @error('sname') is-invalid @enderror"
                                       placeholder="e.g., Mathematics, English"
                                       value="{{old('name')}}"
                                       required>
                                @error('sname')
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
                                    <i class="fas fa-barcode me-2 text-primary"></i>
                                    Subject Code
                                </label>
                                <input type="text"
                                       name="scode"
                                       class="form-control-modern text-uppercase @error('scode') is-invalid @enderror"
                                       placeholder="e.g., MATH, ENG"
                                       value="{{old('code')}}"
                                       required>
                                @error('scode')
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
                            Subject code should be unique and short. Example: "MATH" for Mathematics, "ENG" for English.
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
                        Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Create particles
    createParticles();

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
                Saving...
            `;

            // Validate form
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                loadingSpinner.style.display = 'none';
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Subject';

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

    // Auto uppercase for subject code
    const codeInput = document.querySelector('input[name="scode"]');
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
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
            submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Subject';
        }
        if (loadingSpinner) {
            loadingSpinner.style.display = 'none';
        }
    });
});
</script>
@endsection
