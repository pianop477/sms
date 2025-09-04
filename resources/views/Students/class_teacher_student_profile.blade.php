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
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
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
            padding: 5px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .form-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }

        .form-label {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .required-star {
            color: var(--danger);
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px;
            height: auto;
            background-color: white;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .flatpickr-input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            background-color: white;
        }

        .flatpickr-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .invalid-feedback {
            font-weight: 600;
            color: var(--danger);
            margin-top: 5px;
        }

        .text-danger small {
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: #856404;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-warning-custom:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-success-custom:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .info-alert {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.25) 100%);
            border: 1px solid rgba(23, 162, 184, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            /* padding: 10px; */
            font-weight: 600;
            text-align: center;
        }

        .table-custom tbody td {
            /* padding: 10px; */
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .score-input {
            width: auto;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            transition: all 0.3s;
        }

        .score-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .grade-input {
            width: 60px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            font-weight: bold;
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

        .instruction-text {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.25) 100%);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 193, 7, 0.3);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .card-body {
                padding: 5px;
            }

            .header-title {
                font-size: 20px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .score-input, .grade-input {
                width: 100%;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(78, 84, 200, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0);
            }
        }
    </style>
    <div class="py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-10">
                <h4 class="text-primary fw-bold border-bottom pb-2">STUDENT INFORMATION</h4>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ url()->previous() }}" class="btn btn-info btn-action float-right">
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
                        <h5 class="profile-name mb-1 text-capitalize">{{ucwords(strtolower($students->first_name. ' '. $students->middle_name. ' '. $students->last_name))}}</h4>
                        <p class="mb-0 text-uppercase text-white">Admission #: <strong>{{$students->admission_number}}</strong></p>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-3">
                            <a href="javascript:void(0)" data-photo="{{ $avatarImage }}" class="btn btn-outline-danger mr-1 btn-action me-2 view-photo">
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
                                                {{ucwords(strtolower($students->parent_first_name. ' '. $students->parent_last_name))}}
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
                                    @else
                                        <tr>
                                            <th colspan="2" class="text-primary fw-bold">Mother's Information</th>
                                        </tr>
                                        <tr>
                                            <th>Mother's Name</th>
                                            <td>
                                                {{ucwords(strtolower($students->parent_first_name. ' '. $students->parent_last_name))}}
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
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="text-primary mb-3">{{strtoupper($students->first_name .' ' . $students->middle_name. ' '. $students->last_name)}}</h6>
                    <img id="student-photo" src="" alt="Student Photo" class="photo-modal img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
</body>
</html>
@endsection
