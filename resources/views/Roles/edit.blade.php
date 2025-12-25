@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --success: #28a745;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 10px;
            position: relative;
            overflow: hidden;
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: rotate(30deg);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 24px;
        }

        .card-body {
            padding: 10px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
        }

        .form-control:focus, .form-select:focus, .select2-container--focus .select2-selection {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .form-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }

        .form-label {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .required-star {
            color: var(--danger);
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: white;
            width: 100%;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
            outline: none;
        }

        .form-control-custom:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        select.form-control-custom {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234e54c8' viewBox='0 0 16 16'%3E%3Cpath d='M8 12L2 6h12L8 12z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .invalid-feedback {
            font-weight: 600;
            color: var(--danger);
            margin-top: 5px;
        }

        .text-danger small {
            font-weight: 600;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-success-custom:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .btn-success-custom:disabled {
            opacity: 0.7;
            transform: none;
            box-shadow: none;
        }

        .class-info {
            background: linear-gradient(135deg, rgba(78, 84, 200, 0.1) 0%, rgba(143, 148, 251, 0.1) 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
        }

        .class-name {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .class-details {
            color: #6c757d;
            font-size: 14px;
        }

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 5px;
            }

            .header-title {
                font-size: 20px;
            }

            .form-section {
                padding: 20px;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-chalkboard-teacher me-2"></i> Edit/Change Class Teacher
                        </h4>
                        <p class="mb-0 text-white">Update class teacher assignment</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('Class.Teachers', ['class' => Hashids::encode($classTeacher->class_id)])}}" class="btn btn-back float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-chalkboard floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="class-info">
                    <div class="class-name text-uppercase">
                        <i class="fas fa-chalkboard me-2"></i> {{$classTeacher->class_name}} - {{$classTeacher->class_code}}
                    </div>
                    <div class="class-details text-capitalize">
                        <i class="fas fa-stream me-2"></i> Stream: {{$classTeacher->group}}
                    </div>
                    <div class="class-details text-capitalize">
                        <i class="fas fa-user-tie me-2"></i> Current Teacher: {{$classTeacher->first_name}} {{$classTeacher->last_name}}
                    </div>
                </div>

                <form action="{{route('roles.update.class.teacher', ['classTeacher' => Hashids::encode($classTeacher->id)])}}" class="needs-validation" novalidate method="POST" enctype="multipart/form-data" id="classTeacherForm">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <div class="row">
                            <!-- Class Name (Disabled) -->
                            <div class="col-md-6 mb-4">
                                <label for="className" class="form-label">
                                    <i class="fas fa-chalkboard text-primary"></i>
                                    Class Name
                                </label>
                                <input type="text" name="name" disabled class="form-control-custom text-uppercase" id="className"
                                       value="{{$classTeacher->class_name}}" required>
                            </div>

                            <!-- Class Code (Disabled) -->
                            <div class="col-md-6 mb-4">
                                <label for="classCode" class="form-label">
                                    <i class="fas fa-code text-primary"></i>
                                    Class Code
                                </label>
                                <input type="text" name="code" disabled class="form-control-custom text-uppercase" id="classCode"
                                       value="{{$classTeacher->class_code}}" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Stream (Disabled) -->
                            <div class="col-md-6 mb-4">
                                <label for="stream" class="form-label">
                                    <i class="fas fa-stream text-primary"></i>
                                    Stream
                                </label>
                                <input type="text" disabled name="group" class="form-control-custom text-uppercase" id="stream"
                                       value="{{$classTeacher->group}}" required>
                            </div>

                            <!-- Teacher Selection -->
                            <div class="col-md-6 mb-4">
                                <label for="teacherSelect" class="form-label">
                                    <i class="fas fa-user-tie text-primary"></i>
                                    Select Teacher <span class="required-star">*</span>
                                </label>
                                <select name="teacher" id="teacherSelect" class="form-control-custom select2" required>
                                    <option value="{{$classTeacher->teacher_id}}" selected>{{ucwords(strtolower($classTeacher->first_name))}} {{ucwords(strtolower($classTeacher->last_name))}}</option>
                                    @if ($teachers->isEmpty())
                                        <option value="" class="text-danger" disabled>No teachers found</option>
                                    @else
                                        @foreach ($teachers as $teacher)
                                            <option value="{{$teacher->id}}">{{ucwords(strtolower($teacher->first_name))}} {{ucwords(strtolower($teacher->last_name))}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please select a teacher
                                </div>
                                @error('teacher')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-success-custom pulse-animation" type="submit" id="saveButton">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
             // Initialize Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('#teacherSelect').select2({
                    placeholder: "Search teacher...",
                    allowClear: true,
                    dropdownParent: $('#assignModal')
                }).on('select2:open', function () {
                    $('.select2-results__option').css('text-transform', 'capitalize');
                });
            } else {
                console.error("Select2 is not loaded!");
            }

            const form = document.getElementById("classTeacherForm");
            const submitButton = document.getElementById("saveButton");
            const teacherSelect = document.getElementById("teacherSelect");

            // Form validation
            (function() {
                'use strict';
                window.addEventListener('load', function() {
                    if (!form) return;

                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

                        if (!form.checkValidity()) {
                            event.stopPropagation();
                            form.classList.add('was-validated');

                            // Scroll to first invalid field
                            const invalidElements = form.querySelectorAll(':invalid');
                            if (invalidElements.length > 0) {
                                invalidElements[0].scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            }
                            return;
                        }

                        // Disable button and show loading state
                        submitButton.disabled = true;
                        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving Changes...`;

                        // Submit the form after a brief delay
                        setTimeout(() => {
                            form.submit();
                        }, 500);
                    });
                }, false);
            })();

            // Reset button state when page is shown (for back button navigation)
            window.addEventListener("pageshow", function(event) {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i> Save Changes';
                }
            });
        });
    </script>
@endsection
