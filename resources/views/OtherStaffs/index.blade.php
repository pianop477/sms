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
            padding: 16px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header-row {
            display: flex;
            flex-direction: column;
            gap: 12px;
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
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-mobile {
            padding: 8px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 12px;
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
            font-size: 11px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .stat-card-mobile {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .stat-card-mobile i {
            font-size: 20px;
            color: white;
            margin-bottom: 6px;
            display: block;
        }

        .stat-card-mobile .stat-label {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 4px;
        }

        .stat-card-mobile .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: white;
        }

        /* Staff Cards Grid */
        .staff-grid {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Staff Card */
        .staff-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .staff-card:active {
            transform: scale(0.98);
        }

        /* Card Header */
        .staff-card-header {
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(135deg, var(--gray-100) 0%, white 100%);
        }

        .staff-avatar-mobile {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }

        .staff-info-header {
            flex: 1;
        }

        .staff-name-mobile {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .staff-id-mobile {
            font-size: 11px;
            color: var(--primary);
            font-weight: 600;
            font-family: monospace;
            background: rgba(67, 97, 238, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        /* Status Badge on Card */
        .status-badge-card {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-active-card {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive-card {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Card Body */
        .staff-card-body {
            padding: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 12px;
        }

        .info-item-mobile {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }

        .info-item-mobile i {
            width: 28px;
            height: 28px;
            background: var(--gray-100);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 11px;
        }

        .info-item-mobile .info-label {
            color: var(--gray-600);
            font-size: 9px;
        }

        .info-item-mobile .info-value {
            color: var(--dark);
            font-size: 12px;
            font-weight: 500;
        }

        /* Job Badge */
        .job-badge-mobile {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--gray-100);
            color: var(--dark);
        }

        /* Gender Badge */
        .gender-badge-mobile {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .gender-male-badge {
            background: #e3f2fd;
            color: #1565c0;
        }

        .gender-female-badge {
            background: #fce4ec;
            color: #c2185b;
        }

        /* Phone Link */
        .phone-link-mobile {
            color: var(--dark);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
        }

        /* Card Actions */
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
            font-size: 11px;
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
            font-size: 50px;
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
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-mobile .modal-footer {
            border: none;
            padding: 12px 16px;
            background: var(--gray-100);
        }

        /* Form Controls Mobile */
        .form-group-mobile {
            margin-bottom: 14px;
        }

        .form-label-mobile {
            font-weight: 600;
            font-size: 12px;
            color: var(--dark);
            margin-bottom: 6px;
            display: block;
        }

        .form-label-mobile .required {
            color: var(--danger);
        }

        .form-control-mobile {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .form-control-mobile:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .form-select-mobile {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 13px;
            background: white;
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

            .action-buttons {
                justify-content: stretch;
            }

            .btn-mobile {
                justify-content: center;
                flex: 1;
            }

            .stats-grid {
                gap: 6px;
            }

            .stat-card-mobile {
                padding: 8px;
            }

            .stat-value {
                font-size: 16px;
            }

            .card-actions {
                flex-wrap: wrap;
            }

            .card-action-btn {
                min-width: calc(50% - 4px);
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

            .staff-card {
                background: #2d3748;
                border-color: #4a5568;
            }

            .staff-card-header {
                background: #374151;
                border-bottom-color: #4a5568;
            }

            .staff-name-mobile {
                color: #f8f9fa;
            }

            .info-item-mobile i {
                background: #374151;
                color: var(--primary);
            }

            .info-item-mobile .info-value {
                color: #f8f9fa;
            }

            .job-badge-mobile {
                background: #374151;
                color: #f8f9fa;
            }

            .modal-mobile .modal-content {
                background: #2d3748;
            }

            .modal-mobile .modal-footer {
                background: #374151;
            }

            .form-control-mobile,
            .form-select-mobile {
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
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h3>Staff Management</h3>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <div class="dropdown">
                            <button class="btn-mobile dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{route('export.other.staffs', ['format' => 'excel'])}}">
                                        <i class="fas fa-file-excel text-success"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{route('export.other.staffs', ['format' => 'pdf'])}}" target="_blank">
                                        <i class="fas fa-file-pdf text-danger"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <button type="button" class="btn-mobile" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card-mobile">
                    <i class="fas fa-users"></i>
                    <div class="stat-label">Total Staff</div>
                    <div class="stat-value">{{ $combinedStaffs->filter(fn($s) => $s->status === 1)->count() }}</div>
                </div>
                <div class="stat-card-mobile">
                    <i class="fas fa-male"></i>
                    <div class="stat-label">Male</div>
                    <div class="stat-value">{{ $combinedStaffs->filter(fn($s) => strtolower($s->gender) === 'male' && $s->status == 1)->count() }}</div>
                </div>
                <div class="stat-card-mobile">
                    <i class="fas fa-female"></i>
                    <div class="stat-label">Female</div>
                    <div class="stat-value">{{ $combinedStaffs->filter(fn($s) => strtolower($s->gender) === 'female' && $s->status == 1)->count() }}</div>
                </div>
            </div>

            <!-- Staff Grid - Mobile Cards -->
            @if ($combinedStaffs->isEmpty())
                <div class="empty-state-mobile">
                    <i class="fas fa-users-cog"></i>
                    <h6 class="mt-2">No Staff Found</h6>
                    <p class="text-muted small">Tap "Register" to add your first staff member</p>
                </div>
            @else
                <div class="staff-grid">
                    @foreach ($combinedStaffs as $row)
                        @php
                            $imageName = $row->profile_image ?? null;
                            $imagePath = storage_path('app/public/profile/' . $imageName);
                            $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                ? asset('storage/profile/' . $imageName)
                                : asset('storage/profile/' . ($row->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));

                            $displayName = $row->driver_name ?? ($row->first_name . ' ' . ($row->last_name ?? ''));
                            $staffId = $row->staff_id ?? 'N/A';
                        @endphp

                        <div class="staff-card">
                            <!-- Card Header -->
                            <div class="staff-card-header">
                                <img src="{{ $avatarImage }}" alt="Avatar" class="staff-avatar-mobile">
                                <div class="staff-info-header">
                                    <div class="staff-name-mobile">{{ ucwords(strtolower($displayName)) }}</div>
                                    <div class="staff-id-mobile">
                                        <i class="fas fa-id-card"></i> {{ strtoupper($staffId) }}
                                    </div>
                                </div>
                                @if ($row->status == 1)
                                    <span class="status-badge-card status-active-card">
                                        <i class="fas fa-circle" style="font-size: 6px;"></i> Active
                                    </span>
                                @else
                                    <span class="status-badge-card status-inactive-card">
                                        <i class="fas fa-circle" style="font-size: 6px;"></i> Blocked
                                    </span>
                                @endif
                            </div>

                            <!-- Card Body -->
                            <div class="staff-card-body">
                                <div class="info-grid">
                                    <div class="info-item-mobile">
                                        <i class="fas fa-venus-mars"></i>
                                        <div>
                                            <div class="info-label">Gender</div>
                                            <div class="info-value">
                                                <span class="gender-badge-mobile {{ $row->gender == 'male' ? 'gender-male-badge' : 'gender-female-badge' }}">
                                                    <i class="fas {{ $row->gender == 'male' ? 'fa-mars' : 'fa-venus' }}"></i>
                                                    {{ ucfirst($row->gender) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-briefcase"></i>
                                        <div>
                                            <div class="info-label">Job Title</div>
                                            <div class="info-value">
                                                <span class="job-badge-mobile">
                                                    <i class="fas fa-tag"></i>
                                                    {{ ucwords(str_replace('_', ' ', $row->job_title ?? 'N/A')) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-phone"></i>
                                        <div>
                                            <div class="info-label">Phone</div>
                                            <div class="info-value">
                                                <a href="tel:{{ $row->phone }}" class="phone-link-mobile">
                                                    <i class="fas fa-phone-alt"></i> {{ $row->phone }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-calendar"></i>
                                        <div>
                                            <div class="info-label">Joined</div>
                                            <div class="info-value">{{ $row->joining_year ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="card-actions">
                                    <a href="{{ route('OtherStaffs.profile', ['type' => $row->job_title, 'id' => Hashids::encode($row->id)]) }}"
                                       class="card-action-btn view">
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    @if ($row->status == 1)
                                        <form action="{{ route('block.other.staffs', ['type' => $row->job_title, 'id' => Hashids::encode($row->id)]) }}"
                                              method="POST"
                                              class="d-inline"
                                              style="flex: 1">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" onclick="return confirm('Block {{ $displayName }}?')" class="card-action-btn warning">
                                                <i class="fas fa-ban"></i> Block
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('unblock.other.staffs', ['type' => $row->job_title, 'id' => Hashids::encode($row->id)]) }}"
                                              method="POST"
                                              class="d-inline"
                                              style="flex: 1">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" onclick="return confirm('Unblock {{ $displayName }}?')" class="card-action-btn success">
                                                <i class="fas fa-check"></i> Unblock
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Register Staff Modal -->
    <div class="modal fade modal-mobile" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register New Staff</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form class="needs-validation" novalidate action="{{ route('OtherStaffs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group-mobile">
                            <label class="form-label-mobile">First Name <span class="required">*</span></label>
                            <input type="text" name="fname" class="form-control-mobile" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Other Names <span class="required">*</span></label>
                            <input type="text" name="lname" class="form-control-mobile" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Email</label>
                            <input type="email" name="email" class="form-control-mobile">
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Gender <span class="required">*</span></label>
                            <select name="gender" class="form-select-mobile" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Phone <span class="required">*</span></label>
                            <input type="tel" name="phone" class="form-control-mobile" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Education Level <span class="required">*</span></label>
                            <select name="education" class="form-select-mobile" required>
                                <option value="">Select</option>
                                <option value="university">University</option>
                                <option value="college">College</option>
                                <option value="high_school">High school</option>
                                <option value="secondary">Secondary school</option>
                                <option value="primary">Primary school</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Date of Birth <span class="required">*</span></label>
                            <input type="date" name="dob" class="form-control-mobile" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Year Joined <span class="required">*</span></label>
                            <select name="joined" class="form-select-mobile" required>
                                <option value="">Select Year</option>
                                @for ($year = date('Y'); $year >= 2010; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Street/Village <span class="required">*</span></label>
                            <input type="text" name="street" class="form-control-mobile" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Job Title <span class="required">*</span></label>
                            <select name="job_title" class="form-select-mobile" required>
                                <option value="">Select Job Title</option>
                                <option value="cooks">Cooks</option>
                                <option value="matron">Matron</option>
                                <option value="patron">Patron</option>
                                <option value="cleaner">Cleaner</option>
                                <option value="security guard">Security guard</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">NIN (NIDA)</label>
                            <input type="text" name="nida" class="form-control-mobile" id="nin" maxlength="23" placeholder="Enter NIDA number">
                            <div class="note-text" style="font-size: 10px; color: var(--gray-600); margin-top: 4px;">Optional - Format: 00000000-00000-00000-00</div>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Profile Picture</label>
                            <input type="file" name="image" class="form-control-mobile" accept="image/*">
                            <div class="note-text" style="font-size: 10px; color: var(--gray-600); margin-top: 4px;">Optional - Max 2MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="saveButton">Save Staff</button>
                    </div>
                </form>
            </div>
        </div>
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

                    setTimeout(() => {
                        form.submit();
                    }, 300);
                });
            }

            // NIN formatting
            const ninInput = document.getElementById('nin');
            if (ninInput) {
                ninInput.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    let formatted = '';

                    if (value.length > 0) {
                        formatted += value.substring(0, 8);
                    }
                    if (value.length >= 8) {
                        formatted += '-';
                    }
                    if (value.length > 8) {
                        formatted += value.substring(8, 13);
                    }
                    if (value.length >= 13) {
                        formatted += '-';
                    }
                    if (value.length > 13) {
                        formatted += value.substring(13, 18);
                    }
                    if (value.length >= 18) {
                        formatted += '-';
                    }
                    if (value.length > 18) {
                        formatted += value.substring(18, 20);
                    }

                    e.target.value = formatted;
                });
            }

            // Touch feedback for cards
            const cards = document.querySelectorAll('.staff-card');
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
