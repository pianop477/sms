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
        .edit-container {
            max-width: 1000px;
            margin: 40px auto;
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
        }

        .header-title p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 5px;
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

        /* Form Section */
        .form-section-modern {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 25px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            position: relative;
            overflow: hidden;
        }

        .form-section-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            color: var(--dark);
            font-weight: 700;
            font-size: 1.2rem;
        }

        .section-title i {
            color: var(--primary);
            font-size: 1.4rem;
        }

        /* Form Groups */
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
            font-size: 0.95rem;
        }

        .required-star {
            color: var(--danger);
            font-size: 1.2rem;
        }

        /* Info Display Fields */
        .info-field-modern {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--dark);
            font-weight: 500;
            box-shadow: var(--shadow-sm);
        }

        .info-field-modern i {
            color: var(--primary);
            font-size: 1.2rem;
            opacity: 0.7;
        }

        /* Form Controls */
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

        /* Info Cards */
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .info-card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .info-card-content {
            flex: 1;
        }

        .info-card-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 2px;
        }

        .info-card-value {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.1rem;
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

            .header-left {
                flex-direction: column;
            }

            .header-title h3 {
                font-size: 1.5rem;
            }

            .btn-save-modern {
                width: 100%;
                justify-content: center;
            }

            .form-section-modern {
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .card-header-modern {
                padding: 20px;
            }

            .header-title h3 {
                font-size: 1.2rem;
            }

            .info-card {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            .modern-card {
                background: rgba(33, 37, 41, 0.95);
            }

            .form-section-modern {
                background: linear-gradient(135deg, #2b3035 0%, #343a40 100%);
            }

            .section-title {
                color: #e9ecef;
            }

            .form-label-modern {
                color: #e9ecef;
            }

            .info-field-modern {
                background: #2b3035;
                border-color: #495057;
                color: #e9ecef;
            }

            .info-card {
                background: #2b3035;
            }

            .info-card-value {
                color: #e9ecef;
            }

            .form-control-modern {
                background: #2b3035;
                border-color: #495057;
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
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="header-title">
                            <h3>Change Subject Teacher</h3>
                        </div>
                    </div>
                    <a href="{{route('courses.view.class', ['id' => Hashids::encode($classCourse->class_id)])}}"
                       class="btn-back-modern">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                <!-- Quick Info Card -->
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Current Subject & Class</div>
                        <div class="info-card-value">
                            {{strtoupper($classCourse->course_name)}} - {{strtoupper($classCourse->class_name)}}
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <form class="needs-validation" novalidate
                      action="{{route('courses.assigned.teacher', ['id' => Hashids::encode($classCourse->id)])}}"
                      method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-section-modern">
                        <div class="section-title">
                            <i class="fas fa-info-circle"></i>
                            <span>Course Information</span>
                        </div>

                        <div class="row">
                            <!-- Course Name (Read-only) -->
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-book text-primary"></i>
                                        Course Name
                                    </label>
                                    <div class="info-field-modern">
                                        <span>{{strtoupper($classCourse->course_name)}}</span>
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <input type="hidden" name="course_id" value="{{$classCourse->course_id}}">
                                </div>
                            </div>

                            <!-- Class (Read-only) -->
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-layer-group text-primary"></i>
                                        Class
                                    </label>
                                    <div class="info-field-modern">
                                        <span>{{strtoupper($classCourse->class_name)}}</span>
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <input type="hidden" name="class_id" value="{{$classCourse->class_id}}">
                                </div>
                            </div>

                            <!-- Teacher Selection -->
                            <div class="col-md-4">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-user-tie text-primary"></i>
                                        Subject Teacher
                                        <span class="required-star">*</span>
                                    </label>
                                    <select name="teacher_id" id="teacherSelect" class="form-control-modern select2" required>
                                        <option value="{{$classCourse->teacherId}}" selected class="fw-bold">
                                            {{ucwords(strtolower($classCourse->first_name))}} {{ucwords(strtolower($classCourse->last_name))}} (Current)
                                        </option>
                                        @if ($teachers->isEmpty())
                                            <option value="" disabled class="text-danger">No teachers available</option>
                                        @else
                                            @foreach ($teachers as $teacher)
                                                @if($teacher->id != $classCourse->teacherId)
                                                    <option value="{{$teacher->id}}">
                                                        {{ucwords(strtolower($teacher->first_name))}} {{ucwords(strtolower($teacher->last_name))}}
                                                    </option>
                                                @endif
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

                        <!-- Help Text -->
                        <div class="mt-4 p-3 rounded" style="background: rgba(67, 97, 238, 0.05); border: 1px dashed var(--primary);">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize particles
            createParticles();

            // Initialize Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('#teacherSelect').select2({
                    placeholder: "Search or select teacher...",
                    allowClear: true,
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

                    // Show loading spinner
                    loadingSpinner.style.display = 'block';

                    // Disable button
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Saving Changes...
                    `;

                    // Validate form
                    if (!form.checkValidity()) {
                        form.classList.add("was-validated");
                        loadingSpinner.style.display = 'none';
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';

                        showToast('Please select a teacher', 'error');

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
            document.querySelectorAll('.form-control-modern, .select2-container').forEach(input => {
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
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
                }
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }
            });
        });
    </script>
@endsection
