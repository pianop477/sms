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
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 70% 30%, rgba(67, 97, 238, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 30% 70%, rgba(63, 55, 201, 0.1) 0%, transparent 30%);
            animation: rotate 60s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(100px, -100px) scale(1.2); }
            50% { transform: translate(200px, 0) scale(0.8); }
            75% { transform: translate(100px, 100px) scale(1.1); }
        }

        /* Main Container */
        .dashboard-container {
            max-width: 1600px;
            margin: 30px auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Modern Card */
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Card Header */
        .card-header-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 20px 25px;
            position: relative;
            overflow: visible;
        }

        .card-header-modern::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 60%);
            /* animation: rotate 20s linear infinite; */
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-title {
            color: white;
            margin: 0;
        }

        .header-title h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .header-title p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            position: relative;
            z-index: 100;
        }

        .btn-modern {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            color: white;
            white-space: nowrap;
        }

        .btn-import {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-import:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .btn-add {
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .btn-add:hover {
            background: rgba(255, 255, 255, 0.35);
        }

        /* Card Body */
        .card-body-modern {
            padding: 25px;
        }

        /* Table Container */
        .table-container-modern {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Modern Table */
        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #2b3d5c 0%, #1a2a44 100%);
            /* color: white; */
            font-weight: 600;
            padding: 12px 12px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 12px 12px;
            border-bottom: 1px solid #edf2f7;
            color: #4a5568;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table-modern tbody tr {
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: #f7fafc;
        }

        /* Parent Info */
        .parent-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .parent-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .parent-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }

        /* Gender Badge */
        .gender-badge {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .gender-male {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .gender-female {
            background: linear-gradient(135deg, #e83e8c 0%, #c2185b 100%);
        }

        /* Phone Link */
        .phone-link {
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .phone-link i {
            font-size: 0.8rem;
        }

        /* Email Text */
        .email-text {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.85rem;
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .status-active {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-blocked {
            background: #fed7d7;
            color: #742a2a;
        }

        /* Action Icons */
        .action-icons {
            display: flex;
            gap: 4px;
            justify-content: center;
            flex-wrap: nowrap;
        }

        .action-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .action-icon.view {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .action-icon.warning {
            background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%);
        }

        .action-icon.success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }

        .action-icon.danger {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        }

        .action-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Empty State */
        .empty-state-modern {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
            border-radius: 16px;
            border: 2px dashed #ffc107;
        }

        .empty-state-modern i {
            font-size: 50px;
            color: #ffc107;
            margin-bottom: 15px;
        }

        /* Modal Styles */
        .modal-modern .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px 20px;
        }

        .modal-modern .modal-body {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-modern .modal-footer {
            border: none;
            padding: 15px 20px;
            background: #f8f9fa;
        }

        /* Form Sections */
        .form-section-modern {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .section-title-modern {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }

        /* Form Controls */
        .form-group-modern {
            margin-bottom: 15px;
        }

        .form-label-modern {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 4px;
            font-size: 0.85rem;
        }

        .form-label-modern .required {
            color: var(--danger);
        }

        .form-control-modern {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-modern:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        .note-text {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
        }

        /* Import Modal Specific */
        .import-preview-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            margin-top: 15px;
        }

        .import-preview-container table {
            margin-bottom: 0;
        }

        .import-preview-container thead th {
            position: sticky;
            top: 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            z-index: 10;
        }

        .import-preview-container::-webkit-scrollbar {
            width: 8px;
        }

        .import-preview-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .import-preview-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .import-preview-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Stats Cards */
        .stats-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .stat-card-modern {
            flex: 1;
            min-width: 150px;
            background: white;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .stat-card-modern:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* File Input */
        .file-input-modern {
            border: 2px dashed var(--primary);
            padding: 20px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .file-input-modern:hover {
            background: rgba(67, 97, 238, 0.05);
        }

        .file-input-modern input[type="file"] {
            display: none;
        }

        .file-input-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        /* Import Overlay */
        .import-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .overlay-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }

        .overlay-progress {
            margin: 20px 0;
        }

        .progress {
            height: 25px;
            border-radius: 12px;
            background: #e9ecef;
        }

        .progress-bar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Table disabled state */
        .table-disabled {
            opacity: 0.5;
            pointer-events: none;
            user-select: none;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .table-modern {
                display: block;
                overflow-x: auto;
            }

            .action-icons {
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .action-group {
                justify-content: stretch;
            }

            .btn-modern {
                flex: 1;
                justify-content: center;
            }

            .stats-row {
                flex-direction: column;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
            }

            .modern-card {
                background: rgba(33, 37, 41, 0.95);
            }

            .table-modern tbody td {
                color: #e9ecef;
                border-bottom-color: #495057;
            }

            .table-modern tbody tr:hover {
                background: #343a40;
            }

            .parent-name {
                color: #e9ecef;
            }

            .email-text {
                color: #adb5bd;
            }

            .status-active {
                background: #22543d;
                color: #c6f6d5;
            }

            .status-blocked {
                background: #742a2a;
                color: #fed7d7;
            }

            .form-section-modern {
                background: #2b3035;
                border-color: #495057;
            }

            .form-control-modern {
                background: #2b3035;
                border-color: #495057;
                color: #e9ecef;
            }

            .stat-card-modern {
                background: #2b3035;
                border-color: #495057;
            }

            .stat-label {
                color: #adb5bd;
            }

            .import-overlay {
                background: rgba(33, 37, 41, 0.95);
            }

            .overlay-content {
                background: #2b3035;
                color: #e9ecef;
            }
        }
    </style>

    <div class="animated-bg"></div>
    <div class="particles"></div>
    <div class="loading-spinner" id="loadingSpinner"></div>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Header -->
            <div class="card-header-modern">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="header-title">
                            <h3>Parents Management</h3>
                        </div>
                    </div>
                    <div class="action-group">
                        <!-- Import Button -->
                        <button type="button" class="btn-modern btn-import" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-import"></i>
                            <span>Import</span>
                        </button>

                        <!-- Add Parent Button -->
                        <button type="button" class="btn-modern btn-add" data-bs-toggle="modal" data-bs-target="#parentModal">
                            <i class="fas fa-user-plus"></i>
                            <span>Add Parent</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                @if ($parents->isEmpty())
                    <div class="empty-state-modern">
                        <i class="fas fa-users"></i>
                        <h6>No Parents Found</h6>
                        <p class="text-muted small">Click "Add Parent" to register</p>
                    </div>
                @else
                    <div class="table-container-modern">
                        <table class="table-modern" id="myTable">
                            <thead>
                                <tr>
                                    <th class="text-white">#</th>
                                    <th>Parent</th>
                                    <th>Gender</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parents as $parent)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="parent-info">
                                                <div class="parent-avatar">
                                                    {{ strtoupper(substr($parent->first_name, 0, 1)) }}{{ strtoupper(substr($parent->last_name, 0, 1)) }}
                                                </div>
                                                <span class="parent-name">{{ ucwords(strtolower($parent->first_name . ' ' . $parent->last_name)) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="gender-badge {{ strtolower($parent->gender) == 'male' ? 'gender-male' : 'gender-female' }}">
                                                {{ strtoupper(substr($parent->gender, 0, 1)) }}
                                            </div>
                                        </td>
                                        <td>
                                            <a href="tel:{{ $parent->phone }}" class="phone-link">
                                                <i class="fas fa-phone"></i>
                                                {{ $parent->phone }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="email-text" title="{{ $parent->email ?? 'N/A' }}">
                                                {{ $parent->email ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($parent->status == 1)
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-circle"></i> Active
                                                </span>
                                            @else
                                                <span class="status-badge status-blocked">
                                                    <i class="fas fa-circle"></i> Blocked
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                                <a href="{{ route('Parents.edit', ['parent' => Hashids::encode($parent->id)]) }}"
                                                   class="action-icon view"
                                                   title="View Profile">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if ($parent->status == 1)
                                                    <form action="{{ route('Update.parents.status', ['parent' => Hashids::encode($parent->id)]) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Block {{ $parent->first_name }} {{ $parent->last_name }}?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon warning" title="Block">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('restore.parents.status', ['parent' => Hashids::encode($parent->id)]) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Unblock {{ $parent->first_name }} {{ $parent->last_name }}?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon success" title="Unblock">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('Parents.remove', ['parent' => Hashids::encode($parent->id)]) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Delete {{ $parent->first_name }} {{ $parent->last_name }}? This cannot be undone.')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="action-icon danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade modal-modern" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-import me-2"></i>
                        Import Parents Data
                    </h5>
                    <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal"><i class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Upload -->
                    <div id="uploadStep">
                        <div class="text-center mb-4">
                            <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
                            <h5>Upload Excel File</h5>
                            <p class="text-muted small">Supported formats: .xlsx, .xls, .csv (Max: 2MB)</p>
                        </div>

                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="file-input-modern">
                                <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" required>
                                <label for="fileInput" class="file-input-label">
                                    <i class="fas fa-file-excel fa-3x text-success"></i>
                                    <span class="fw-bold">Click to browse or drag & drop</span>
                                    <span class="small text-muted">Excel file only</span>
                                </label>
                            </div>
                            <div id="fileError" class="text-danger small mt-2 text-center d-none"></div>

                            <div class="mt-3 text-center">
                                <p class="mb-0">Need a template?
                                    <a href="{{ route('parent.template.export') }}" class="text-decoration-none">
                                        <i class="fas fa-download me-1"></i> Download Sample
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: Preview -->
                    <div id="previewStep" class="d-none">
                        <!-- Stats Cards -->
                        <div class="stats-row">
                            <div class="stat-card-modern">
                                <div class="stat-value" id="totalRows">0</div>
                                <div class="stat-label">
                                    <i class="fas fa-file-archive text-primary"></i>
                                    Total Rows
                                </div>
                            </div>
                            <div class="stat-card-modern">
                                <div class="stat-value text-success" id="validRows">0</div>
                                <div class="stat-label">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Valid Data
                                </div>
                            </div>
                            <div class="stat-card-modern">
                                <div class="stat-value text-danger" id="invalidRows">0</div>
                                <div class="stat-label">
                                    <i class="fas fa-exclamation-circle text-danger"></i>
                                    Errors
                                </div>
                            </div>
                            <div class="stat-card-modern">
                                <button type="button" id="startImportBtn" class="btn btn-success w-100" disabled>
                                    <i class="fas fa-cloud-upload-alt me-1"></i> Import All
                                </button>
                            </div>
                        </div>

                        <!-- Errors Display -->
                        <div id="errorsContainer" class="d-none">
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors</h6>
                                <ul id="errorsList" class="mb-0 small"></ul>
                            </div>
                        </div>

                        <!-- Preview Table -->
                        <div class="import-preview-container">
                            <table class="table table-hover table-bordered mb-0" id="previewTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Parent Name</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Student Name</th>
                                        <th>Student Gender</th>
                                        <th>Class</th>
                                        <th>Stream</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody"></tbody>
                            </table>
                        </div>

                        <div id="tableInfo" class="text-muted small mt-2 text-center d-none">
                            <i class="fas fa-info-circle me-1"></i>
                            <span id="rowCount">0</span> records displayed
                        </div>

                        <!-- Import Progress -->
                        <div id="importProgress" class="d-none mt-4">
                            <div class="progress">
                                <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                     role="progressbar" style="width: 0%">0%</div>
                            </div>
                            <div class="mt-2 text-center">
                                <span id="importStatus">Preparing import...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="uploadButton" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload & Preview
                    </button>
                    <button type="button" id="backButton" class="btn btn-secondary d-none">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Registration Modal -->
    <div class="modal fade modal-modern" id="parentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>
                        Parent Registration Form
                    </h5>
                    <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal"><i class="fas fa-close"></i></button>
                </div>
                <form class="needs-validation" novalidate action="{{ route('Parents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Parent Information -->
                        <div class="form-section-modern">
                            <h6 class="section-title-modern">
                                <i class="fas fa-user"></i>
                                Parent/Guardian Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">First Name <span class="required">*</span></label>
                                        <input type="text" name="fname" class="form-control-modern" value="{{ old('fname') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Last Name <span class="required">*</span></label>
                                        <input type="text" name="lname" class="form-control-modern" value="{{ old('lname') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Gender <span class="required">*</span></label>
                                        <select name="gender" class="form-control-modern" required>
                                            <option value="">Select</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Phone <span class="required">*</span></label>
                                        <input type="text" name="phone" class="form-control-modern" value="{{ old('phone') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Email</label>
                                        <input type="email" name="email" class="form-control-modern" value="{{ old('email') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Street/Village <span class="required">*</span></label>
                                        <input type="text" name="street" class="form-control-modern" value="{{ old('street') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Student Information -->
                        <div class="form-section-modern">
                            <h6 class="section-title-modern">
                                <i class="fas fa-user-graduate"></i>
                                Student Information
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">First Name <span class="required">*</span></label>
                                        <input type="text" name="student_first_name" class="form-control-modern" value="{{ old('student_first_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Middle Name <span class="required">*</span></label>
                                        <input type="text" name="student_middle_name" class="form-control-modern" value="{{ old('student_middle_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Last Name <span class="required">*</span></label>
                                        <input type="text" name="student_last_name" class="form-control-modern" value="{{ old('student_last_name') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Gender <span class="required">*</span></label>
                                        <select name="student_gender" class="form-control-modern" required>
                                            <option value="">Select</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Date of Birth <span class="required">*</span></label>
                                        <input type="date" name="dob" class="form-control-modern" value="{{ old('dob') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Class <span class="required">*</span></label>
                                        <select name="class" class="form-control-modern" required>
                                            <option value="">Select</option>
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Stream <span class="required">*</span></label>
                                        <select name="group" class="form-control-modern" required>
                                            <option value="">Select</option>
                                            <option value="a">A</option>
                                            <option value="b">B</option>
                                            <option value="c">C</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Bus Number</label>
                                        <select name="bus_no" class="form-control-modern">
                                            <option value="">Select</option>
                                            @foreach ($buses as $bus)
                                                <option value="{{ $bus->id }}">Bus No. {{ $bus->bus_no }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Student Photo</label>
                                        <input type="file" name="passport" class="form-control-modern" accept="image/*">
                                        <div class="note-text">Max 1MB - Blue background</div>
                                    </div>
                                </div>
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

    <!-- Import Overlay (Hidden by default) -->
    <div id="importOverlay" class="import-overlay d-none">
        <div class="overlay-content">
            <div class="text-center mb-3">
                <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
                <h5>Importing Records</h5>
            </div>
            <div class="overlay-progress">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" id="overlayProgress" style="width: 0%">
                        <span class="progress-text">0%</span>
                    </div>
                </div>
            </div>
            <div class="status-text">
                <i class="fas fa-sync-alt fa-spin me-2"></i>
                <span id="overlayStatus">Starting import process...</span>
            </div>
            <div class="mt-4">
                <button class="btn btn-sm btn-outline-secondary" id="cancelImportBtn">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ========== PART 1: Form Validation ==========
            const forms = document.querySelectorAll(".needs-validation");
            forms.forEach(form => {
                const submitButton = form.querySelector('button[type="submit"]');
                if (form && submitButton) {
                    form.addEventListener("submit", function(event) {
                        event.preventDefault();

                        submitButton.disabled = true;
                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

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

            // ========== PART 2: Import Functionality ==========
            const uploadForm = document.getElementById('uploadForm');
            const fileInput = document.getElementById('fileInput');
            const uploadButton = document.getElementById('uploadButton');
            const backButton = document.getElementById('backButton');
            const startImportBtn = document.getElementById('startImportBtn');
            const uploadStep = document.getElementById('uploadStep');
            const previewStep = document.getElementById('previewStep');
            const previewTableBody = document.getElementById('previewTableBody');
            const errorsContainer = document.getElementById('errorsContainer');
            const errorsList = document.getElementById('errorsList');
            const importProgress = document.getElementById('importProgress');
            const importProgressBar = document.getElementById('importProgressBar');
            const importStatus = document.getElementById('importStatus');
            const importOverlay = document.getElementById('importOverlay');
            const overlayProgress = document.getElementById('overlayProgress');
            const overlayStatus = document.getElementById('overlayStatus');

            // File input change handler
            fileInput.addEventListener('change', function() {
                if (!this.files.length) {
                    showFileError('');
                    return;
                }

                const file = this.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (file.size > maxSize) {
                    showFileError('File size exceeds 2MB limit');
                    return;
                }

                if (!file.name.match(/\.(xlsx|xls|csv)$/i)) {
                    showFileError('Please select an Excel file (.xlsx, .xls, .csv)');
                    return;
                }

                uploadButton.disabled = true;
                uploadButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                showFileError('');

                uploadAndPreviewFile(file);
            });

            // Upload button click
            uploadButton.addEventListener('click', function() {
                if (!fileInput.files.length) {
                    showFileError('Please select a file');
                    return;
                }
                fileInput.dispatchEvent(new Event('change'));
            });

            // Upload and preview function
            function uploadAndPreviewFile(file) {
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
                        showPreview(data);
                    } else {
                        showFileError(data.message || 'Failed to process file');
                    }
                    resetUploadButton();
                })
                .catch(error => {
                    showFileError('Network error. Please try again.');
                    console.error('Error:', error);
                    resetUploadButton();
                });
            }

            // Show preview function
            function showPreview(data) {
                document.getElementById('totalRows').textContent = data.total_rows;
                document.getElementById('validRows').textContent = data.valid_rows;
                document.getElementById('invalidRows').textContent = data.invalid_rows;

                if (data.errors && data.errors.length > 0) {
                    errorsContainer.classList.remove('d-none');
                    errorsList.innerHTML = data.errors.map(err => `<li>${err}</li>`).join('');
                } else {
                    errorsContainer.classList.add('d-none');
                }

                previewTableBody.innerHTML = '';
                if (data.preview_data && data.preview_data.length > 0) {
                    data.preview_data.forEach((row, index) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${index + 1}</td>
                            <td class="fw-bold">${row.parent_name}</td>
                            <td><span class="badge bg-info text-white">${row.parent_gender[0]}</span></td>
                            <td>${row.parent_phone}</td>
                            <td>${row.parent_email}</td>
                            <td>${row.student_name}</td>
                            <td><span class="badge bg-secondary text-white">${row.student_gender[0]}</span></td>
                            <td>${row.class_name}</td>
                            <td><span class="badge bg-primary">${row.student_group}</span></td>
                            <td><span class="badge bg-warning">Pending</span></td>
                        `;
                        previewTableBody.appendChild(tr);
                    });

                    document.getElementById('rowCount').textContent = data.preview_data.length;
                    document.getElementById('tableInfo').classList.remove('d-none');
                }

                uploadStep.classList.add('d-none');
                previewStep.classList.remove('d-none');
                uploadButton.classList.add('d-none');
                backButton.classList.remove('d-none');

                startImportBtn.disabled = data.valid_rows === 0;
                startImportBtn.innerHTML = `<i class="fas fa-cloud-upload-alt me-1"></i> Import ${data.valid_rows} Records`;
            }

            // Back button
            backButton.addEventListener('click', function() {
                previewStep.classList.add('d-none');
                uploadStep.classList.remove('d-none');
                uploadButton.classList.remove('d-none');
                backButton.classList.add('d-none');
                importProgress.classList.add('d-none');
                fileInput.value = '';
                showFileError('');
                previewTableBody.innerHTML = '';
                document.getElementById('tableInfo').classList.add('d-none');
            });

            // Start import
            startImportBtn.addEventListener('click', function() {
                const validRows = document.getElementById('validRows').textContent;
                if (confirm(`Import ${validRows} records?`)) {
                    showImportOverlay();
                    processImport();
                }
            });

            // Show import overlay
            function showImportOverlay() {
                importOverlay.classList.remove('d-none');
                document.querySelector('.table-container-modern')?.classList.add('table-disabled');
                updateOverlayProgress(0, 'Starting import...');
            }

            // Hide import overlay
            function hideImportOverlay() {
                importOverlay.classList.add('d-none');
                document.querySelector('.table-container-modern')?.classList.remove('table-disabled');
            }

            // Update overlay progress
            function updateOverlayProgress(percent, status) {
                if (overlayProgress) overlayProgress.style.width = percent + '%';
                if (overlayProgress) overlayProgress.querySelector('.progress-text').textContent = percent + '%';
                if (overlayStatus) overlayStatus.textContent = status;
            }

            // Process import
            function processImport() {
                startImportBtn.disabled = true;
                startImportBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importing...';

                let progress = 0;
                const interval = setInterval(() => {
                    progress += 5;
                    if (progress > 95) progress = 95;
                    updateOverlayProgress(progress, `Processing... ${progress}%`);
                }, 300);

                fetch('{{ route('process.import') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    clearInterval(interval);
                    if (data.success) {
                        updateOverlayProgress(100, 'Import completed!');
                        setTimeout(() => {
                            hideImportOverlay();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: `${data.count} records imported successfully`,
                                timer: 3000
                            }).then(() => window.location.reload());
                        }, 1000);
                    } else {
                        clearInterval(interval);
                        hideImportOverlay();
                        Swal.fire({
                            icon: 'error',
                            title: 'Import Failed',
                            text: data.message || 'Failed to import records'
                        });
                    }
                })
                .catch(error => {
                    clearInterval(interval);
                    hideImportOverlay();
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: error.message
                    });
                });
            }

            // Cancel import
            document.getElementById('cancelImportBtn')?.addEventListener('click', function() {
                if (confirm('Cancel import?')) {
                    hideImportOverlay();
                }
            });

            // Helper functions
            function showFileError(message) {
                const errorDiv = document.getElementById('fileError');
                if (message) {
                    errorDiv.textContent = message;
                    errorDiv.classList.remove('d-none');
                } else {
                    errorDiv.textContent = '';
                    errorDiv.classList.add('d-none');
                }
            }

            function resetUploadButton() {
                uploadButton.disabled = false;
                uploadButton.innerHTML = '<i class="fas fa-upload me-1"></i> Upload & Preview';
            }
        });
    </script>
@endsection
