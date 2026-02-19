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

        .teacher-card {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0.15rem 0.5rem 0 rgba(58, 59, 69, 0.1);
        }

        .photo-modal img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.3);
        }

        .section-title {
            font-weight: 600;
            color: var(--primary-color);
            border-bottom: 2px solid #e3e6f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
         .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .note-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
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
                <h4 class="text-primary fw-bold border-bottom pb-2">ACCOUNTANT INFORMATION</h4>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('Accountants.index') }}" class="btn btn-info btn-action float-right">
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
                            $imagePath = storage_path('app/public/profile/' . $imageName);

                            // Check if the image exists and is not empty
                            if (!empty($imageName) && file_exists($imagePath)) {
                                $avatarImage = asset('storage/profile/' . $imageName);
                            } else {
                                // Use default avatar based on gender
                                $avatarImage = asset('storage/profile/' . (strtolower($user->gender) == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                            }
                        @endphp
                        <img src="{{ $avatarImage }}" class="profile-img" alt="Teacher Photo">
                        <h5 class="profile-name mb-1" style="color:gold;">{{ucwords(strtolower($user->first_name. ' '. $user->last_name))}}</h5>
                        <p class="mb-0 text-white text-uppercase"><strong>Accountant</strong></p>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-3">
                            <a href="javascript:void(0)" data-photo="{{ $avatarImage }}" class="btn btn-outline-danger mr-1 btn-action me-2 view-photo">
                                <i class="fas fa-image me-1"></i> View Photo
                            </a>
                        </div>

                        <div class="profile-detail">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Gender</span>
                                <span class="text-capitalize fw-bold">{{$user->gender}}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Job Title</span>
                                <span class="text-capitalize fw-bold">Accountant</span>
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
                                <a class="nav-link active" href="#teacher" data-bs-toggle="tab">
                                    <i class="fas fa-user-tie me-1"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#edit" data-bs-toggle="tab">
                                    <i class="ti-user-pencil me-1"></i> Edit information
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Profile Tab -->
                            <div class="tab-pane fade show active" id="teacher">
                                <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i> Personal Particulars</h5>
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
                                                <span class="text-muted">N/A</span>
                                            @else
                                                <a href="mailto:{{$user->email}}" class="text-decoration-none">
                                                    {{$user->email}}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Registered On</th>
                                        <td>{{\Carbon\Carbon::parse($user->created_at)->format('d-m-Y')}}</td>
                                    </tr>
                                </table>
                            </div>
                            <!-- Edit information Tab -->
                            <div class="tab-pane fade" id="edit">
                                <h5 class="mb-4"><i class="ti-pencil me-2"></i> Edit Information</h5>

                                <form action="{{route('Accountants.update', ['id' => Hashids::encode($user->id)])}}" method="POST" novalidate class="needs-validation" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <input type="text" name="fname" class="form-control-custom" value="{{$user->first_name}}" required>
                                            @error('fname')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" name="lname" class="form-control-custom" value="{{$user->last_name}}" required>
                                            @error('lname')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone" class="form-control-custom" value="{{$user->phone}}" required>
                                            @error('phone')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email ID</label>
                                            <input type="email" name="email" class="form-control-custom" value="{{$user->email}}">
                                            @error('email')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Gender</label>
                                            <select name="gender" class="form-control-custom" required>
                                                <option value="{{$user->gender}}" selected>{{ucfirst($user->gender)}}</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                            @error('gender')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Profile Photo</label>
                                            <input type="file" name="image" class="form-control-custom">
                                            <div class="note-text">Maximum 1MB - Recommended size 300x300 pixels</div>
                                            @error('image')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <button type="submit" id="saveButton" class="btn btn-success btn-action">
                                        <i class="fas fa-save me-2"></i> Save Changes
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
    <div class="modal fade" id="teacherPhotoModal" tabindex="-1" aria-labelledby="teacherPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="teacherPhotoModalLabel">Profile Picture</h5>
                    <button type="button" class="btn-close btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="text-primary mb-3">{{strtoupper($user->first_name .' '. $user->last_name)}}</h6>
                    <img id="teacher-photo" src="" alt="Teacher Photo" class="photo-modal img-fluid">
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
                    document.getElementById('teacher-photo').setAttribute('src', photoUrl);
                    var modal = new bootstrap.Modal(document.getElementById('teacherPhotoModal'));
                    modal.show();
                });
            });

            // Activate tabs
            const triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))
            triggerTabList.forEach(function (triggerEl) {
                new bootstrap.Tab(triggerEl)
            });
        });
    </script>
@endsection
