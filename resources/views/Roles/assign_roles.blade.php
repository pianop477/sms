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
            /* padding: 20px; */
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
            width: 100%;
            font-size: 16px;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .form-control-custom:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px;
            height: auto;
            background-color: white;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
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

        .teacher-info {
            background: linear-gradient(135deg, rgba(78, 84, 200, 0.1) 0%, rgba(143, 148, 251, 0.1) 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
        }

        .teacher-name {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .teacher-role {
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
                            <i class="fas fa-user-cog me-2"></i> Assign User Role
                        </h4>
                        <p class="mb-0 text-white"> Update user roles and permissions</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('roles.updateRole')}}" class="btn btn-back float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-users-cog floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="teacher-info">
                    <div class="teacher-name text-uppercase">
                        <i class="fas fa-user-tie me-2"></i> {{ucwords(strtolower($teachers->first_name. ' '. $teachers->last_name))}}
                    </div>
                    <div class="teacher-role text-capitalize">
                        <i class="fas fa-shield-alt me-2"></i> Current Role: {{$teachers->role_name ?? 'No role assigned'}}
                    </div>
                </div>

                <form action="{{route('roles.assign.new', ['user' => Hashids::encode($teachers->id)])}}" class="needs-validation" novalidate method="POST" enctype="multipart/form-data" id="roleForm">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <div class="row">
                            <!-- Teacher Name (Disabled) -->
                            <div class="col-md-6 mb-4">
                                <label for="teacher" class="form-label">
                                    <i class="fas fa-user-graduate text-primary"></i>
                                    Teacher's Name
                                </label>
                                <input type="text" name="teacher" disabled class="form-control-custom text-capitalize" id="teacher"
                                       value="{{$teachers->first_name}} {{$teachers->last_name}} - {{$teachers->role_name ?? ''}}">
                            </div>

                            <!-- Role Selection -->
                            <div class="col-md-6 mb-4">
                                <label for="role" class="form-label">
                                    <i class="fas fa-shield-alt text-primary"></i>
                                    Assign Role <span class="required-star">*</span>
                                </label>
                                <select name="role" id="role" class="form-control-custom text-capitalize" required>
                                    <option value="">-- Select Role --</option>
                                    @if ($roles->isEmpty())
                                        <option value="" class="text-danger" disabled>No Roles Available</option>
                                    @else
                                        @foreach ($roles as $role)
                                            <option value="{{$role->id}}" {{old('role') == $role->id ? 'selected' : ''}}>
                                                {{$role->role_name}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please select a role
                                </div>
                                @error('role')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-success-custom pulse-animation" type="submit" id="saveButton">
                            <i class="fas fa-check-circle me-2"></i> Assign Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("roleForm");
            const submitButton = document.getElementById("saveButton");
            const roleSelect = document.getElementById("role");


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
                        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Assigning Role...`;

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
                    submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i> Assign Role';
                }
            });
        });
    </script>
@endsection
