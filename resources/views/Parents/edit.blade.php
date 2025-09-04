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
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-10">
                <h4 class="header-title">PARENT/GUARDIAN INFORMATION</h4>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{route('Parents.index')}}" class="btn btn-info btn-action float-right">
                    <i class="fas fa-arrow-circle-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Profile Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="profile-header text-center">
                        <div>
                            @if ($parents->image == Null)
                                @if ($parents->gender == 'male')
                                    <img src="{{asset('assets/img/profile/avatar.jpg')}}" alt="Parent Avatar" class="profile-img">
                                @else
                                    <img src="{{asset('assets/img/profile/avatar-female.jpg')}}" alt="Parent Avatar" class="profile-img">
                                @endif
                            @else
                                <img src="{{asset('assets/img/profile/'. $parents->image)}}" alt="Parent Avatar" class="profile-img">
                            @endif
                        </div>
                        <h4 class="profile-name mb-1">{{ucwords(strtolower($parents->first_name. ' '. $parents->last_name))}}</h4>
                        <p class="mb-0 text-white">Parent/Guardian</p>
                    </div>

                    <div class="card-body">
                        <div class="profile-detail">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Gender</span>
                                <span class="text-capitalize fw-bold">{{$parents->gender}}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Status</span>
                                @if ($parents->status === 1)
                                    <span class="badge-status bg-success text-white">Active</span>
                                @else
                                    <span class="badge-status bg-secondary text-white">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Details Card -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-pills flex-column flex-lg-row">
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link active" href="#profile" data-bs-toggle="tab">
                                    <i class="fas fa-user me-1"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#students" data-bs-toggle="tab">
                                    <i class="fas fa-user-graduate me-1"></i> Student List
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#edit" data-bs-toggle="tab">
                                    <i class="fas fa-user-pen me-1"></i> Edit Information
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Profile Tab -->
                            <div class="tab-pane fade show active" id="profile">
                                <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i> Personal Information</h5>
                                <table class="info-table">
                                    <tr>
                                        <th><i class="fas fa-phone me-2"></i> Phone</th>
                                        <td>{{$parents->phone}}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-envelope me-2"></i> Email</th>
                                        <td>{{$parents->email ?? 'Email not provided'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Registration Date</th>
                                        <td>
                                            @if ($parents->created_at == Null)
                                                Unknown
                                            @else
                                                {{\Carbon\Carbon::parse($parents->user_created_at)->format('d-m-Y')}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-location-dot me-2"></i> Street Address</th>
                                        <td class="text-capitalize">{{$parents->address}}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Students Tab -->
                            <div class="tab-pane fade" id="students">
                                <h5 class="mb-4"><i class="fas fa-user-graduate me-2"></i> Students Information</h5>

                                <div class="row">
                                    @foreach ($students as $student)
                                    <div class="col-md-6 mb-3">
                                        <div class="student-card">
                                            <p class="mb-1"><strong>Admission Number:</strong> <span class="text-uppercase">{{$student->admission_number}}</span></p>
                                            <p class="mb-1"><strong>Name:</strong>
                                                <a href="{{route('Students.show', ['student' => Hashids::encode($student->id)])}}" class="text-decoration-none">
                                                    {{$student->first_name}} {{$student->middle_name}} {{$student->last_name}}
                                                </a>
                                            </p>
                                            <p class="mb-1"><strong>Gender:</strong> <span class="text-capitalize">{{$student->gender}}</span></p>
                                            <p class="mb-2"><strong>Class:</strong> <span class="text-uppercase">{{$student->class_name}} - {{$student->class_code}}</span></p>
                                            <form action="{{route('Students.destroy', ['student' => Hashids::encode($student->id)])}}" method="POST">
                                                @csrf
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to block {{strtoupper($student->first_name)}} {{strtoupper($student->middle_name)}} {{strtoupper($student->last_name)}}?')">
                                                    <i class="ti-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Edit Tab -->
                            <div class="tab-pane fade" id="edit">
                                <h5 class="mb-4"><i class="fas fa-edit me-2"></i> Edit Parent/Guardian Information</h5>

                                <form action="{{route('Parents.update', ['parents' => Hashids::encode($parents->id)])}}" method="POST" novalidate class="needs-validation" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <input type="text" name="fname" class="form-control" value="{{$parents->first_name}}" required>
                                            @error('fname')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" name="lname" class="form-control" value="{{$parents->last_name}}" required>
                                            @error('lname')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone" class="form-control" value="{{$parents->phone}}" required>
                                            @error('phone')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" name="email" class="form-control" value="{{$parents->email}}">
                                            @error('email')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Gender</label>
                                            <select name="gender" class="form-control" required>
                                                <option value="{{$parents->gender}}" selected>{{ucfirst($parents->gender)}}</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                            @error('gender')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Address</label>
                                            <input type="text" name="street" class="form-control" value="{{$parents->address}}" required>
                                            @error('street')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Profile Photo</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                            <div class="note-text">Maximum 1MB - Recommended size 300x300 pixels</div>
                                            @error('image')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <button type="submit" id="saveButton" class="btn btn-success btn-action">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...`;

                // Check form validity
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = `<i class="fas fa-save me-2"></i> Save Changes`;
                    return;
                }

                // Delay submission to show loading state
                setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Initialize Bootstrap tabs
            const triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))
            triggerTabList.forEach(function (triggerEl) {
                new bootstrap.Tab(triggerEl)
            });
        });
    </script>
@endsection
