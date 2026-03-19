@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-dark: #2e59d9;
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
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }

        /* Profile Header */
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 15px 15px 0 0;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '👤';
            font-size: 8rem;
            position: absolute;
            right: 10px;
            bottom: -20px;
            opacity: 0.1;
            transform: rotate(10deg);
        }

        .profile-img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 0.5rem 2rem 0 rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.05);
        }

        .profile-name {
            font-weight: 700;
            margin-top: 15px;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Navigation Pills */
        .nav-pills {
            background: white;
            padding: 10px;
            border-radius: 10px;
        }

        .nav-pills .nav-link {
            color: var(--dark-color);
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 2px;
        }

        .nav-pills .nav-link i {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #eaecf4;
            transform: translateY(-2px);
        }

        /* Teacher Avatars */
        .teacher-avatar-container,
        .class-teacher-avatar-container {
            position: relative;
            display: inline-block;
            cursor: pointer;
            outline: none;
        }

        .teacher-avatar {
            width: 45px !important;
            height: 45px !important;
            min-width: 45px !important;
            min-height: 45px !important;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .class-teacher-avatar {
            width: 100px !important;
            height: 100px !important;
            min-width: 100px !important;
            min-height: 100px !important;
            object-fit: cover;
            border: 4px solid var(--primary-color);
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .avatar-overlay,
        .avatar-overlay-large {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-overlay {
            width: 45px;
            height: 45px;
        }

        .avatar-overlay-large {
            width: 100px;
            height: 100px;
        }

        .teacher-avatar-container:hover .avatar-overlay,
        .class-teacher-avatar-container:hover .avatar-overlay-large {
            opacity: 1;
        }

        .teacher-avatar-container:hover .teacher-avatar,
        .class-teacher-avatar-container:hover .class-teacher-avatar {
            transform: scale(1.1);
            filter: brightness(0.8);
        }

        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table th {
            width: 35%;
            padding: 12px 15px;
            background-color: #f8f9fc;
            font-weight: 600;
            color: var(--dark-color);
            border-bottom: 1px solid #e3e6f0;
        }

        .info-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e3e6f0;
            color: #333;
        }

        .info-table tr:last-child th,
        .info-table tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge-status {
            padding: 0.4em 0.8em;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }

        .bg-success {
            background-color: var(--success-color);
            color: white;
        }

        .bg-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .bg-secondary {
            background-color: var(--dark-color);
            color: white;
        }

        .bg-info {
            background-color: var(--info-color);
            color: white;
        }

        /* Buttons */
        .btn-action {
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-outline-danger {
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
        }

        .btn-outline-danger:hover {
            background: var(--danger-color);
            color: white;
        }

        .btn-outline-success {
            border: 2px solid var(--success-color);
            color: var(--success-color);
        }

        .btn-outline-success:hover {
            background: var(--success-color);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            color: white;
        }

        /* Teacher Card */
        .teacher-card {
            border: 2px solid #e3e6f0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            transition: all 0.3s ease;
        }

        .teacher-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 5px 20px rgba(78, 115, 223, 0.15);
        }

        /* Package Table */
        .package-table {
            width: 100%;
            border-collapse: collapse;
        }

        .package-table thead {
            background: linear-gradient(135deg, var(--info-color) 0%, #2c9faf 100%);
            color: white;
        }

        .package-table th {
            padding: 15px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .package-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e3e6f0;
        }

        .package-table tbody tr:hover {
            background: #f8f9fc;
        }

        /* Modals */
        .photo-modal img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 15px;
            box-shadow: 0 0.5rem 2rem 0 rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 15px 20px;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-footer {
            border-top: 1px solid #e3e6f0;
            padding: 15px 20px;
        }

        /* Zoom effect for images */
        #modalTeacherImage.zoomed {
            transform: scale(1.5);
            cursor: zoom-out;
            transition: transform 0.3s ease;
        }

        /* Loading Spinner */
        #imageLoading {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            padding: 20px;
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast-notification {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            padding: 15px 20px;
            margin-bottom: 10px;
            min-width: 300px;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toast-notification.success {
            border-left-color: var(--success-color);
        }

        .toast-notification.error {
            border-left-color: var(--danger-color);
        }

        .toast-notification.warning {
            border-left-color: var(--warning-color);
        }

        .toast-notification.info {
            border-left-color: var(--info-color);
        }

        .toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .toast-icon.success {
            background: var(--success-color);
        }

        .toast-icon.error {
            background: var(--danger-color);
        }

        .toast-icon.warning {
            background: var(--warning-color);
        }

        .toast-icon.info {
            background: var(--info-color);
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .toast-close {
            cursor: pointer;
            color: #6c757d;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .profile-img {
                width: 110px;
                height: 110px;
            }

            .profile-name {
                font-size: 1.4rem;
            }
        }

        @media (max-width: 768px) {
            .profile-header {
                padding: 20px 15px;
            }

            .profile-img {
                width: 100px;
                height: 100px;
            }

            .profile-name {
                font-size: 1.2rem;
            }

            .nav-pills {
                flex-direction: column;
            }

            .nav-pills .nav-link {
                margin: 2px 0;
            }

            .info-table th,
            .info-table td {
                display: block;
                width: 100%;
            }

            .info-table th {
                background: none;
                padding-bottom: 5px;
            }

            .info-table td {
                padding-top: 0;
                padding-bottom: 15px;
            }

            .teacher-card {
                padding: 15px;
            }

            .class-teacher-avatar {
                width: 80px !important;
                height: 80px !important;
                min-width: 80px !important;
                min-height: 80px !important;
            }

            .package-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 576px) {
            .profile-header {
                padding: 15px;
            }

            .profile-img {
                width: 80px;
                height: 80px;
            }

            .profile-name {
                font-size: 1rem;
            }

            .btn-action {
                padding: 6px 12px;
                font-size: 0.85rem;
            }

            .teacher-avatar {
                width: 35px !important;
                height: 35px !important;
                min-width: 35px !important;
                min-height: 35px !important;
            }

            .class-teacher-avatar {
                width: 60px !important;
                height: 60px !important;
                min-width: 60px !important;
                min-height: 60px !important;
            }

            .avatar-overlay {
                width: 35px;
                height: 35px;
            }

            .avatar-overlay-large {
                width: 60px;
                height: 60px;
            }
        }
    </style>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="py-4">
        <!-- Header Section -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h4 class="text-primary fw-bold border-bottom pb-2">
                    <i class="fas fa-user-graduate me-2"></i>Student Profile
                </h4>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('home') }}" class="btn btn-secondary btn-action">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column - Profile Card -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="profile-header text-center">
                        @php
                            $imageName = $students->image;
                            $imagePath = storage_path('app/public/students/' . $imageName);
                            $defaultAvatar = asset('storage/students/student.jpg');

                            if (!empty($imageName) && file_exists($imagePath)) {
                                $avatarImage = asset('storage/students/' . $imageName);
                            } else {
                                $avatarImage = $defaultAvatar;
                            }
                        @endphp
                        <img src="{{ $avatarImage }}" class="profile-img" alt="Student Photo" id="profileImage"
                            onclick="openPhotoModal('{{ $avatarImage }}', '{{ ucwords(strtolower($students->first_name . ' ' . $students->last_name)) }}')"
                            style="cursor: pointer;">
                        <h5 class="profile-name mb-1" style="color: gold;">
                            {{ ucwords(strtolower($students->first_name . ' ' . $students->middle_name . ' ' . $students->last_name)) }}
                        </h5>
                        <p class="mb-0 text-white">
                            <i class="fas fa-id-card me-1"></i>
                            Admission: <strong>{{ $students->admission_number }}</strong>
                        </p>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <button type="button" class="btn btn-outline-danger btn-action"
                                onclick="openPhotoModal('{{ $avatarImage }}', '{{ ucwords(strtolower($students->first_name . ' ' . $students->last_name)) }}')">
                                <i class="fas fa-image me-1"></i> View
                            </button>
                            <a href="{{ route('student.profile.picture', ['student' => Hashids::encode($students->id)]) }}"
                                class="btn btn-outline-success btn-action">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>

                        <div class="profile-detail">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted"><i class="fas fa-venus-mars me-2"></i>Gender</span>
                                <span class="text-capitalize fw-bold">{{ $students->gender }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted"><i class="fas fa-users me-2"></i>Stream</span>
                                <span class="text-uppercase fw-bold">{{ $students->group }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted"><i class="fas fa-circle me-2"></i>Status</span>
                                @if ($students->status === 1)
                                    <span class="badge-status bg-success">Active</span>
                                @else
                                    <span class="badge-status bg-secondary">Inactive</span>
                                @endif
                            </div>
                            @if ($students->status === 0)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="fas fa-info-circle me-2"></i>Reason</span>
                                    @if ($students->graduated_at == null)
                                        <span class="badge-status bg-danger">Account Blocked</span>
                                    @else
                                        <span class="badge-status bg-success">Graduated</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <hr class="my-3">

                        <a href="{{ route('parent.edit.student', ['students' => Hashids::encode($students->id)]) }}"
                            class="btn btn-primary btn-action w-100">
                            <i class="fas fa-edit me-2"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column - Details Card -->
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-pills p-2" id="profileTabs" role="tablist">
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link active" id="student-tab" data-bs-toggle="pill"
                                    data-bs-target="#student" type="button" role="tab">
                                    <i class="fas fa-user-graduate me-1"></i> Profile
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link" id="parents-tab" data-bs-toggle="pill" data-bs-target="#parents"
                                    type="button" role="tab">
                                    <i class="fas fa-user-shield me-1"></i> Parent
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link" id="subjects-tab" data-bs-toggle="pill" data-bs-target="#subjects"
                                    type="button" role="tab">
                                    <i class="fas fa-book me-1"></i> Subjects
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link" id="attendance-tab" data-bs-toggle="pill"
                                    data-bs-target="#attendance" type="button" role="tab">
                                    <i class="fas fa-calendar-check me-1"></i> Attendance
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link" id="results-tab" data-bs-toggle="pill"
                                    data-bs-target="#results" type="button" role="tab">
                                    <i class="fas fa-chart-bar me-1"></i> Results
                                </button>
                            </li>
                            @if ($students->transport_id != null)
                                <li class="nav-item flex-fill text-center" role="presentation">
                                    <button class="nav-link" id="transport-tab" data-bs-toggle="pill"
                                        data-bs-target="#transport" type="button" role="tab">
                                        <i class="fas fa-bus me-1"></i> Transport
                                    </button>
                                </li>
                            @endif
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link" id="package-tab" data-bs-toggle="pill"
                                    data-bs-target="#package" type="button" role="tab">
                                    <i class="fas fa-layer-group me-1"></i> E-Library
                                </button>
                            </li>
                            @if (Auth::user()->school->package === 'premium')
                                <li class="nav-item flex-fill text-center" role="presentation">
                                    <button class="nav-link" id="payment-tab" data-bs-toggle="pill"
                                        data-bs-target="#payment" type="button" role="tab">
                                        <i class="fas fa-credit-card me-1"></i> Payment
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content" id="profileTabsContent">
                            <!-- Student Information Tab -->
                            <div class="tab-pane fade show active" id="student" role="tabpanel">
                                <h5 class="mb-4">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>
                                    Student Details
                                </h5>
                                <table class="info-table">
                                    <tr>
                                        <th><i class="fas fa-school me-2"></i>Class</th>
                                        <td class="text-uppercase fw-bold">{{ $students->class_name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-calendar me-2"></i>Date of Birth</th>
                                        <td>{{ \Carbon\Carbon::parse($students->dob)->format('d F, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-calendar-plus me-2"></i>Registered on</th>
                                        <td>{{ \Carbon\Carbon::parse($students->created_at)->format('d F, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-map-marker-alt me-2"></i>Street Address</th>
                                        <td class="text-capitalize">{{ ucwords(strtolower($students->address)) }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-bus me-2"></i>School Bus</th>
                                        <td>
                                            @if ($students->transport_id == null)
                                                <span class="text-muted">Not Assigned</span>
                                            @else
                                                <span class="badge-status bg-success">Assigned</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Parents Information Tab -->
                            <div class="tab-pane fade" id="parents" role="tabpanel">
                                <h5 class="mb-4">
                                    <i class="fas fa-users me-2 text-primary"></i>
                                    Parent/Guardian Details
                                </h5>
                                <table class="info-table">
                                    <tr>
                                        <th colspan="2" class="bg-light">
                                            <i class="fas fa-user me-2"></i>
                                            {{ strtolower($students->parent_gender) == 'male' ? "Father's" : "Mother's" }}
                                            Information
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Full Name</th>
                                        <td>{{ ucwords(strtolower($students->parent_first_name . ' ' . $students->parent_last_name)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-phone-alt me-2"></i> Phone</th>
                                        <td>
                                            <a href="tel:{{ $students->phone }}" class="text-decoration-none">
                                                <i class="fas fa-phone-alt me-1"></i>
                                                {{ $students->phone }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-envelope me-2"></i> Email</th>
                                        <td>
                                            @if ($students->email == null)
                                                <span class="text-muted">Not provided</span>
                                            @else
                                                <a href="mailto:{{ $students->email }}" class="text-decoration-none">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    {{ $students->email }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-map-marker-alt me-2"></i> Address</th>
                                        <td class="text-capitalize">{{ ucwords(strtolower($students->address)) }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-calendar-plus me-2"></i> Registered on</th>
                                        <td>{{ \Carbon\Carbon::parse($students->parent_created_at)->format('d F, Y') }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Subjects Tab -->
                            <div class="tab-pane fade" id="subjects" role="tabpanel">
                                <h5 class="mb-4">
                                    <i class="fas fa-book me-2 text-primary"></i>
                                    Subjects Enrollment
                                </h5>

                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Subject</th>
                                                <th>Teacher</th>
                                                <th>Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($class_course as $course)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="text-capitalize fw-bold">
                                                        {{ ucwords(strtolower($course->course_name)) }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @php
                                                                $teacherImage = $course->image ?? '';
                                                                $teacherImagePath = storage_path(
                                                                    'app/public/profile/' . $teacherImage,
                                                                );
                                                                $teacherName = ucwords(
                                                                    strtolower(
                                                                        $course->first_name . ' ' . $course->last_name,
                                                                    ),
                                                                );

                                                                if (
                                                                    !empty($teacherImage) &&
                                                                    file_exists($teacherImagePath)
                                                                ) {
                                                                    $teacherAvatar = asset(
                                                                        'storage/profile/' . $teacherImage,
                                                                    );
                                                                } else {
                                                                    $teacherAvatar = asset(
                                                                        'storage/profile/' .
                                                                            (strtolower($course->gender) == 'male'
                                                                                ? 'avatar.jpg'
                                                                                : 'avatar-female.jpg'),
                                                                    );
                                                                }
                                                            @endphp

                                                            <!-- FIX: Onclick function call -->
                                                            <div class="teacher-avatar-container"
                                                                onclick="openTeacherModal('{{ $teacherAvatar }}', '{{ $teacherName }}', '{{ $course->course_name }}')"
                                                                data-bs-toggle="tooltip"
                                                                title="Click to view teacher photo"
                                                                style="cursor: pointer;">
                                                                <img src="{{ $teacherAvatar }}"
                                                                    alt="{{ $teacherName }}" class="teacher-avatar me-3"
                                                                    loading="lazy">
                                                                <div class="avatar-overlay">
                                                                    <i class="fas fa-search-plus"></i>
                                                                </div>
                                                            </div>
                                                            <span>{{ $teacherName }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="tel:{{ $course->teacher_phone }}"
                                                            class="text-decoration-none">
                                                            <i class="fas fa-phone-alt me-1"></i>
                                                            {{ $course->teacher_phone }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-danger py-4">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        No subjects assigned yet!
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <hr class="my-4">

                                <h6 class="mb-3">
                                    <i class="fas fa-chalkboard-teacher me-2 text-primary"></i>
                                    Class Teacher
                                </h6>
                                @forelse ($myClassTeacher as $classTeacher)
                                    <div class="teacher-card">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 text-center">
                                                @php
                                                    $classTeacherImage = $classTeacher->image ?? '';
                                                    $classTeacherImagePath = storage_path(
                                                        'app/public/profile/' . $classTeacherImage,
                                                    );
                                                    $classTeacherName = strtoupper(
                                                        $classTeacher->first_name . ' ' . $classTeacher->last_name,
                                                    );

                                                    if (
                                                        !empty($classTeacherImage) &&
                                                        file_exists($classTeacherImagePath)
                                                    ) {
                                                        $classTeacherAvatar = asset(
                                                            'storage/profile/' . $classTeacherImage,
                                                        );
                                                    } else {
                                                        $classTeacherAvatar = asset(
                                                            'storage/profile/' .
                                                                (strtolower($classTeacher->gender) == 'male'
                                                                    ? 'avatar.jpg'
                                                                    : 'avatar-female.jpg'),
                                                        );
                                                    }
                                                @endphp

                                                <!-- FIX: Onclick function call -->
                                                <div class="class-teacher-avatar-container mx-auto"
                                                    onclick="openTeacherModal('{{ $classTeacherAvatar }}', '{{ $classTeacherName }}', 'Class Teacher')"
                                                    data-bs-toggle="tooltip" title="Click to view teacher photo"
                                                    style="cursor: pointer;">
                                                    <img src="{{ $classTeacherAvatar }}" alt="{{ $classTeacherName }}"
                                                        class="class-teacher-avatar" loading="lazy">
                                                    <div class="avatar-overlay-large">
                                                        <i class="fas fa-search-plus fa-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-2">
                                                            <strong><i class="fas fa-user me-2"></i>Name:</strong>
                                                            {{ $classTeacherName }}
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong><i class="fas fa-venus-mars me-2"></i>Gender:</strong>
                                                            {{ strtoupper($classTeacher->gender) }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-2">
                                                            <strong><i class="fas fa-phone-alt me-2"></i>Phone:</strong>
                                                            <a href="tel:{{ $classTeacher->phone }}"
                                                                class="text-decoration-none">
                                                                {{ $classTeacher->phone }}
                                                            </a>
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong><i class="fas fa-school me-2"></i>Class:</strong>
                                                            {{ strtoupper($classTeacher->class_name) }} - Stream
                                                            {{ strtoupper($classTeacher->group) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No class teacher assigned!
                                    </div>
                                @endforelse
                            </div>

                            <!-- Attendance Tab -->
                            <div class="tab-pane fade" id="attendance" role="tabpanel">
                                <h5 class="mb-4">
                                    <i class="fas fa-calendar-check me-2 text-primary"></i>
                                    Attendance Report
                                </h5>
                                <div class="text-center py-5">
                                    <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                                    <h5>View attendance records for {{ ucwords(strtolower($students->first_name)) }}</h5>
                                    <p class="text-muted mb-4">Track daily attendance and generate reports</p>
                                    <a href="{{ route('attendance.byYear', ['student' => Hashids::encode($students->id)]) }}"
                                        class="btn btn-primary btn-action">
                                        <i class="fas fa-chart-line me-2"></i> View Attendance Reports
                                    </a>
                                </div>
                            </div>

                            <!-- Results Tab -->
                            <div class="tab-pane fade" id="results" role="tabpanel">
                                <h5 class="mb-4">
                                    <i class="fas fa-chart-bar me-2 text-primary"></i>
                                    Academic Results
                                </h5>
                                <div class="text-center py-5">
                                    <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                                    <h5>View results for {{ ucwords(strtolower($students->first_name)) }}</h5>
                                    <p class="text-muted mb-4">Check exam results and academic progress</p>
                                    <a href="{{ route('results.index', ['student' => Hashids::encode($students->id)]) }}"
                                        class="btn btn-primary btn-action">
                                        <i class="fas fa-file-alt me-2"></i> View Results Reports
                                    </a>
                                </div>
                            </div>

                            <!-- Transport Tab -->
                            @if ($students->transport_id != null)
                                <div class="tab-pane fade" id="transport" role="tabpanel">
                                    <h5 class="mb-4">
                                        <i class="fas fa-bus me-2 text-primary"></i>
                                        Transport Information
                                    </h5>
                                    <table class="info-table">
                                        <tr>
                                            <th><i class="fas fa-user me-2"></i>Driver Name</th>
                                            <td class="text-capitalize">{{ ucwords(strtolower($students->driver_name)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone-alt me-2"></i> Phone</th>
                                            <td>
                                                <a href="tel:{{ $students->driver_phone }}" class="text-decoration-none">
                                                    <i class="fas fa-phone me-1"></i>
                                                    {{ $students->driver_phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-venus-mars me-2"></i>Gender</th>
                                            <td class="text-capitalize">
                                                {{ ucwords(strtolower($students->driver_gender)) }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-bus me-2"></i>Bus Number</th>
                                            <td class="text-uppercase">{{ $students->bus_no }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-route me-2"></i>Route</th>
                                            <td class="text-capitalize">{{ ucwords(strtolower($students->routine)) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @endif

                            <!-- Packages Tab -->
                            <div class="tab-pane fade" id="package" role="tabpanel">
                                <h5 class="mb-4">
                                    <i class="fas fa-layer-group me-2 text-primary"></i>
                                    Online Library & Academic Materials
                                </h5>

                                @if ($packages->isEmpty())
                                    <div class="alert alert-info text-center py-4">
                                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                                        <h5>No Resources Available</h5>
                                        <p class="mb-0">Learning materials will appear here when uploaded.</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover package-table">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th>Term</th>
                                                    <th>Status</th>
                                                    <th>Released</th>
                                                    <th>Expires</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($packages as $item)
                                                    <tr>
                                                        <td class="fw-bold">{{ ucwords(strtolower($item->title)) }}</td>
                                                        <td>{{ ucwords(strtolower($item->description)) ?? 'N/A' }}</td>
                                                        <td>Term {{ $item->term }}</td>
                                                        <td>
                                                            @if ($item->is_active)
                                                                <span class="badge-status bg-success">Active</span>
                                                            @else
                                                                <span class="badge-status bg-secondary">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->release_date ? \Carbon\Carbon::parse($item->release_date)->format('d/m/Y') : 'N/A' }}
                                                        </td>
                                                        <td>{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') : 'N/A' }}
                                                        </td>
                                                        <td>
                                                            @if ($item->is_active)
                                                                <a href="{{ route('student.holiday.package', ['id' => Hashids::encode($item->id), 'preview' => true]) }}"
                                                                    target="_blank" class="btn btn-sm btn-success"
                                                                    onclick="return confirm('Download this package?')">
                                                                    <i class="fas fa-download me-1"></i> Download
                                                                </a>
                                                            @else
                                                                <button class="btn btn-sm btn-danger" disabled>
                                                                    <i class="fas fa-lock me-1"></i> Locked
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <!-- Payment Tab -->
                            <div class="tab-pane fade" id="payment" role="tabpanel">
                                <h5 class="mb-4">
                                    <i class="fas fa-credit-card me-2 text-primary"></i>
                                    Payment Information
                                </h5>
                                <div class="text-center py-5">
                                    <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                    <h5>Payment History</h5>
                                    <p class="text-muted mb-4">View all payments and transaction records</p>
                                    <a href="{{ route('student.payment.history', ['studentId' => Hashids::encode($students->id)]) }}"
                                        class="btn btn-primary btn-action">
                                        <i class="fas fa-history me-2"></i> View Payment History
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Photo Modal -->
    <div class="modal fade" id="studentPhotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-graduate me-2"></i>
                        <span id="studentPhotoName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img id="studentPhotoFull" src="" alt="Student Photo" class="photo-modal img-fluid">
                </div>
                <div class="modal-footer">
                    <!-- FIX: Tumia button ya kawaida, si btn-close -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Image Modal -->
    <div class="modal fade" id="teacherImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        <span id="modalTeacherName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <span class="badge bg-info text-white" id="modalTeacherRole"></span>
                    </div>
                    <div class="image-container position-relative">
                        <!-- Loading Spinner -->
                        <div id="imageLoading" class="position-absolute top-50 start-50 translate-middle"
                            style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div id="imageError" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Failed to load image
                        </div>

                        <!-- Image -->
                        <img id="modalTeacherImage" src="" alt="Teacher Photo"
                            class="img-fluid rounded shadow-lg"
                            style="max-height: 70vh; max-width: 100%; object-fit: contain; cursor: pointer; transition: transform 0.3s ease;"
                            onclick="zoomCurrentImage()">
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- FIX: Tumia button ya kawaida, si btn-close -->
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-info" onclick="zoomCurrentImage()" id="zoomButton">
                        <i class="fas fa-search-plus me-2"></i>Zoom
                    </button>
                    <button type="button" class="btn btn-primary" onclick="openFullScreenModal()">
                        <i class="fas fa-expand me-2"></i>Full Screen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Screen Image Modal -->
    <div class="modal fade" id="fullScreenImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center">
                    <img id="fullScreenImage" src="" alt="Full Screen" class="img-fluid"
                        style="max-height: 90vh;">
                </div>
                <div class="modal-footer border-0 bg-dark">
                    <!-- FIX: Ongeza close button chini pia -->
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize components
            initializeComponents();

            // Setup image modals
            setupImageModals();

            // Show any session messages as toasts
            showSessionMessages();

            // Setup manual modal close handlers
            setupManualModalClose();
        });

        // ============ MANUAL MODAL CLOSE HANDLERS (HAITEGEMEI BOOTSTRAP) ============
        function setupManualModalClose() {
            // Handle all close buttons
            document.querySelectorAll(
                '[data-bs-dismiss="modal"], .modal .btn-close, .modal .btn-secondary, .modal .btn-outline-secondary, .modal .btn-outline-light'
                ).forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const modal = this.closest('.modal');
                    if (modal) {
                        closeModal(modal);
                    }
                });
            });

            // Handle backdrop click
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModal(this);
                    }
                });
            });

            // Handle ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const openModal = document.querySelector('.modal.show');
                    if (openModal) {
                        closeModal(openModal);
                    }
                }
            });
        }

        // Function to close modal manually
        function closeModal(modal) {
            if (!modal) return;

            // Remove show class
            modal.classList.remove('show');

            // Set aria-hidden
            modal.setAttribute('aria-hidden', 'true');

            // Hide modal
            modal.style.display = 'none';

            // Remove modal-open class from body
            document.body.classList.remove('modal-open');

            // Remove padding-right from body (Bootstrap adds this)
            document.body.style.removeProperty('padding-right');

            // Remove backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }

            // Return focus to the element that opened the modal
            setTimeout(() => {
                const opener = document.querySelector(
                    '[onclick*="openTeacherModal"], [onclick*="openPhotoModal"], #profileImage');
                if (opener) {
                    opener.focus();
                }
            }, 100);
        }

        // Function to open modal manually
        function openModal(modal) {
            if (!modal) return;

            // Add show class
            modal.classList.add('show');

            // Remove aria-hidden
            modal.setAttribute('aria-hidden', 'false');

            // Show modal
            modal.style.display = 'block';

            // Add modal-open class to body
            document.body.classList.add('modal-open');

            // Create backdrop if not exists
            if (!document.querySelector('.modal-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }

            // Focus on modal title or close button
            setTimeout(() => {
                const title = modal.querySelector('.modal-title');
                const closeBtn = modal.querySelector('.btn-close, [data-bs-dismiss="modal"]');
                if (title) {
                    title.setAttribute('tabindex', '-1');
                    title.focus();
                } else if (closeBtn) {
                    closeBtn.focus();
                }
            }, 100);
        }

        // ============ TEACHER IMAGE MODAL FUNCTIONS ============
        let currentImageUrl = '';
        let isZoomed = false;

        window.openTeacherModal = function(imageUrl, teacherName, teacherRole) {
            console.log('Opening teacher modal:', teacherName);

            const modal = document.getElementById('teacherImageModal');
            if (!modal) {
                console.error('Teacher modal element not found!');
                return;
            }

            currentImageUrl = imageUrl;

            // Set modal content
            const nameElement = document.getElementById('modalTeacherName');
            const roleElement = document.getElementById('modalTeacherRole');
            if (nameElement) nameElement.textContent = teacherName;
            if (roleElement) roleElement.textContent = teacherRole;

            // Show loading
            const loadingEl = document.getElementById('imageLoading');
            const errorEl = document.getElementById('imageError');
            const imageEl = document.getElementById('modalTeacherImage');

            if (loadingEl) loadingEl.style.display = 'block';
            if (errorEl) errorEl.style.display = 'none';
            if (imageEl) {
                imageEl.style.display = 'none';
                imageEl.src = ''; // Clear previous image
            }

            // Load image
            const img = new Image();
            img.onload = function() {
                if (imageEl) {
                    imageEl.src = imageUrl;
                    imageEl.style.display = 'block';
                }
                if (loadingEl) loadingEl.style.display = 'none';
                resetZoom();
            };
            img.onerror = function() {
                if (loadingEl) loadingEl.style.display = 'none';
                if (errorEl) errorEl.style.display = 'block';
            };
            img.src = imageUrl;

            // Open modal
            openModal(modal);
        }

        // ============ STUDENT PHOTO MODAL ============
        window.openPhotoModal = function(imageUrl, studentName) {
            console.log('Opening student photo modal:', studentName);

            const modal = document.getElementById('studentPhotoModal');
            if (!modal) {
                console.error('Student photo modal element not found!');
                return;
            }

            // Set image and name
            const imageEl = document.getElementById('studentPhotoFull');
            const nameEl = document.getElementById('studentPhotoName');

            if (imageEl) imageEl.src = imageUrl;
            if (nameEl) nameEl.textContent = studentName;

            // Open modal
            openModal(modal);
        }

        // ============ ZOOM FUNCTIONS ============
        window.zoomCurrentImage = function() {
            const image = document.getElementById('modalTeacherImage');
            const zoomButton = document.getElementById('zoomButton');

            if (!image) return;

            if (!isZoomed) {
                image.style.transform = 'scale(1.5)';
                image.style.cursor = 'zoom-out';
                if (zoomButton) {
                    zoomButton.innerHTML = '<i class="fas fa-search-minus me-2"></i>Reset';
                }
                isZoomed = true;
            } else {
                resetZoom();
            }
        }

        function resetZoom() {
            const image = document.getElementById('modalTeacherImage');
            const zoomButton = document.getElementById('zoomButton');

            if (image) {
                image.style.transform = 'scale(1)';
                image.style.cursor = 'zoom-in';
            }
            if (zoomButton) {
                zoomButton.innerHTML = '<i class="fas fa-search-plus me-2"></i>Zoom';
            }
            isZoomed = false;
        }

        // ============ FULL SCREEN FUNCTION ============
        window.openFullScreenModal = function() {
            if (currentImageUrl) {
                const fullScreenImage = document.getElementById('fullScreenImage');
                if (fullScreenImage) {
                    fullScreenImage.src = currentImageUrl;
                }

                // Close teacher modal
                const teacherModal = document.getElementById('teacherImageModal');
                if (teacherModal && teacherModal.classList.contains('show')) {
                    closeModal(teacherModal);
                }

                // Open full screen modal
                setTimeout(() => {
                    const fullScreenModal = document.getElementById('fullScreenImageModal');
                    if (fullScreenModal) {
                        openModal(fullScreenModal);
                    }
                }, 300);
            }
        }

        // ============ CLOSE ALL MODALS ============
        window.closeAllModals = function() {
            document.querySelectorAll('.modal.show').forEach(modal => {
                closeModal(modal);
            });
        }

        // ============ INITIALIZE COMPONENTS ============
        function initializeComponents() {
            // Initialize tooltips (if using Bootstrap tooltips)
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Initialize tabs (if using Bootstrap tabs)
            if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                var triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="pill"]'));
                triggerTabList.forEach(function(triggerEl) {
                    new bootstrap.Tab(triggerEl);
                });
            }

            // Activate tab from URL hash if present
            if (window.location.hash) {
                const tab = document.querySelector(`[data-bs-target="${window.location.hash}"]`);
                if (tab && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    new bootstrap.Tab(tab).show();
                }
            }
        }

        // ============ SETUP IMAGE MODALS ACCESSIBILITY ============
        function setupImageModals() {
            // Make teacher images keyboard accessible
            document.querySelectorAll('.teacher-avatar-container, .class-teacher-avatar-container').forEach(container => {
                container.setAttribute('tabindex', '0');
                container.setAttribute('role', 'button');
                container.setAttribute('aria-label', 'Click to view teacher photo');

                container.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        // Find the image and get details
                        const img = this.querySelector('img');
                        if (!img) return;

                        // Try to find teacher name
                        let name = '';
                        let role = '';

                        if (this.closest('td')) {
                            // Subject teacher
                            const nameSpan = this.closest('.d-flex')?.querySelector('span');
                            name = nameSpan ? nameSpan.textContent.trim() : 'Teacher';
                            role = 'Subject Teacher';
                        } else if (this.closest('.teacher-card')) {
                            // Class teacher
                            const strongEl = this.closest('.teacher-card')?.querySelector('strong');
                            name = strongEl ? strongEl.nextSibling?.textContent?.trim() || 'Class Teacher' :
                                'Class Teacher';
                            role = 'Class Teacher';
                        }

                        openTeacherModal(img.src, name, role);
                    }
                });
            });

            // Make profile image accessible
            const profileImage = document.getElementById('profileImage');
            if (profileImage) {
                profileImage.setAttribute('tabindex', '0');
                profileImage.setAttribute('role', 'button');
                profileImage.setAttribute('aria-label', 'Click to view student photo');

                profileImage.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            }
        }

        // ============ TOAST NOTIFICATION SYSTEM ============
        function showToast(type, title, message) {
            const toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) return;

            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            toast.innerHTML = `
            <div class="toast-icon ${type}">
                <i class="fas ${icons[type]}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <div class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </div>
        `;

            toastContainer.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }

        function showSessionMessages() {
            @if (session('success'))
                showToast('success', 'Success', '{{ session('success') }}');
            @endif

            @if (session('error'))
                showToast('error', 'Error', '{{ session('error') }}');
            @endif

            @if (session('warning'))
                showToast('warning', 'Warning', '{{ session('warning') }}');
            @endif

            @if (session('info'))
                showToast('info', 'Info', '{{ session('info') }}');
            @endif
        }

        // ============ AUTHORIZATION CHECK ============
        @if (Auth::user()->usertype != 4)
            window.location.href = '/error-page';
        @endif

        // ============ PREVENT FORM RESUBMISSION ============
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
@endsection
