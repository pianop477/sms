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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        z-index: 0;
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
            radial-gradient(circle at 70% 30%, rgba(255, 255, 255, 0.15) 0%, transparent 30%),
            radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.15) 0%, transparent 30%);
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
        z-index: 0;
    }

    .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
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
    .edit-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }

    /* Modern Card */
    .modern-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border-radius: 40px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.5);
        animation: slideUp 0.8s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
        display: flex;
        align-items: center;
        gap: 5px;
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

    /* Back Button */
    .btn-back-modern {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-back-modern::before {
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

    .btn-back-modern:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-3px);
        color: white;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-back-modern:hover::before {
        width: 300px;
        height: 300px;
    }

    /* Card Body */
    .card-body-modern {
        padding: 40px;
    }

    /* Info Card */
    .info-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 25px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid rgba(255, 255, 255, 0.7);
        position: relative;
        overflow: hidden;
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
    }

    .info-title {
        color: var(--dark);
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .info-icon {
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

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 2px;
    }

    .info-value {
        font-weight: 700;
        color: var(--dark);
        font-size: 1.1rem;
    }

    .info-badge {
        background: var(--primary);
        color: white;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Form Section */
    .form-section-modern {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 25px;
        padding: 30px;
        border: 1px solid rgba(255, 255, 255, 0.7);
    }

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

    .required-star {
        color: var(--danger);
        font-size: 1.2rem;
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

    .form-control-modern:disabled {
        background: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
    }

    select.form-control-modern {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234361ee' viewBox='0 0 16 16'%3E%3Cpath d='M8 12L2 6h12L8 12z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 18px center;
        padding-right: 45px;
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

    /* Validation Feedback */
    .invalid-feedback-modern {
        color: var(--danger);
        font-size: 0.85rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Save Button */
    .btn-save-modern {
        background: linear-gradient(135deg, var(--success) 0%, #4cc9f0 100%);
        color: white;
        border: none;
        padding: 16px 40px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-md);
    }

    .btn-save-modern::before {
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

    .btn-save-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(76, 201, 240, 0.4);
    }

    .btn-save-modern:hover::before {
        width: 400px;
        height: 400px;
    }

    .btn-save-modern:disabled {
        opacity: 0.7;
        transform: none;
        box-shadow: none;
        cursor: not-allowed;
    }

    /* Loading Spinner */
    .loading-spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 70px;
        height: 70px;
        border: 5px solid rgba(255, 255, 255, 0.3);
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
    @media (max-width: 768px) {
        .edit-container {
            margin: 20px auto;
        }

        .card-body-modern {
            padding: 25px;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .header-title h3 {
            font-size: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .btn-save-modern {
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
        }

        .form-section-modern {
            padding: 20px;
        }

        .info-item {
            flex-direction: column;
            text-align: center;
        }
    }

    /* Dark Mode */
    @media (prefers-color-scheme: dark) {
        .modern-card {
            background: rgba(33, 37, 41, 0.98);
        }

        .info-card {
            background: linear-gradient(135deg, #2b3035 0%, #343a40 100%);
        }

        .info-value {
            color: #e9ecef;
        }

        .info-label {
            color: #adb5bd;
        }

        .form-section-modern {
            background: linear-gradient(135deg, #2b3035 0%, #343a40 100%);
        }

        .form-label-modern {
            color: #e9ecef;
        }

        .form-control-modern {
            background: #2b3035;
            border-color: #495057;
            color: #e9ecef;
        }

        .form-control-modern:disabled {
            background: #343a40;
            color: #adb5bd;
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

        .toast-notification {
            background: #2b3035;
            color: #e9ecef;
        }
    }
</style>

<div class="animated-bg"></div>
<div class="particles"></div>
<div class="loading-spinner" id="loadingSpinner"></div>

<div class="edit-container">
    <div class="modern-card">
        <!-- Header -->
        <div class="card-header-modern">
            <div class="header-content">
                <div class="header-title">
                    <h3>
                        <span class="header-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </span>
                        Edit Class Teacher Assignment
                    </h3>
                </div>
                <a href="{{route('Class.Teachers', ['class' => Hashids::encode($classTeacher->class_id)])}}"
                   class="btn-back-modern">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body-modern">
            <!-- Class Information Card -->
            <div class="info-card">
                <h6 class="info-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Current Class Information
                </h6>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Class Name</div>
                            <div class="info-value text-uppercase">{{$classTeacher->class_name}}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-barcode"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Class Code</div>
                            <div class="info-value text-uppercase">{{$classTeacher->class_code}}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Stream</div>
                            <div class="info-value text-uppercase">{{$classTeacher->group}}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Current Teacher</div>
                            <div class="info-value">
                                {{ucwords(strtolower($classTeacher->first_name))}} {{ucwords(strtolower($classTeacher->last_name))}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <form action="{{route('roles.update.class.teacher', ['classTeacher' => Hashids::encode($classTeacher->id)])}}"
                  class="needs-validation"
                  novalidate
                  method="POST"
                  id="classTeacherForm">
                @csrf
                @method('PUT')

                <div class="form-section-modern">
                    <div class="row">
                        <!-- Class Name (Disabled) -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-chalkboard text-primary"></i>
                                    Class Name
                                </label>
                                <input type="text"
                                       class="form-control-modern text-uppercase"
                                       value="{{$classTeacher->class_name}}"
                                       disabled>
                            </div>
                        </div>

                        <!-- Class Code (Disabled) -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-barcode text-primary"></i>
                                    Class Code
                                </label>
                                <input type="text"
                                       class="form-control-modern text-uppercase"
                                       value="{{$classTeacher->class_code}}"
                                       disabled>
                            </div>
                        </div>

                        <!-- Stream (Disabled) -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-layer-group text-primary"></i>
                                    Stream
                                </label>
                                <input type="text"
                                       class="form-control-modern text-uppercase"
                                       value="{{$classTeacher->group}}"
                                       disabled>
                            </div>
                        </div>

                        <!-- Teacher Selection -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-user-tie text-primary"></i>
                                    Select New Teacher
                                    <span class="required-star">*</span>
                                </label>
                                <select name="teacher" id="teacherSelect" class="form-control-modern select2" required>
                                    <option value="{{$classTeacher->teacher_id}}" selected class="fw-bold">
                                        {{ucwords(strtolower($classTeacher->first_name))}} {{ucwords(strtolower($classTeacher->last_name))}} (Current)
                                    </option>
                                    @if ($teachers->isEmpty())
                                        <option value="" disabled class="text-danger">No other teachers available</option>
                                    @else
                                        @foreach ($teachers as $teacher)
                                            @if($teacher->id != $classTeacher->teacher_id)
                                                <option value="{{$teacher->id}}">
                                                    {{ucwords(strtolower($teacher->first_name))}} {{ucwords(strtolower($teacher->last_name))}}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback-modern">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Please select a teacher
                                </div>
                                @error('teacher')
                                    <div class="invalid-feedback-modern">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Help Text -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>
                            Select a new teacher from the list above. The current teacher will be replaced.
                        </small>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button class="btn-save-modern" type="submit" id="saveButton">
                        <i class="fas fa-save"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize particles
    createParticles();

    // Initialize Select2
    if (typeof $.fn.select2 !== 'undefined') {
        $('#teacherSelect').select2({
            placeholder: "Search or select teacher...",
            allowClear: true,
            dropdownParent: $('body'),
            width: '100%'
        });
    }

    const form = document.getElementById("classTeacherForm");
    const submitButton = document.getElementById("saveButton");
    const loadingSpinner = document.getElementById('loadingSpinner');

    if (form && submitButton) {
        form.addEventListener("submit", function(event) {
            event.preventDefault();

            // Check form validity
            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add('was-validated');

                // Scroll to first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                showToast('Please select a teacher', 'error');
                return;
            }

            // Show loading
            loadingSpinner.style.display = 'block';
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <i class="fas fa-spinner fa-spin me-2"></i>
                Saving Changes...
            `;

            // Submit form after delay
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

    // Reset button state on page show
    window.addEventListener("pageshow", function(event) {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-save"></i><span>Save Changes</span>';
        }
        if (loadingSpinner) {
            loadingSpinner.style.display = 'none';
        }
    });

    // Input animations
    document.querySelectorAll('.form-control-modern').forEach(input => {
        input.addEventListener('focus', () => {
            input.style.transform = 'translateY(-2px)';
        });
        input.addEventListener('blur', () => {
            input.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endsection
