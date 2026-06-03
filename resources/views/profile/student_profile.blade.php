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
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            color: #333;
            line-height: 1.5;
        }

        /* SIMPLE CARD DESIGN - No complex transforms */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            background: white;
        }

        /* PROFILE HEADER - Simplified */
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 25px 20px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .profile-name {
            font-weight: 600;
            margin-top: 12px;
            margin-bottom: 5px;
            font-size: 1.2rem;
            color: #ffd700;
        }

        /* SIMPLE BUTTONS - Large and clear */
        .btn-simple {
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #ddd;
            color: #555;
        }

        .btn-outline:hover {
            background: #f5f5f5;
        }

        /* SIMPLE NAVIGATION TABS - Large touch targets */
        .simple-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 12px;
            background: #f8f9fc;
            border-radius: 12px;
            margin-bottom: 0;
        }

        .tab-btn {
            flex: 1;
            min-width: 100px;
            padding: 12px 8px;
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            color: #555;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .tab-btn i {
            font-size: 1rem;
        }

        .tab-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .tab-btn:hover:not(.active) {
            background: #e9ecef;
        }

        /* SIMPLE INFO TABLE - Easy to read */
        .info-simple {
            width: 100%;
        }

        .info-row {
            display: flex;
            flex-wrap: wrap;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-label {
            width: 140px;
            font-weight: 600;
            color: #555;
            font-size: 0.85rem;
        }

        .info-value {
            flex: 1;
            color: #333;
            font-size: 0.9rem;
        }

        @media (max-width: 576px) {
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
            .info-value {
                width: 100%;
            }
        }

        /* SIMPLE BADGES */
        .badge-simple {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active { background: var(--success-color); color: white; }
        .badge-inactive { background: #adb5bd; color: white; }
        .badge-info { background: var(--info-color); color: white; }

        /* SUBJECTS GRID - Card based, no complex table */
        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .subject-card {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #eef2f6;
            transition: all 0.2s ease;
        }

        .subject-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .subject-name {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 8px;
            color: var(--primary-color);
        }

        .teacher-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #eef2f6;
        }

        .teacher-avatar-small {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }

        .teacher-details {
            flex: 1;
        }

        .teacher-name {
            font-weight: 500;
            font-size: 0.85rem;
        }

        .teacher-phone {
            font-size: 0.75rem;
            color: #666;
        }

        /* CLASS TEACHER CARD - Prominent */
        .class-teacher-card {
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f0fe 100%);
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid rgba(78, 115, 223, 0.2);
        }

        .class-teacher-header {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .class-teacher-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            cursor: pointer;
        }

        /* ATTENDANCE & RESULTS - Large action buttons */
        .action-center {
            text-align: center;
            padding: 40px 20px;
        }

        .action-icon {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .action-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .action-desc {
            color: #6c757d;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        .btn-large {
            padding: 12px 28px;
            font-size: 1rem;
            border-radius: 50px;
        }

        /* PACKAGES TABLE - Simple scrollable */
        .packages-container {
            overflow-x: auto;
        }

        .packages-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .packages-table th,
        .packages-table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #eef2f6;
        }

        .packages-table th {
            background: #f8f9fc;
            font-weight: 600;
            color: #555;
        }

        /* TOAST NOTIFICATIONS - Simple */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast-simple {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 14px 18px;
            margin-bottom: 10px;
            min-width: 280px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease;
            border-left: 4px solid;
        }

        .toast-simple.success { border-left-color: var(--success-color); }
        .toast-simple.error { border-left-color: var(--danger-color); }
        .toast-simple.warning { border-left-color: var(--warning-color); }
        .toast-simple.info { border-left-color: var(--info-color); }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* MODALS - Simple */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-content-simple {
            background: white;
            border-radius: 16px;
            max-width: 90%;
            max-height: 90%;
            overflow: auto;
            text-align: center;
        }

        .modal-content-simple img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 12px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .simple-tabs {
                flex-direction: column;
            }
            .tab-btn {
                justify-content: center;
            }
            .class-teacher-header {
                flex-direction: column;
                text-align: center;
            }
            .subjects-grid {
                grid-template-columns: 1fr;
            }
        }

        /* HELPER CLASSES */
        .text-center { text-align: center; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 16px; }
        .mt-4 { margin-top: 24px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 16px; }
        .mb-4 { margin-bottom: 24px; }
        .fw-bold { font-weight: 700; }
        .text-muted { color: #6c757d; }
        .text-primary { color: var(--primary-color); }
    </style>

    <!-- Simple Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Simple Modal for Images -->
    <div id="imageModal" class="modal-overlay" onclick="closeModal()">
        <div class="modal-content-simple" onclick="event.stopPropagation()">
            <img id="modalImage" src="" alt="Full view">
            <div style="padding: 12px;">
                <button class="btn-simple btn-danger" onclick="closeModal()" style="width: auto; padding: 6px 20px;">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid px-3 px-md-4 py-3 py-md-4">
        <!-- Header with Back Button -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h4 class="fw-bold text-primary mb-0">
                <i class="fas fa-user-graduate me-2"></i>My Child's Profile
            </h4>
            <a href="{{ route('home') }}" class="btn-simple btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="row g-4">
            <!-- LEFT COLUMN - Student Profile Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="profile-header">
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
                        <img src="{{ $avatarImage }}" class="profile-img" alt="Student photo"
                             onclick="showImage('{{ $avatarImage }}')" style="cursor: pointer;">
                        <h5 class="profile-name">
                            {{ ucwords(strtolower($students->first_name . ' ' . $students->last_name)) }}
                        </h5>
                        <p class="mb-0 small text-warning">
                            <i class="fas fa-id-card"></i>
                            <strong>Admission No: {{ strtoupper($students->admission_number) }}</strong>
                        </p>
                    </div>

                    <div class="p-3">
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 mb-3">
                            <button class="btn-simple btn-danger flex-fill" onclick="showImage('{{ $avatarImage }}')">
                                <i class="fas fa-image"></i> View Photo
                            </button>
                            <a href="{{ route('student.profile.picture', ['student' => Hashids::encode($students->id)]) }}"
                               class="btn-simple btn-success flex-fill text-decoration-none">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>

                        <!-- Student Details -->
                        <div class="info-simple">
                            <div class="info-row">
                                <div class="info-label"><i class="fas fa-venus-mars"></i> Gender</div>
                                <div class="info-value text-capitalize">{{ $students->gender }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><i class="fas fa-users"></i> Class</div>
                                <div class="info-value text-uppercase">{{ $students->class_name }} - {{ $students->group }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><i class="fas fa-calendar"></i> Date of Birth</div>
                                <div class="info-value">{{ \Carbon\Carbon::parse($students->dob)->format('d M, Y') }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><i class="fas fa-circle"></i> Status</div>
                                <div class="info-value">
                                    @if ($students->status === 1)
                                        <span class="badge-simple badge-active">Active</span>
                                    @else
                                        <span class="badge-simple badge-inactive">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            @if ($students->transport_id)
                            <div class="info-row">
                                <div class="info-label"><i class="fas fa-bus"></i> School Bus</div>
                                <div class="info-value"><span class="badge-simple badge-info">Assigned</span></div>
                            </div>
                            @endif
                        </div>

                        <hr class="my-3">

                        <a href="{{ route('parent.edit.student', ['students' => Hashids::encode($students->id)]) }}"
                           class="btn-simple btn-primary w-100">
                            <i class="fas fa-edit"></i> Edit Information
                        </a>
                    </div>
                </div>

                <!-- Parent Information Card -->
                <div class="card mt-3">
                    <div class="p-3">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-user-shield text-primary"></i> Parent/Guardian
                        </h6>
                        <div class="info-simple">
                            <div class="info-row">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">{{ ucwords(strtolower($students->parent_first_name . ' ' . $students->parent_last_name)) }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><i class="fas fa-phone"></i> Phone</div>
                                <div class="info-value">
                                    <a href="tel:{{ $students->phone }}" class="text-decoration-none">{{ $students->phone }}</a>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="info-value">{{ $students->email ?? 'Not provided' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><i class="fas fa-map-marker-alt"></i> Address</div>
                                <div class="info-value text-capitalize">{{ ucwords(strtolower($students->address)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN - Main Content -->
            <div class="col-lg-8">
                <div class="card">
                    <!-- Simple Tabs -->
                    <div class="simple-tabs">
                        <button class="tab-btn active" data-tab="subjects">
                            <i class="fas fa-book"></i> Subjects
                        </button>
                        <button class="tab-btn" data-tab="attendance">
                            <i class="fas fa-calendar-check"></i> Attendance
                        </button>
                        <button class="tab-btn" data-tab="results">
                            <i class="fas fa-chart-bar"></i> Results
                        </button>
                        @if($students->transport_id)
                        <button class="tab-btn" data-tab="transport">
                            <i class="fas fa-bus"></i> Transport
                        </button>
                        @endif
                        <button class="tab-btn" data-tab="library">
                            <i class="fas fa-layer-group"></i> E-Library
                        </button>
                        @if(Auth::user()->school->package === 'premium')
                        <button class="tab-btn" data-tab="payment">
                            <i class="fas fa-credit-card"></i> Payments
                        </button>
                        @endif
                    </div>

                    <div class="p-3">
                        <!-- SUBJECTS TAB -->
                        <div id="tab-subjects" class="tab-content active">
                            <h6 class="fw-bold mb-3"><i class="fas fa-book text-primary"></i> Enrolled Subjects</h6>

                            @if($class_course->isEmpty())
                                <div class="text-center py-4">
                                    <i class="fas fa-book-open fa-3x text-muted mb-2"></i>
                                    <p class="text-muted">No subjects enrolled yet</p>
                                </div>
                            @else
                                <div class="subjects-grid">
                                    @foreach($class_course as $course)
                                    <div class="subject-card">
                                        <div class="subject-name">{{ ucwords(strtolower($course->course_name)) }}</div>
                                        <div class="teacher-info">
                                            @php
                                                $teacherImg = $course->image ?? '';
                                                $teacherPath = storage_path('app/public/profile/' . $teacherImg);
                                                if(!empty($teacherImg) && file_exists($teacherPath)) {
                                                    $teacherAvatar = asset('storage/profile/' . $teacherImg);
                                                } else {
                                                    $teacherAvatar = asset('storage/profile/' . (strtolower($course->gender) == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                                                }
                                                $teacherFullName = ucwords(strtolower($course->first_name . ' ' . $course->last_name));
                                            @endphp
                                            <img src="{{ $teacherAvatar }}" class="teacher-avatar-small"
                                                 onclick="showImage('{{ $teacherAvatar }}')"
                                                 style="cursor: pointer;" alt="Teacher">
                                            <div class="teacher-details">
                                                <div class="teacher-name">{{ $teacherFullName }}</div>
                                                <div class="teacher-phone">
                                                    <i class="fas fa-phone"></i>
                                                    <a href="tel:{{ $course->teacher_phone }}" class="text-decoration-none">{{ $course->teacher_phone }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Class Teacher Section -->
                            <div class="class-teacher-card mt-4">
                                <h6 class="fw-bold mb-3"><i class="fas fa-chalkboard-teacher text-primary"></i> Class Teacher</h6>
                                @forelse($myClassTeacher as $classTeacher)
                                @php
                                    $ctImg = $classTeacher->image ?? '';
                                    $ctPath = storage_path('app/public/profile/' . $ctImg);
                                    if(!empty($ctImg) && file_exists($ctPath)) {
                                        $ctAvatar = asset('storage/profile/' . $ctImg);
                                    } else {
                                        $ctAvatar = asset('storage/profile/' . (strtolower($classTeacher->gender) == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                                    }
                                    $ctName = strtoupper($classTeacher->first_name . ' ' . $classTeacher->last_name);
                                @endphp
                                <div class="class-teacher-header">
                                    <img src="{{ $ctAvatar }}" class="class-teacher-avatar"
                                         onclick="showImage('{{ $ctAvatar }}')" style="cursor: pointer;" alt="Class Teacher">
                                    <div>
                                        <div class="fw-bold mb-1">{{ $ctName }}</div>
                                        <div class="small text-muted mb-2">
                                            <i class="fas fa-venus-mars"></i> {{ ucfirst($classTeacher->gender) }} |
                                            <i class="fas fa-phone"></i> <a href="tel:{{ $classTeacher->phone }}">{{ $classTeacher->phone }}</a>
                                        </div>
                                        <div class="small">
                                            <span class="badge-simple badge-info">Class {{ strtoupper($classTeacher->class_name) }} - Stream {{ strtoupper($classTeacher->group) }}</span>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted">No class teacher assigned yet</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- ATTENDANCE TAB -->
                        <div id="tab-attendance" class="tab-content">
                            <div class="action-center">
                                <div class="action-icon"><i class="fas fa-calendar-check"></i></div>
                                <div class="action-title">Attendance Records</div>
                                <div class="action-desc">Track daily attendance and view reports</div>
                                <a href="{{ route('attendance.byYear', ['student' => Hashids::encode($students->id)]) }}"
                                   class="btn-simple btn-primary btn-large">
                                    <i class="fas fa-chart-line"></i> View Attendance
                                </a>
                            </div>
                        </div>

                        <!-- RESULTS TAB -->
                        <div id="tab-results" class="tab-content">
                            <div class="action-center">
                                <div class="action-icon"><i class="fas fa-file-alt"></i></div>
                                <div class="action-title">Academic Results</div>
                                <div class="action-desc">Check exam results and academic progress</div>
                                <a href="{{ route('results.index', ['student' => Hashids::encode($students->id)]) }}"
                                   class="btn-simple btn-primary btn-large">
                                    <i class="fas fa-file-alt"></i> View Results
                                </a>
                            </div>
                        </div>

                        <!-- TRANSPORT TAB -->
                        @if($students->transport_id)
                        <div id="tab-transport" class="tab-content">
                            <h6 class="fw-bold mb-3"><i class="fas fa-bus text-primary"></i> Transport Information</h6>
                            <div class="info-simple">
                                <div class="info-row"><div class="info-label">Driver Name</div><div class="info-value">{{ ucwords(strtolower($students->driver_name)) }}</div></div>
                                <div class="info-row"><div class="info-label">Driver Phone</div><div class="info-value"><a href="tel:{{ $students->driver_phone }}">{{ $students->driver_phone }}</a></div></div>
                                <div class="info-row"><div class="info-label">Bus Number</div><div class="info-value text-uppercase">{{ $students->bus_no }}</div></div>
                                <div class="info-row"><div class="info-label">Route</div><div class="info-value text-capitalize">{{ ucwords(strtolower($students->routine)) }}</div></div>
                            </div>
                        </div>
                        @endif

                        <!-- E-LIBRARY TAB -->
                        <div id="tab-library" class="tab-content">
                            <h6 class="fw-bold mb-3"><i class="fas fa-layer-group text-primary"></i> Learning Materials</h6>
                            @if($packages->isEmpty())
                                <div class="text-center py-4">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-2"></i>
                                    <p class="text-muted">No learning materials available yet</p>
                                </div>
                            @else
                                <div class="packages-container">
                                    <table class="packages-table">
                                        <thead>
                                            <tr><th>Title</th><th>Term</th><th>Status</th><th>Action</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($packages as $item)
                                            <tr>
                                                <td>{{ ucwords(strtolower($item->title)) }}</td>
                                                <td>Term {{ $item->term }}</td>
                                                <td>@if($item->is_active)<span class="badge-simple badge-active">Active</span>@else<span class="badge-simple badge-inactive">Locked</span>@endif</td>
                                                <td>
                                                    @if($item->is_active)
                                                    <a href="{{ route('student.holiday.package', ['id' => Hashids::encode($item->id), 'preview' => true]) }}"
                                                       class="btn-simple btn-primary" style="padding: 6px 12px; font-size: 0.75rem;" target="_blank">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                    @else
                                                    <button class="btn-simple btn-outline" disabled style="padding: 6px 12px; opacity: 0.5;"><i class="fas fa-lock"></i> Locked</button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- PAYMENT TAB -->
                        @if(Auth::user()->school->package === 'premium')
                        <div id="tab-payment" class="tab-content">
                            <div class="action-center">
                                <div class="action-icon"><i class="fas fa-history"></i></div>
                                <div class="action-title">Payment History</div>
                                <div class="action-desc">View all payments and transaction records</div>
                                <a href="{{ route('student.payment.history', ['studentId' => Hashids::encode($students->id)]) }}"
                                   class="btn-simple btn-primary btn-large">
                                    <i class="fas fa-history"></i> View Payments
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching - Simple and reliable
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons and contents
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                // Add active class to clicked button
                this.classList.add('active');

                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                const content = document.getElementById(`tab-${tabId}`);
                if (content) content.classList.add('active');
            });
        });

        // Image Modal Functions
        function showImage(imageUrl) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            if (modal && modalImg) {
                modalImg.src = imageUrl;
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Toast notifications
        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast-simple ${type}`;
            toast.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle')}"></i>
                <div style="flex:1"><strong>${title}</strong><br><small>${message}</small></div>
                <i class="fas fa-times" onclick="this.parentElement.remove()" style="cursor:pointer"></i>
            `;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }

        // Session messages
        @if(session('success'))
            showToast('success', 'Success', '{{ session('success') }}');
        @endif
        @if(session('error'))
            showToast('error', 'Error', '{{ session('error') }}');
        @endif

        // Authorization check
        @if(Auth::user()->usertype != 4)
            window.location.href = '/error-page';
        @endif
    </script>

    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
@endsection
