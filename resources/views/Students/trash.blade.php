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
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: visible;
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

        .dropdown-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            position: relative;
            z-index: 101;
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
            z-index: 1000;
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
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-user-graduate me-2"></i> Deleted Student Accounts
                        </h4>
                        <p class="mb-0 text-white-50"> Manage deleted student accounts and restore access</p>
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
                                @foreach ($students as $student)
                                    <tr>
                                        <td class="text-uppercase fw-bold">{{$student->admission_number}}</td>
                                        <td>
                                            <div class="student-info">
                                                <div class="student-avatar text-capitalize">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
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
                                                    <a href="{{route('student.delete.permanent', ['student' => Hashids::encode($student->id)])}}"
                                                       class="action-btn action-btn-danger"
                                                       title="Delete Permanently"
                                                       onclick="return confirmAction('delete permanently', '{{$student->first_name}}', '{{$student->last_name}}')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </li>
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
