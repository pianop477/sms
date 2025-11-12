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
                <h4 class="text-primary fw-bold border-bottom pb-2">Profile Information</h4>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{route('OtherStaffs.index')}}" class="btn btn-info btn-action float-right">
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
                            $imageName = $staff->profile_image;
                            $imagePath = public_path('assets/img/profile/' . $imageName);

                            // Check if the image exists and is not empty
                            if (!empty($imageName) && file_exists($imagePath)) {
                                $avatarImage = asset('assets/img/profile/' . $imageName);
                            } else {
                                // Use default avatar based on gender
                                $avatarImage = asset('assets/img/profile/' . ($staff->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                            }
                        @endphp
                        <img src="{{ $avatarImage }}" class="profile-img" alt="Teacher Photo">
                        <h5 class="profile-name mb-1" style="color:gold;">{{$type == 'driver' ? ucwords(strtolower($staff->driver_name)) : ucwords(strtolower($staff->first_name. ' '. $staff->last_name))}}</h5>
                        <p class="mb-0 text-white text-uppercase">ID #: <strong>{{$staff->staff_id ?? 'n/a'}}</strong></p>
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
                                <span class="text-capitalize fw-bold">{{$staff->gender}}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Job Title</span>
                                <span class="text-capitalize fw-bold">{{$staff->job_title ?? 'N/A'}}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Status</span>
                                @if ($staff->status === 1)
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
                                    <i class="fas fa-user-tie me-1"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#education" data-bs-toggle="tab">
                                    <i class="fas fa-award me-1"></i> Education
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#edit" data-bs-toggle="tab">
                                    <i class="fas fa-pencil me-1"></i> Edit Information
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Profile Tab -->
                            <div class="tab-pane fade show active" id="profile">
                                <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i> Personal Particulars</h5>
                                <table class="info-table">
                                    <tr>
                                        <th><i class="fas fa-phone me-2"></i> Phone</th>
                                        <td>
                                            <a href="tel:{{$staff->phone}}" class="text-decoration-none">
                                                {{$staff->phone ?? 'N/A'}}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-envelope me-2"></i> Email</th>
                                        <td>
                                            @if ($staff->email == NULL)
                                                <span class="text-muted">N/A</span>
                                            @else
                                                <a href="mailto:{{$staff->email}}" class="text-decoration-none">
                                                    {{$staff->email}}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>{{\Carbon\Carbon::parse($staff->date_of_birth)->format('d-m-Y') ?? 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Joined Since</th>
                                        <td>{{$staff->joining_year ?? 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Registration Date</th>
                                        <td>{{\Carbon\Carbon::parse($staff->created_at)->format('d-m-Y') ?? 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-location-dot me-2"></i> Street Address</th>
                                        <td class="text-capitalize">{{ucwords(strtolower($staff->street_address)) ?? 'N/A'}}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Qualification Tab -->
                            <div class="tab-pane fade" id="education">
                                <h5 class="mb-4"><i class="fas fa-award me-2"></i> Education Level</h5>
                                <table class="info-table">
                                    <tr>
                                        <th>Qualification</th>
                                        <td>
                                            {{ucwords(strtolower($staff->educational_level)) ?? 'N/A'}}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Subjects Tab -->
                            <div class="tab-pane fade" id="edit">
                                <h5 class="mb-4"><i class="fas fa-pencil me-2"></i> Edit Information</h5>
                                {{-- edit form --}}
                                <form action="{{route('OtherStaffs.update', ['type' => $type, 'id' => Hashids::encode($staff->id)])}}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="first-name">First Name</label>
                                                <input type="text" name="fname" class="form-control-custom" value="{{$type == 'driver' ? old('fname', $staff->driver_name) : old('fname', $staff->first_name)}}">
                                                @error('fname')
                                                    <span class="text-danger text-sm">{{$message}}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        @if ($type != 'driver')
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label for="last-name">Last Name</label>
                                                        <input type="text" name="lname" class="form-control-custom" value="{{old('lname', $staff->last_name)}}">
                                                        @error('lname')
                                                            <span class="text-danger">{{$message}}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                        @endif
                                        <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label for="gender">Gender</label>
                                                    <select name="gender" id="gender" class="form-control-custom">
                                                        <option value="{{$staff->gender}}">{{$staff->gender}}</option>
                                                        <option value="male" >Male</option>
                                                        <option value="female" >Female</option>
                                                    </select>
                                                    @error('gender')
                                                        <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" name="email" class="form-control-custom" value="{{old('email', $staff->email)}}">
                                                @error('email')
                                                    <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="email">Phone Number</label>
                                                <input type="text" name="phone" class="form-control-custom" value="{{old('phone', $staff->phone)}}">
                                                @error('phone')
                                                    <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="email">Date of Birth</label>
                                                <input type="date" required name="dob" class="form-control-custom" value="{{old('dob', $staff->date_of_birth)}}">
                                                @error('dob')
                                                    <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="education" class="form-label">Education Level</label>
                                            <select name="education" id="education" class="form-control-custom" required>
                                                <option value="{{$staff->educational_level}}">{{$staff->educational_level}}</option>
                                                <option value="university">University</option>
                                                <option value="college">College</option>
                                                <option value="high_school">High school</option>
                                                <option value="secondary">Secondary school</option>
                                                <option value="primary">Primary school</option>
                                                <option value="other">Other</option>
                                            </select>
                                            @error('education')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="joined" class="form-label">Year Joined</label>
                                            <select name="joined" id="joined" class="form-control-custom" required>
                                                <option value="{{$staff->joining_year}}" selected>{{$staff->joining_year}}</option>
                                                @for ($year = date('Y'); $year >= 2010; $year--)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endfor
                                            </select>
                                            @error('joined')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="job_title" class="form-label">Job Title</label>
                                            <select name="job_title" id="job_title" class="form-control-custom" required>
                                                <option value="{{$staff->job_title}}">{{$staff->job_title}}</option>
                                                <option value="cooks">Cooks</option>
                                                <option value="matron">Matron</option>
                                                <option value="patron">Patron</option>
                                                <option value="cleaner">Cleaner</option>
                                                <option value="security guard">Security guard</option>
                                                <option value="other">Other</option>
                                            </select>
                                            @error('job_title')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="street" class="form-label">Street/Village</label>
                                            <input type="text" required name="street" class="form-control-custom" id="street" value="{{old('street', $staff->street_address)}}" placeholder="Street Address">
                                            @error('street')
                                            <div class="text-danger small">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                                <label for="profile_image" class="form-label">Profile Picture</label>
                                                <input type="file" name="image" class="form-control-custom" id="image" value="{{old('image')}}" placeholder="">
                                                @error('image')
                                                <div class="text-danger small">{{$message}}</div>
                                                @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <br>
                                        <button type="submit" class="btn btn-success float-right">
                                            <i class="fas fa-save"></i> Save Changes
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

    <!-- Photo Modal -->
    <div class="modal fade" id="teacherPhotoModal" tabindex="-1" aria-labelledby="teacherPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="teacherPhotoModalLabel">Profile Picture</h5>
                    <button type="button" class="btn-close btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="text-primary mb-3">{{$type == 'driver' ? strtoupper($staff->driver_name) : strtoupper($staff->first_name .' '. $staff->last_name)}}</h6>
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
