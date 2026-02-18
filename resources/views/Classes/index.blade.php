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
        position: relative;
    }

    .modern-card::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, var(--primary), var(--secondary), var(--accent));
        border-radius: 32px;
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: -1;
    }

    .modern-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 50px rgba(0, 0, 0, 0.3);
    }

    .modern-card:hover::before {
        opacity: 0.2;
    }

    /* Card Header */
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

    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-icon {
        width: 50px;
        height: 50px;
        background: var(--gradient-1);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        box-shadow: var(--shadow-md);
    }

    .header-title {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--dark);
    }

    /* .header-title span {
        background: var(--gradient-1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    } */

    /* Gradient Headers */
    .gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }

    .gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .header-modern.gradient-success,
    .header-modern.gradient-primary {
        color: white;
    }

    .header-modern.gradient-success .header-title,
    .header-modern.gradient-primary .header-title {
        color: white;
    }

    .header-modern.gradient-success .header-title span,
    .header-modern.gradient-primary .header-title span {
        -webkit-text-fill-color: white;
        background: none;
    }

    /* Add Button */
    .btn-add-modern {
        background: white;
        color: var(--primary);
        border: none;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
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
        background: rgba(67, 97, 238, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-add-modern:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
        color: var(--primary-dark);
    }

    .btn-add-modern:hover::before {
        width: 300px;
        height: 300px;
    }

    /* Card Body */
    .card-body-modern {
        padding: 30px;
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
        font-size: 0.95rem;
    }

    .table-modern tbody tr {
        transition: all 0.3s ease;
    }

    .table-modern tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
        box-shadow: var(--shadow-sm);
    }

    /* Class Name with Icon */
    .class-name-modern {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .class-icon {
        width: 40px;
        height: 40px;
        background: var(--gradient-3);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .class-details h6 {
        margin: 0;
        font-weight: 700;
        color: var(--dark);
    }

    .class-details small {
        color: #6c757d;
        font-size: 0.8rem;
    }

    /* Code Badge */
    .code-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 15px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
        letter-spacing: 1px;
    }

    /* Action Buttons */
    .action-group {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-action-modern {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        position: relative;
        overflow: hidden;
    }

    .btn-action-modern::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.4s, height 0.4s;
    }

    .btn-action-modern:hover::before {
        width: 100px;
        height: 100px;
    }

    .btn-action-modern:hover {
        transform: translateY(-3px) rotate(360deg);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-warning-modern {
        background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%);
    }

    .btn-success-modern {
        background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
    }

    .btn-danger-modern {
        background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
    }

    /* Class Link */
    .class-link-modern {
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px 15px;
        border-radius: 15px;
        transition: all 0.3s ease;
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .class-link-modern:hover {
        transform: translateX(10px);
        background: var(--gradient-1);
        box-shadow: var(--shadow-md);
    }

    .class-link-modern:hover .class-link-text {
        color: white;
    }

    .class-link-modern:hover .link-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .link-icon {
        width: 45px;
        height: 45px;
        background: var(--gradient-1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        transition: all 0.3s ease;
    }

    .class-link-text {
        font-weight: 600;
        color: var(--dark);
        transition: all 0.3s ease;
    }

    /* Empty State */
    .empty-state-modern {
        text-align: center;
        padding: 50px 20px;
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
        border-radius: 20px;
        border: 2px dashed #ffc107;
    }

    .empty-state-modern i {
        font-size: 60px;
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
        margin-bottom: 20px;
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

    .form-control-modern.error {
        border-color: var(--danger);
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
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .btn-modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .btn-modal-save {
        background: linear-gradient(135deg, var(--success) 0%, #4cc9f0 100%);
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

    .btn-modal-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(76, 201, 240, 0.4);
    }

    .btn-modal-cancel {
        background: #6c757d;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-modal-cancel:hover {
        background: #5a6268;
        transform: translateY(-3px);
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

    /* Toast Notifications */
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

        .action-group {
            justify-content: flex-start;
        }

        .btn-add-modern {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .card-body-modern {
            padding: 20px;
        }

        .header-title {
            font-size: 1.3rem;
        }

        .class-link-modern {
            flex-direction: column;
            text-align: center;
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

        .class-link-modern {
            background: #2b3035;
            border-color: #495057;
        }

        .class-link-text {
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
        <div class="col-lg-6 mb-4">
            <div class="modern-card">
                <div class="card-header-modern gradient-success">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h3 class="header-title">
                            <span>Class</span> Management
                        </h3>
                    </div>
                    <button type="button" class="btn-add-modern" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="fas fa-plus"></i>
                        <span>New Class</span>
                    </button>
                </div>
                <div class="card-body-modern">
                    <div class="table-container-modern">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Class Code</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($classes->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <div class="empty-state-modern">
                                                <i class="fas fa-layer-group"></i>
                                                <h6 class="mt-3">No Classes Found</h6>
                                                <p class="text-muted">Click "New Class" to create your first class</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($classes as $class)
                                        <tr>
                                            <td>
                                                <div class="class-name-modern">
                                                    <div class="class-icon">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </div>
                                                    <div class="class-details">
                                                        <h6>{{ strtoupper($class->class_name) }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="code-badge">{{ strtoupper($class->class_code) }}</span>
                                            </td>
                                            <td>
                                                <div class="action-group">
                                                    @if ($class->status == 1)
                                                        <form action="{{ route('Classes.block', ['id' => Hashids::encode($class->id)]) }}"
                                                              method="POST"
                                                              class="d-inline"
                                                              onsubmit="return confirm('⚠️ Are you sure you want to disable this class?')">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit"
                                                                    class="btn-action-modern btn-warning-modern"
                                                                    title="Disable Class">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('Classes.unblock', ['id' => Hashids::encode($class->id)]) }}"
                                                              method="POST"
                                                              class="d-inline"
                                                              onsubmit="return confirm('✅ Enable this class?')">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit"
                                                                    class="btn-action-modern btn-success-modern"
                                                                    title="Enable Class">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('Classes.destroy', ['id' => Hashids::encode($class->id)]) }}"
                                                              method="POST"
                                                              class="d-inline"
                                                              onsubmit="return confirm('⚠️ This action cannot be undone. Delete class permanently?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn-action-modern btn-danger-modern"
                                                                    title="Delete Class">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Teachers Section -->
        <div class="col-lg-6 mb-4">
            <div class="modern-card">
                <div class="card-header-modern gradient-primary">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3 class="header-title">
                            <span>Class</span> Teachers Management
                        </h3>
                    </div>
                </div>
                <div class="card-body-modern">
                    @if ($classes->isEmpty())
                        <div class="empty-state-modern">
                            <i class="fas fa-chalkboard"></i>
                            <h6 class="mt-3">No Classes Available</h6>
                            <p>Create a class first to assign teachers</p>
                        </div>
                    @else
                        <div class="table-container-modern">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Class Name & Code</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classes as $class)
                                        <tr>
                                            <td>
                                                <a href="{{ route('Class.Teachers', ['class' => Hashids::encode($class->id)]) }}"
                                                   class="class-link-modern">
                                                    <div class="link-icon">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <span class="class-link-text">
                                                        {{ strtoupper($class->class_name) }} - {{ strtoupper($class->class_code) }}
                                                    </span>
                                                    <i class="fas fa-arrow-right ms-auto" style="color: var(--primary);"></i>
                                                </a>
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
<div class="modal fade modal-modern" id="addClassModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Create New Class
                </h5>
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="needs-validation" novalidate action="{{ route('Classes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-school me-2 text-primary"></i>
                                    Class Name
                                </label>
                                <input type="text"
                                       name="name"
                                       class="form-control-modern @error('name') error @enderror"
                                       placeholder="e.g., Form One, Standard Seven"
                                       value="{{ old('name') }}"
                                       required>
                                @error('name')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-barcode me-2 text-primary"></i>
                                    Class Code
                                </label>
                                <input type="text"
                                       name="code"
                                       class="form-control-modern @error('code') error @enderror"
                                       placeholder="e.g., F1, STD7"
                                       value="{{ old('code') }}"
                                       required>
                                @error('code')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-2 text-info"></i>
                            Class code should be unique and easy to identify. Example: "F1" for Form One, "STD7" for Standard Seven.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn-modal-save" id="saveButton">
                        <i class="fas fa-save me-2"></i>
                        Save Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Form validation and loading
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector(".needs-validation");
    const submitButton = document.getElementById("saveButton");

    if (form && submitButton) {
        form.addEventListener("submit", function(event) {
            event.preventDefault();

            // Show loading spinner
            document.getElementById('loadingSpinner').style.display = 'block';

            // Disable button
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Saving...
            `;

            // Validate form
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                document.getElementById('loadingSpinner').style.display = 'none';
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Class';

                // Show error toast
                showToast('Please fill all required fields', 'error');
                return;
            }

            // Submit form after delay
            setTimeout(() => {
                form.submit();
            }, 500);
        });
    }

    // Create particles
    function createParticles() {
        const particlesContainer = document.querySelector('.particles');
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.width = Math.random() * 10 + 5 + 'px';
            particle.style.height = particle.style.width;
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = Math.random() * 10 + 20 + 's';
            particlesContainer.appendChild(particle);
        }
    }
    createParticles();

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} fa-2x"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // Input animations
    document.querySelectorAll('.form-control-modern').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});

// Auto uppercase for inputs
document.querySelectorAll('input[type="text"]').forEach(input => {
    input.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endsection
