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
            grid-template-columns: repeat(2, 1fr);
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

        /* Parents Grid */
        .parents-grid {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Parent Card */
        .parent-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .parent-card:active {
            transform: scale(0.98);
        }

        /* Card Header */
        .parent-card-header {
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(135deg, var(--gray-100) 0%, white 100%);
        }

        .parent-avatar-mobile {
            width: 50px;
            height: 50px;
            border-radius: 25px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
            box-shadow: var(--shadow-sm);
        }

        .parent-info-header {
            flex: 1;
        }

        .parent-name-mobile {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        /* Status Badge */
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
        .parent-card-body {
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

        /* Phone/Email Links */
        .contact-link {
            color: var(--dark);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
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

        /* Form Sections */
        .form-section-mobile {
            background: var(--gray-100);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 16px;
        }

        .section-title-mobile {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--gray-200);
        }

        /* Form Controls */
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

        .note-text {
            font-size: 10px;
            color: var(--gray-600);
            margin-top: 4px;
        }

        /* File Input */
        .file-input-mobile {
            border: 2px dashed var(--primary);
            padding: 16px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
        }

        .file-input-mobile input {
            display: none;
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

            .parent-card {
                background: #2d3748;
                border-color: #4a5568;
            }

            .parent-card-header {
                background: #374151;
                border-bottom-color: #4a5568;
            }

            .parent-name-mobile {
                color: #f8f9fa;
            }

            .info-item-mobile i {
                background: #374151;
                color: var(--primary);
            }

            .info-item-mobile .info-value {
                color: #f8f9fa;
            }

            .contact-link {
                color: #f8f9fa;
            }

            .form-section-mobile {
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

            .modal-mobile .modal-content {
                background: #2d3748;
            }

            .modal-mobile .modal-footer {
                background: #374151;
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
                            <h3>Parents Management</h3>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn-mobile" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-import"></i> Import
                        </button>
                        <button type="button" class="btn-mobile" data-bs-toggle="modal" data-bs-target="#parentModal">
                            <i class="fas fa-user-plus"></i> Add
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            @php
                $activeCount = $parents->filter(fn($p) => $p->status == 1)->count();
                $maleCount = $parents->filter(fn($p) => strtolower($p->gender) == 'male')->count();
                $femaleCount = $parents->filter(fn($p) => strtolower($p->gender) == 'female')->count();
            @endphp
            <div class="stats-grid">
                <div class="stat-card-mobile">
                    <i class="fas fa-user-check"></i>
                    <div class="stat-label">Active</div>
                    <div class="stat-value">{{ $activeCount }}</div>
                </div>
                <div class="stat-card-mobile">
                    <i class="fas fa-male"></i>
                    <div class="stat-label">Male</div>
                    <div class="stat-value">{{ $maleCount }}</div>
                </div>
                <div class="stat-card-mobile">
                    <i class="fas fa-female"></i>
                    <div class="stat-label">Female</div>
                    <div class="stat-value">{{ $femaleCount }}</div>
                </div>
                <div class="stat-card-mobile">
                    <i class="fas fa-users"></i>
                    <div class="stat-label">Total</div>
                    <div class="stat-value">{{ $parents->count() }}</div>
                </div>
            </div>

            <!-- Parents Grid -->
            @if ($parents->isEmpty())
                <div class="empty-state-mobile">
                    <i class="fas fa-users"></i>
                    <h6 class="mt-2">No Parents Found</h6>
                    <p class="text-muted small">Tap "Add" to register your first parent</p>
                </div>
            @else
                <div class="parents-grid">
                    @foreach ($parents as $parent)
                        <div class="parent-card">
                            <!-- Card Header -->
                            <div class="parent-card-header">
                                <div class="parent-avatar-mobile">
                                    {{ strtoupper(substr($parent->first_name, 0, 1)) }}{{ strtoupper(substr($parent->last_name, 0, 1)) }}
                                </div>
                                <div class="parent-info-header">
                                    <div class="parent-name-mobile">{{ ucwords(strtolower($parent->first_name . ' ' . $parent->last_name)) }}</div>
                                </div>
                                @if ($parent->status == 1)
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
                            <div class="parent-card-body">
                                <div class="info-grid">
                                    <div class="info-item-mobile">
                                        <i class="fas fa-venus-mars"></i>
                                        <div>
                                            <div class="info-label">Gender</div>
                                            <div class="info-value">
                                                <span class="gender-badge-mobile {{ strtolower($parent->gender) == 'male' ? 'gender-male-badge' : 'gender-female-badge' }}">
                                                    <i class="fas {{ strtolower($parent->gender) == 'male' ? 'fa-mars' : 'fa-venus' }}"></i>
                                                    {{ ucfirst($parent->gender) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-phone"></i>
                                        <div>
                                            <div class="info-label">Phone</div>
                                            <div class="info-value">
                                                <a href="tel:{{ $parent->phone }}" class="contact-link">
                                                    <i class="fas fa-phone-alt"></i> {{ $parent->phone }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-envelope"></i>
                                        <div>
                                            <div class="info-label">Email</div>
                                            <div class="info-value">
                                                @if($parent->email)
                                                    <a href="mailto:{{ $parent->email }}" class="contact-link">
                                                        <i class="fas fa-envelope"></i> {{ substr($parent->email, 0, 20) }}{{ strlen($parent->email) > 20 ? '...' : '' }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <div>
                                            <div class="info-label">Location</div>
                                            <div class="info-value">{{ $parent->street ?? 'Not specified' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="card-actions">
                                    <a href="{{ route('Parents.edit', ['parent' => Hashids::encode($parent->id)]) }}"
                                       class="card-action-btn view">
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    @if ($parent->status == 1)
                                        <form action="{{ route('Update.parents.status', ['parent' => Hashids::encode($parent->id)]) }}"
                                              method="POST"
                                              class="d-inline"
                                              style="flex: 1"
                                              onsubmit="return confirm('Block {{ $parent->first_name }} {{ $parent->last_name }}?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="card-action-btn warning">
                                                <i class="fas fa-ban"></i> Block
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('restore.parents.status', ['parent' => Hashids::encode($parent->id)]) }}"
                                              method="POST"
                                              class="d-inline"
                                              style="flex: 1"
                                              onsubmit="return confirm('Unblock {{ $parent->first_name }} {{ $parent->last_name }}?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="card-action-btn success">
                                                <i class="fas fa-check"></i> Unblock
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('Parents.remove', ['parent' => Hashids::encode($parent->id)]) }}"
                                          method="POST"
                                          class="d-inline"
                                          style="flex: 1"
                                          onsubmit="return confirm('Delete {{ $parent->first_name }} {{ $parent->last_name }}? This cannot be undone.')">
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

    <!-- Import Modal (Simplified for Mobile) -->
    <div class="modal fade modal-mobile" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-import me-2"></i>Import Parents</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                        <p class="small text-muted">Upload Excel file (.xlsx, .xls, .csv)</p>
                    </div>
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="file-input-mobile">
                            <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv">
                            <label for="fileInput" class="d-block">
                                <i class="fas fa-file-excel fa-2x text-success mb-2 d-block"></i>
                                <span class="fw-bold">Click to browse</span>
                                <span class="small text-muted d-block">Max 2MB</span>
                            </label>
                        </div>
                        <div id="fileError" class="text-danger small mt-2 text-center d-none"></div>
                        <div class="mt-3 text-center">
                            <a href="{{ route('parent.template.export') }}" class="text-decoration-none small">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="uploadButton" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Registration Modal -->
    <div class="modal fade modal-mobile" id="parentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Register Parent</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form class="needs-validation" novalidate action="{{ route('Parents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Parent Information -->
                        <div class="form-section-mobile">
                            <h6 class="section-title-mobile">
                                <i class="fas fa-user"></i> Parent Info
                            </h6>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">First Name <span class="required">*</span></label>
                                <input type="text" name="fname" class="form-control-mobile" required>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Last Name <span class="required">*</span></label>
                                <input type="text" name="lname" class="form-control-mobile" required>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Gender <span class="required">*</span></label>
                                <select name="gender" class="form-select-mobile" required>
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Phone <span class="required">*</span></label>
                                <input type="tel" name="phone" class="form-control-mobile" required>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Email</label>
                                <input type="email" name="email" class="form-control-mobile">
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Street/Village <span class="required">*</span></label>
                                <input type="text" name="street" class="form-control-mobile" required>
                            </div>
                        </div>

                        <!-- Student Information -->
                        <div class="form-section-mobile">
                            <h6 class="section-title-mobile">
                                <i class="fas fa-user-graduate"></i> Student Info
                            </h6>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">First Name <span class="required">*</span></label>
                                <input type="text" name="student_first_name" class="form-control-mobile" required>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Middle Name <span class="required">*</span></label>
                                <input type="text" name="student_middle_name" class="form-control-mobile" required>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Last Name <span class="required">*</span></label>
                                <input type="text" name="student_last_name" class="form-control-mobile" required>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Gender <span class="required">*</span></label>
                                <select name="student_gender" class="form-select-mobile" required>
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Date of Birth <span class="required">*</span></label>
                                <input type="date" name="dob" class="form-control-mobile" required>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Class <span class="required">*</span></label>
                                <select name="class" class="form-select-mobile" required>
                                    <option value="">Select</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Stream <span class="required">*</span></label>
                                <select name="group" class="form-select-mobile" required>
                                    <option value="">Select</option>
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                </select>
                            </div>
                            <div class="form-group-mobile">
                                <label class="form-label-mobile">Bus Number</label>
                                <select name="bus_no" class="form-select-mobile">
                                    <option value="">None</option>
                                    @foreach ($buses as $bus)
                                        <option value="{{ $bus->id }}">Bus {{ $bus->bus_no }}</option>
                                    @endforeach
                                </select>
                                <div class="note-text">Optional</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="saveButton">Save Parent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Form validation
            const forms = document.querySelectorAll(".needs-validation");
            forms.forEach(form => {
                const submitButton = form.querySelector('button[type="submit"]');
                if (form && submitButton) {
                    form.addEventListener("submit", function(event) {
                        event.preventDefault();

                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

                        if (!form.checkValidity()) {
                            form.classList.add("was-validated");
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Save Parent';
                            return;
                        }

                        setTimeout(() => form.submit(), 500);
                    });
                }
            });

            // Import functionality
            const fileInput = document.getElementById('fileInput');
            const uploadButton = document.getElementById('uploadButton');
            const fileError = document.getElementById('fileError');

            if (uploadButton && fileInput) {
                uploadButton.addEventListener('click', function() {
                    if (!fileInput.files.length) {
                        showFileError('Please select a file');
                        return;
                    }

                    const file = fileInput.files[0];
                    const maxSize = 2 * 1024 * 1024;

                    if (file.size > maxSize) {
                        showFileError('File size exceeds 2MB');
                        return;
                    }

                    if (!file.name.match(/\.(xlsx|xls|csv)$/i)) {
                        showFileError('Please select an Excel file');
                        return;
                    }

                    uploadButton.disabled = true;
                    uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route('import.parents.students') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message || 'File processed successfully',
                                timer: 2000
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Import Failed',
                                text: data.message || 'Failed to process file'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Network error. Please try again.'
                        });
                    })
                    .finally(() => {
                        uploadButton.disabled = false;
                        uploadButton.innerHTML = 'Upload';
                    });
                });
            }

            function showFileError(message) {
                if (fileError) {
                    if (message) {
                        fileError.textContent = message;
                        fileError.classList.remove('d-none');
                    } else {
                        fileError.classList.add('d-none');
                    }
                }
            }

            // Touch feedback for cards
            const cards = document.querySelectorAll('.parent-card');
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
