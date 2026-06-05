@extends('SRTDashboard.frame')

@section('content')
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
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            position: relative;
        }

        /* Mobile Container */
        .dashboard-container {
            max-width: 100%;
            padding: 12px;
            position: relative;
            z-index: 1;
        }

        /* Mobile Card */
        .mobile-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        /* Card Header */
        .mobile-card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 20px 16px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .header-title-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .header-title-section h3 {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-mobile {
            padding: 8px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
        }

        .btn-mobile i {
            font-size: 12px;
        }

        /* Teacher Cards Grid - Mobile First */
        .teachers-grid {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Teacher Card - App Style */
        .teacher-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            position: relative;
        }

        .teacher-card:active {
            transform: scale(0.98);
        }

        /* Card Header with Status Badge */
        .teacher-card-header {
            padding: 16px;
            background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f5 100%);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .teacher-info-section {
            display: flex;
            gap: 12px;
            flex: 1;
        }

        .teacher-avatar-large {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }

        .teacher-details {
            flex: 1;
        }

        .teacher-name-large {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .teacher-id-large {
            font-size: 11px;
            color: var(--primary);
            font-weight: 600;
            font-family: monospace;
            background: rgba(67, 97, 238, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 4px;
        }

        /* Status Badge on Card */
        .status-badge-card {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .status-active-card {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive-card {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Card Body - Stats Grid */
        .teacher-card-body {
            padding: 16px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .info-item i {
            width: 28px;
            height: 28px;
            background: var(--gray-100);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 12px;
        }

        .info-item span {
            color: var(--gray-600);
            font-size: 11px;
        }

        .info-item strong {
            color: var(--dark);
            font-size: 13px;
            display: block;
        }

        /* Role Badge */
        .role-badge-card {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--gray-100);
            color: var(--dark);
        }

        /* Action Buttons on Card */
        .card-actions {
            display: flex;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px solid var(--gray-200);
            margin-top: 8px;
        }

        .card-action-btn {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .card-action-btn.view {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .card-action-btn.warning {
            background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%);
            color: white;
        }

        .card-action-btn.success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
        }

        .card-action-btn.danger {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            color: white;
        }

        .card-action-btn:active {
            transform: scale(0.97);
        }

        /* Empty State */
        .empty-state-mobile {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
            border-radius: 16px;
            margin: 16px;
        }

        .empty-state-mobile i {
            font-size: 60px;
            color: #ffc107;
            margin-bottom: 15px;
        }

        /* Modal Mobile Optimized */
        .modal-mobile .modal-content {
            border-radius: 20px;
            border: none;
            margin: 16px;
        }

        .modal-mobile .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 16px;
        }

        .modal-mobile .modal-body {
            padding: 16px;
        }

        .modal-mobile .modal-footer {
            border: none;
            padding: 12px 16px;
            background: var(--gray-100);
        }

        /* Form Styles Mobile */
        .form-group-mobile {
            margin-bottom: 16px;
        }

        .form-label-mobile {
            font-weight: 600;
            font-size: 13px;
            color: var(--dark);
            margin-bottom: 6px;
            display: block;
        }

        .form-control-mobile {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control-mobile:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        /* Dropdown Mobile */
        .dropdown-mobile .dropdown-menu {
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border: none;
            padding: 8px;
            min-width: 140px;
        }

        .dropdown-mobile .dropdown-item {
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .loading-spinner.active {
            display: flex;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .dashboard-container {
                padding: 8px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .card-actions {
                flex-wrap: wrap;
            }

            .card-action-btn {
                min-width: calc(50% - 4px);
            }

            .header-row {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons {
                justify-content: stretch;
            }

            .btn-mobile {
                justify-content: center;
                flex: 1;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
            }

            .mobile-card {
                background: #2d3748;
            }

            .teacher-card {
                background: #2d3748;
                border-color: #4a5568;
            }

            .teacher-card-header {
                background: #374151;
                border-bottom-color: #4a5568;
            }

            .teacher-name-large {
                color: #f8f9fa;
            }

            .info-item i {
                background: #374151;
                color: var(--primary);
            }

            .info-item strong {
                color: #f8f9fa;
            }

            .form-control-mobile {
                background: #374151;
                border-color: #4a5568;
                color: #f8f9fa;
            }

            .form-label-mobile {
                color: #f8f9fa;
            }
        }
    </style>

    <div class="dashboard-container">
        <div class="mobile-card">
            <!-- Header -->
            <div class="mobile-card-header">
                <div class="header-row">
                    <div class="header-title-section">
                        <div class="header-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>Teachers</h3>
                    </div>
                    <div class="action-buttons">
                        <div class="dropdown dropdown-mobile">
                            <button class="btn-mobile dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i>
                                <span>Export</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{route('teachers.excel.export')}}">
                                        <i class="fas fa-file-excel text-success"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{route('teachers.pdf.export')}}" target="_blank">
                                        <i class="fas fa-file-pdf text-danger"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <button type="button" class="btn-mobile" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                            <i class="fas fa-plus"></i>
                            <span>Add</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Body - Mobile Card Layout -->
            @if ($teachers->isEmpty())
                <div class="empty-state-mobile">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h6>No Teachers Found</h6>
                    <p class="text-muted small">Tap "Add" to register your first teacher</p>
                </div>
            else
                <div class="teachers-grid">
                    @foreach ($teachers as $teacher)
                        @php
                            $imageName = $teacher->image;
                            $imagePath = storage_path('app/public/profile/' . $imageName);
                            $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                ? asset('storage/profile/' . $imageName)
                                : asset('storage/profile/' . ($teacher->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));

                            $roleClass = match($teacher->role_id) {
                                1 => 'role-admin',
                                2 => 'role-teacher',
                                3 => 'role-staff',
                                default => 'role-other'
                            };
                        @endphp

                        <div class="teacher-card">
                            <!-- Card Header -->
                            <div class="teacher-card-header">
                                <div class="teacher-info-section">
                                    <img src="{{ $avatarImage }}" alt="Avatar" class="teacher-avatar-large">
                                    <div class="teacher-details">
                                        <div class="teacher-name-large">
                                            {{ ucwords(strtolower($teacher->first_name . ' ' . $teacher->last_name)) }}
                                        </div>
                                        <div class="teacher-id-large">
                                            <i class="fas fa-id-card"></i> {{ strtoupper($teacher->member_id) }}
                                        </div>
                                    </div>
                                </div>
                                @if ($teacher->status == 1)
                                    <span class="status-badge-card status-active-card">
                                        <i class="fas fa-circle" style="font-size: 8px;"></i> Active
                                    </span>
                                @else
                                    <span class="status-badge-card status-inactive-card">
                                        <i class="fas fa-circle" style="font-size: 8px;"></i> Blocked
                                    </span>
                                @endif
                            </div>

                            <!-- Card Body -->
                            <div class="teacher-card-body">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <i class="fas fa-venus-mars"></i>
                                        <div>
                                            <span>Gender</span>
                                            <strong>{{ ucfirst($teacher->gender) }}</strong>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-tag"></i>
                                        <div>
                                            <span>Role</span>
                                            <strong class="role-badge-card {{ $roleClass }}">
                                                {{ ucwords(strtolower($teacher->role_name)) }}
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <div>
                                            <span>Phone</span>
                                            <strong><a href="tel:{{ $teacher->phone }}" style="color: inherit; text-decoration: none;">{{ $teacher->phone }}</a></strong>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <div>
                                            <span>Joined</span>
                                            <strong>{{ $teacher->joined }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="card-actions">
                                    <a href="{{ route('teacher.profile', ['teacher' => Hashids::encode($teacher->id)]) }}"
                                       class="card-action-btn view">
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    @if ($teacher->status == 1)
                                        <form action="{{ route('update.teacher.status', ['teacher' => Hashids::encode($teacher->id)]) }}"
                                              method="POST"
                                              class="d-inline"
                                              style="flex: 1"
                                              onsubmit="return confirm('Block {{ $teacher->first_name }}?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="card-action-btn warning">
                                                <i class="fas fa-ban"></i> Block
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('teachers.restore', ['teacher' => Hashids::encode($teacher->id)]) }}"
                                              method="POST"
                                              class="d-inline"
                                              style="flex: 1"
                                              onsubmit="return confirm('Unblock {{ $teacher->first_name }}?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="card-action-btn success">
                                                <i class="fas fa-check"></i> Unblock
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('Teachers.remove', ['teacher' => Hashids::encode($teacher->id)]) }}"
                                          method="POST"
                                          class="d-inline"
                                          style="flex: 1"
                                          onsubmit="return confirm('Delete {{ $teacher->first_name }}? This cannot be undone.')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="card-action-btn danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Add Teacher Modal - Mobile Optimized -->
    <div class="modal fade modal-mobile" id="addTeacherModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register New Teacher</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form class="needs-validation" novalidate action="{{ route('Teachers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group-mobile">
                            <label class="form-label-mobile">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="fname" class="form-control-mobile" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Other Names <span class="text-danger">*</span></label>
                            <input type="text" name="lname" class="form-control-mobile" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Email</label>
                            <input type="email" name="email" class="form-control-mobile">
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control-mobile" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control-mobile" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Qualification <span class="text-danger">*</span></label>
                            <select name="qualification" class="form-control-mobile" required>
                                <option value="">Select Qualification</option>
                                <option value="1">Masters</option>
                                <option value="2">Degree</option>
                                <option value="3">Diploma</option>
                                <option value="4">Certificate</option>
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control-mobile" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Year Joined <span class="text-danger">*</span></label>
                            <select name="joined" class="form-control-mobile" required>
                                <option value="">Select Year</option>
                                @for ($year = date('Y'); $year >= 2010; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Street/Village <span class="text-danger">*</span></label>
                            <input type="text" name="street" class="form-control-mobile" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="saveButton">Save Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Form handling
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");
            const loadingSpinner = document.getElementById("loadingSpinner");

            if (form && submitButton) {
                form.addEventListener("submit", function(event) {
                    event.preventDefault();

                    if (!form.checkValidity()) {
                        form.classList.add("was-validated");
                        return;
                    }

                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                    loadingSpinner.classList.add('active');

                    setTimeout(() => {
                        form.submit();
                    }, 300);
                });
            }

            // Touch feedback for cards
            const cards = document.querySelectorAll('.teacher-card');
            cards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });
    </script>
@endsection
