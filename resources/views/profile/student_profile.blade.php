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
            font-weight: 500;
            margin-top: 10px;
        }

        .nav-pills .nav-link {
            color: var(--dark-color);
            border-radius: 0;
            padding: 12px 20px;
        }

        .teacher-avatar {
            min-width: 40px !important;
            min-height: 40px !important;
            width: 40px !important;
            height: 40px !important;
            object-fit: cover;
            border: 2px solid #dee2e6;
            transition: transform 0.3s ease;
        }

        .class-teacher-avatar {
            min-width: 80px !important;
            min-height: 80px !important;
            width: 80px !important;
            height: 80px !important;
            object-fit: cover;
            border: 3px solid #dee2e6;
            transition: transform 0.3s ease;
        }

        .avatar-overlay,
        .avatar-overlay-large {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .avatar-overlay {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-overlay-large {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .teacher-avatar-container:hover .avatar-overlay,
        .class-teacher-avatar-container:hover .avatar-overlay-large {
            opacity: 1;
        }

        .teacher-avatar-container:hover .teacher-avatar,
        .class-teacher-avatar-container:hover .class-teacher-avatar {
            transform: scale(1.05);
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

        .teacher-card {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0.15rem 0.5rem 0 rgba(58, 59, 69, 0.1);
        }

        .package-table th {
            background-color: var(--info-color);
            color: white;
        }

        .photo-modal img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.3);
        }

        @media (max-width: 768px) {
            .profile-img {
                width: 100px;
                height: 100px;
            }

            /* Fix table layout for mobile */
            .table-responsive-md table {
                display: block;
            }

            .table-responsive-md thead {
                display: none;
            }

            .table-responsive-md tbody tr {
                display: flex;
                flex-direction: column;
                margin-bottom: 15px;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                background-color: white;
            }

            .table-responsive-md tbody td {
                display: flex;
                align-items: center;
                padding: 10px 0;
                border: none;
                border-bottom: 1px solid #f0f0f0;
            }

            .table-responsive-md tbody td:last-child {
                border-bottom: none;
            }

            /* Custom styling for each cell in mobile */
            .table-responsive-md tbody td:nth-child(1):before {
                content: "#";
                font-weight: bold;
                margin-right: 10px;
                min-width: 30px;
            }

            .table-responsive-md tbody td:nth-child(2):before {
                content: "Subject:";
                font-weight: bold;
                margin-right: 10px;
                min-width: 80px;
            }

            .table-responsive-md tbody td:nth-child(3):before {
                content: "Teacher:";
                font-weight: bold;
                margin-right: 10px;
                min-width: 80px;
            }

            .table-responsive-md tbody td:nth-child(4):before {
                content: "Phone:";
                font-weight: bold;
                margin-right: 10px;
                min-width: 80px;
            }

            /* Ensure teacher avatar maintains size in mobile */
            .teacher-avatar {
                min-width: 50px !important;
                min-height: 50px !important;
                width: 50px !important;
                height: 50px !important;
            }

            /* Fix class teacher card for mobile */
            .teacher-card {
                margin-bottom: 15px;
            }

            .teacher-card .row {
                flex-direction: column;
            }

            .teacher-card .col-md-3 {
                margin-bottom: 15px;
            }

            .class-teacher-avatar {
                min-width: 100px !important;
                min-height: 100px !important;
                width: 100px !important;
                height: 100px !important;
            }

            /* Remove column layout for class teacher info */
            .teacher-card .col-md-9 .row {
                flex-direction: column;
            }

            .teacher-card .col-md-9 .col-md-6 {
                margin-bottom: 10px;
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

        @media (max-width: 480px) {

            /* Extra small devices */
            .teacher-avatar {
                min-width: 45px !important;
                min-height: 45px !important;
                width: 45px !important;
                height: 45px !important;
            }

            .class-teacher-avatar {
                min-width: 80px !important;
                min-height: 80px !important;
                width: 80px !important;
                height: 80px !important;
            }

            /* Adjust font sizes */
            .teacher-card p {
                font-size: 0.9rem;
            }
        }
    </style>
    <div class="py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-10">
                <h4 class="text-primary fw-bold border-bottom pb-2">Student Profile</h4>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('home') }}" class="btn btn-secondary btn-action float-right">
                    <i class="fas fa-arrow-circle-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Profile Card -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="profile-header text-center">
                        @php
                            $imageName = $students->image;
                            $imagePath = storage_path('app/public/students/' . $imageName);

                            if (!empty($imageName) && file_exists($imagePath)) {
                                $avatarImage = asset('storage/students/' . $imageName);
                            } else {
                                $avatarImage = asset('storage/students/student.jpg');
                            }
                        @endphp
                        <img src="{{ $avatarImage }}" class="profile-img" alt="Student Photo">
                        <h5 class="profile-name mb-1" style="color:gold;">
                            {{ ucwords(strtolower($students->first_name . ' ' . $students->middle_name . ' ' . $students->last_name)) }}
                        </h5>
                        <p class="mb-0 text-white text-uppercase">Admission #:
                            <strong>{{ $students->admission_number }}</strong>
                        </p>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-3">
                            <a href="javascript:void(0)" data-photo="{{ $avatarImage }}"
                                class="btn btn-outline-danger mr-1 btn-action me-2 view-photo">
                                <i class="fas fa-image me-1"></i> View Photo
                            </a>
                            <a href="{{ route('student.profile.picture', ['student' => Hashids::encode($students->id)]) }}"
                                class="btn btn-outline-success btn-action">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>

                        <div class="profile-detail">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Gender</span>
                                <span class="text-capitalize fw-bold">{{ $students->gender }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Stream</span>
                                <span class="text-capitalize fw-bold">{{ $students->group }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Status</span>
                                @if ($students->status === 1)
                                    <span class="badge-status bg-success text-white">Active</span>
                                @else
                                    <span class="badge-status bg-secondary text-white">Inactive</span>
                                @endif
                            </div>
                            @if ($students->status === 0)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Reason</span>
                                    @if ($students->graduated_at == null)
                                        <span class="badge-status bg-danger text-white">Account Blocked</span>
                                    @else
                                        <span class="badge-status bg-success text-white">Graduated</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('parent.edit.student', ['students' => Hashids::encode($students->id)]) }}"
                            class="btn btn-primary btn-action w-100 mt-3">
                            <i class="fas fa-edit me-1"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>
            <!-- Right Column - Details Card -->
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-pills flex-column flex-lg-row">
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link active" href="#student" data-bs-toggle="tab">
                                    <i class="fas fa-user-graduate me-1"></i> Profile
                                </a>
                            </li>
                            {{-- <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#parents" data-bs-toggle="tab">
                                    <i class="fas fa-user-shield me-1"></i> Parent
                                </a>
                            </li> --}}
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#subjects" data-bs-toggle="tab">
                                    <i class="ti-book me-1"></i> Subjects
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#attendance" data-bs-toggle="tab">
                                    <i class="fas fa-calendar-check me-1"></i> Attendance
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#results" data-bs-toggle="tab">
                                    <i class="fas fa-chart-bar me-1"></i> Results
                                </a>
                            </li>
                            @if ($students->transport_id != null)
                                <li class="nav-item flex-fill text-center">
                                    <a class="nav-link" href="#transport" data-bs-toggle="tab">
                                        <i class="fas fa-bus me-1"></i> Transport
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#package" data-bs-toggle="tab">
                                    <i class="fas fa-layer-group me-1"></i> E-Library
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#payment" data-bs-toggle="tab">
                                    <i class="fas fa-credit-card me-1"></i> Payment
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Student Information Tab -->
                            <div class="tab-pane fade show active" id="student">
                                <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i> Student Details</h5>
                                <table class="info-table">
                                    <tr>
                                        <th>Class</th>
                                        <td class="text-uppercase">{{ $students->class_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>{{ \Carbon\Carbon::parse($students->dob)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Registered on</th>
                                        <td>{{ \Carbon\Carbon::parse($students->created_at)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-location-dot me-2"></i> Street Address</th>
                                        <td class="text-capitalize">{{ ucwords(strtolower($students->address)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>School Bus</th>
                                        <td class="text-capitalize">
                                            @if ($students->transport_id == null)
                                                <span class="text-muted">N/A</span>
                                            @else
                                                <span class="text-success">Yes</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Parents Information Tab -->
                            <div class="tab-pane fade" id="parents">
                                <h5 class="mb-4"><i class="fas fa-users me-2"></i> Parent/Guardian Details</h5>
                                <table class="info-table">
                                    @if ($students->parent_gender == 'male')
                                        <tr>
                                            <th colspan="2" class="text-primary fw-bold">Father's Information</th>
                                        </tr>
                                        <tr>
                                            <th>Father's Name</th>
                                            <td>{{ ucwords(strtolower($students->parent_first_name . ' ' . $students->parent_last_name)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone me-2"></i> Phone</th>
                                            <td>
                                                <a href="tel:{{ $students->phone }}" class="text-decoration-none">
                                                    {{ $students->phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-envelope me-2"></i> Email</th>
                                            <td>
                                                @if ($students->email == null)
                                                    <span class="text-muted">N/A</span>
                                                @else
                                                    <a href="mailto:{{ $students->parent_email }}"
                                                        class="text-decoration-none">
                                                        {{ $students->email }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-location-dot me-2"></i> Street Address</th>
                                            <td class="text-capitalize">{{ ucwords(strtolower($students->address)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Registered on</th>
                                            <td>{{ \Carbon\Carbon::parse($students->parent_created_at)->format('d-m-Y') }}
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <th colspan="2" class="text-primary fw-bold">Mother's Information</th>
                                        </tr>
                                        <tr>
                                            <th>Mother's Name</th>
                                            <td>{{ ucwords(strtolower($students->parent_first_name . ' ' . $students->parent_last_name)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone me-2"></i> Phone</th>
                                            <td>
                                                <a href="tel:{{ $students->phone }}" class="text-decoration-none">
                                                    {{ $students->phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-envelope me-2"></i> Email</th>
                                            <td>
                                                @if ($students->email == null)
                                                    <span class="text-muted">N/A</span>
                                                @else
                                                    <a href="mailto:{{ $students->parent_email }}"
                                                        class="text-decoration-none">
                                                        {{ $students->email }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-location-dot me-2"></i> Street Address</th>
                                            <td class="text-capitalize">{{ ucwords(strtolower($students->address)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Registered on</th>
                                            <td>{{ \Carbon\Carbon::parse($students->parent_created_at)->format('d-m-Y') }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Subjects Tab -->
                            <div class="tab-pane fade" id="subjects">
                                <h5 class="mb-4"><i class="ti-book me-2"></i> Subjects Enrollment</h5>

                                <div class="table-responsive table-responsive-md">
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
                                            @if ($class_course->isEmpty())
                                                <tr>
                                                    <td colspan="4" class="text-danger text-center py-4">No Available
                                                        subjects assigned!</td>
                                                </tr>
                                            @else
                                                @foreach ($class_course as $course)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="text-capitalize">
                                                            {{ ucwords(strtolower($course->course_name)) }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @php
                                                                    $imageName = $course->image ?? '';
                                                                    $imagePath = storage_path(
                                                                        'app/public/profile/' . $imageName,
                                                                    );
                                                                    $teacherName = ucwords(
                                                                        strtolower(
                                                                            $course->first_name .
                                                                                ' ' .
                                                                                $course->last_name,
                                                                        ),
                                                                    );

                                                                    if (!empty($imageName) && file_exists($imagePath)) {
                                                                        $avatarImage = asset(
                                                                            'storage/profile/' . $imageName,
                                                                        );
                                                                        $hasImage = true;
                                                                    } else {
                                                                        $avatarImage = asset(
                                                                            'storage/profile/' .
                                                                                ($course->gender == 'male'
                                                                                    ? 'avatar.jpg'
                                                                                    : 'avatar-female.jpg'),
                                                                        );
                                                                        $hasImage = false;
                                                                    }
                                                                @endphp

                                                                <!-- Clickable Image for Modal -->
                                                                <div class="teacher-avatar-container position-relative"
                                                                    style="cursor: pointer;"
                                                                    onclick="openTeacherModal('{{ $avatarImage }}', '{{ $teacherName }}', '{{ $course->course_name }}')"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-title="Click to view photo"
                                                                    data-bs-placement="top">
                                                                    <img src="{{ $avatarImage }}" alt="Teacher"
                                                                        class="rounded-circle me-3 teacher-avatar"
                                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                                    <div class="avatar-overlay">
                                                                        <i class="fas fa-search-plus"></i>
                                                                    </div>
                                                                </div>

                                                                <span class="text-capitalize">{{ $teacherName }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a href="tel:{{ $course->teacher_phone }}"
                                                                class="text-decoration-none">
                                                                <i class="fas fa-phone me-1"></i>
                                                                {{ $course->teacher_phone }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <hr>

                                <h6 class="mb-3"><i class="fas fa-chalkboard-teacher me-2"></i> Class Teacher Attendant
                                </h6>
                                @if ($myClassTeacher->isEmpty())
                                    <div class="alert alert-warning text-center">
                                        No class teacher assigned!
                                    </div>
                                @else
                                    @foreach ($myClassTeacher as $classTeacher)
                                        <div class="teacher-card mb-4 p-3 border rounded">
                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    @php
                                                        $imageName = $classTeacher->image ?? '';
                                                        $imagePath = storage_path('app/public/profile/' . $imageName);
                                                        $teacherName = strtoupper(
                                                            $classTeacher->first_name . ' ' . $classTeacher->last_name,
                                                        );

                                                        if (!empty($imageName) && file_exists($imagePath)) {
                                                            $avatarImage = asset('storage/profile/' . $imageName);
                                                            $hasImage = true;
                                                        } else {
                                                            $avatarImage = asset(
                                                                'storage/profile/' .
                                                                    ($classTeacher->gender == 'male'
                                                                        ? 'avatar.jpg'
                                                                        : 'avatar-female.jpg'),
                                                            );
                                                            $hasImage = false;
                                                        }
                                                    @endphp

                                                    <!-- Clickable Image for Class Teacher Modal -->
                                                    <div class="class-teacher-avatar-container position-relative"
                                                        style="cursor: pointer; display: inline-block; width: 120px; height: 120px;"
                                                        onclick="openTeacherModal('{{ $avatarImage }}', '{{ $teacherName }}', 'Class Teacher')"
                                                        data-bs-toggle="tooltip" data-bs-title="Click to view photo"
                                                        data-bs-placement="top">
                                                        <img src="{{ $avatarImage }}" alt="Class Teacher"
                                                            class="class-teacher-avatar"
                                                            style="width: 100%; height: 100%; object-fit: cover;">
                                                        <div class="avatar-overlay-large">
                                                            <i class="fas fa-search-plus fa-lg"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Name:</strong> {{ $teacherName }}
                                                            </p>
                                                            <p class="mb-1"><strong>Gender:</strong>
                                                                {{ strtoupper($classTeacher->gender) }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Phone:</strong>
                                                                <a href="tel:{{ $classTeacher->phone }}"
                                                                    class="text-decoration-none">
                                                                    <i class="fas fa-phone me-1"></i>
                                                                    {{ $classTeacher->phone }}
                                                                </a>
                                                            </p>
                                                            <p class="mb-0"><strong>Class:</strong>
                                                                {{ strtoupper($classTeacher->class_name) }}</p>
                                                            <p class="mb-0"><strong>Stream: </strong>
                                                                {{ strtoupper($classTeacher->group) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Teacher Image Modal -->
                            <div class="modal fade" id="teacherImageModal" tabindex="-1"
                                aria-labelledby="teacherImageModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="teacherImageModalLabel">
                                                <i class="fas fa-user-circle me-2"></i>
                                                <span id="modalTeacherName"></span>
                                            </h5>
                                            <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal"
                                                aria-label="Close"><i class="fas fa-close"></i></button>
                                        </div>
                                        <div class="modal-body text-center p-4">
                                            <div class="mb-3">
                                                <span class="badge bg-info text-white" id="modalTeacherRole"></span>
                                            </div>
                                            <div class="image-container position-relative">
                                                <img id="modalTeacherImage" src="" alt="Teacher Photo"
                                                    class="img-fluid rounded shadow-lg"
                                                    style="max-height: 70vh; max-width: 100%; object-fit: contain;">

                                                <!-- Loading Spinner -->
                                                <div id="imageLoading"
                                                    class="position-absolute top-50 start-50 translate-middle"
                                                    style="display: none;">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>

                                                <!-- Error Message -->
                                                <div id="imageError" class="alert alert-danger mt-3"
                                                    style="display: none;">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Failed to load image
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">
                                                <i class="fas fa-times me-1"></i> Close
                                            </button>
                                            {{-- <button type="button" class="btn btn-primary"
                                                onclick="downloadTeacherImage()">
                                                <i class="fas fa-download me-1"></i> Download
                                            </button> --}}
                                            <button type="button" class="btn btn-info" onclick="zoomInImage()">
                                                <i class="fas fa-search-plus me-1"></i> Zoom
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
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body d-flex align-items-center justify-content-center">
                                            <img id="fullScreenImage" src="" alt="Full Screen" class="img-fluid"
                                                style="max-height: 90vh;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Tab -->
                            <div class="tab-pane fade" id="attendance">
                                <h5 class="mb-4"><i class="fas fa-calendar-check me-2"></i> Attendance for: <span
                                        class="text-uppercase">{{ $students->first_name . ' ' . $students->last_name }}</span>
                                </h5>
                                <div class="text-center py-4">
                                    <a href="{{ route('attendance.byYear', ['student' => Hashids::encode($students->id)]) }}"
                                        class="btn btn-primary btn-action">
                                        <i class="fas fa-chart-line me-2"></i> View Attendance Reports
                                    </a>
                                </div>
                            </div>

                            <!-- Results Tab -->
                            <div class="tab-pane fade" id="results">
                                <h5 class="mb-4"><i class="fas fa-chart-bar me-2"></i> Results Report for: <span
                                        class="text-uppercase">{{ $students->first_name . ' ' . $students->last_name }}</span>
                                </h5>
                                <div class="text-center py-4">
                                    <a href="{{ route('results.index', ['student' => Hashids::encode($students->id)]) }}"
                                        class="btn btn-primary btn-action">
                                        <i class="fas fa-file-alt me-2"></i> View Results Reports
                                    </a>
                                </div>
                            </div>

                            <!-- Transport Tab -->
                            @if ($students->transport_id != null)
                                <div class="tab-pane fade" id="transport">
                                    <h5 class="mb-4"><i class="fas fa-bus me-2"></i> Transport Information</h5>
                                    <table class="info-table">
                                        <tr>
                                            <th>Driver Name</th>
                                            <td>{{ ucwords(strtolower($students->driver_name)) }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone me-2"></i> Phone</th>
                                            <td>
                                                <a href="tel:{{ $students->driver_phone }}">
                                                    {{ $students->driver_phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Gender</th>
                                            <td class="text-capitalize">
                                                {{ ucwords(strtolower($students->driver_gender)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bus Number</th>
                                            <td class="text-capitalize">{{ ucwords(strtolower($students->bus_no)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>School Bus Route</th>
                                            <td class="text-capitalize">
                                                {{ ucwords(strtolower($students->routine)) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            @endif

                            <!-- Packages Tab -->
                            <div class="tab-pane fade" id="package">
                                <h5 class="mb-4"><i class="fas fa-file-archive-o me-2"></i> Online Library & Academic
                                    Materials </h5>

                                @if ($packages->isEmpty())
                                    <div class="alert alert-warning text-center py-4">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No Data uploaded yet!
                                    </div>
                                @else
                                    <div class="table-responsive table-responsive-md">
                                        <table class="table table-hover package-table">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th>Term</th>
                                                    <th>Status</th>
                                                    <th>Released At</th>
                                                    <th>Expire On</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($packages as $item)
                                                    <tr class="text-capitalize">
                                                        <td>{{ ucwords(strtolower($item->title)) }}</td>
                                                        <td>{{ ucwords(strtolower($item->description)) ?? 'N/A' }}</td>
                                                        <td>Term {{ ucwords(strtolower($item->term)) }}</td>
                                                        <td>
                                                            @if ($item->is_active == true)
                                                                <span
                                                                    class="badge-status bg-success text-white">Active</span>
                                                            @else
                                                                <span
                                                                    class="badge-status bg-secondary text-white">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->release_date ?? 'N/A' }}</td>
                                                        <td>{{ $item->due_date ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($item->is_active == true)
                                                                <a href="{{ route('student.holiday.package', ['id' => Hashids::encode($item->id), 'preview' => true]) }}"
                                                                    target="_blank" class="btn btn-sm btn-success btn-xs"
                                                                    onclick="return confirm('Are you sure you want to download this package?')">
                                                                    <i class="fas fa-download me-1"></i> Download
                                                                </a>
                                                            @else
                                                                <button class="btn btn-sm btn-danger disabled btn-xs">
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
                            <div class="tab-pane fade" id="payment">
                                <h5 class="mb-4"><i class="fas fa-credit-card me-2"></i> Payment Information</h5>
                                <div class="text-center py-4">
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

        <!-- Photo Modal -->
        <div class="modal fade" id="studentPhotoModal" tabindex="-1" aria-labelledby="studentPhotoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="studentPhotoModalLabel">Student Photo</h5>
                        <button type="button" class="btn btn-xs btn-danger" data-dismiss="modal" aria-label="Close"><i
                                class="fas fa-close"></i></button>
                    </div>
                    <div class="modal-body text-center">
                        <h6 class="text-primary mb-3">
                            {{ strtoupper($students->first_name . ' ' . $students->middle_name . ' ' . $students->last_name) }}
                        </h6>
                        <img id="student-photo" src="" alt="Student Photo" class="photo-modal img-fluid">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-close btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Handle photo view modal
                document.querySelectorAll('.view-photo').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var photoUrl = this.getAttribute('data-photo');
                        document.getElementById('student-photo').setAttribute('src', photoUrl);
                        var modal = new bootstrap.Modal(document.getElementById('studentPhotoModal'));
                        modal.show();
                    });
                });

                // Activate tabs
                const triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))
                triggerTabList.forEach(function(triggerEl) {
                    new bootstrap.Tab(triggerEl)
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const teacherModalElement = document.getElementById('teacherImageModal');
                const fullScreenModalElement = document.getElementById('fullScreenImageModal');
                // Initialize Bootstrap tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Initialize modals
                const teacherModal = new bootstrap.Modal(document.getElementById('teacherImageModal'));
                const fullScreenModal = new bootstrap.Modal(document.getElementById('fullScreenImageModal'));

                // Track current image for zoom functionality
                let currentImageUrl = '';
                let isZoomed = false;

                // Global function to open teacher modal
                window.openTeacherModal = function(imageUrl, teacherName, teacherRole) {
                    currentImageUrl = imageUrl;

                    // Set modal content
                    document.getElementById('modalTeacherName').textContent = teacherName;
                    document.getElementById('modalTeacherRole').textContent = teacherRole;

                    // Show loading spinner
                    document.getElementById('imageLoading').style.display = 'block';
                    document.getElementById('imageError').style.display = 'none';
                    document.getElementById('modalTeacherImage').style.display = 'none';

                    // Load image
                    const img = new Image();
                    img.onload = function() {
                        document.getElementById('modalTeacherImage').src = imageUrl;
                        document.getElementById('modalTeacherImage').style.display = 'block';
                        document.getElementById('imageLoading').style.display = 'none';
                        resetImageZoom();
                    };

                    img.onerror = function() {
                        document.getElementById('imageLoading').style.display = 'none';
                        document.getElementById('imageError').style.display = 'block';
                    };

                    img.src = imageUrl;

                    // Show modal
                    teacherModal.show();
                };

                // Function to download image
                window.downloadTeacherImage = function() {
                    if (!currentImageUrl) return;

                    const link = document.createElement('a');
                    link.href = currentImageUrl;
                    link.download = 'teacher_photo_' + Date.now() + '.jpg';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Show toast notification (optional)
                    showToast('Download started', 'success');
                };

                // Function to zoom image
                window.zoomInImage = function() {
                    const image = document.getElementById('modalTeacherImage');

                    if (!isZoomed) {
                        image.classList.add('zoomed');
                        isZoomed = true;

                        // Add click to view full screen
                        image.style.cursor = 'zoom-out';
                        image.onclick = function() {
                            openFullScreenModal(currentImageUrl);
                        };

                        // Update button text
                        document.querySelector('.btn-info i').className = 'fas fa-search-minus me-1';
                    } else {
                        resetImageZoom();
                    }
                };

                // Function to reset zoom
                function resetImageZoom() {
                    const image = document.getElementById('modalTeacherImage');
                    image.classList.remove('zoomed');
                    image.style.cursor = 'default';
                    image.onclick = null;
                    isZoomed = false;

                    // Update button text
                    document.querySelector('.btn-info i').className = 'fas fa-search-plus me-1';
                }

                // Function to open full screen modal
                window.openFullScreenModal = function(imageUrl) {
                    document.getElementById('fullScreenImage').src = imageUrl;
                    fullScreenModal.show();
                };

                // Close full screen modal with ESC key
                document.getElementById('fullScreenImageModal').addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        fullScreenModal.hide();
                    }
                });

                // Keyboard navigation for teacher modal
                document.getElementById('teacherImageModal').addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        resetImageZoom();
                    }
                });

                // Function to show toast (optional)
                function showToast(message, type) {
                    // You can implement toast notification here
                    // Example: using Bootstrap toasts
                    console.log(message, type);
                }

                // Make teacher images tabbable for accessibility
                document.querySelectorAll('.teacher-avatar-container, .class-teacher-avatar-container').forEach(
                    container => {
                        container.setAttribute('tabindex', '0');
                        container.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter' || e.key === ' ') {
                                e.preventDefault();
                                const img = this.querySelector('img');
                                const name = this.closest('.d-flex').querySelector('span').textContent ||
                                    this.closest('.teacher-card').querySelector('strong').textContent;
                                const role = this.closest('tr') ? 'Subject Teacher' : 'Class Teacher';
                                openTeacherModal(img.src, name.trim(), role);
                            }
                        });
                    });
            });
        </script>
    @endsection
