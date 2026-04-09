{{-- resources/views/Contract/non_teaching/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | Contracts Gateway</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-16x16.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-32x32.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-192x192.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-512x512.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
                <img src="" alt="Logo" class="school-logo" id="school-logo">
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
                    <i class="fas fa-plus-circle mr-2"></i> Omba Mkataba
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

    <!-- Dynamic Modals Container - Instead of static modals -->
    <div id="dynamic-modals-container"></div>

    <!-- Contract Modal Template -->
    <template id="contract-modal-template">
        <div class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-contract mr-2"></i>
                            Maelezo ya Mkataba
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#details-placeholder" role="tab">
                                    <i class="fas fa-info-circle mr-2"></i> Maelezo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#documents-placeholder" role="tab">
                                    <i class="fas fa-file-alt mr-2"></i> Nyaraka
                                </a>
                            </li>
                            <li class="nav-item termination-tab" style="display: none;">
                                <a class="nav-link" data-bs-toggle="tab" href="#termination-placeholder" role="tab">
                                    <i class="fas fa-ban mr-2"></i> Umesitishwa
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="details-placeholder" role="tabpanel"></div>
                            <div class="tab-pane fade" id="documents-placeholder" role="tabpanel"></div>
                            <div class="tab-pane fade" id="termination-placeholder" role="tabpanel"></div>
                        </div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    </template>

    <!-- Profile Modal -->
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
                    <img src="" alt="Profile" class="rounded-circle border border-3 border-primary p-1 mb-3"
                        id="modal-profile-img" style="width: 100px; height: 100px; object-fit: cover;">

                    <h5 id="modal-profile-name" class="mb-2 text-uppercase">-</h5>

                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">ID ya Mtumiaji</small>
                        <strong id="modal-profile-id" class="text-uppercase">-</strong>
                    </div>

                    <div class="bg-light p-2 rounded mb-2">
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
                        <small class="text-muted d-block">Unapoishi</small>
                        <strong id="modal-profile-address" class="text-uppercase">-</strong>
                    </div>
                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">Akaunti ya Benki</small>
                        <strong id="modal-profile-account-number" class="text-uppercase">-</strong>
                    </div>
                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">Jina la Akaunti</small>
                        <strong id="modal-profile-account-name" class="text-uppercase">-</strong>
                    </div>
                    <div class="bg-light p-2 rounded mb-2">
                        <small class="text-muted d-block">Jina la Benki</small>
                        <strong id="modal-profile-bank-name" class="text-uppercase">-</strong>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{asset('assets/js/scripts.js')}}?v={{ time() }}"></script>

    <script>
        // Global variables
        let authToken = localStorage.getItem('contract_auth_token');
        let sessionExpiresAt = null;
        let sessionCheckInterval = null;
        let contracts = [];
        let schoolData = null;
        let staffData = null;
        let profileDetails = null;

        // DOM Elements
        const elements = {
            loading: document.getElementById('loading-state'),
            error: document.getElementById('error-state'),
            errorMessage: document.getElementById('error-message'),
            content: document.getElementById('dashboard-content'),
            emptyState: document.getElementById('empty-state'),
            contractsTable: document.getElementById('contracts-table'),
            contractsTableBody: document.getElementById('contracts-table-body'),

            schoolLogo: document.getElementById('school-logo'),
            schoolName: document.getElementById('school-name'),
            schoolAddress: document.getElementById('school-address'),
            profileName: document.getElementById('profile-name'),
            profileRole: document.getElementById('profile-role'),
            profileImg: document.getElementById('profile-img'),
            welcomeMessage: document.getElementById('welcome-message'),
            welcomeSubtitle: document.getElementById('welcome-subtitle'),

            totalContracts: document.getElementById('total-contracts'),
            activeContracts: document.getElementById('active-contracts'),
            pendingContracts: document.getElementById('pending-contracts'),
            terminatedContracts: document.getElementById('terminated-contracts'),

            contractType: document.getElementById('contract-type'),
            applicationLetter: document.getElementById('application-letter'),
            submitBtn: document.getElementById('submit-application'),
            submitSpinner: document.getElementById('submit-spinner'),
            submitText: document.getElementById('submit-text'),

            sessionWarning: document.getElementById('session-warning'),
            sessionWarningText: document.getElementById('session-warning-text')
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Check if bootstrap is loaded
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap not loaded!');
                Swal.fire({
                    icon: 'error',
                    title: 'Hitilafu',
                    text: 'Bootstrap haijapakia. Tafadhali wasiliana na msimamizi.'
                });
                return;
            }

            fetchDashboard();
            startSessionCheck();
            setupFormSubmission();
        });

        function setupFormSubmission() {
            const form = document.getElementById('application-form');
            if (form) {
                form.addEventListener('submit', submitApplication);
            }
        }

        async function fetchDashboard() {
            showLoading(true);

            try {
                const response = await fetch('/contracts/dashboard/data', {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    if (data.data.auth_token) {
                        localStorage.setItem('contract_auth_token', data.data.auth_token);
                        authToken = data.data.auth_token;
                    }

                    staffData = data.data.applicant;
                    schoolData = data.data.school;
                    contracts = data.data.contracts || [];

                    console.log('Contracts data loaded:', contracts.length);

                    updateHeader();
                    updateStats();
                    renderContractsTable();
                    showContent();
                    checkSessionExpiry();

                    await fetchFreshProfileDetails();

                } else {
                    showError(data.message);
                    if (response.status === 401) handleUnauthorized();
                }
            } catch (err) {
                console.error('Fetch error:', err);
                showError('Imeshindwa kupakia dashi bodi. Tafadhali angalia muunganisho wako.');
            } finally {
                showLoading(false);
            }
        }

        async function fetchFreshProfileDetails() {
            try {
                const response = await fetch('/contracts/profile/details', {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
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
                        address: data.data.address || "N/A",
                    };

                    updateHeaderFromProfileDetails();
                }
            } catch (err) {
                console.error('Error fetching fresh profile:', err);
            }
        }

        function updateHeader() {
            if (schoolData) {
                elements.schoolName.textContent = schoolData.school_name || 'Shule';
                elements.schoolAddress.textContent = schoolData.full_address ||
                    `${schoolData.postal_address || ''} ${schoolData.postal_name || ''}`.trim() || 'Anuani ya Shule';

                if (schoolData.logo) {
                    elements.schoolLogo.src = `/storage/logo/${schoolData.logo}`;
                }
            }

            if (profileDetails) {
                updateHeaderFromProfileDetails();
            } else if (staffData) {
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

        function updateHeaderFromProfileDetails() {
            if (!profileDetails) return;

            elements.profileName.textContent = profileDetails.full_name || 'Mjumbe';
            elements.profileRole.textContent = profileDetails.staff_type || 'Staff';
            elements.welcomeMessage.textContent =
                `Karibu, ${profileDetails.first_name || profileDetails.full_name || 'Mjumbe'}!`;

            let imagePath;
            if (profileDetails.profile_image && profileDetails.profile_image !== 'null') {
                imagePath = `/storage/profile/${profileDetails.profile_image}`;
            } else {
                imagePath = profileDetails.gender === 'female' ?
                    '/storage/profile/avatar-female.jpg' :
                    '/storage/profile/avatar.jpg';
            }

            elements.profileImg.src = imagePath;

            elements.profileImg.onerror = function() {
                this.src = '/storage/profile/avatar.jpg';
            };
        }

        function updateStats() {
            const total = contracts.length;
            const active = contracts.filter(c => c.status === 'activated').length;
            const pending = contracts.filter(c => c.status === 'pending').length;
            const terminated = contracts.filter(c => ['terminated', 'expired', 'rejected'].includes(c.status)).length;

            elements.totalContracts.textContent = total;
            elements.activeContracts.textContent = active;
            elements.pendingContracts.textContent = pending;
            elements.terminatedContracts.textContent = terminated;
        }

        // ============= NEW FUNCTION: Create contract modal dynamically =============
        function createContractModal(contract) {
            // Clone template
            const template = document.getElementById('contract-modal-template');
            const modalDiv = template.content.cloneNode(true).firstElementChild;

            // Set unique ID
            const modalId = `contractModal${contract.id}`;
            modalDiv.id = modalId;

            // Find elements
            const modalHeader = modalDiv.querySelector('.modal-header');
            const modalTitle = modalDiv.querySelector('.modal-title');
            const modalFooter = modalDiv.querySelector('.modal-footer');

            // Status configurations
            const statusConfig = {
                pending: { gradient: 'linear-gradient(135deg, #f6c23e 0%, #f8b500 100%)', icon: 'fa-clock', title: 'Inasubiri', badge: 'badge-warning' },
                rejected: { gradient: 'linear-gradient(135deg, #6c757d 0%, #495057 100%)', icon: 'fa-times-circle', title: 'Imekataliwa', badge: 'badge-secondary' },
                approved: { gradient: 'linear-gradient(135deg, #36b9cc 0%, #1e8a9e 100%)', icon: 'fa-check-circle', title: 'Imekubaliwa', badge: 'badge-info' },
                activated: { gradient: 'linear-gradient(135deg, #28a745 0%, #20c997 100%)', icon: 'fa-check-circle', title: 'Inatumika', badge: 'badge-success' },
                expired: { gradient: 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)', icon: 'fa-hourglass-end', title: 'Imeisha', badge: 'badge-warning' },
                terminated: { gradient: 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)', icon: 'fa-ban', title: 'Imesitishwa', badge: 'badge-danger' }
            };

            const config = statusConfig[contract.status] || {
                gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                icon: 'fa-file-contract',
                title: contract.status.charAt(0).toUpperCase() + contract.status.slice(1),
                badge: 'badge-secondary'
            };

            // Set header
            modalHeader.style.background = config.gradient;
            modalTitle.innerHTML = `<i class="fas ${config.icon} mr-2"></i> Maelezo ya Mkataba - ${config.title}`;

            // Get tabs
            const navTabs = modalDiv.querySelector('.nav');
            const detailsTab = navTabs.querySelector('a[href="#details-placeholder"]');
            const documentsTab = navTabs.querySelector('a[href="#documents-placeholder"]');
            const terminationTab = navTabs.querySelector('.termination-tab');

            // Update tab hrefs with contract ID
            detailsTab.href = `#details${contract.id}`;
            documentsTab.href = `#documents${contract.id}`;

            // Get tab panes
            const detailsPane = modalDiv.querySelector('#details-placeholder');
            const documentsPane = modalDiv.querySelector('#documents-placeholder');
            const terminationPane = modalDiv.querySelector('#termination-placeholder');

            detailsPane.id = `details${contract.id}`;
            documentsPane.id = `documents${contract.id}`;
            terminationPane.id = `termination${contract.id}`;

            // FILL DETAILS TAB
            let detailsHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-label">Aina ya Mkataba</div>
                            <div class="detail-value">${contract.contract_type === 'provision' ? 'Mkataba wa Matazamio' : 'Mkataba Mpya'}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-label">Wadhifa</div>
                            <div class="detail-value">${contract.job_title || 'N/A'}</div>
                        </div>
                    </div>
            `;

            if (contract.status === 'rejected') {
                detailsHtml += `
                    <div class="col-12">
                        <div class="detail-card" style="border-left-color: #dc3545;">
                            <div class="detail-label text-danger">Sababu ya Kukataliwa</div>
                            <div class="detail-value">${contract.remarks || 'Hakuna sababu iliyotolewa'}</div>
                        </div>
                    </div>
                `;
            }

            if (['approved', 'activated', 'expired', 'terminated'].includes(contract.status)) {
                detailsHtml += `
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-label">Tarehe ya Kuanza</div>
                            <div class="detail-value">${formatDate(contract.start_date)}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-label">Tarehe ya Kuisha</div>
                            <div class="detail-value">${formatDate(contract.end_date)}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-label">Mshahara wa Msingi</div>
                            <div class="detail-value">${Number(contract.basic_salary || 0).toLocaleString()} TZS</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-label">Posho</div>
                            <div class="detail-value">${Number(contract.allowances || 0).toLocaleString()} TZS</div>
                        </div>
                    </div>
                `;
            }

            detailsHtml += `
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-label">Hali</div>
                            <div class="detail-value">
                                <span class="badge ${config.badge}">${config.title}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            detailsPane.innerHTML = detailsHtml;

            // FILL DOCUMENTS TAB
            let documentsHtml = `
                <div class="row">
                    <div class="col-12">
                        <div class="detail-card">
                            <div class="detail-label mb-3">Nyaraka za Mkataba</div>
                            <div class="d-flex flex-wrap gap-2">
            `;

            if (contract.applicant_file_path) {
                documentsHtml += `<a href="/storage/${contract.applicant_file_path}" class="btn btn-info mr-2 mb-2" target="_blank"><i class="fas fa-file-alt mr-2"></i> Barua ya Maombi</a>`;
            }

            if (contract.contract_file_path) {
                // Use the same route pattern as before
                const approvalUrl = contract.approval_letter_url || `/contracts/approval-letter/${btoa(contract.id)}`;
                documentsHtml += `<a href="${approvalUrl}" class="btn btn-success mr-2 mb-2" target="_blank"><i class="fas fa-file-pdf mr-2"></i> Barua ya Ajira</a>`;
                documentsHtml += `<a href="/storage/${contract.contract_file_path}" class="btn btn-primary mr-2 mb-2" target="_blank"><i class="fas fa-file-signature mr-2"></i> Mkataba Uliosainiwa</a>`;
            }

            documentsHtml += `
                            </div>
                        </div>
                    </div>
                </div>
            `;

            documentsPane.innerHTML = documentsHtml;

            // CHECK FOR TERMINATION HISTORY
            const terminationHistory = contract.status_histories?.find(h => h.new_status === 'terminated');

            if (terminationHistory || contract.status === 'terminated' || contract.terminated_at) {
                // Show termination tab
                terminationTab.style.display = 'list-item';
                terminationTab.querySelector('a').href = `#termination${contract.id}`;

                const metadata = terminationHistory?.metadata || {};

                let terminationHtml = `
                    <div class="row">
                        <div class="col-12">
                            <div class="detail-card" style="border-left-color: #dc3545;">
                                <div class="detail-label text-danger">Sababu ya Kusitishwa</div>
                                <div class="detail-value">${terminationHistory?.reason || contract.reason || 'Hakuna sababu iliyotolewa'}</div>
                            </div>
                        </div>
                `;

                // Add metadata if exists
                if (Object.keys(metadata).length > 0) {
                    if (metadata.termination_type) {
                        terminationHtml += `
                            <div class="col-md-6 mt-3">
                                <div class="detail-card">
                                    <div class="detail-label">Aina ya Kusitishwa</div>
                                    <div class="detail-value">${metadata.termination_type}</div>
                                </div>
                            </div>
                        `;
                    }

                    if (metadata.effective_date) {
                        terminationHtml += `
                            <div class="col-md-6 mt-3">
                                <div class="detail-card">
                                    <div class="detail-label">Tarehe ya Kusitishwa</div>
                                    <div class="detail-value">${formatDate(metadata.effective_date)}</div>
                                </div>
                            </div>
                        `;
                    }

                    // ===== IMPORTANT: DOCUMENT PATH FOR TERMINATION =====
                    if (metadata.document_path) {
                        terminationHtml += `
                            <div class="col-12 mt-3">
                                <div class="detail-card">
                                    <div class="detail-label">Barua ya Kusitishwa</div>
                                    <div class="detail-value">
                                        <a href="/storage/${metadata.document_path}" class="btn btn-danger" target="_blank">
                                            <i class="fas fa-file-pdf mr-2"></i> Pakua Barua ya Kusitishwa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    if (metadata.notes) {
                        terminationHtml += `
                            <div class="col-12 mt-3">
                                <div class="detail-card">
                                    <div class="detail-label">Maelezo ya Ziada</div>
                                    <div class="detail-value">${metadata.notes}</div>
                                </div>
                            </div>
                        `;
                    }
                }

                if (terminationHistory?.changed_by) {
                    terminationHtml += `
                        <div class="col-12 mt-3">
                            <div class="detail-card">
                                <div class="detail-label">Umesitishwa na</div>
                                <div class="detail-value">${terminationHistory.changed_by}</div>
                            </div>
                        </div>
                    `;
                }

                if (terminationHistory?.created_at) {
                    terminationHtml += `
                        <div class="col-12 mt-2">
                            <div class="detail-card">
                                <div class="detail-label">Tarehe ya Kubadilisha</div>
                                <div class="detail-value">${formatDate(terminationHistory.created_at)}</div>
                            </div>
                        </div>
                    `;
                }

                terminationHtml += `</div>`;
                terminationPane.innerHTML = terminationHtml;
            }

            // FILL FOOTER
            let footerHtml = '';

            if (contract.status === 'pending') {
                footerHtml = `
                    <form action="/contracts/${contract.id}" method="POST" class="d-inline delete-form" data-contract-id="${contract.id}" onsubmit="return deleteContract(event, this)">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-2"></i> Futa Ombi
                        </button>
                    </form>
                `;
            }

            if (contract.status === 'rejected') {
                footerHtml = `<a href="/contracts/reapply/${btoa(contract.id)}" class="btn btn-primary"><i class="fas fa-redo-alt mr-2"></i> Tuma Tena</a>`;
            }

            footerHtml += `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times mr-2"></i> Funga</button>`;
            modalFooter.innerHTML = footerHtml;

            // Add to container
            document.getElementById('dynamic-modals-container').appendChild(modalDiv);
        }

        // ============= UPDATED renderContractsTable =============
        function renderContractsTable() {
            if (!contracts || contracts.length === 0) {
                elements.contractsTable.style.display = 'none';
                elements.emptyState.style.display = 'block';
                return;
            }

            elements.contractsTable.style.display = 'table';
            elements.emptyState.style.display = 'none';

            // Clear existing modals
            document.getElementById('dynamic-modals-container').innerHTML = '';

            let html = '';
            contracts.forEach((contract, index) => {
                const appliedDate = formatDate(contract.applied_at);

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

                // Create modal for this contract
                createContractModal(contract);
            });

            elements.contractsTableBody.innerHTML = html;
        }

        function viewContractDetails(contractId) {
            const modalElement = document.getElementById(`contractModal${contractId}`);
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hitilafu',
                    text: 'Modal haipatikani kwa mkataba huu'
                });
            }
        }

        function openApplyModal() {
            const modalElement = document.getElementById('applyModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        async function submitApplication(e) {
            e.preventDefault();

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

            const contractType = elements.contractType.value;
            const file = elements.applicationLetter.files[0];

            if (!contractType || !file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Hitilafu',
                    text: 'Tafadhali jaza sehemu zote zinazohitajika',
                    confirmButtonColor: '#667eea'
                });
                return;
            }

            elements.submitBtn.disabled = true;
            elements.submitSpinner.style.display = 'inline-block';
            elements.submitText.textContent = 'Inawasilisha...';

            const formData = new FormData();
            formData.append('contract_type', contractType);
            formData.append('application_letter', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            try {
                const response = await fetch('/contracts/store', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.status === 401) {
                    handleUnauthorized();
                    return;
                }

                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
                    if (modal) modal.hide();

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
                console.error('Error submitting form:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Hitilafu',
                    text: 'Hitilafu katika kuwasilisha ombi. Tafadhali jaribu tena.',
                    confirmButtonColor: '#dc2626'
                });
            } finally {
                elements.submitBtn.disabled = false;
                elements.submitSpinner.style.display = 'none';
                elements.submitText.textContent = 'Wasilisha Ombi';
            }
        }

        function openProfileModal() {
            if (profileDetails) {
                populateProfileModal();
                const modalElement = document.getElementById('profileModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            } else if (staffData) {
                profileDetails = {
                    full_name: `${staffData.first_name || ''} ${staffData.last_name || ''}`.trim(),
                    first_name: staffData.first_name,
                    last_name: staffData.last_name,
                    staff_id: staffData.staff_id || staffData.member_id || 'N/A',
                    phone: staffData.phone || 'N/A',
                    profile_image: staffData.profile_image,
                    gender: staffData.gender || 'male',
                    staff_type: staffData.staff_type || 'Staff',
                    nida: staffData.nida || "N/A",
                    dob: staffData.dob || "N/A",
                    address: staffData.address || "N/A",
                    accountNumber: staffData.bank_account_number || "N/A",
                    accountName: staffData.bank_account_name || "N/A",
                    bankName: staffData.bank_name || "N/A",
                };

                populateProfileModal();
                const modalElement = document.getElementById('profileModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            } else {
                fetchProfileDetailsForModal();
            }
        }

        function populateProfileModal() {
            if (!profileDetails) return;

            const modalProfileImg = document.getElementById('modal-profile-img');
            if (profileDetails.profile_image && profileDetails.profile_image !== 'null') {
                modalProfileImg.src = `/storage/profile/${profileDetails.profile_image}`;
            } else {
                modalProfileImg.src = profileDetails.gender === 'female' ?
                    '/storage/profile/avatar-female.jpg' : '/storage/profile/avatar.jpg';
            }

            document.getElementById('modal-profile-name').textContent = profileDetails.full_name || 'N/A';
            document.getElementById('modal-profile-id').textContent = profileDetails.staff_id || 'N/A';
            document.getElementById('modal-profile-phone').textContent = profileDetails.phone || 'N/A';
            document.getElementById('modal-profile-nida').textContent = profileDetails.nida || 'N/A';
            document.getElementById('modal-profile-dob').textContent = profileDetails.dob || 'N/A';
            document.getElementById('modal-profile-address').textContent = profileDetails.address || 'N/A';
            document.getElementById('modal-profile-account-number').textContent = profileDetails.accountNumber || 'N/A';
            document.getElementById('modal-profile-account-name').textContent = profileDetails.accountName || 'N/A';
            document.getElementById('modal-profile-bank-name').textContent = profileDetails.bankName || 'N/A';
        }

        async function fetchProfileDetailsForModal() {
            try {
                const response = await fetch('/contracts/profile/details', {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
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
                        address: data.data.address || "N/A",
                        accountNumber: data.data.bank_account_number || "N/A",
                        accountName: data.data.bank_account_name || "N/A",
                        bankName: data.data.bank_name || "N/A",
                    };

                    populateProfileModal();
                    const modalElement = document.getElementById('profileModal');
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    }
                }
            } catch (err) {
                console.error('Error fetching profile:', err);
            }
        }

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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                } finally {
                    localStorage.removeItem('contract_auth_token');
                    window.location.href = '/contract-gateway';
                }
            }
        }

        async function extendSession() {
            try {
                const response = await fetch('/contract-gateway/api/extend-session', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                console.error('Failed to extend session', err);
            }
        }

        // Delete contract function
        async function deleteContract(event, form) {
            event.preventDefault();

            // Show confirmation dialog
            const result = await Swal.fire({
                title: 'Una uhakika?',
                text: 'Unakaribia kufuta ombi hili la mkataba',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ndio, Futa',
                cancelButtonText: 'Ghairi'
            });

            if (!result.isConfirmed) {
                return false;
            }

            // Show loading
            Swal.fire({
                title: 'Inafuta...',
                text: 'Tafadhali subiri',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const formData = new FormData(form);

                const response = await fetch(form.action, {
                    method: 'POST', // Laravel uses POST with _method=DELETE
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Close the modal
                    const modalElement = document.getElementById(`contractModal${form.dataset.contractId}`);
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                    }

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Imefanikiwa!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Refresh the dashboard data
                        fetchDashboard();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hitilafu!',
                        text: data.message || 'Imeshindwa kufuta ombi',
                        confirmButtonColor: '#dc2626'
                    });
                }
            } catch (err) {
                console.error('Delete error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Hitilafu!',
                    text: 'Imeshindwa kuwasiliana na seva',
                    confirmButtonColor: '#dc2626'
                });
            }

            return false;
        }

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

        function showSessionWarning(minutesLeft) {
            elements.sessionWarningText.textContent = `Muda wa Session utaisha baada ya dakika ${minutesLeft}`;
            elements.sessionWarning.style.display = 'block';
        }

        function hideSessionWarning() {
            elements.sessionWarning.style.display = 'none';
        }

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

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('sw-TZ', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            } catch {
                return dateString;
            }
        }

        window.addEventListener('beforeunload', () => {
            if (sessionCheckInterval) {
                clearInterval(sessionCheckInterval);
            }
        });
    </script>
    @include('sweetalert::alert');
</body>

</html>
