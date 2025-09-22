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
            font-weight: 700;
            margin-top: 15px;
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
                <h4 class="text-primary fw-bold border-bottom pb-2">STUDENT'S INFORMATION</h4>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('create.selected.class', Hashids::encode($students->class_id)) }}" class="btn btn-info btn-action float-right">
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
                            $imageName = $students->image;
                            $imagePath = public_path('assets/img/students/' . $imageName);

                            if (!empty($imageName) && file_exists($imagePath)) {
                                $avatarImage = asset('assets/img/students/' . $imageName);
                            } else {
                                $avatarImage = asset('assets/img/students/student.jpg');
                            }
                        @endphp
                        <img src="{{ $avatarImage }}" class="profile-img" alt="Student Photo">
                        <h5 class="profile-name mb-1 text-capitalize" style="color:rgb(206, 177, 8)">{{ucwords(strtolower($students->first_name. ' '. $students->middle_name. ' '. $students->last_name))}}</h5>
                        <p class="mb-0 text-uppercase text-white">Admission #: <strong>{{$students->admission_number}}</strong></p>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-3">
                            <a href="javascript:void(0)" data-photo="{{ $avatarImage }}" class="btn btn-outline-danger btn-action me-2 mr-1 view-photo">
                                <i class="fas fa-image me-1"></i> View Photo
                            </a>
                            <a href="{{ route('student.profile.picture', ['student' => Hashids::encode($students->id)]) }}" class="btn btn-outline-success btn-action">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>

                        <div class="profile-detail">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Gender</span>
                                <span class="text-capitalize fw-bold">{{$students->gender}}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Stream</span>
                                <span class="text-capitalize fw-bold">{{$students->group}}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Status</span>
                                @if ($students->status === 1)
                                    <span class="badge-status bg-success text-white">Active</span>
                                @else
                                    <span class="badge-status bg-secondary text-white">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <a href="{{route('students.modify', ['students' => Hashids::encode($students->id)])}}" class="btn btn-primary btn-action w-100 mt-3">
                            <i class="fas fa-edit me-1"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column - Details Card -->
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-pills flex-column flex-lg-row">
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link active" href="#student" data-bs-toggle="tab">
                                    <i class="fas fa-user-graduate me-1"></i> Student
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" href="#parents" data-bs-toggle="tab">
                                    <i class="fas fa-user-shield me-1"></i> Parents
                                </a>
                            </li>
                            @if ($students->transport_id != Null)
                                <li class="nav-item flex-fill text-center">
                                    <a class="nav-link" href="#transport" data-bs-toggle="tab">
                                        <i class="fas fa-bus me-1"></i> Transport
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Student Information Tab -->
                            <div class="tab-pane fade show active" id="student">
                                <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i> Student Details</h5>
                                <table class="info-table">
                                    <tr>
                                        <th>Class</th>
                                        <td class="text-uppercase">{{$students->class_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>{{\Carbon\Carbon::parse($students->dob)->format('d-m-Y')}}</td>
                                    </tr>
                                    <tr>
                                        <th>Registration Date</th>
                                        <td>{{\Carbon\Carbon::parse($students->created_at)->format('d-m-Y')}}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-location-dot me-2"></i> Street Address</th>
                                        <td class="text-capitalize">{{ucwords(strtolower($students->address))}}</td>
                                    </tr>
                                    <tr>
                                        <th>School Bus</th>
                                        <td class="text-capitalize">
                                            @if ($students->transport_id == Null)
                                                <span class="text-muted">N/A</span>
                                            @else
                                                <span class="text-success">Yes</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Parents Information Tab -->
                            <div class="tab-pane fade" id="parents">
                                <h5 class="mb-4"><i class="fas fa-users me-2"></i> Parents/Guardian Details</h5>
                                <table class="info-table">
                                    @if ($students->parent_gender == 'male')
                                        <tr>
                                            <th colspan="2" class="text-primary fw-bold">Father's Information</th>
                                        </tr>
                                        <tr>
                                            <th>Father's Name</th>
                                            <td>
                                                <a href="{{route('Parents.edit', ['parent' => Hashids::encode($students->parent_id)])}}" class="text-decoration-none">
                                                    {{ucwords(strtolower($students->parent_first_name. ' '. $students->parent_last_name))}}
                                                    <i class="fas fa-pen-to-square ms-1 text-primary"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone me-2"></i> Phone</th>
                                            <td>
                                                <a href="tel:{{ $students->phone }}" class="text-decoration-none">
                                                    {{ $students->phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-envelope me-2"></i> Email</th>
                                            <td>
                                                @if ($students->email == NULL)
                                                    <span class="text-muted">N/A</span>
                                                @else
                                                    <a href="mailto:{{ $students->parent_email }}" class="text-decoration-none">
                                                        {{ $students->email }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-location-dot me-2"></i> Street Address</th>
                                            <td class="text-capitalize">{{ucwords(strtolower($students->address))}}</td>
                                        </tr>
                                        <tr>
                                            <th>Registration Date</th>
                                            <td>{{\Carbon\Carbon::parse($students->parent_created_at)->format('d-m-Y')}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <th colspan="2" class="text-primary fw-bold">Mother's Information</th>
                                        </tr>
                                        <tr>
                                            <th>Mother's Name</th>
                                            <td>
                                                <a href="{{route('Parents.edit', ['parent' => Hashids::encode($students->parent_id)])}}" class="text-decoration-none">
                                                    {{ucwords(strtolower($students->parent_first_name. ' '. $students->parent_last_name))}}
                                                    <i class="fas fa-pen-to-square ms-1 text-primary"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone me-2"></i> Phone</th>
                                            <td>
                                                <a href="tel:{{ $students->parent_phone }}" class="text-decoration-none">
                                                    {{ $students->phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-envelope me-2"></i> Email</th>
                                            <td>
                                                @if ($students->email == NULL)
                                                    <span class="text-muted">N/A</span>
                                                @else
                                                    <a href="mailto:{{ $students->parent_email }}" class="text-decoration-none">
                                                        {{ $students->email }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-location-dot me-2"></i> Street Address</th>
                                            <td class="text-capitalize">{{ucwords(strtolower($students->address))}}</td>
                                        </tr>
                                        <tr>
                                            <th>Registration Date</th>
                                            <td>{{\Carbon\Carbon::parse($students->parent_created_at)->format('d-m-Y')}}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Transport Tab -->
                            @if ($students->transport_id != Null)
                            <div class="tab-pane fade" id="transport">
                                <h5 class="mb-4"><i class="fas fa-bus me-2"></i> Transport Information</h5>
                                <table class="info-table">
                                    <tr>
                                        <th>Driver Name</th>
                                        <td>{{ucwords(strtolower($students->driver_name))}}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-phone me-2"></i> Phone</th>
                                        <td>
                                            <a href="tel:{{$students->driver_phone}}">
                                                {{$students->driver_phone}}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td class="text-capitalize">{{ucwords(strtolower($students->driver_gender))}}</td>
                                    </tr>
                                    <tr>
                                        <th>Bus Number</th>
                                        <td class="text-capitalize">{{ucwords(strtolower($students->bus_no))}}</td>
                                    </tr>
                                    <tr>
                                        <th>School Bus Route</th>
                                        <td class="text-capitalize">
                                           {{$students->routine}}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div class="modal fade" id="studentPhotoModal" tabindex="-1" aria-labelledby="studentPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentPhotoModalLabel">Student Photo</h5>
                    <button type="button" class="btn-close btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="text-primary mb-3">{{strtoupper($students->first_name .' ' . $students->middle_name. ' '. $students->last_name)}}</h6>
                    <img id="student-photo" src="" alt="Student Photo" class="photo-modal img-fluid">
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
                    document.getElementById('student-photo').setAttribute('src', photoUrl);
                    var modal = new bootstrap.Modal(document.getElementById('studentPhotoModal'));
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
