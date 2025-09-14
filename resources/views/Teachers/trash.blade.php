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
            /* padding: 20px; */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: visible; /* Changed from hidden to visible */
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative; /* Added for proper dropdown positioning */
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 12px 15px;
            position: relative;
            overflow: visible; /* Changed from hidden to visible */
            z-index: 100; /* Lower than dropdown */
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
            z-index: -1; /* Keep background behind content */
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
            z-index: 1; /* Ensure content stays above background */
        }

        .dropdown-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 5px 10px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            position: relative;
            z-index: 101; /* Higher than card header */
        }

        .dropdown-custom:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }

        .dropdown-menu-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            z-index: 1000; /* Highest z-index to ensure visibility */
            position: absolute;
            margin-top: 5px;
        }

        .dropdown-item-custom {
            padding: 12px 20px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .dropdown-item-custom:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
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

        .badge-danger-custom {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
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
            gap: 12px;
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

        .user-avatar {
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

        .user-info {
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

        /* Fix for dropdown positioning */
        .dropdown-container {
            position: relative;
            display: inline-block;
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
                gap: 8px;
            }

            .user-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-avatar {
                margin-right: 0;
                margin-bottom: 8px;
            }

            .dropdown-container {
                margin-top: 15px;
                width: 100%;
            }

            .dropdown-custom {
                width: 100%;
                text-align: center;
            }

            .dropdown-menu-custom {
                width: 100%;
                left: 0 !important;
                right: 0 !important;
            }
        }
    </style>

    <div class="">
        <div class="glass-card">
            <!-- Card Header with Dropdown -->
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-trash-alt me-2"></i> Deleted Teachers Accounts
                        </h4>
                        <p class="mb-0 text-white">Manage deleted teacher accounts and restore access</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="dropdown-container">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="dropdown-custom" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-archive me-2"></i> Deleted Accounts
                                </button>
                                <div class="dropdown-menu dropdown-menu-custom" aria-labelledby="btnGroupDrop1">
                                    <a href="{{route('Teachers.trashed')}}" class="dropdown-item dropdown-item-custom">
                                        <i class="fas fa-chalkboard-teacher me-2"></i> Teachers
                                    </a>
                                    <a class="dropdown-item dropdown-item-custom" href="{{route('students.trash')}}">
                                        <i class="fas fa-user-graduate me-2"></i> Students
                                    </a>
                                    <a class="dropdown-item dropdown-item-custom" href="#">
                                        <i class="fas fa-user-friends me-2"></i> Parents
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <i class="fas fa-user-slash floating-icons"></i>
            </div>

            <!-- Card Body with Table -->
            <div class="card-body">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom table-responsive-md" id="myTable">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Joined</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teachers as $teacher)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar text-capitalize">
                                                    {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-capitalize fw-bold">{{ucwords(strtolower($teacher->first_name. ' '. $teacher->last_name))}}</div>
                                                    <small class="text-muted">ID: {{$teacher->id}}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if(strtolower($teacher->gender[0]) === 'm')
                                                <span class="gender-badge male" title="Male">M</span>
                                            @elseif(strtolower($teacher->gender[0]) === 'f')
                                                <span class="gender-badge female" title="Female">F</span>
                                            @else
                                                <span class="gender-badge" title="Other">{{$teacher->gender[0]}}</span>
                                            @endif
                                        </td>
                                        <td>{{$teacher->phone ?? 'N/A'}}</td>
                                        <td>{{$teacher->email ?? 'N/A'}}</td>
                                        <td>{{$teacher->joined ?? 'N/A'}}</td>
                                        <td>
                                            @if ($teacher->status == 2)
                                                <span class="badge-danger-custom">
                                                    <i class="fas fa-trash me-1"></i> Deleted
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="action-list">
                                                @if ($teacher->status == 1)
                                                    <li>
                                                        <a href="{{route('Teachers.show.profile', ['teacher' => Hashids::encode($teacher->id)])}}"
                                                           class="action-btn"
                                                           title="View Profile">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{route('update.teacher.status', ['teacher' => Hashids::encode($teacher->id)])}}"
                                                              method="POST"
                                                              class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="action-btn action-btn-danger" onclick="return confirm('Block', '{{$teacher->first_name}}', '{{$teacher->last_name}}')" title="Block Teacher">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{route('teachers.restore', ['teacher' => Hashids::encode($teacher->id)])}}"
                                                              method="POST"
                                                              class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="action-btn action-btn-success" onclick="return confirm('Restore', '{{$teacher->first_name}}', '{{$teacher->last_name}}')" title="Restore Teacher">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Function to handle confirmation dialogs
            window.confirmAction = function(action, firstName, lastName) {
                const userName = `${firstName.toUpperCase()} ${lastName.toUpperCase()}`;
                return confirm(`Are you sure you want to ${action} ${userName}?`);
            };

            // Initialize dropdown functionality
            $('.dropdown-toggle').dropdown();

            // Ensure dropdown menu stays visible when clicked
            $(document).on('click', '.dropdown-custom', function(e) {
                e.stopPropagation();
            });
        });
    </script>
@endsection
