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
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 10px;
            position: relative;
            overflow: hidden;
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
            /* padding: 15px 12px; */
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody td {
            /* padding: 15px 12px; */
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .badge-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 50px;
            padding: 4px 8px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            border-radius: 50px;
            padding: 4px 8px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            color: #856404;
            border-radius: 50px;
            padding: 4px 8px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-secondary-custom {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            border-radius: 50px;
            padding: 4px 8px;
            font-weight: 600;
            font-size: 12px;
        }

        .btn-update {
            background: linear-gradient(135deg, var(--info) 0%, #17a2b8 100%);
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-update:hover {
            background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3);
            color: white;
            text-decoration: none;
        }

        .gender-badge {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            border-radius: 50px;
            padding: 3px 6px;
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

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 25px;
        }

        .pagination-custom .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-color: var(--primary);
            color: white;
        }

        .pagination-custom .page-link {
            color: var(--primary);
            border-radius: 10px;
            margin: 0 3px;
            border: 1px solid #e9ecef;
            /* padding: 8px 16px; */
            font-weight: 600;
        }

        .pagination-custom .page-link:hover {
            background-color: rgba(78, 84, 200, 0.1);
            color: var(--primary);
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

        @media (max-width: 768px) {
            .card-body {
                padding: 5px;
            }

            .header-title {
                font-size: 20px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .btn-update {
                padding: 3px 6px;
                font-size: 12px;
            }

            .user-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-avatar {
                margin-right: 0;
                margin-bottom: 8px;
            }
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-12 text-center">
                        <h4 class="header-title text-white">
                            <i class="fas fa-user-shield me-2"></i> Roles & Permissions Management
                        </h4>
                        <p class="mb-0 text-white"> Manage user roles and permissions across the system</p>
                    </div>
                </div>
                <i class="fas fa-lock floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom table-responsive-md">
                            <thead class="">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col" class="text-center">Gender</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Role</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar text-capitalize">
                                                    {{ ucwords(strtolower(substr($user->first_name, 0, 1))) }}{{ ucwords(strtolower(substr($user->last_name, 0, 1))) }}
                                                </div>
                                                <div>
                                                    <div class="text-capitalize fw-bold">{{ucwords(strtolower($user->first_name))}} {{ucwords(strtolower($user->last_name))}}</div>
                                                    <small class="text-muted">{{strtolower($user->email) ?? 'No email'}}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if(strtolower($user->gender[0]) === 'm')
                                                <span class="gender-badge male" title="Male">M</span>
                                            @elseif(strtolower($user->gender[0]) === 'f')
                                                <span class="gender-badge female" title="Female">F</span>
                                            @else
                                                <span class="gender-badge" title="Other">{{$user->gender[0]}}</span>
                                            @endif
                                        </td>
                                        <td>{{$user->phone ?? 'N/A'}}</td>
                                        <td>
                                            @if ($user->role_id == 3)
                                                <span class="badge-primary-custom">{{$user->role_name}}</span>
                                            @elseif ($user->role_id == 2)
                                                <span class="badge-success-custom">{{$user->role_name}}</span>
                                            @elseif ($user->role_id == 4)
                                                <span class="badge-warning-custom">{{$user->role_name}}</span>
                                            @else
                                                <span class="badge-secondary-custom">{{$user->role_name}}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('roles.assign', ['user' => Hashids::encode($user->id)])}}" class="btn-update">
                                                <i class="fas fa-edit"></i> Update Role
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pagination-container">
                    {{$users->links('vendor.pagination.bootstrap-5')}}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add custom styling to pagination
            const pagination = document.querySelector('.pagination');
            if (pagination) {
                pagination.classList.add('pagination-custom');
            }
        });
    </script>
@endsection
