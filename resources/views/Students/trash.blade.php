@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --success: #28a745;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow-y: visible;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 7px 15px;
            position: relative;
            overflow: visible;
            z-index: 100;
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: rotate(30deg);
            z-index: -1;
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 24px;
        }

        .card-body {
            padding: 10px;
            position: relative;
            z-index: 1;
        }

        /* Tabs Styling */
        .nav-tabs-custom {
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 0;
            background: transparent;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            padding: 8px 20px;
            transition: all 0.3s;
            position: relative;
            background: transparent;
            border-radius: 0;
            font-size: 14px;
        }

        .nav-tabs-custom .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-tabs-custom .nav-link.active {
            color: white;
            background: transparent;
        }

        .nav-tabs-custom .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: white;
            border-radius: 3px;
        }

        .nav-tabs-custom .nav-link i {
            margin-right: 8px;
        }

        .badge-tab {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 50px;
            padding: 2px 8px;
            font-size: 11px;
            margin-left: 8px;
        }

        .nav-tabs-custom .nav-link.active .badge-tab {
            background: rgba(255, 255, 255, 0.3);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .table-custom {
            margin-bottom: 0;
            width: 100%;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody td {
            padding: 15px 12px;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .badge-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-danger-custom {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-secondary-custom {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 12px;
        }

        .action-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .action-list li {
            display: inline-block;
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 16px;
            transition: all 0.3s;
            padding: 8px;
            border-radius: 5px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            background-color: rgba(78, 84, 200, 0.1);
            transform: scale(1.1);
            color: var(--secondary);
        }

        .action-btn-success:hover {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .action-btn-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-right: 10px;
        }

        .student-info {
            display: flex;
            align-items: center;
        }

        .gender-badge {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }

        .gender-badge.male {
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
        }

        .gender-badge.female {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff9e9e 100%);
        }

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 10px;
            }

            .header-title {
                font-size: 20px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .action-list {
                flex-direction: column;
                gap: 10px;
            }

            .student-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .student-avatar {
                margin-right: 0;
                margin-bottom: 8px;
            }

            .nav-tabs-custom .nav-link {
                padding: 6px 12px;
                font-size: 12px;
            }

            .nav-tabs-custom .nav-link i {
                margin-right: 4px;
            }

            .badge-tab {
                font-size: 9px;
                padding: 1px 6px;
                margin-left: 4px;
            }
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="header-title text-white">
                            <i class="fas fa-user-graduate me-2"></i> Deleted Student Accounts
                        </h4>
                        <p class="mb-0 text-white">Manage deleted students</p>
                    </div>
                    <div class="col-md-6">
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs-custom justify-content-end" id="deletedAccountsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request()->routeIs('Teachers.trashed') ? 'active' : '' }}"
                                   href="{{ route('Teachers.trashed') }}"
                                   role="tab">
                                    <i class="fas fa-chalkboard-teacher"></i> Teachers
                                    <span class="badge-tab">{{ $teachersCount ?? 0 }}</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request()->routeIs('students.trash') ? 'active' : '' }}"
                                   href="{{ route('students.trash') }}"
                                   role="tab">
                                    <i class="fas fa-user-graduate"></i> Students
                                    <span class="badge-tab">{{ $students->count() ?? 0 }}</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request()->routeIs('staffs.trash') ? 'active' : '' }}"
                                   href="{{ route('staffs.trash') }}"
                                   role="tab">
                                    <i class="fas fa-user-friends"></i> Other Staffs
                                    <span class="badge-tab">{{ $staffsCount ?? 0 }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <i class="fas fa-user-graduate floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom table-responsive-md" id="myTable">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Admission No</th>
                                    <th scope="col">Student Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Class</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student)
                                    <tr>
                                        <td class="text-uppercase fw-bold">{{$student->admission_number}}</td>
                                        <td>
                                            <div class="student-info">
                                                <div class="student-avatar text-capitalize">
                                                    {{ ucwords(strtolower(substr($student->first_name, 0, 1))) }}{{ ucwords(strtolower(substr($student->last_name, 0, 1))) }}
                                                </div>
                                                <div>
                                                    <div class="text-capitalize fw-bold">
                                                        {{ucwords(strtolower($student->first_name. ' '. $student->middle_name. ' '. $student->last_name))}}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if(strtolower($student->gender[0]) === 'm')
                                                <span class="gender-badge male" title="Male">M</span>
                                            @elseif(strtolower($student->gender[0]) === 'f')
                                                <span class="gender-badge female" title="Female">F</span>
                                            @else
                                                <span class="gender-badge" title="Other">{{$student->gender[0]}}</span>
                                            @endif
                                        </td>
                                        <td class="text-uppercase fw-bold">{{$student->class_name}}</td>
                                        <td>
                                            @if ($student->status == 1)
                                                <span class="badge-success-custom">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @elseif ($student->status == 2)
                                                <span class="badge-danger-custom">
                                                    <i class="fas fa-trash me-1"></i> Deleted
                                                </span>
                                            @else
                                                <span class="badge-secondary-custom">
                                                    <i class="fas fa-ban me-1"></i> Blocked
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="action-list">
                                                <li>
                                                    <form action="{{route('student.restored.trash', ['student' => Hashids::encode($student->id)])}}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirmAction('restore', '{{$student->first_name}}', '{{$student->last_name}}')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-btn action-btn-success" title="Restore Student">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{route('student.delete.permanent', ['student' => Hashids::encode($student->id)])}}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirmPermanentDelete('{{strtoupper($student->first_name)}} {{strtoupper($student->last_name)}}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="action-btn action-btn-danger" title="Permanently Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-trash-alt fa-3x text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">No Deleted Students Found</h5>
                                            <p class="text-muted">There are no deleted student accounts at the moment.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Function to handle restore confirmation dialogs
            window.confirmAction = function(action, firstName, lastName) {
                const userName = `${firstName.toUpperCase()} ${lastName.toUpperCase()}`;
                return confirm(`Are you sure you want to ${action} ${userName}?`);
            };

            // Function to handle permanent delete confirmation
            window.confirmPermanentDelete = function(userName) {
                return confirm(`⚠️ WARNING: Are you sure you want to PERMANENTLY DELETE ${userName}?\n\nThis action cannot be undone!`);
            };
        });
    </script>
@endsection
