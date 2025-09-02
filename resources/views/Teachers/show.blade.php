@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-action {
            border-radius: 5px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .note-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .teacher-form-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-section {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }

        .form-section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e3e6f0;
        }

        @media (max-width: 768px) {
            .teacher-form-container {
                padding: 15px;
            }

            .form-section {
                padding: 15px;
            }
        }
    </style>
    <div class="container py-4">
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
