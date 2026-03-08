{{-- resources/views/Contract/non_teaching/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | Contracts Gateway</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ filemtime(public_path('manifest.json')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-16x16.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-32x32.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-192x192.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-512x512.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.5.2-web/css/all.css') }}">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --dark: #5a5c69;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
        }

        /* Navbar/Header Styles - Improved for mobile */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            padding: 0.5rem 1rem;
        }

        @media (min-width: 768px) {
            .navbar-custom {
                padding: 0.8rem 2rem;
            }
        }

        .school-logo {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #e9ecef;
            padding: 2px;
        }

        @media (min-width: 768px) {
            .school-logo {
                width: 50px;
                height: 50px;
                border-radius: 12px;
            }
        }

        .school-info {
            display: flex;
            flex-direction: column;
            margin-left: 8px;
            max-width: 150px;
        }

        @media (min-width: 480px) {
            .school-info {
                max-width: 200px;
                margin-left: 12px;
            }
        }

        @media (min-width: 768px) {
            .school-info {
                max-width: 300px;
            }
        }

        .school-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1.2;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (min-width: 480px) {
            .school-name {
                font-size: 0.95rem;
            }
        }

        @media (min-width: 768px) {
            .school-name {
                font-size: 1.1rem;
                white-space: normal;
            }
        }

        .school-address {
            font-size: 0.65rem;
            color: #718096;
            text-transform: capitalize;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (min-width: 480px) {
            .school-address {
                font-size: 0.7rem;
            }
        }

        @media (min-width: 768px) {
            .school-address {
                font-size: 0.75rem;
                white-space: normal;
            }
        }

        .profile-dropdown {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (min-width: 768px) {
            .profile-dropdown {
                gap: 15px;
            }
        }

        .profile-img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            padding: 2px;
            background: white;
        }

        @media (min-width: 768px) {
            .profile-img {
                width: 45px;
                height: 45px;
                border-width: 3px;
            }
        }

        .profile-info {
            text-align: right;
            display: none;
        }

        @media (min-width: 480px) {
            .profile-info {
                display: block;
                max-width: 120px;
            }
        }

        @media (min-width: 768px) {
            .profile-info {
                max-width: 200px;
            }
        }

        .profile-name {
            font-weight: 700;
            color: #2d3748;
            font-size: 0.8rem;
            text-transform: capitalize;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (min-width: 768px) {
            .profile-name {
                font-size: 0.95rem;
            }
        }

        .profile-role {
            font-size: 0.65rem;
            color: #718096;
            text-transform: capitalize;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (min-width: 768px) {
            .profile-role {
                font-size: 0.75rem;
            }
        }

        .logout-btn {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        @media (min-width: 480px) {
            .logout-btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
                gap: 6px;
            }
        }

        @media (min-width: 768px) {
            .logout-btn {
                padding: 0.5rem 1.2rem;
                font-size: 0.85rem;
                gap: 8px;
            }
        }

        .logout-btn i {
            font-size: 0.8rem;
        }

        @media (min-width: 768px) {
            .logout-btn i {
                font-size: 1rem;
            }
        }

        /* Main Container */
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 30px;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            margin: 20px 0 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-transform: capitalize;
        }

        .welcome-subtitle {
            opacity: 0.9;
            font-size: 1rem;
        }

        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stats-card.primary {
            border-left-color: var(--primary);
        }

        .stats-card.success {
            border-left-color: var(--success);
        }

        .stats-card.warning {
            border-left-color: var(--warning);
        }

        .stats-card.danger {
            border-left-color: var(--danger);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-top: 1.5rem;
            padding: 12px;
        }

        .table thead th {
            background: linear-gradient(135deg, #839db7 0%, #e9ecef 100%);
            color: #495057;
            font-weight: 700;
            text-transform: capitalize;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 4px;
            border: none;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f5;
        }

        /* Badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-warning {
            background: #f6c23e;
            color: #856404;
        }

        .badge-info {
            background: #36b9cc;
            color: white;
        }

        .badge-danger {
            background: #e74a3b;
            color: white;
        }

        .badge-secondary {
            background: #6c757d;
            color: white;
        }

        .badge-success {
            background: #1cc88a;
            color: white;
        }

        .badge-primary {
            background: #667eea;
            color: white;
        }

        .badge-expired {
            background: #ffc107;
            color: #856404;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-manage {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .btn-manage:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .detail-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.2rem;
        }

        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: #343a40;
            text-transform: capitalize;
        }

        .attachment-link {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 50px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .attachment-link:hover {
            background: #667eea;
            color: white;
            text-decoration: none;
        }

        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 0.75rem 1.5rem;
        }

        .nav-tabs .nav-link.active {
            color: #667eea;
            border-bottom: 2px solid #667eea;
            background: transparent;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 3rem;
            height: 3rem;
            border: 0.25rem solid rgba(102, 126, 234, 0.2);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Session Warning */
        .session-warning {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            animation: slideIn 0.5s ease;
            background: white;
            border-left: 4px solid var(--warning);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
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
    </style>
</head>

<body>
    <!-- Navbar / Header -->
    <nav class="navbar navbar-custom">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="{{ asset('storage/logo/default-logo.png') }}" alt="Logo" class="school-logo"
                    id="school-logo">
                <div class="school-info">
                    <span class="school-name" id="school-name">...</span>
                    <span class="school-address" id="school-address">...</span>
                </div>
            </div>

            <div class="profile-dropdown" style="cursor: pointer;" onclick="openProfileModal()">
                <div class="profile-info">
                    <div class="profile-name" id="profile-name">...</div>
                    <div class="profile-role" id="profile-role">...</div>
                </div>
                <!-- Profile Image -->
                <img src="" alt="Profile" class="profile-img" id="profile-img"
                    onerror="this.onerror=null; this.src='/storage/profile/avatar.jpg';">
                <button onclick="event.stopPropagation(); logout()" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="d-none d-md-inline">Toka</span>
                </button>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Loading State -->
        <div id="loading-state" class="text-center py-5">
            <div class="loading-spinner"></div>
            <p class="mt-3 text-muted">Inapakia dashi bodi yako...</p>
        </div>

        <!-- Error State -->
        <div id="error-state" class="alert alert-danger text-center py-4" style="display: none;">
            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
            <h5>Hitilafu ya Kupakia</h5>
            <p id="error-message"></p>
            <button onclick="fetchDashboard()" class="btn btn-outline-danger mt-3">
                <i class="fas fa-redo-alt mr-2"></i> Jaribu Tena
            </button>
        </div>

        <!-- Dashboard Content -->
        <div id="dashboard-content" style="display: none;">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="welcome-title" id="welcome-message">Karibu!</h1>
                        <p class="welcome-subtitle" id="welcome-subtitle"> Hapa unaweza kuona na kusimamia mikataba yako
                            yote</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button onclick="openApplyModal()" class="btn btn-light">
                            <i class="fas fa-plus-circle mr-2"></i> Omba Mkataba
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4 g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2"> Mikataba uliyoomba</h6>
                                <h3 class="mb-0" id="total-contracts">0</h3>
                            </div>
                            <div class="stats-icon bg-primary">
                                <i class="fas fa-file-contract text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2"> Inayotumika</h6>
                                <h3 class="mb-0" id="active-contracts">0</h3>
                            </div>
                            <div class="stats-icon bg-success">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2"> Inayosubiri</h6>
                                <h3 class="mb-0" id="pending-contracts">0</h3>
                            </div>
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-clock text-warning text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card danger">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Sitishwa/Kataliwa/Kwisha</h6>
                                <h3 class="mb-0" id="terminated-contracts">0</h3>
                            </div>
                            <div class="stats-icon bg-danger">
                                <i class="fas fa-ban text-danger text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contracts Table -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table" id="contracts-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Aina ya Mkataba</th>
                                <th>Wadhifa</th>
                                <th>Tarehe ya Kuomba</th>
                                <th>Hali</th>
                                <th>Vitendo</th>
                            </tr>
                        </thead>
                        <tbody id="contracts-table-body">
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="text-center py-5 bg-light rounded-3" style="display: none;">
                <i class="fas fa-file-contract fa-4x text-muted mb-3"></i>
                <h5> Hujaomba Mikataba wowote</h5>
                <p class="text-muted mb-4"> Bado hujaomba ombi la mkataba wowote.</p>
                <button onclick="openApplyModal()" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-2"></i> Omba Mkataba Wako wa Kwanza
                </button>
            </div>
        </div>
    </div>

    <!-- Apply Modal -->
    <div class="modal fade" id="applyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-contract mr-2"></i>
                        Omba Mkataba
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="application-form" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Aina ya Mkataba</label>
                            <select class="form-control" id="contract-type" name="contract_type" required>
                                <option value="">-- Chagua Aina --</option>
                                <option value="provision"> Mkataba wa Matazamio</option>
                                <option value="new"> Mkataba Mpya</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Barua ya Maombi</label>
                            <input type="file" class="form-control" id="application-letter"
                                name="application_letter" accept=".pdf" required>
                            <small class="text-muted">Pakia barua yako ya maombi (PDF - Upeo 2MB)</small>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ghairi</button>
                            <button type="submit" class="btn btn-primary" id="submit-application">
                                <span class="spinner-border spinner-border-sm mr-2" id="submit-spinner"
                                    style="display: none;"></span>
                                <span id="submit-text">Wasilisha Ombi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Contract Modals -->
    {{-- DYNAMIC CONTRACT MODALS --}}
    @foreach ($contracts as $contract)
        @php
            // Determine status and styling
            $statusConfig = [
                'pending' => [
                    'gradient' => 'linear-gradient(135deg, #f6c23e 0%, #f8b500 100%)',
                    'icon' => 'fa-clock',
                    'title' => 'Inasubiri',
                    'badge' => 'badge-warning',
                ],
                'rejected' => [
                    'gradient' => 'linear-gradient(135deg, #6c757d 0%, #495057 100%)',
                    'icon' => 'fa-times-circle',
                    'title' => 'Imekataliwa',
                    'badge' => 'badge-secondary',
                ],
                'approved' => [
                    'gradient' => 'linear-gradient(135deg, #36b9cc 0%, #1e8a9e 100%)',
                    'icon' => 'fa-check-circle',
                    'title' => 'Imekubaliwa',
                    'badge' => 'badge-info',
                ],
                'activated' => [
                    'gradient' => 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
                    'icon' => 'fa-check-circle',
                    'title' => 'Inatumika',
                    'badge' => 'badge-success',
                ],
                'expired' => [
                    'gradient' => 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)',
                    'icon' => 'fa-hourglass-end',
                    'title' => 'Imeisha',
                    'badge' => 'badge-warning',
                ],
                'terminated' => [
                    'gradient' => 'linear-gradient(135deg, ##dc3545 0%, ##fe0545 100%)',
                    'icon' => 'fa-ban',
                    'title' => 'Imesitishwa',
                    'badge' => 'badge-danger',
                ],
            ];

            $config = $statusConfig[$contract->status] ?? [
                'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'icon' => 'fa-file-contract',
                'title' => ucfirst($contract->status),
                'badge' => 'badge-secondary',
            ];

            // Get auth token for links
            $authToken = session('contract_auth_token') ?? ($authToken ?? null);
        @endphp

        <div class="modal fade" id="contractModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    {{-- Modal Header --}}
                    <div class="modal-header" style="background: {{ $config['gradient'] }};">
                        <h5 class="modal-title">
                            <i class="fas {{ $config['icon'] }} mr-2"></i>
                            Maelezo ya Mkataba - {{ $config['title'] }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Nav Tabs --}}
                        <ul class="nav nav-tabs mb-4" id="contractTab{{ $contract->id }}" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="details-tab{{ $contract->id }}" data-bs-toggle="tab"
                                    href="#details{{ $contract->id }}" role="tab">
                                    <i class="fas fa-info-circle mr-2"></i> Maelezo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="documents-tab{{ $contract->id }}" data-bs-toggle="tab"
                                    href="#documents{{ $contract->id }}" role="tab">
                                    <i class="fas fa-file-alt mr-2"></i> Nyaraka
                                </a>
                            </li>
                            @if (in_array($contract->status, ['terminated', 'expired']) || isset($contract->terminated_at))
                                <li class="nav-item">
                                    <a class="nav-link" id="termination-tab{{ $contract->id }}" data-bs-toggle="tab"
                                        href="#termination{{ $contract->id }}" role="tab">
                                        <i class="fas fa-ban mr-2"></i> Umesitishwa
                                    </a>
                                </li>
                            @endif
                        </ul>

                        {{-- Tab Content --}}
                        <div class="tab-content">
                            {{-- DETAILS TAB --}}
                            <div class="tab-pane active" id="details{{ $contract->id }}" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-card">
                                            <div class="detail-label">Aina ya Mkataba</div>
                                            <div class="detail-value">
                                                {{ $contract->contract_type == 'provision' ? 'Mkataba wa Matazamio' : 'Mkataba Mpya' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-card">
                                            <div class="detail-label">Wadhifa</div>
                                            <div class="detail-value">{{ $contract->job_title ?? 'N/A' }}</div>
                                        </div>
                                    </div>

                                    @if (in_array($contract->status, ['pending', 'rejected']))
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Tarehe ya Kuomba</div>
                                                <div class="detail-value">
                                                    {{ \Carbon\Carbon::parse($contract->applied_at)->format('d M Y, H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($contract->status == 'rejected')
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Tarehe ya Kukataliwa</div>
                                                <div class="detail-value">
                                                    {{ $contract->rejected_at ? \Carbon\Carbon::parse($contract->rejected_at)->format('d M Y, H:i') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="detail-card" style="border-left-color: #dc3545;">
                                                <div class="detail-label text-danger">Sababu ya Kukataliwa</div>
                                                <div class="detail-value">
                                                    {{ $contract->remarks ?? 'Hakuna sababu iliyotolewa' }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (in_array($contract->status, ['approved', 'activated', 'expired', 'terminated']))
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Tarehe ya Kuanza</div>
                                                <div class="detail-value">
                                                    {{ \Carbon\Carbon::parse($contract->start_date)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Tarehe ya Kuisha</div>
                                                <div class="detail-value">
                                                    {{ \Carbon\Carbon::parse($contract->end_date)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Muda</div>
                                                <div class="detail-value">{{ $contract->duration }} miezi</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Mshahara wa Msingi</div>
                                                <div class="detail-value">
                                                    {{ number_format($contract->basic_salary ?? 0, 2) }} TZS</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Posho</div>
                                                <div class="detail-value">
                                                    {{ number_format($contract->allowances ?? 0, 2) }} TZS</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Jumla</div>
                                                <div class="detail-value text-success fw-bold">
                                                    {{ number_format(($contract->basic_salary ?? 0) + ($contract->allowances ?? 0), 2) }}
                                                    TZS</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Aliyeidhinisha</div>
                                                <div class="detail-value">{{ $contract->approved_by ?? 'System' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (in_array($contract->status, ['activated', 'expired', 'terminated']))
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Tarehe ya Kuidhinishwa</div>
                                                <div class="detail-value">
                                                    {{ \Carbon\Carbon::parse($contract->activated_at)->format('d M Y, H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($contract->status == 'approved')
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Aliyeidhinisha</div>
                                                <div class="detail-value">{{ $contract->approved_by ?? 'System' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-6">
                                        <div class="detail-card">
                                            <div class="detail-label">Hali</div>
                                            <div class="detail-value">
                                                <span
                                                    class="badge {{ $config['badge'] }}">{{ $config['title'] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if (isset($contract->holder_id))
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Mshikiliaji</div>
                                                <div class="detail-value">
                                                    {{ App\Models\User::find($contract->holder_id)->first_name ?? 'Unknown' }}
                                                    {{ App\Models\User::find($contract->holder_id)->last_name ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($contract->status == 'pending' && isset($contract->updated_at))
                                        <div class="col-md-6">
                                            <div class="detail-card">
                                                <div class="detail-label">Sasisho la Mwisho</div>
                                                <div class="detail-value">
                                                    {{ \Carbon\Carbon::parse($contract->updated_at)->format('d M Y, H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- DOCUMENTS TAB --}}
                            <div class="tab-pane fade" id="documents{{ $contract->id }}" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="detail-card">
                                            <div class="detail-label mb-3">Nyaraka za Mkataba</div>
                                            <div class="d-flex flex-wrap gap-2">
                                                {{-- Application Letter --}}
                                                @if ($contract->applicant_file_path)
                                                    <a href="{{ asset('storage/' . $contract->applicant_file_path) }}"
                                                        class="btn btn-info mr-2 mb-2" target="_blank"
                                                        rel="noopener noreferrer">
                                                        <i class="fas fa-file-alt mr-2"></i> Barua ya Maombi
                                                    </a>
                                                @endif

                                                {{-- Approval Letter --}}
                                                @if (in_array($contract->status, ['approved', 'activated', 'expired', 'terminated']))
                                                    @php
                                                        $approvalUrl = route('contract.approval.letter', [
                                                            'id' => Hashids::encode($contract->id),
                                                        ]);
                                                        if ($authToken) {
                                                            $approvalUrl .= '?auth_token=' . urlencode($authToken);
                                                        }
                                                    @endphp
                                                    <a href="{{ $approvalUrl }}" class="btn btn-success mr-2 mb-2"
                                                        target="_blank" rel="noopener noreferrer"
                                                        onclick="return handleSecureLink('{{ $authToken }}', '{{ Hashids::encode($contract->id) }}')">
                                                        <i class="fas fa-file-pdf mr-2"></i> Barua ya Kuidhinishwa
                                                    </a>
                                                @endif

                                                {{-- Signed Contract --}}
                                                @if ($contract->contract_file_path)
                                                    <a href="{{ asset('storage/' . $contract->contract_file_path) }}"
                                                        class="btn btn-primary mr-2 mb-2" target="_blank"
                                                        rel="noopener noreferrer">
                                                        <i class="fas fa-file-signature mr-2"></i> Mkataba Uliosainiwa
                                                    </a>
                                                @endif

                                                {{-- Termination Document --}}
                                                @if (isset($contract->termination_document) && $contract->termination_document)
                                                    <a href="{{ asset('storage/' . $contract->termination_document) }}"
                                                        class="btn btn-danger mr-2 mb-2" target="_blank"
                                                        rel="noopener noreferrer">
                                                        <i class="fas fa-ban mr-2"></i> Barua ya Kusitisha
                                                    </a>
                                                @endif

                                                {{-- QR Code --}}
                                                @if ($contract->qr_code_path)
                                                    <a href="{{ asset('storage/' . $contract->qr_code_path) }}"
                                                        class="btn btn-secondary mr-2 mb-2" target="_blank"
                                                        rel="noopener noreferrer">
                                                        <i class="fas fa-qrcode mr-2"></i> QR Code
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TERMINATION TAB --}}
                            @if (in_array($contract->status, ['terminated', 'expired']) || isset($contract->terminated_at))
                                <div class="tab-pane fade" id="termination{{ $contract->id }}" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="detail-card" style="border-left-color: #dc3545;">
                                                <div class="detail-label text-danger">Tarehe ya Kusitishwa</div>
                                                <div class="detail-value">
                                                    {{ $contract->terminated_at ? \Carbon\Carbon::parse($contract->terminated_at)->format('d M Y, H:i') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card" style="border-left-color: #dc3545;">
                                                <div class="detail-label text-danger">Aina ya Kusitishwa</div>
                                                <div class="detail-value">
                                                    @switch($contract->termination_type)
                                                        @case('mutual')
                                                            Mutual Agreement
                                                        @break

                                                        @case('resignation')
                                                            Resignation
                                                        @break

                                                        @case('dismissal')
                                                            Dismissal
                                                        @break

                                                        @case('breach')
                                                            Contract Breach
                                                        @break

                                                        @case('end_of_contract')
                                                            End of Contract (Early)
                                                        @break

                                                        @default
                                                            {{ ucfirst($contract->termination_type ?? 'Haijaelezwa') }}
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-card" style="border-left-color: #dc3545;">
                                                <div class="detail-label text-danger">Aliyevunja</div>
                                                <div class="detail-value">
                                                    {{ ucfirst($contract->terminated_by ?? 'System') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="detail-card" style="border-left-color: #dc3545;">
                                                <div class="detail-label text-danger">Sababu ya Kusitishwa</div>
                                                <div class="detail-value">
                                                    {{ $contract->termination_reason ?? 'Hakuna sababu iliyotolewa' }}
                                                </div>
                                            </div>
                                        </div>
                                        @if (isset($contract->termination_notes) && $contract->termination_notes)
                                            <div class="col-12">
                                                <div class="detail-card" style="border-left-color: #dc3545;">
                                                    <div class="detail-label text-danger">Maelezo ya Ziada</div>
                                                    <div class="detail-value">{{ $contract->termination_notes }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer">
                        @if ($contract->status == 'pending')
                            <form action="{{ route('contract.delete', ['id' => Hashids::encode($contract->id)]) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Una uhakika unataka kufuta ombi hili?')">
                                    <i class="fas fa-trash mr-2"></i> Futa Ombi
                                </button>
                            </form>
                        @endif

                        @if ($contract->status == 'rejected')
                            <a href="{{ route('contract.reapply', ['id' => Hashids::encode($contract->id)]) }}"
                                class="btn btn-primary">
                                <i class="fas fa-redo-alt mr-2"></i> Tuma Tena
                            </a>
                        @endif

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times mr-2"></i> Funga
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- Profile Modal - Simple Version -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title" id="profileModalLabel">
                        <i class="fas fa-user-circle mr-2"></i>
                        Taarifa Zako
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <!-- Profile Image -->
                    <img src="" alt="Profile" class="rounded-circle border border-3 border-primary p-1 mb-3"
                        id="modal-profile-img" style="width: 100px; height: 100px; object-fit: cover;">

                    <!-- Name -->
                    <h5 id="modal-profile-name" class="mb-2 text-uppercase">-</h5>

                    <!-- Staff ID -->
                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">ID ya Mtumiaji</small>
                        <strong id="modal-profile-id" class="text-uppercase">-</strong>
                    </div>

                    <!-- Phone -->
                    <div class="bg-light p-2 rounded">
                        <small class="text-muted d-block">Namba ya Simu</small>
                        <strong id="modal-profile-phone">-</strong>
                    </div>
                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">Namba ya NIDA</small>
                        <strong id="modal-profile-nida">-</strong>
                    </div>
                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">Tarehe ya Kuzaliwa</small>
                        <strong id="modal-profile-dob">-</strong>
                    </div>
                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">Benki Akaunti</small>
                        <strong id="modal-profile-accountNumber" class="text-uppercase">-</strong>
                    </div>
                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">Jina la Benki</small>
                        <strong id="modal-profile-accountName" class="text-uppercase">-</strong>
                    </div>
                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">Unapoishi</small>
                        <strong id="modal-profile-address" class="text-uppercase">-</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times mr-2"></i> Funga
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Session Warning -->
    <div id="session-warning" class="session-warning p-3 rounded-3" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle text-warning fa-2x mr-4"></i>
            <div>
                <strong> Muda wa Session unaisha</strong>
                <p class="mb-0" id="session-warning-text">...</p>
            </div>
            <button onclick="extendSession()" class="btn btn-sm btn-warning ms-3"> Ongeza Muda</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('assets/js/js.js')}}"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Global variables
        let authToken = localStorage.getItem('contract_auth_token');
        let sessionExpiresAt = null;
        let sessionCheckInterval = null;
        let contracts = [];
        let schoolData = null;
        let staffData = null;

        $(document).ready(function() {
            // Check for flash messages on page load
            @if (session('success'))
                // console.log('Success flash found:', '{{ session('success') }}');
                Swal.fire({
                    icon: 'success',
                    title: 'Imefanikiwa!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                // console.log('Error flash found:', '{{ session('error') }}');
                Swal.fire({
                    icon: 'error',
                    title: 'Hitilafu!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#dc2626'
                });
            @endif

            @if (session('info'))
                // console.log('Info flash found:', '{{ session('info') }}');
                Swal.fire({
                    icon: 'info',
                    title: 'Taarifa',
                    text: '{{ session('info') }}',
                    confirmButtonColor: '#667eea'
                });
            @endif
        });

        function handleSecureLink(token, contractId) {
            // Try to get token from multiple sources
            if (!token) {
                token = localStorage.getItem('contract_auth_token');
            }

            if (!token) {
                // Try to get from meta or session (will be injected by PHP)
                token = '{{ session('contract_auth_token') }}';
            }

            if (token && token !== '') {
                // Build URL with token
                const url = `/contracts/${contractId}/approval-letter?auth_token=${encodeURIComponent(token)}`;

                // Open in new tab with referrer policy for security
                window.open(url, '_blank', 'noopener,noreferrer');
                return false; // Prevent default
            }

            // If no token, let default behavior happen (will redirect to gateway)
            return true;
        }

        // DOM Elements
        const elements = {
            loading: document.getElementById('loading-state'),
            error: document.getElementById('error-state'),
            errorMessage: document.getElementById('error-message'),
            content: document.getElementById('dashboard-content'),
            emptyState: document.getElementById('empty-state'),
            contractsTable: document.getElementById('contracts-table'),
            contractsTableBody: document.getElementById('contracts-table-body'),

            // Header elements
            schoolLogo: document.getElementById('school-logo'),
            schoolName: document.getElementById('school-name'),
            schoolAddress: document.getElementById('school-address'),
            profileName: document.getElementById('profile-name'),
            profileRole: document.getElementById('profile-role'),
            profileImg: document.getElementById('profile-img'),
            welcomeMessage: document.getElementById('welcome-message'),
            welcomeSubtitle: document.getElementById('welcome-subtitle'),

            // Stats elements
            totalContracts: document.getElementById('total-contracts'),
            activeContracts: document.getElementById('active-contracts'),
            pendingContracts: document.getElementById('pending-contracts'),
            terminatedContracts: document.getElementById('terminated-contracts'),

            // Modal elements
            contractType: document.getElementById('contract-type'),
            applicationLetter: document.getElementById('application-letter'),
            submitBtn: document.getElementById('submit-application'),
            submitSpinner: document.getElementById('submit-spinner'),
            submitText: document.getElementById('submit-text'),

            // Session warning
            sessionWarning: document.getElementById('session-warning'),
            sessionWarningText: document.getElementById('session-warning-text')
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            fetchDashboard().then(() => {
                // Profile details are already fetched inside fetchDashboard via fetchFreshProfileDetails
                // console.log('Dashboard loaded, profile details should be ready');
            });
            startSessionCheck();
        });

        // Fetch dashboard data
        async function fetchDashboard() {
            showLoading(true);

            try {
                // console.log('Fetching dashboard with token:', authToken ? 'Token exists' : 'No token');

                const response = await fetch('/contracts/dashboard/data', {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                // console.log('Dashboard response status:', response.status);
                const data = await response.json();

                if (data.success) {
                    // Store token
                    if (data.data.auth_token) {
                        localStorage.setItem('contract_auth_token', data.data.auth_token);
                        authToken = data.data.auth_token;
                    }

                    // Save dashboard data
                    staffData = data.data.applicant;
                    schoolData = data.data.school;
                    contracts = data.data.contracts;
                    sessionExpiresAt = data.data.expires_at;

                    // Update header with basic info first
                    updateHeader();

                    // Update stats and table
                    updateStats();
                    renderContractsTable();
                    showContent();
                    checkSessionExpiry();

                    // ===== CRITICAL: Fetch fresh profile details immediately =====
                    // This will update header with correct gender and profile image
                    await fetchFreshProfileDetails();

                } else {
                    showError(data.message);
                    if (response.status === 401) handleUnauthorized();
                }
            } catch (err) {
                // console.error('Fetch error:', err);
                showError('Imeshindwa kupakia dashi bodi. Tafadhali angalia muunganisho wako.');
            } finally {
                showLoading(false);
            }
        }

        // New function to fetch fresh profile details
        async function fetchFreshProfileDetails() {
            try {
                const response = await fetch('/contracts/profile/details', {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Store complete profile details
                    profileDetails = {
                        full_name: data.data.full_name ||
                            `${data.data.first_name || ''} ${data.data.last_name || ''}`.trim(),
                        first_name: data.data.first_name,
                        last_name: data.data.last_name,
                        staff_id: data.data.staff_id || data.data.member_id || 'N/A',
                        phone: data.data.phone || 'N/A',
                        profile_image: data.data.profile_image,
                        gender: data.data.gender || 'male',
                        staff_type: data.data.staff_type || 'Staff',
                        nida: data.data.nida || "N/A",
                        dob: data.data.dob || "N/A",
                        accountName: data.data.bank_name || "N/A",
                        accountNumber: data.data.bank_account_number || "N/A",
                        address: data.data.address || "N/A",
                    };

                    // console.log('Profile details loaded:', profileDetails);

                    // Update header with correct profile data
                    updateHeaderFromProfileDetails();
                }
            } catch (err) {
                // console.error('Error fetching fresh profile:', err);
            }
        }

        // Update header with school and staff info
        function updateHeader() {
            // School Info
            if (schoolData) {
                // console.log('School Data:', schoolData);
                elements.schoolName.textContent = schoolData.school_name || 'Shule';
                elements.schoolAddress.textContent = schoolData.full_address ||
                    `${schoolData.postal_address || ''} ${schoolData.postal_name || ''}`.trim() || 'Anuani ya Shule';

                if (schoolData.logo) {
                    elements.schoolLogo.src = `/storage/logo/${schoolData.logo}`;
                }
            }

            // ===== STAFF INFO - NOW USING profileDetails =====
            // Ikiwa profileDetails ipo, tumia hiyo
            if (profileDetails) {
                updateHeaderFromProfileDetails();
            }
            // Kama hakuna profileDetails, tumia staffData kama fallback
            else if (staffData) {
                // console.log('Using staffData as fallback for header:', staffData);

                const fullName = `${staffData.first_name || ''} ${staffData.last_name || ''}`.trim() || 'Mjumbe';
                elements.profileName.textContent = fullName;
                elements.profileRole.textContent = staffData.staff_type || 'Staff';
                elements.welcomeMessage.textContent = `Karibu, ${staffData.first_name || 'Mjumbe'}!`;

                const imageName = staffData.profile_image;
                const gender = staffData.gender || 'male';

                let imagePath;
                if (imageName && imageName !== 'null' && imageName !== 'undefined') {
                    imagePath = `/storage/profile/${imageName}`;
                } else {
                    imagePath = gender === 'female' ? '/storage/profile/avatar-female.jpg' : '/storage/profile/avatar.jpg';
                }

                elements.profileImg.src = imagePath;

                elements.profileImg.onerror = function() {
                    this.src = '/storage/profile/avatar.jpg';
                };
            }
        }

        // New function to update header from profileDetails
        function updateHeaderFromProfileDetails() {
            if (!profileDetails) return;

            // console.log('Updating header from profileDetails:', profileDetails);

            // Full name
            elements.profileName.textContent = profileDetails.full_name || 'Mjumbe';
            elements.profileRole.textContent = profileDetails.staff_type || 'Staff';
            elements.welcomeMessage.textContent =
                `Karibu, ${profileDetails.first_name || profileDetails.full_name || 'Mjumbe'}!`;

            // Profile image
            let imagePath;
            if (profileDetails.profile_image && profileDetails.profile_image !== 'null' && profileDetails.profile_image !==
                'undefined') {
                imagePath = `/storage/profile/${profileDetails.profile_image}`;
            } else {
                imagePath = profileDetails.gender === 'female' ?
                    '/storage/profile/avatar-female.jpg' :
                    '/storage/profile/avatar.jpg';
            }

            elements.profileImg.src = imagePath;
            // console.log('Header profile image set to:', imagePath);

            // Error handler
            elements.profileImg.onerror = function() {
                this.src = '/storage/profile/avatar.jpg';
            };
        }


        // Update statistics
        function updateStats() {
            const total = contracts.length;
            const active = contracts.filter(c => c.status === 'activated' && c.is_active).length;
            const pending = contracts.filter(c => c.status === 'pending').length;
            const terminated = contracts.filter(c => ['terminated', 'expired', 'rejected'].includes(c.status)).length;

            elements.totalContracts.textContent = total;
            elements.activeContracts.textContent = active;
            elements.pendingContracts.textContent = pending;
            elements.terminatedContracts.textContent = terminated;
        }

        // Render contracts table
        function renderContractsTable() {
            if (!contracts || contracts.length === 0) {
                elements.contractsTable.style.display = 'none';
                elements.emptyState.style.display = 'block';
                return;
            }

            elements.contractsTable.style.display = 'table';
            elements.emptyState.style.display = 'none';

            let html = '';
            contracts.forEach((contract, index) => {
                const appliedDate = formatDate(contract.applied_at);

                // Status in Swahili with correct Bootstrap classes
                let statusText = contract.status;
                let statusClass = '';

                switch (contract.status) {
                    case 'pending':
                        statusText = 'Inasubiri';
                        statusClass = 'badge-warning';
                        break;
                    case 'approved':
                        statusText = 'Imekubaliwa';
                        statusClass = 'badge-info';
                        break;
                    case 'rejected':
                        statusText = 'Imekataliwa';
                        statusClass = 'badge-secondary';
                        break;
                    case 'activated':
                        statusText = 'Inatumika';
                        statusClass = 'badge-success';
                        break;
                    case 'expired':
                        statusText = 'Imeisha';
                        statusClass = 'badge-expired';
                        break;
                    case 'terminated':
                        statusText = 'Imesitishwa';
                        statusClass = 'badge-danger';
                        break;
                    default:
                        statusText = contract.status;
                        statusClass = 'badge-secondary';
                }

                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <span class="font-weight-bold text-capitalize">
                                ${contract.contract_type === 'provision' ? 'Mkataba wa Matazamio' : 'Mkataba Mpya'}
                            </span>
                        </td>
                        <td class="text-capitalize">${contract.job_title || 'N/A'}</td>
                        <td>${appliedDate}</td>
                        <td>
                            <span class="badge ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-manage" onclick="viewContractDetails(${contract.id})">
                                    <i class="fas fa-eye"></i> Angalia
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            elements.contractsTableBody.innerHTML = html;

            // Initialize DataTable
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#contracts-table')) {
                $('#contracts-table').DataTable({
                    language: {
                        search: "Tafuta:",
                        lengthMenu: "Onyesha _MENU_ kwa ukurasa",
                        info: "Inaonyesha _START_ hadi _END_ ya _TOTAL_ ",
                        infoEmpty: "Hakuna data",
                        infoFiltered: "(imechujwa kutoka _MAX_ jumla)",
                        paginate: {
                            first: "Mwanzo",
                            last: "Mwisho",
                            next: "Ijayo",
                            previous: "Nyuma"
                        }
                    }
                });
            }
        }

        // View contract details - open the appropriate modal
        function viewContractDetails(contractId) {
            const modalElement = document.getElementById(`contractModal${contractId}`);
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                // console.error('Modal not found for contract:', contractId);
                Swal.fire({
                    icon: 'error',
                    title: 'Hitilafu',
                    text: 'Modal haipatikani kwa mkataba huu'
                });
            }
        }

        // Open apply modal
        function openApplyModal() {
            const modal = new bootstrap.Modal(document.getElementById('applyModal'));
            modal.show();
        }


        // Handle form submission
        document.getElementById('application-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            // ===== DEBUG TOKEN =====
            // console.log('=== FORM SUBMISSION DEBUG ===');
            // console.log('Auth Token from localStorage:', authToken);
            // console.log('Token length:', authToken ? authToken.length : 0);
            // console.log('Token first 20 chars:', authToken ? authToken.substring(0, 20) + '...' : 'N/A');

            if (!authToken) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hitilafu ya Token',
                    text: 'Hakuna token ya authentication. Tafadhali ingia tena.',
                    confirmButtonColor: '#dc2626'
                }).then(() => {
                    window.location.href = '/contract-gateway';
                });
                return;
            }

            // ===== CHECK TOKEN VALIDITY FIRST =====
            try {
                // console.log('Checking token validity...');
                const checkResponse = await fetch('/contracts/dashboard/data', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json'
                    }
                });

                // console.log('Token check response status:', checkResponse.status);

                if (checkResponse.status === 401) {
                    // console.log('Token is invalid or expired');

                    // Try to get new token from localStorage again
                    const newToken = localStorage.getItem('contract_auth_token');
                    // console.log('New token from localStorage:', newToken ? 'Exists' : 'Not found');

                    if (newToken && newToken !== authToken) {
                        authToken = newToken;
                        // console.log('Using new token');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Session imekwisha',
                            text: 'Tafadhali ingia tena kutumia OTP',
                            confirmButtonColor: '#dc2626'
                        }).then(() => {
                            localStorage.removeItem('contract_auth_token');
                            window.location.href = '/contract-gateway';
                        });
                        return;
                    }
                }
            } catch (err) {
                // console.error('Token check failed:', err);
            }

            const contractType = elements.contractType.value;
            const file = elements.applicationLetter.files[0];

            // console.log('Contract Type:', contractType);
            // console.log('File:', file ? file.name : 'No file');
            // console.log('File size:', file ? file.size : 0);
            // console.log('File type:', file ? file.type : 'N/A');

            if (!contractType || !file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Hitilafu',
                    text: 'Tafadhali jaza sehemu zote zinazohitajika',
                    confirmButtonColor: '#667eea'
                });
                return;
            }

            // Show loading
            elements.submitBtn.disabled = true;
            elements.submitSpinner.style.display = 'inline-block';
            elements.submitText.textContent = 'Inawasilisha...';

            const formData = new FormData();
            formData.append('contract_type', contractType);
            formData.append('application_letter', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                'content'));

            try {
                // console.log('Sending request to /contracts/store...');
                // console.log('Authorization Header:', `Bearer ${authToken.substring(0, 20)}...`);

                const response = await fetch('/contracts/store', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                });

                // console.log('Response status:', response.status);
                // console.log('Response headers:', [...response.headers.entries()]);

                const data = await response.json();
                // console.log('Response data:', data);

                if (response.status === 401) {
                    // console.log('Unauthorized response received');

                    Swal.fire({
                        icon: 'error',
                        title: 'Session imekwisha',
                        text: data.message || 'Tafadhali ingia tena kutumia OTP',
                        confirmButtonColor: '#dc2626'
                    }).then(() => {
                        localStorage.removeItem('contract_auth_token');
                        window.location.href = '/contract-gateway';
                    });
                    return;
                }

                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
                    if (modal) modal.hide();

                    // Reset form
                    elements.contractType.value = '';
                    elements.applicationLetter.value = '';

                    Swal.fire({
                        icon: 'success',
                        title: 'Imefanikiwa!',
                        text: data.message || 'Ombi limewasilishwa kikamilifu!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hitilafu',
                        text: data.message || 'Imeshindwa kuwasilisha ombi',
                        confirmButtonColor: '#dc2626'
                    });
                }
            } catch (err) {
                // console.error('Error submitting form:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Hitilafu',
                    text: 'Hitilafu katika kuwasilisha ombi. Tafadhali jaribu tena.',
                    confirmButtonColor: '#dc2626'
                });
            } finally {
                // Hide loading
                elements.submitBtn.disabled = false;
                elements.submitSpinner.style.display = 'none';
                elements.submitText.textContent = 'Wasilisha Ombi';
            }
        });

        // Profile data
        let profileDetails = null;

        // Open profile modal
        function openProfileModal() {
            // If we already have profileDetails, use them
            if (profileDetails) {
                populateSimpleProfileModal();
                const modal = new bootstrap.Modal(document.getElementById('profileModal'));
                modal.show();
                return;
            }

            // If we have staffData but no profileDetails, create temporary profileDetails
            if (staffData) {
                profileDetails = {
                    full_name: `${staffData.first_name || ''} ${staffData.last_name || ''}`.trim(),
                    first_name: staffData.first_name,
                    last_name: staffData.last_name,
                    staff_id: staffData.staff_id || staffData.member_id || 'N/A',
                    phone: staffData.phone || 'N/A',
                    profile_image: staffData.profile_image,
                    gender: staffData.gender || 'male',
                    staff_type: staffData.staff_type || 'Staff',
                    nida: data.data.nida || "N/A",
                    dob: data.data.dob || "N/A",
                    accountNumber: data.data.bank_account_number || "N/A",
                    accountName: data.data.bank_name || "N/A",
                    address: data.data.address || "N/A",
                };

                populateSimpleProfileModal();
                const modal = new bootstrap.Modal(document.getElementById('profileModal'));
                modal.show();
                return;
            }

            // If nothing exists, fetch
            fetchProfileDetailsForModal();
        }

        // Populate simple profile modal
        function populateSimpleProfileModal() {
            if (!profileDetails) return;

            // console.log('Populating modal with:', profileDetails);

            // Set profile image
            const modalProfileImg = document.getElementById('modal-profile-img');
            if (profileDetails.profile_image && profileDetails.profile_image !== 'null' && profileDetails.profile_image !==
                'undefined') {
                modalProfileImg.src = `/storage/profile/${profileDetails.profile_image}`;
            } else {
                modalProfileImg.src = profileDetails.gender === 'female' ?
                    '/storage/profile/avatar-female.jpg' : '/storage/profile/avatar.jpg';
            }

            // Set details
            document.getElementById('modal-profile-name').textContent = profileDetails.full_name || 'N/A';
            document.getElementById('modal-profile-id').textContent = profileDetails.staff_id || 'N/A';
            document.getElementById('modal-profile-phone').textContent = profileDetails.phone || 'N/A';
            document.getElementById('modal-profile-nida').textContent = profileDetails.nida || 'N/A';
            document.getElementById('modal-profile-dob').textContent = profileDetails.dob || 'N/A';
            document.getElementById('modal-profile-accountNumber').textContent = profileDetails.accountNumber || 'N/A';
            document.getElementById('modal-profile-accountName').textContent = profileDetails.accountName || 'N/A';
            document.getElementById('modal-profile-address').textContent = profileDetails.address || 'N/A';
        }

        // Rename existing fetchProfileDetails to be modal-specific
        async function fetchProfileDetailsForModal() {
            try {
                showLoading(true);

                const response = await fetch('/contracts/profile/details', {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    profileDetails = {
                        full_name: data.data.full_name ||
                            `${data.data.first_name || ''} ${data.data.last_name || ''}`.trim(),
                        first_name: data.data.first_name,
                        last_name: data.data.last_name,
                        staff_id: data.data.staff_id || data.data.member_id || 'N/A',
                        phone: data.data.phone || 'N/A',
                        profile_image: data.data.profile_image,
                        gender: data.data.gender || 'male',
                        staff_type: data.data.staff_type || 'Staff',
                        nida: data.data.nida || "N/A",
                        dob: data.data.dob || "N/A",
                        accountNumber: data.data.bank_account_number || "N/A",
                        accountName: data.data.bank_name || "N/A",

                    };

                    populateSimpleProfileModal();
                    const modal = new bootstrap.Modal(document.getElementById('profileModal'));
                    modal.show();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hitilafu',
                        text: data.message || 'Imeshindwa kupakia taarifa za wasifu'
                    });
                }
            } catch (err) {
                // console.error('Error fetching profile:', err);
            } finally {
                showLoading(false);
            }
        }

        // Logout
        async function logout() {
            const result = await Swal.fire({
                title: 'Una uhakika?',
                text: 'Unakaribia kutoka kwenye mfumo',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ndio, Toka',
                cancelButtonText: 'Ghairi'
            });

            if (result.isConfirmed) {
                try {
                    await fetch('/contract-gateway/api/logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${authToken}`,
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });
                } finally {
                    localStorage.removeItem('contract_auth_token');
                    window.location.href = '/contract-gateway';
                }
            }
        }

        // Extend session
        async function extendSession() {
            try {
                const response = await fetch('/contract-gateway/api/extend-session', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    sessionExpiresAt = data.expires_at;
                    hideSessionWarning();

                    Swal.fire({
                        icon: 'success',
                        title: 'Imefanikiwa!',
                        text: 'Muda wa session umeongezwa kwa saa 1',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } catch (err) {
                // console.error('Failed to extend session', err);
            }
        }

        // Start session check interval
        function startSessionCheck() {
            sessionCheckInterval = setInterval(() => {
                if (sessionExpiresAt) {
                    const now = new Date().getTime();
                    const expiry = new Date(sessionExpiresAt).getTime();
                    const minutesLeft = Math.floor((expiry - now) / (1000 * 60));

                    if (minutesLeft > 0 && minutesLeft < 5) {
                        showSessionWarning(minutesLeft);
                    } else {
                        hideSessionWarning();
                    }
                }
            }, 30000);
        }

        // Show session warning
        function showSessionWarning(minutesLeft) {
            elements.sessionWarningText.textContent = `Muda wa Session utaisha baada ya dakika ${minutesLeft}`;
            elements.sessionWarning.style.display = 'block';
        }

        function hideSessionWarning() {
            elements.sessionWarning.style.display = 'none';
        }

        // Check session expiry on load
        function checkSessionExpiry() {
            if (sessionExpiresAt) {
                const expiryTime = new Date(sessionExpiresAt).getTime();
                const now = new Date().getTime();
                const minutesLeft = Math.floor((expiryTime - now) / (1000 * 60));

                if (minutesLeft < 5 && minutesLeft > 0) {
                    showSessionWarning(minutesLeft);
                }
            }
        }

        // Handle unauthorized
        function handleUnauthorized() {
            localStorage.removeItem('contract_auth_token');
            Swal.fire({
                icon: 'info',
                title: 'Session imekwisha',
                text: 'Tafadhali ingia tena',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '/contract-gateway';
            });
        }

        // UI Helpers
        function showLoading(isLoading) {
            elements.loading.style.display = isLoading ? 'block' : 'none';
        }

        function showError(message) {
            elements.errorMessage.textContent = message;
            elements.error.style.display = 'block';
            elements.content.style.display = 'none';
        }

        function showContent() {
            elements.error.style.display = 'none';
            elements.content.style.display = 'block';
        }

        // Format date
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('sw-TZ', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // Clean up interval on page unload
        window.addEventListener('beforeunload', () => {
            if (sessionCheckInterval) {
                clearInterval(sessionCheckInterval);
            }
        });
    </script>
    @include('sweetalert::alert');
</body>

</html>
