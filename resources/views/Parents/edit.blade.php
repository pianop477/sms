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

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.3);
        }

        .profile-name {
            font-weight: 700;
            margin-top: 15px;
        }

        .nav-pills .nav-link {
            color: var(--dark-color);
            border-radius: 0;
            padding: 12px 20px;
        }

        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #eaecf4;
        }

        .info-table {
            width: 100%;
        }

        .info-table th {
            width: 30%;
            padding: 12px 15px;
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        .info-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e3e6f0;
        }

        .info-table tr:last-child th,
        .info-table tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
        }

        .student-card {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0.15rem 0.5rem 0 rgba(58, 59, 69, 0.1);
            transition: all 0.3s ease;
        }

        .student-card:hover {
            box-shadow: 0 0.15rem 1rem 0 rgba(58, 59, 69, 0.15);
            transform: translateY(-2px);
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

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .note-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .profile-img {
                width: 100px;
                height: 100px;
            }

            .info-table th,
            .info-table td {
                display: block;
                width: 100%;
            }

            .info-table th {
                background-color: transparent;
                padding-bottom: 5px;
                font-weight: 600;
            }

            .info-table td {
                padding-top: 5px;
                padding-bottom: 15px;
            }

            .nav-pills {
                flex-direction: column;
            }

            .nav-pills .nav-item {
                margin-bottom: 5px;
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
                                    <img src="{{asset('storage/profile/avatar.jpg')}}" alt="Parent Avatar" class="profile-img">
                                @else
                                    <img src="{{asset('storage/profile/avatar-female.jpg')}}" alt="Parent Avatar" class="profile-img">
                                @endif
                            @else
                                <img src="{{asset('storage/profile/'. $parents->image)}}" alt="Parent Avatar" class="profile-img">
                            @endif
                        </div>
                        <h4 class="profile-name mb-1" style="color:gold">{{ucwords(strtolower($parents->first_name. ' '. $parents->last_name))}}</h4>
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
                                <h5 class="mb-4"><i class="fas fa-user-graduate me-2"></i> Students Lists</h5>

                                <div class="row">
                                    @foreach ($students as $student)
                                    <div class="col-md-6 mb-3">
                                        <div class="student-card">
                                            <p class="mb-1"><strong>Admission Number:</strong> <span class="text-uppercase">{{$student->admission_number}}</span></p>
                                            <p class="mb-1"><strong>Name:</strong>
                                                <a href="{{route('manage.student.profile', ['student' => Hashids::encode($student->id)])}}" class="text-decoration-none">
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
                                            <input type="text" name="fname" class="form-control-custom" value="{{$parents->first_name}}" required>
                                            @error('fname')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" name="lname" class="form-control-custom" value="{{$parents->last_name}}" required>
                                            @error('lname')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone" class="form-control-custom" value="{{$parents->phone}}" required>
                                            @error('phone')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" name="email" class="form-control-custom" value="{{$parents->email}}">
                                            @error('email')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Gender</label>
                                            <select name="gender" class="form-control-custom" required>
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
                                            <input type="text" name="street" class="form-control-custom" value="{{$parents->address}}" required>
                                            @error('street')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Profile Photo</label>
                                            <input type="file" name="image" class="form-control-custom" accept="image/*">
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
