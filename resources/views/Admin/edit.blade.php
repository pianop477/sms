@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient-start: #4361ee;
            --gradient-end: #3a0ca3;
            --card-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .glass-card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-5px);
        }

        .profile-card {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(58, 12, 163, 0.1));
            border-radius: 24px;
            border: 2px solid rgba(67, 97, 238, 0.2);
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
        }

        .profile-avatar:hover {
            transform: scale(1.1);
            border-color: var(--primary);
        }

        .nav-pills .nav-link {
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            color: var(--dark);
            font-weight: 600;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background: rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .form-control {
            border: 2px solid rgba(67, 97, 238, 0.2);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--success), #0f9d58);
            border: none;
            border-radius: 20px;
            padding: 1rem 2rem;
            color: white;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 15px 35px rgba(28, 200, 138, 0.3);
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 45px rgba(28, 200, 138, 0.4);
            color: white;
        }

        .btn-back {
            background: linear-gradient(135deg, var(--info), var(--primary));
            border: none;
            border-radius: 16px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.3);
            color: white;
        }

        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .info-table tr {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .info-table tr:hover {
            background: rgba(67, 97, 238, 0.05);
            transform: translateX(5px);
        }

        .info-table th,
        .info-table td {
            padding: 1.2rem;
            border: none;
        }

        .info-table th {
            color: var(--primary);
            font-weight: 600;
            width: 30%;
        }

        .badge-modern {
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .list-group-item {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(67, 97, 238, 0.1);
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background: rgba(67, 97, 238, 0.05);
            transform: translateX(3px);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
            }

            .nav-pills .nav-link {
                padding: 0.75rem 1rem;
                margin: 0.25rem;
                font-size: 0.9rem;
            }

            .info-table th,
            .info-table td {
                padding: 0.8rem;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .slide-in {
            animation: slideInRight 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .tab-content {
            min-height: 400px;
        }
    </style>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="glass-card header-section fade-in" style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));">
            <div class="row align-items-center">
                <div class="col-md-10">
                    <h1 class="display-5 fw-bold mb-2 text-white">👑 Admin Profile</h1>
                    <p class="lead mb-0 opacity-90 text-white">Manage administrator account information</p>
                </div>
                <div class="col-md-2 text-end">
                    <a href="#" class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row fade-in">
            <!-- Profile Sidebar -->
            <div class="col-md-4 mb-4">
                <div class="glass-card profile-card text-center p-4">
                    <div class="mb-4">
                        @if ($user->image == Null)
                            @if ($user->gender == 'male')
                                <img src="{{ asset('assets/img/profile/avatar.jpg') }}" alt="Profile" class="profile-avatar">
                            @else
                                <img src="{{ asset('assets/img/profile/avatar-female.jpg') }}" alt="Profile" class="profile-avatar">
                            @endif
                        @else
                            <img src="{{ asset('assets/img/profile/'. $user->image) }}" alt="Profile" class="profile-avatar">
                        @endif
                    </div>

                    <h3 class="text-primary mb-2">
                        {{ ucwords(strtolower($user->first_name . ' ' . $user->last_name)) }}
                    </h3>
                    <p class="text-muted mb-4">System Administrator</p>

                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Gender</span>
                                <span class="text-capitalize badge-modern bg-info">{{ $user->gender }}</span>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Status</span>
                                @if ($user->status === 1)
                                    <span class="badge-modern bg-success">Active</span>
                                @else
                                    <span class="badge-modern bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="col-md-8 mb-4">
                <div class="glass-card">
                    <div class="card-header p-4 border-bottom">
                        <ul class="nav nav-pills justify-content-center">
                            <li class="nav-item">
                                <a class="nav-link active" href="#profile" data-bs-toggle="tab">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#edit" data-bs-toggle="tab">
                                    <i class="fas fa-user-pen me-2"></i>Edit Information
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content">
                            <!-- Profile Tab -->
                            <div class="tab-pane fade show active" id="profile">
                                <div class="table-responsive">
                                    <table class="info-table">
                                        <tbody>
                                            <tr class="slide-in">
                                                <th><i class="fas fa-phone me-2 text-primary"></i>Phone</th>
                                                <td class="fw-bold">{{ $user->phone }}</td>
                                            </tr>
                                            <tr class="slide-in" style="animation-delay: 0.1s;">
                                                <th><i class="fas fa-envelope me-2 text-primary"></i>Email</th>
                                                <td>{{ $user->email ?? 'Email not provided' }}</td>
                                            </tr>
                                            <tr class="slide-in" style="animation-delay: 0.2s;">
                                                <th><i class="fas fa-calendar me-2 text-primary"></i>Registration Date</th>
                                                <td>
                                                    @if ($user->created_at == Null)
                                                        Unknown
                                                    @else
                                                        {{ \Carbon\Carbon::parse($user->user_created_at)->format('d M Y') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr class="slide-in" style="animation-delay: 0.3s;">
                                                <th><i class="fas fa-id-card me-2 text-primary"></i>User ID</th>
                                                <td class="text-muted">#{{ $user->id }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Edit Tab -->
                            <div class="tab-pane fade" id="edit">
                                <div class="text-center mb-4">
                                    <h4 class="text-primary">
                                        <i class="fas fa-user-edit me-2"></i>Edit User Information
                                    </h4>
                                    <p class="text-muted">Update administrator account details</p>
                                </div>

                                <form action="{{ route('admin.account.update', ['user' => Hashids::encode($user->id)]) }}" method="POST" enctype="multipart/form-data" class="needs-validation">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-user me-2"></i>First Name
                                            </label>
                                            <input type="text" name="fname" class="form-control" value="{{ $user->first_name }}" required>
                                            @error('fname')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-user me-2"></i>Last Name
                                            </label>
                                            <input type="text" name="lname" class="form-control" value="{{ $user->last_name }}" required>
                                            @error('lname')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-phone me-2"></i>Phone
                                            </label>
                                            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
                                            @error('phone')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-envelope me-2"></i>Email
                                            </label>
                                            <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                            @error('email')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-venus-mars me-2"></i>Gender
                                            </label>
                                            <select name="gender" class="form-control" required>
                                                <option value="{{ $user->gender }}" selected>{{ ucfirst($user->gender) }}</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                            @error('gender')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-image me-2"></i>Profile Photo
                                            </label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                            <small class="text-muted">Maximum 1MB • JPG, PNG, GIF</small>
                                            @error('image')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="submit" id="saveButton" class="btn btn-modern">
                                            <i class="fas fa-save me-2"></i>Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Saving Changes...
                `;

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Add GSAP animations if available
            if (typeof gsap !== 'undefined') {
                gsap.from('.fade-in', {
                    duration: 1,
                    y: 30,
                    opacity: 0,
                    stagger: 0.2,
                    ease: "power3.out"
                });

                gsap.from('.slide-in', {
                    duration: 0.8,
                    x: 50,
                    opacity: 0,
                    stagger: 0.1,
                    ease: "power2.out"
                });
            }

            // Tab switching animation
            const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const target = this.getAttribute('href');
                    const tabPanes = document.querySelectorAll('.tab-pane');

                    tabPanes.forEach(pane => {
                        if (pane.id === target.substring(1)) {
                            pane.classList.add('fade-in');
                        } else {
                            pane.classList.remove('fade-in');
                        }
                    });
                });
            });
        });
    </script>
@endsection
