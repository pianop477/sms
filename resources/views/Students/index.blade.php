@extends('SRTDashboard.frame')

@section('content')
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

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

        .header-title-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 11px;
            margin: 2px 0 0;
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

        /* Stats Cards */
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

        /* Batch Update Form - Mobile */
        .batch-form-mobile {
            padding: 16px;
            background: var(--gray-100);
            margin: 0 0 16px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .form-group-mobile {
            margin-bottom: 12px;
        }

        .form-label-mobile {
            font-weight: 600;
            font-size: 12px;
            color: var(--dark);
            margin-bottom: 6px;
            display: block;
        }

        .form-select-mobile {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 13px;
            background: white;
        }

        .selected-counter-mobile {
            background: white;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 12px;
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 12px;
        }

        .btn-warning-mobile {
            background: linear-gradient(135deg, var(--warning) 0%, #e67e22 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-warning-mobile:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Student Cards Grid */
        .students-grid {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Student Card */
        .student-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            position: relative;
        }

        .student-card:active {
            transform: scale(0.98);
        }

        /* Card Selection */
        .card-selection {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 2;
        }

        .checkbox-mobile {
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        /* Card Header */
        .student-card-header {
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--gray-200);
        }

        .student-avatar-mobile {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }

        .student-info-header {
            flex: 1;
        }

        .student-name-mobile {
            font-size: 14px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .student-adm-mobile {
            font-size: 11px;
            color: var(--primary);
            font-weight: 600;
            font-family: monospace;
            background: rgba(67, 97, 238, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        /* Card Body */
        .student-card-body {
            padding: 12px;
        }

        .info-row {
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
            font-size: 10px;
        }

        .info-item-mobile .info-value {
            color: var(--dark);
            font-size: 12px;
            font-weight: 500;
        }

        /* Stream Badge */
        .stream-badge-mobile {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .stream-A {
            background: #e8f0fe;
            color: #1a73e8;
        }

        .stream-B {
            background: #e6f4ea;
            color: #0f9d58;
        }

        .stream-C {
            background: #fce8e6;
            color: #d93025;
        }

        /* Gender Badge */
        .gender-badge-mobile {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11px;
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
            padding: 8px;
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

        .card-action-btn.edit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .card-action-btn.view {
            background: linear-gradient(135deg, #36b9cc 0%, #1a8a9e 100%);
            color: white;
        }

        .card-action-btn.delete {
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

        .note-text {
            font-size: 10px;
            color: var(--gray-600);
            margin-top: 4px;
        }

        /* Select2 Mobile Fix */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 1px solid var(--gray-300) !important;
            border-radius: 10px !important;
            padding: 5px 12px !important;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .dashboard-container {
                padding: 8px;
            }

            .info-row {
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
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
            }

            .mobile-card {
                background: #2d3748;
            }

            .student-card {
                background: #2d3748;
                border-color: #4a5568;
            }

            .student-name-mobile {
                color: #f8f9fa;
            }

            .info-item-mobile i {
                background: #374151;
                color: var(--primary);
            }

            .info-item-mobile .info-value {
                color: #f8f9fa;
            }

            .batch-form-mobile {
                background: #374151;
                border-bottom-color: #4a5568;
            }

            .form-label-mobile {
                color: #f8f9fa;
            }

            .form-select-mobile {
                background: #374151;
                border-color: #4a5568;
                color: #f8f9fa;
            }

            .selected-counter-mobile {
                background: #374151;
                color: #f8f9fa;
            }

            .modal-mobile .modal-content {
                background: #2d3748;
            }

            .modal-mobile .modal-footer {
                background: #374151;
            }

            .form-control-mobile {
                background: #374151;
                border-color: #4a5568;
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
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3>{{ strtoupper($classId->class_name) }} - {{ strtoupper($classId->class_code) }}</h3>
                            <p><i class="fas fa-graduation-cap"></i> Class Management</p>
                        </div>
                    </div>
                    <div class="action-buttons">
                        @if ($students->isNotEmpty())
                            @if (auth()->user()->usertype != 5)
                                <button type="button" class="btn-mobile" data-bs-toggle="modal" data-bs-target="#promoteModal">
                                    <i class="fas fa-exchange-alt"></i> Promote
                                </button>
                            @endif

                            <div class="dropdown">
                                <button class="btn-mobile dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('students.export.excel', ['class' => Hashids::encode($classId->id)]) }}">
                                            <i class="fas fa-file-excel text-success"></i> Excel
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('export.student.pdf', ['class' => Hashids::encode($classId->id)]) }}" target="_blank">
                                            <i class="fas fa-file-pdf text-danger"></i> PDF
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif

                        <a href="{{ route('classes.list', ['class' => Hashids::encode($classId->id)]) }}" class="btn-mobile">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>

                        @if (auth()->user()->usertype != 5)
                            <button type="button" class="btn-mobile" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card-mobile">
                    <i class="fas fa-users"></i>
                    <div class="stat-label">Total</div>
                    <div class="stat-value">{{ $students->count() }}</div>
                </div>
                <div class="stat-card-mobile">
                    <i class="fas fa-male"></i>
                    <div class="stat-label">Boys</div>
                    <div class="stat-value">{{ $students->filter(fn($s) => strtolower($s->gender) === 'male')->count() }}</div>
                </div>
                <div class="stat-card-mobile">
                    <i class="fas fa-female"></i>
                    <div class="stat-label">Girls</div>
                    <div class="stat-value">{{ $students->filter(fn($s) => strtolower($s->gender) === 'female')->count() }}</div>
                </div>
            </div>

            <!-- Batch Update Form -->
            @if ($students->isNotEmpty())
            <div class="batch-form-mobile">
                <form id="batchForm" action="{{ route('students.batchUpdateStream') }}" method="POST">
                    @csrf
                    <div class="form-group-mobile">
                        <label class="form-label-mobile">Transfer to Stream</label>
                        <select name="new_stream" class="form-select-mobile" required>
                            <option value="">-- Select Stream --</option>
                            <option value="A">Stream A</option>
                            <option value="B">Stream B</option>
                            <option value="C">Stream C</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-warning-mobile" id="updateStreamBtn" disabled>
                        <i class="fas fa-random"></i> Shift Stream
                    </button>

                    <div class="selected-counter-mobile" id="selectedCount">
                        <span><i class="fas fa-check-square"></i> <span id="selectedCountText">0</span> students selected</span>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <input type="checkbox" id="selectAll" class="checkbox-mobile"> Select All
                        </label>
                    </div>
                </form>
            </div>
            @endif

            <!-- Students Grid - Mobile Cards -->
            @if ($students->isEmpty())
                <div class="empty-state-mobile">
                    <i class="fas fa-users fa-3x"></i>
                    <h6 class="mt-2">No Students Found</h6>
                    <p class="text-muted small">Tap "Add" to register your first student</p>
                </div>
            @else
                <div class="students-grid">
                    @foreach ($students as $student)
                        @php
                            $imageName = $student->image;
                            $imagePath = storage_path('app/public/students/' . $imageName);
                            $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                ? asset('storage/students/' . $imageName)
                                : asset('storage/students/student.jpg');
                        @endphp

                        <div class="student-card">
                            <div class="card-selection">
                                <input type="checkbox" name="student[]" value="{{ $student->id }}"
                                    class="student-checkbox checkbox-mobile">
                            </div>

                            <div class="student-card-header">
                                <img src="{{ $avatarImage }}" class="student-avatar-mobile" alt="Student">
                                <div class="student-info-header">
                                    <div class="student-name-mobile">{{ ucwords(strtolower($student->first_name)) }} {{ ucwords(strtolower($student->middle_name)) }} {{ ucwords(strtolower($student->last_name)) }}</div>
                                    <div class="student-adm-mobile">{{ $student->admission_number }}</div>
                                </div>
                            </div>

                            <div class="student-card-body">
                                <div class="info-row">
                                    <div class="info-item-mobile">
                                        <i class="fas fa-venus-mars"></i>
                                        <div>
                                            <div class="info-label">Gender</div>
                                            <div class="info-value">
                                                <span class="gender-badge-mobile {{ strtolower($student->gender) == 'male' ? 'gender-male-badge' : 'gender-female-badge' }}">
                                                    <i class="fas {{ strtolower($student->gender) == 'male' ? 'fa-mars' : 'fa-venus' }}"></i>
                                                    {{ ucfirst($student->gender) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-layer-group"></i>
                                        <div>
                                            <div class="info-label">Stream</div>
                                            <div class="info-value">
                                                <span class="stream-badge-mobile stream-{{ strtoupper($student->group) }}">
                                                    Stream {{ strtoupper($student->group) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-calendar"></i>
                                        <div>
                                            <div class="info-label">Date of Birth</div>
                                            <div class="info-value">{{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-id-card"></i>
                                        <div>
                                            <div class="info-label">Parent ID</div>
                                            <div class="info-value">{{ $student->parent_id ?? 'Not assigned' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="card-actions">
                                    <a href="{{ route('students.modify', ['students' => Hashids::encode($student->id)]) }}"
                                       class="card-action-btn edit">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                    <a href="{{ route('manage.student.profile', ['student' => Hashids::encode($student->id)]) }}"
                                       class="card-action-btn view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if (auth()->user()->usertype != 5)
                                        <form method="POST"
                                            action="{{ route('Students.destroy', ['student' => Hashids::encode($student->id)]) }}"
                                            class="d-inline" style="flex: 1">
                                            @csrf
                                            <button type="submit" class="card-action-btn delete"
                                                onclick="return confirm('Move {{ strtoupper($student->first_name) }} to trash?')">
                                                <i class="fas fa-trash"></i> Delete
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

    <!-- Promote Modal -->
    <div class="modal fade modal-mobile" id="promoteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Promote Students</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Select class to promote students to</p>
                    <form action="{{ route('promote.student.class', ['class' => Hashids::encode($classId->id)]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Class Name <span class="text-danger">*</span></label>
                            <select name="class_id" id="classSelect" class="form-select-mobile" required>
                                <option value="">-- Select Class --</option>
                                @if ($classes->isEmpty())
                                    <option value="" disabled>No more classes found</option>
                                    <option value="0" class="text-success fw-bold">🎓 Graduate Class 🎉</option>
                                @else
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                    <option value="0" class="text-success fw-bold">🎓 Graduate Class 🎉</option>
                                @endif
                            </select>
                        </div>

                        <div id="graduationYearField" class="form-group-mobile" style="display: none;">
                            <label class="form-label-mobile">Graduation Year <span class="text-danger">*</span></label>
                            <input type="number" name="graduation_year" id="graduation_year"
                                class="form-control-mobile" placeholder="e.g 2025"
                                min="{{ date('Y') - 5 }}" max="{{ date('Y') + 5 }}">
                            <div class="note-text">Enter graduation year</div>
                        </div>

                        <div class="modal-footer px-0 pb-0 mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" onclick="return confirm('Promote this class?')">Promote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade modal-mobile" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student - {{ $classId->class_name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('student.store', ['class' => Hashids::encode($classId->id)]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group-mobile">
                            <label class="form-label-mobile">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-mobile" name="fname" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Middle Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-mobile" name="middle" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-mobile" name="lname" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Gender <span class="text-danger">*</span></label>
                            <select class="form-select-mobile" name="gender" required>
                                <option value="">-- Select --</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control-mobile" name="dob" required>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Parent/Guardian <span class="text-danger">*</span></label>
                            <select name="parent" id="parentSelect" class="form-select-mobile" required>
                                <option value="">-- Select --</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}">
                                        {{ ucwords($parent->first_name . ' ' . $parent->last_name) }} - {{ $parent->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Stream <span class="text-danger">*</span></label>
                            <select class="form-select-mobile" name="group" required>
                                <option value="">-- Select --</option>
                                <option value="a">Stream A</option>
                                <option value="b">Stream B</option>
                                <option value="c">Stream C</option>
                            </select>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Bus Number</label>
                            <select name="driver" class="form-select-mobile">
                                <option value="">-- None --</option>
                                @foreach ($buses as $bus)
                                    <option value="{{ $bus->id }}">Bus No. {{ $bus->bus_no }}</option>
                                @endforeach
                            </select>
                            <div class="note-text">Optional - if using school bus</div>
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Photo</label>
                            <input type="file" class="form-control-mobile" name="image" accept="image/*">
                            <div class="note-text">Optional - Max 2MB</div>
                        </div>

                        <div class="modal-footer px-0 pb-0 mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Batch form variables
            const batchForm = document.getElementById('batchForm');
            const updateStreamBtn = document.getElementById('updateStreamBtn');
            const selectAllCheckbox = document.getElementById('selectAll');
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');
            const selectedCountText = document.getElementById('selectedCountText');

            // Update selection state
            function updateSelectionState() {
                const selectedStudents = document.querySelectorAll('.student-checkbox:checked');
                const count = selectedStudents.length;

                if (selectedCountText) {
                    selectedCountText.textContent = count;
                }

                if (updateStreamBtn) {
                    updateStreamBtn.disabled = count === 0;
                }

                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = count > 0 && count === studentCheckboxes.length;
                    selectAllCheckbox.indeterminate = count > 0 && count < studentCheckboxes.length;
                }
            }

            // Select all
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    studentCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateSelectionState();
                });
            }

            // Individual checkboxes
            studentCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectionState));

            // Batch form submit
            if (batchForm) {
                batchForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const selected = document.querySelectorAll('.student-checkbox:checked');
                    const newStream = document.querySelector('select[name="new_stream"]').value;

                    if (selected.length === 0) {
                        alert('Select at least one student');
                        return;
                    }

                    if (!newStream) {
                        alert('Select a stream');
                        return;
                    }

                    if (confirm(`Move ${selected.length} student(s) to Stream ${newStream}?`)) {
                        selected.forEach(cb => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'students[]';
                            input.value = cb.value;
                            batchForm.appendChild(input);
                        });
                        this.submit();
                    }
                });
            }

            // Graduation year field
            const classSelect = document.getElementById('classSelect');
            const gradYearField = document.getElementById('graduationYearField');
            const gradYearInput = document.getElementById('graduation_year');

            if (classSelect) {
                classSelect.addEventListener('change', function() {
                    if (this.value === '0') {
                        gradYearField.style.display = 'block';
                        gradYearInput.setAttribute('required', 'required');
                    } else {
                        gradYearField.style.display = 'none';
                        gradYearInput.removeAttribute('required');
                        gradYearInput.value = '';
                    }
                });
            }

            // Initialize
            updateSelectionState();
        });
    </script>
@endsection
