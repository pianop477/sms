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

        .badge-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 50px;
            padding: 4px 8px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-info-custom {
            background: linear-gradient(135deg, var(--info) 0%, #17a2b8 100%);
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

        .badge-secondary-custom {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            border-radius: 50px;
            padding: 4px 8px;
            font-weight: 600;
            font-size: 12px;
        }

        .btn-reset {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            border: none;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 12px;
        }

        .btn-reset:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-reset:disabled {
            opacity: 0.7;
            transform: none;
            box-shadow: none;
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

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .action-form {
            margin: 0;
            display: inline;
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

            .btn-reset {
                padding: 6px 12px;
                font-size: 11px;
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
                            <i class="fas fa-users me-2"></i> All System Users
                        </h4>
                        <p class="mb-0 text-white-50"> Manage user accounts and reset passwords</p>
                    </div>
                </div>
                <i class="fas fa-user-gear floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom table-responsive-md" id="myTable">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">User Type</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Status</th>
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
                                                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-capitalize fw-bold">{{ucwords(strtolower($user->first_name . ' '. $user->last_name))}}</div>
                                                    <small class="text-muted">{{$user->email ?? 'N/A'}}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($user->usertype == 3)
                                                <span class="badge-info-custom">
                                                    <i class="fas fa-chalkboard-teacher me-1"></i> Teacher
                                                </span>
                                            @else
                                                <span class="badge-primary-custom">
                                                    <i class="fas fa-user-friends me-1"></i> Parent
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{$user->phone ?? 'N/A'}}</td>
                                        <td>
                                            @if ($user->status == 1)
                                                <span class="badge-success-custom">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @else
                                                <span class="badge-secondary-custom">
                                                    <i class="fas fa-ban me-1"></i> Blocked
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{route('users.reset.password', ['user' => Hashids::encode($user->id)])}}"
                                                  method="POST"
                                                  class="action-form"
                                                  onsubmit="return confirmReset('{{$user->first_name}}', '{{$user->last_name}}', this)">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn-reset" id="resetBtn-{{$user->id}}">
                                                    <i class="fas fa-unlock me-1"></i> Reset Password
                                                </button>
                                            </form>
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
            // Function to handle password reset confirmation and loading state
            window.confirmReset = function(firstName, lastName, form) {
                const userName = `${firstName.toUpperCase()} ${lastName.toUpperCase()}`;
                const button = form.querySelector('button[type="submit"]');

                if (confirm(`Are you sure you want to reset password for ${userName}?`)) {
                    // Show loading state
                    button.disabled = true;
                    button.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Resetting...`;

                    // Submit the form
                    form.submit();
                    return true;
                }
                return false;
            };

            // Reset button states when page is loaded (in case of back navigation)
            const resetButtons = document.querySelectorAll('.btn-reset');
            resetButtons.forEach(button => {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-unlock me-1"></i> Reset Password';
            });
        });
    </script>
@endsection
