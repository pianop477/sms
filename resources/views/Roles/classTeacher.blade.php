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

    .header-title {
        color: white;
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
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
        font-size: 24px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .class-name-highlight {
        background: var(--warning);
        color: var(--dark);
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 1.2rem;
        font-weight: 600;
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
        color: white;
    }

    .btn-info-modern:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }

    .btn-primary-modern {
        background: var(--gradient-1);
        color: white;
    }

    .btn-primary-modern:hover {
        background: linear-gradient(135deg, #5a68e5 0%, #6a42a0 100%);
        color: white;
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
        padding: 18px 20px;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
    }

    .table-modern tbody td {
        padding: 18px 20px;
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

    /* Stream Badge */
    .stream-badge {
        background: linear-gradient(135deg, #36b9cc 0%, #1a8a9e 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-block;
    }

    /* Teacher Info */
    .teacher-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .teacher-avatar {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.2rem;
    }

    .teacher-details {
        display: flex;
        flex-direction: column;
    }

    .teacher-name {
        font-weight: 700;
        color: var(--dark);
        font-size: 1rem;
    }

    .teacher-role {
        font-size: 0.8rem;
        color: #6c757d;
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

    .action-icon.edit {
        background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%);
    }

    .action-icon.delete {
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

    /* Select2 Customization */
    .select2-container--default .select2-selection--single {
        height: 52px !important;
        border: 2px solid #e9ecef !important;
        border-radius: 15px !important;
        padding: 12px 18px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        color: #495057 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px !important;
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
    }

    .select2-results__option--highlighted {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
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

    @keyframes spin {
        to { transform: translate(-50%, -50%) rotate(360deg); }
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

        .header-title {
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            margin: 15px auto;
        }

        .card-body-modern {
            padding: 20px;
        }

        .table-modern {
            display: block;
            overflow-x: auto;
        }

        .action-icons {
            flex-wrap: wrap;
        }

        .action-group {
            justify-content: center;
            width: 100%;
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

        .header-title {
            font-size: 1.3rem;
            flex-direction: column;
        }

        .class-name-highlight {
            font-size: 1rem;
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
                <div class="header-title">
                    <div class="header-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <span>Class Teachers Assignment</span>
                    <span class="class-name-highlight">{{ strtoupper($classes->class_name) }}</span>
                </div>
                <div class="action-group">
                    <a href="{{route('Classes.index', ['class' => Hashids::encode($classes->id)])}}"
                       class="btn-modern btn-info-modern">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>
                    <button type="button" class="btn-modern btn-primary-modern"
                            data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="fas fa-user-plus"></i>
                        <span>Assign Teacher</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body-modern">
            <div class="table-container-modern">
                <table class="table-modern" id="teachersTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Class Name</th>
                            <th>Stream</th>
                            <th>Teacher Information</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($classTeacher->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state-modern">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        <h6 class="mt-4">No Teachers Assigned Yet</h6>
                                        <p class="text-muted">Click "Assign Teacher" to add a class teacher</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($classTeacher as $teacher)
                                <tr>
                                    <td><span class="fw-bold">{{$loop->iteration}}</span></td>
                                    <td class="text-uppercase fw-bold">{{strtoupper($teacher->class_name)}}</td>
                                    <td>
                                        <span class="stream-badge">
                                            <i class="fas fa-users me-1"></i>
                                            Stream {{strtoupper($teacher->group)}}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="teacher-info">
                                            <div class="teacher-details">
                                                <span class="teacher-name">
                                                    {{ucwords(strtolower($teacher->teacher_first_name))}} {{ucwords(strtolower($teacher->teacher_last_name))}}
                                                </span>
                                                <span class="teacher-role">
                                                    <i class="fas fa-badge-check me-1 text-primary"></i>
                                                    Class Teacher
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-icons">
                                            <a href="{{route('roles.edit', ['teacher' => Hashids::encode($teacher->id)])}}"
                                               class="action-icon edit"
                                               title="Edit Assignment">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a href="{{route('roles.destroy', ['teacher' => Hashids::encode($teacher->id)])}}"
                                               class="action-icon delete"
                                               title="Remove Teacher"
                                               onclick="return confirm('⚠️ Are you sure you want to remove {{ strtoupper($teacher->teacher_first_name) }} {{ strtoupper($teacher->teacher_last_name) }} from this class? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
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

<!-- Modern Modal -->
<div class="modal fade modal-modern" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Assign Class Teacher
                </h5>
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="needs-validation" novalidate
                  action="{{route('Class.teacher.assign', ['classes' => Hashids::encode($classes->id)])}}"
                  method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-user-tie me-2 text-primary"></i>
                            Select Teacher
                        </label>
                        <select name="teacher" id="teacherSelect" class="form-control-modern select2" style="width: 100%;" required>
                            <option value="" disabled selected>-- Search or select teacher --</option>
                            @if ($teachers->isEmpty())
                                <option value="" disabled class="text-danger">No teachers available</option>
                            @else
                                @foreach ($teachers as $teacher)
                                    <option value="{{$teacher->id}}" class="text-capitalize">
                                        {{ucwords(strtolower($teacher->teacher_first_name))}} {{ucwords(strtolower($teacher->teacher_last_name))}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('teacher')
                            <div class="text-danger small mt-2">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{$message}}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-layer-group me-2 text-primary"></i>
                            Select Stream
                        </label>
                        <select name="group" id="groupSelect" class="form-control-modern" required>
                            <option value="" disabled selected>-- Choose stream --</option>
                            <option value="A">Stream A</option>
                            <option value="B">Stream B</option>
                            <option value="C">Stream C</option>
                        </select>
                        @error('group')
                            <div class="text-danger small mt-2">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{$message}}
                            </div>
                        @enderror
                    </div>

                    <div class="mt-4 p-3 bg-light rounded">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-2 text-info"></i>
                            Each class can have multiple teachers for different streams. Select the appropriate stream for this teacher.
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
                        Assign Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize Select2
    if (typeof $.fn.select2 !== 'undefined') {
        $('#teacherSelect').select2({
            placeholder: "Search teacher by name...",
            allowClear: true,
            dropdownParent: $('#assignModal'),
            width: '100%'
        });
    }

    // Form validation and loading
    const form = document.querySelector(".needs-validation");
    const submitButton = document.getElementById("saveButton");
    const loadingSpinner = document.getElementById('loadingSpinner');

    if (form && submitButton) {
        form.addEventListener("submit", function(event) {
            event.preventDefault();

            // Show loading spinner
            loadingSpinner.style.display = 'block';

            // Disable button
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Assigning...
            `;

            // Check form validity
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                loadingSpinner.style.display = 'none';
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Assign Teacher';

                // Show error toast
                showToast('Please select both teacher and stream', 'error');
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
            this.style.transform = 'translateY(-2px)';
        });

        input.addEventListener('blur', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endsection
