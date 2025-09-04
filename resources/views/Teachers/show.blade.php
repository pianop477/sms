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
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
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
            padding: 5px;
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
            font-size: 16px;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
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

        .flatpickr-input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            background-color: white;
        }

        .flatpickr-input:focus {
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

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: #856404;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-warning-custom:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
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

        .info-alert {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.25) 100%);
            border: 1px solid rgba(23, 162, 184, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            /* padding: 10px; */
            font-weight: 600;
            text-align: center;
        }

        .table-custom tbody td {
            /* padding: 10px; */
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .score-input {
            width: auto;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            transition: all 0.3s;
        }

        .score-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .grade-input {
            width: 60px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            font-weight: bold;
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

        .instruction-text {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.25) 100%);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 193, 7, 0.3);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .card-body {
                padding: 5px;
            }

            .header-title {
                font-size: 20px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .score-input, .grade-input {
                width: 100%;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(78, 84, 200, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0);
            }
        }
    </style>
    <div class="py-4">
        <div class="teacher-form-container">
            <div class="card">
                <div class="card-body">
                    <!-- Header Section -->
                    <div class="row mb-4">
                        <div class="col-md-10">
                            <h4 class="header-title">Update Teacher's Details</h4>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{{ route('teacher.profile', Hashids::encode($teachers->id))}}" class="btn btn-info btn-action float-right">
                                <i class="fas fa-arrow-circle-left me-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <!-- Teacher Update Form -->
                    <form action="{{route('Update.teachers', ['teachers' => Hashids::encode($teachers->id)])}}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <h5 class="form-section-title"><i class="fas fa-user me-2"></i> Personal Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" name="fname" class="form-control" value="{{$teachers->first_name}}" required>
                                    @error('fname')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" name="lname" class="form-control" value="{{$teachers->last_name}}" required>
                                    @error('lname')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select name="gender" class="form-control" required>
                                        <option value="{{$teachers->gender}}" selected>{{ucfirst($teachers->gender)}}</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    @error('gender')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" value="{{$teachers->dob}}" required min="{{\Carbon\Carbon::now()->subYears(60)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(18)->format('Y-m-d')}}">
                                    @error('dob')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="form-section">
                            <h5 class="form-section-title"><i class="fas fa-address-card me-2"></i> Contact Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{$teachers->phone}}" required>
                                    @error('phone')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="{{old('email', $teachers->email)}}">
                                    @error('email')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="street" class="form-label">Street/Village</label>
                                    <input type="text" name="street" class="form-control" value="{{$teachers->address}}" required>
                                    @error('street')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information Section -->
                        <div class="form-section">
                            <h5 class="form-section-title"><i class="fas fa-briefcase me-2"></i> Professional Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="qualification" class="form-label">Qualification</label>
                                    <select name="qualification" class="form-control" required>
                                        <option value="{{$teachers->qualification}}" selected>
                                            @if ($teachers->qualification == 1) Masters
                                            @elseif ($teachers->qualification == 2) Degree
                                            @elseif ($teachers->qualification == 3) Diploma
                                            @else Certificate
                                            @endif
                                        </option>
                                        <option value="1">Masters</option>
                                        <option value="2">Degree</option>
                                        <option value="3">Diploma</option>
                                        <option value="4">Certificate</option>
                                    </select>
                                    @error('qualification')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="joined_at" class="form-label">Member Since</label>
                                    <select name="joined_at" class="form-control" required>
                                        <option value="{{$teachers->joined}}" selected>{{$teachers->joined}}</option>
                                        @for ($year = date('Y'); $year >= 2000; $year--)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                    @error('joined')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Profile Photo Section -->
                        <div class="form-section">
                            <h5 class="form-section-title"><i class="fas fa-camera me-2"></i> Profile Photo</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="image" class="form-label">Passport Size Photo</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <div class="note-text">Maximum 1MB - Recommended size 300x300 pixels</div>
                                    @error('image')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center h-100">
                                        @php
                                            $imageName = $teachers->image;
                                            $imagePath = public_path('assets/img/profile/' . $imageName);

                                            if (!empty($imageName) && file_exists($imagePath)) {
                                                $avatarImage = asset('assets/img/profile/' . $imageName);
                                            } else {
                                                $avatarImage = asset('assets/img/profile/' . ($teachers->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                                            }
                                        @endphp
                                        <img src="{{ $avatarImage }}" alt="Current Photo" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                        <span class="ms-3 text-muted">Current Photo</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-action">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector(".needs-validation");
            const submitButton = form.querySelector('button[type="submit"]');

            form.addEventListener("submit", function(event) {
                // Client-side validation
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...';
            });
        });
    </script>
@endsection
