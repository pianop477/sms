@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.3);
        }

        .profile-name {
            font-weight: 500;
            margin-top: 10px;
        }

        .nav-pills .nav-link {
            color: var(--dark-color);
            border-radius: 0;
            padding: 12px 20px;
        }

        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #eaecf4;
        }

        .info-table {
            width: 100%;
        }

        .info-table th {
            width: 30%;
            padding: 12px 15px;
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        .info-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e3e6f0;
        }

        .info-table tr:last-child th,
        .info-table tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
        }

        .form-control-custom {
            border: 1px solid #d1d3e2;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control-custom:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .photo-modal img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.3);
        }

        @media (max-width: 768px) {
            .profile-img {
                width: 100px;
                height: 100px;
            }

            .info-table th,
            .info-table td {
                display: block;
                width: 100%;
            }

            .info-table th {
                background-color: transparent;
                padding-bottom: 5px;
                font-weight: 600;
            }

            .info-table td {
                padding-top: 5px;
                padding-bottom: 15px;
            }

            .nav-pills {
                flex-direction: column;
            }

            .nav-pills .nav-item {
                margin-bottom: 5px;
            }
        }
    </style>

    <div class="py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-10">
                <h4 class="text-primary fw-bold border-bottom pb-2">MY PROFILE</h4>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{route('home')}}" class="btn btn-info btn-action float-right">
                    <i class="fas fa-arrow-circle-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Profile Card -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="profile-header text-center">
                        @php
                            // Determine the image path
                            $imageName = $user->image;
                            $imagePath = public_path('assets/img/profile/' . $imageName);

                            // Check if the image exists and is not empty
                            if (!empty($imageName) && file_exists($imagePath)) {
                                $avatarImage = asset('assets/img/profile/' . $imageName);
                            } else {
                                // Use default avatar based on gender
                                $avatarImage = asset('assets/img/profile/' . ($user->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                            }
                        @endphp
                        <img src="{{ $avatarImage }}" class="profile-img" alt="User Photo">
                        <h5 class="profile-name mb-1" style="color:gold">{{ucwords(strtolower($user->first_name. ' '. $user->last_name))}}</h5>
                        <p class="mb-0 text-white">
                            @if ($user->usertype == 1)
                                System Administrator
                            @elseif ($user->usertype == 2)
                                School Administrator
                            @elseif($user->usertype == 3)
                                Teacher
                            @elseif ($user->usertype == 4)
                                Parent
                            @else
                                Accountant
                            @endif
                        </p>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-3">
                            <a href="javascript:void(0)" data-photo="{{ $avatarImage }}" class="btn btn-outline-danger mr-1 btn-action me-2 view-photo">
                                <i class="fas fa-image me-1"></i> View Photo
                            </a>
                        </div>

                        <div class="profile-detail">
                            @if ($user->usertype == 3)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Member ID</span>
                                <span class="text-uppercase fw-bold text-white">{{$user->member_id}}</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Role</span>
                                @if ($user->usertype == 1 || $user->usertype == 2)
                                    <span class="" style="font-weight: bold">Admin</span>
                                @elseif ($user->usertype == 3)
                                    <span class="text-capitalize" style="font-weight: bold">{{$user->role_name}}</span>
                                @elseif ($user->usertype == 4)
                                    <span class="" style="font-weight: bold">Parent</span>
                                @else
                                   <span class="" style="font-weight: bold">Accountant</span>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Gender</span>
                                <span class="text-capitalize fw-bold" style="font-weight: bold">{{$user->gender}}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Status</span>
                                @if ($user->status === 1)
                                    <span class="badge-status bg-success text-white">Active</span>
                                @else
                                    <span class="badge-status bg-secondary text-white">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Column - Details Card -->
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-pills flex-column flex-lg-row">
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link active" href="#profile" data-bs-toggle="tab">
                                    <i class="fas fa-user me-1"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#edit" data-bs-toggle="tab">
                                    <i class="fas fa-user-pen me-1"></i> Edit Account
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Profile Tab -->
                            <div class="tab-pane fade show active" id="profile">
                                <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i> Account Information</h5>
                                <table class="info-table">
                                    <tr>
                                        <th><i class="fas fa-phone me-2"></i> Phone</th>
                                        <td>
                                            <a href="tel:{{$user->phone}}" class="text-decoration-none">
                                                {{$user->phone}}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-envelope me-2"></i> Email</th>
                                        <td>
                                            @if ($user->email == NULL)
                                                <span class="text-muted">Not provided</span>
                                            @else
                                                <a href="mailto:{{$user->email}}" class="text-decoration-none">
                                                    {{$user->email}}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Registration Date</th>
                                        <td>
                                            @if ($user->created_at == Null)
                                                Unknown
                                            @else
                                                {{\Carbon\Carbon::parse($user->created_at)->format('d-m-Y')}}
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($user->usertype == 3 || $user->usertype == 4)
                                    <tr>
                                        <th><i class="fas fa-location-dot me-2"></i> Street Address</th>
                                        <td class="text-capitalize">{{$user->teacher_address ?? $user->parent_address}}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Edit Account Tab -->
                            <div class="tab-pane fade" id="edit">
                                <h5 class="mb-4"><i class="fas fa-user-pen me-2"></i> Edit Account Information</h5>

                                <form action="{{route('update.profile', $user->id)}}" method="POST" enctype="multipart/form-data" novalidate class="needs-validation">
                                    @csrf
                                    @method('PUT')

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">First Name</label>
                                                <input type="text" name="fname" class="form-control form-control-custom" value="{{$user->first_name}}" required>
                                                @error('fname')
                                                    <div class="text-danger text-sm mt-1">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" name="lname" class="form-control form-control-custom" value="{{$user->last_name}}" required>
                                                @error('lname')
                                                    <div class="text-danger text-sm mt-1">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Phone</label>
                                                <input type="text" name="phone" class="form-control form-control-custom" value="{{$user->phone}}" required>
                                                @error('phone')
                                                    <div class="text-danger text-sm mt-1">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control form-control-custom" value="{{$user->email}}">
                                                @error('email')
                                                    <div class="text-danger text-sm mt-1">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Gender</label>
                                                <select name="gender" class="form-control form-control-custom text-capitalize">
                                                    <option value="{{$user->gender}}" selected>{{$user->gender}}</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                                @error('gender')
                                                    <div class="text-danger text-sm mt-1">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if ($user->usertype == 3 || $user->usertype == 4)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Address</label>
                                                <input type="text" name="address" class="form-control form-control-custom" value="{{$user->parent_address ?? $user->teacher_address}}">
                                                @error('address')
                                                    <div class="text-danger text-sm mt-1">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Photo <span class="text-danger text-sm">(Max 1MB)</span></label>
                                                <input type="file" name="image" class="form-control form-control-custom">
                                                @error('image')
                                                    <div class="text-danger text-sm mt-1">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" id="saveButton" class="btn btn-success btn-action">
                                        <i class="fas fa-save me-1"></i> Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div class="modal fade" id="userPhotoModal" tabindex="-1" aria-labelledby="userPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userPhotoModalLabel">Profile Photo</h5>
                    <button type="button" class="btn-close btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="text-primary mb-3">{{strtoupper($user->first_name .' '. $user->last_name)}}</h6>
                    <img id="user-photo" src="" alt="User Photo" class="photo-modal img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-close btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Handle photo view modal
            document.querySelectorAll('.view-photo').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var photoUrl = this.getAttribute('data-photo');
                    document.getElementById('user-photo').setAttribute('src', photoUrl);
                    var modal = new bootstrap.Modal(document.getElementById('userPhotoModal'));
                    modal.show();
                });
            });

            // Activate tabs
            const triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))
            triggerTabList.forEach(function (triggerEl) {
                new bootstrap.Tab(triggerEl)
            });

            // Form submission handling
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (form && submitButton) {
                form.addEventListener("submit", function (event) {
                    event.preventDefault();

                    // Disable button and show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...`;

                    // Validate form
                    if (!form.checkValidity()) {
                        form.classList.add("was-validated");
                        submitButton.disabled = false;
                        submitButton.innerHTML = `<i class="fas fa-save me-1"></i> Save Changes`;
                        return;
                    }

                    // Submit form after a short delay
                    setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }
        });
    </script>
@endsection
