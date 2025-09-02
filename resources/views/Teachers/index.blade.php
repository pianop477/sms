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

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .teacher-avatar {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid #e3e6f0;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-table {
            background-color: white;
        }

        .progress-table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .progress-table th {
            padding: 15px 10px;
            font-weight: 600;
            vertical-align: middle;
        }

        .progress-table td {
            padding: 15px 10px;
            vertical-align: middle;
        }

        .badge-role {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-buttons a, .action-buttons button {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .dropdown-menu {
            border-radius: 5px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .btn-action {
                margin-bottom: 10px;
            }
        }
    </style>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="header-title">Registered Teachers</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-action dropdown-toggle mr-2" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cloud-arrow-down me-1"></i> Export
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                            <li>
                                                <a class="dropdown-item" href="{{route('teachers.excel.export')}}">
                                                    <i class="fas fa-file-excel me-1 text-success"></i> Excel
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{route('teachers.pdf.export')}}" target="_blank">
                                                    <i class="fas fa-file-pdf me-1 text-danger"></i> PDF
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                                        <i class="fas fa-user-plus me-1"></i> New Teacher
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Teachers Table -->
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table table-hover progress-table" id="teachersTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Member ID</th>
                                            <th scope="col">Teacher's Name</th>
                                            <th scope="col">Gender</th>
                                            <th scope="col">Role</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Joined</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($teachers as $teacher)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td class="fw-bold">{{strtoupper($teacher->member_id)}}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php
                                                            $imageName = $teacher->image;
                                                            $imagePath = public_path('assets/img/profile/' . $imageName);

                                                            if (!empty($imageName) && file_exists($imagePath)) {
                                                                $avatarImage = asset('assets/img/profile/' . $imageName);
                                                            } else {
                                                                $avatarImage = asset('assets/img/profile/' . ($teacher->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                                                            }
                                                        @endphp
                                                        <img src="{{ $avatarImage }}" alt="Teacher Avatar" class="teacher-avatar">
                                                        <span class="text-capitalize">{{ucwords(strtolower($teacher->first_name. ' '. $teacher->last_name))}}</span>
                                                    </div>
                                                </td>
                                                <td class="text-capitalize">
                                                    <span class="badge bg-info text-white">{{$teacher->gender[0]}}</span>
                                                </td>
                                                <td>
                                                    @if ($teacher->role_id == 1)
                                                        <span class="badge-role bg-danger text-white">{{$teacher->role_name}}</span>
                                                    @elseif ($teacher->role_id == 3)
                                                        <span class="badge-role bg-info text-white">{{$teacher->role_name}}</span>
                                                    @else
                                                        <span class="badge-role bg-success text-white">{{$teacher->role_name}}</span>
                                                    @endif
                                                </td>
                                                <td>{{$teacher->phone}}</td>
                                                <td>{{$teacher->joined}}</td>
                                                <td>
                                                    @if ($teacher->status == 1)
                                                        <span class="badge bg-success text-white">Active</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Blocked</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{route('teacher.profile', ['teacher' => Hashids::encode($teacher->id)])}}" class="btn btn-sm btn-primary" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        @if ($teacher->status == 1)
                                                            <form action="{{route('update.teacher.status', ['teacher' => Hashids::encode($teacher->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-warning" title="Block" onclick="return confirm('Are you sure you want to Block {{strtoupper($teacher->first_name)}} {{strtoupper($teacher->last_name)}}?')">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{route('teachers.restore', ['teacher' => Hashids::encode($teacher->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-success" title="Unblock" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($teacher->first_name)}} {{strtoupper($teacher->last_name)}}?')">
                                                                    <i class="ti-reload"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <form action="{{route('Teachers.remove', ['teacher' => Hashids::encode($teacher->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="btn btn-sm btn-danger" type="submit" title="Delete" onclick="return confirm('Are you sure you want to Delete {{ strtoupper($teacher->first_name) }} {{ strtoupper($teacher->last_name) }} Permanently?')">
                                                                <i class="ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
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
        </div>
    </div>

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTeacherModalLabel">Teacher Registration Form</h5>
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('Teachers.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" required name="fname" class="form-control" id="fname" placeholder="First name" value="{{old('fname')}}">
                                @error('fname')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lname" class="form-label">Other Names</label>
                                <input type="text" required name="lname" class="form-control" id="lname" placeholder="Middle & Last name" value="{{old('lname')}}">
                                @error('lname')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="emailPrefix">@</span>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Email ID" value="{{old('email')}}">
                                </div>
                                @error('email')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" id="gender" class="form-control" required>
                                    <option value="">-- select gender --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                @error('gender')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Mobile Phone</label>
                                <input type="text" required name="phone" class="form-control" id="phone" placeholder="Phone Number" value="{{old('phone')}}">
                                @error('phone')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="qualification" class="form-label">Qualification</label>
                                <select name="qualification" id="qualification" class="form-control" required>
                                    <option value="">-- Select Qualification --</option>
                                    <option value="1">Masters</option>
                                    <option value="2">Degree</option>
                                    <option value="3">Diploma</option>
                                    <option value="4">Certificate</option>
                                </select>
                                @error('qualification')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" required name="dob" class="form-control" id="dob" value="{{old('dob')}}" min="{{\Carbon\Carbon::now()->subYears(60)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(18)->format('Y-m-d')}}">
                                @error('dob')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="joined" class="form-label">Year Joined</label>
                                <select name="joined" id="joined" class="form-control" required>
                                    <option value="">-- Select Year --</option>
                                    @for ($year = date('Y'); $year >= 2010; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                @error('joined')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="street" class="form-label">Street/Village</label>
                                <input type="text" required name="street" class="form-control" id="street" value="{{old('street')}}" placeholder="Street Address">
                                @error('street')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="saveButton" class="btn btn-success">Save Teacher</button>
                        </div>
                    </form>
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
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...`;

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = "Save Teacher";
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
@endsection
