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

        /* Tabs Styling */
        .nav-tabs-custom {
            border-bottom: none;
            margin-bottom: 0;
            display: flex;
            gap: 5px;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            padding: 8px 20px;
            transition: all 0.3s;
            background: transparent;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nav-tabs-custom .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-tabs-custom .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-tabs-custom .nav-link i {
            margin-right: 4px;
        }

        .badge-tab {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 50px;
            padding: 2px 8px;
            font-size: 11px;
            margin-left: 6px;
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

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
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

        .status-badge i {
            font-size: 0.7rem;
        }

        .status-deleted {
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

        .action-icon.success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
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

            .nav-tabs-custom {
                justify-content: center;
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
                /* color: #e9ecef; */
                border-bottom-color: #495057;
            }

            .table-modern tbody tr:hover {
                background: #343a40;
            }

            .status-deleted {
                background: #742a2a;
                color: #fed7d7;
            }
        }
    </style>

    <div class="animated-bg"></div>
    <div class="particles"></div>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Header with Tabs -->
            <div class="card-header-modern">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <div class="header-title">
                            <h3>Deleted Accounts</h3>
                            <p>Manage and restore deleted accounts</p>
                        </div>
                    </div>
                    <div class="nav-tabs-custom">
                        <a class="nav-link {{ request()->routeIs('Teachers.trashed') ? 'active' : '' }}"
                           href="{{ route('Teachers.trashed') }}">
                            <i class="fas fa-chalkboard-teacher"></i> Teachers
                            <span class="badge-tab">{{ $teachers->count() }}</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('students.trash') ? 'active' : '' }}"
                           href="{{ route('students.trash') }}">
                            <i class="fas fa-user-graduate"></i> Students
                            <span class="badge-tab">{{ $studentsCount ?? 0 }}</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('staffs.trash') ? 'active' : '' }}"
                           href="{{ route('staffs.trash') }}">
                            <i class="fas fa-user-tie"></i> Other Staffs
                            <span class="badge-tab">{{ $staffsCount ?? 0 }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                @if ($teachers->isEmpty())
                    <div class="empty-state-modern">
                        <i class="fas fa-trash-alt"></i>
                        <h6>No Deleted Accounts Found</h6>
                        <p class="text-muted small">There are no deleted teacher accounts at the moment.</p>
                    </div>
                @else
                    <div class="table-container-modern">
                        <table class="table-modern" id="deletedTable">
                            <thead>
                                <tr>
                                    <th class="">#</th>
                                    <th class="">Staff ID</th>
                                    <th class="">Teacher Name</th>
                                    <th class="">Gender</th>
                                    <th class="">Phone</th>
                                    <th class="">Email</th>
                                    <th class="">Joined</th>
                                    <th class="">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teachers as $teacher)
                                    <tr>
                                        <td><span class="fw-bold">{{ $loop->iteration }}</span></td>
                                        <td>
                                            <span class="badge bg-secondary">{{ strtoupper($teacher->member_id) }}</span>
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar text-uppercase">
                                                    {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong class="text-capitalize">{{ $teacher->first_name }} {{ $teacher->last_name }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="gender-badge {{ strtolower($teacher->gender) == 'male' ? 'gender-male' : 'gender-female' }}">
                                                {{ strtoupper(substr($teacher->gender, 0, 1)) }}
                                            </div>
                                        </td>
                                        <td>{{ $teacher->phone ?? 'N/A' }}</td>
                                        <td>{{ $teacher->email ?? 'N/A' }}</td>
                                        <td>{{ $teacher->joined ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-badge status-deleted">
                                                <i class="fas fa-trash"></i> Deleted
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-icons justify-content-center">
                                                <form action="{{ route('teachers.restore', ['teacher' => Hashids::encode($teacher->id)]) }}"
                                                      method="POST"
                                                      class="restore-form"
                                                      data-name="{{ $teacher->first_name }} {{ $teacher->last_name }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="action-icon success" title="Restore Teacher">
                                                        <i class="fas fa-undo-alt"></i>
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // ============ DATATABLE INITIALIZATION ============
            // Check if table exists and destroy any existing instance
            if ($.fn.DataTable.isDataTable('#deletedTable')) {
                $('#deletedTable').DataTable().destroy();
            }

            // Initialize DataTable with safe options
            $('#deletedTable').DataTable({
                paging: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                ordering: true,
                info: true,
                searching: true,
                autoWidth: false,
                stateSave: false,
                language: {
                    emptyTable: "No deleted records found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    lengthMenu: "Show _MENU_ entries",
                    search: "Search:",
                    zeroRecords: "No matching records found",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // ============ RESTORE CONFIRMATION WITH SWEETALERT ============
            $('.restore-form').on('submit', function(e) {
                e.preventDefault();

                const userName = $(this).data('name');

                Swal.fire({
                    title: 'Restore Account?',
                    html: `Are you sure you want to restore <strong>${userName}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, restore it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Restoring...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the form
                        this.submit();
                    }
                });
            });
        });
    </script>
@endsection
